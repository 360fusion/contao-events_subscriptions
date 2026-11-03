<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests\Payment;

use Codefog\EventsSubscriptionsBundle\Payment\GovUkPayProvider;
use Codefog\EventsSubscriptionsBundle\Payment\MollieProvider;
use Codefog\EventsSubscriptionsBundle\Payment\PayPalProvider;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Codefog\EventsSubscriptionsBundle\Payment\StripeProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * The providers against canned API answers: what is sent (amount in pence, who pays, which payment), how the answers
 * become a payment status, and what a webhook may tell us. Nothing leaves the machine.
 */
class ProviderTest extends TestCase
{
    /** @var array<int, array{method: string, url: string, body: string, options: array}> */
    private array $sent = [];

    private function client(array $answers): MockHttpClient
    {
        $this->sent = [];

        return new MockHttpClient(function (string $method, string $url, array $options) use (&$answers): MockResponse {
            $this->sent[] = ['method' => $method, 'url' => $url, 'body' => (string) ($options['body'] ?? ''), 'options' => $options];
            $answer = array_shift($answers) ?? [];

            return new MockResponse(json_encode($answer), ['response_headers' => ['content-type: application/json']]);
        });
    }

    private function request(int $amount = 1250): PaymentRequest
    {
        return new PaymentRequest($amount, 'GBP', 'Cannulation course', 'tok123', 'https://example.test/return', 'https://example.test/hook', 'a@example.test');
    }

    public function testAmountsAreFormattedFromPence(): void
    {
        $this->assertSame('12.50', PaymentRequest::decimal(1250));
        $this->assertSame('0.05', PaymentRequest::decimal(5));
        $this->assertSame('100.00', PaymentRequest::decimal(10000));
        $this->assertSame('1999.99', PaymentRequest::decimal(199999));
    }

    public function testStripeSendsThePriceInPenceAndMapsStatuses(): void
    {
        $provider = new StripeProvider($this->client([['id' => 'cs_1', 'url' => 'https://checkout.stripe.test/cs_1']]), 'sk_test_x');
        $checkout = $provider->createCheckout($this->request());

        $this->assertSame('cs_1', $checkout->reference);
        $this->assertSame('https://checkout.stripe.test/cs_1', $checkout->url);
        $this->assertSame('https://api.stripe.com/v1/checkout/sessions', $this->sent[0]['url']);
        parse_str($this->sent[0]['body'], $body);
        $this->assertSame('1250', $body['line_items'][0]['price_data']['unit_amount']);
        $this->assertSame('gbp', $body['line_items'][0]['price_data']['currency']);
        $this->assertSame('tok123', $body['client_reference_id']);
        $this->assertSame('a@example.test', $body['customer_email']);

        foreach ([
            [['payment_status' => 'paid', 'status' => 'complete'], PaymentStatus::PAID],
            [['payment_status' => 'unpaid', 'status' => 'open'], PaymentStatus::PENDING],
            [['payment_status' => 'unpaid', 'status' => 'expired'], PaymentStatus::FAILED],
        ] as [$answer, $expected]) {
            $this->assertSame($expected, (new StripeProvider($this->client([$answer]), 'sk_test_x'))->fetchStatus('cs_1'));
        }
    }

    public function testStripeRefundsTheWholePaymentIntent(): void
    {
        $provider = new StripeProvider($this->client([['payment_intent' => 'pi_9'], []]), 'sk_test_x');
        $provider->refund('cs_1', 1250, 'GBP');

        $this->assertSame('https://api.stripe.com/v1/refunds', $this->sent[1]['url']);
        parse_str($this->sent[1]['body'], $body);
        $this->assertSame(['payment_intent' => 'pi_9', 'amount' => '1250'], $body);
    }

    public function testMollieSendsADecimalAmountAndSkipsTheWebhookOnLocalSites(): void
    {
        $provider = new MollieProvider($this->client([['id' => 'tr_1', '_links' => ['checkout' => ['href' => 'https://pay.mollie.test/tr_1']]]]), 'test_key');
        $checkout = $provider->createCheckout($this->request());

        $json = $this->sent[0]['options']['body'] ?? null;
        $sent = json_decode((string) $this->sent[0]['body'], true);
        $this->assertSame(['currency' => 'GBP', 'value' => '12.50'], $sent['amount']);
        $this->assertArrayNotHasKey('webhookUrl', $sent, 'a .test site cannot be reached by Mollie');
        $this->assertSame('tr_1', $checkout->reference);

        $public = new PaymentRequest(1250, 'GBP', 'x', 'tok', 'https://events.example.com/r', 'https://events.example.com/hook');
        (new MollieProvider($this->client([['id' => 'tr_2', '_links' => ['checkout' => ['href' => 'https://pay.mollie.test/tr_2']]]]), 'test_key'))->createCheckout($public);
        $this->assertSame('https://events.example.com/hook', json_decode((string) $this->sent[0]['body'], true)['webhookUrl']);
    }

    public function testMollieStatusesAndWebhook(): void
    {
        foreach (['paid' => PaymentStatus::PAID, 'failed' => PaymentStatus::FAILED, 'canceled' => PaymentStatus::FAILED, 'expired' => PaymentStatus::FAILED, 'open' => PaymentStatus::PENDING, 'pending' => PaymentStatus::PENDING] as $status => $expected) {
            $this->assertSame($expected, (new MollieProvider($this->client([['status' => $status]]), 'k'))->fetchStatus('tr_1'), $status);
        }

        $provider = new MollieProvider($this->client([]), 'k');
        $this->assertSame('tr_5', $provider->getWebhookReference(Request::create('/hook', 'POST', ['id' => 'tr_5'])));
        $this->assertNull($provider->getWebhookReference(Request::create('/hook', 'POST', [])));
        $this->assertTrue(MollieProvider::isLocal('https://events-site57.test/x'));
        $this->assertFalse(MollieProvider::isLocal('https://events.example.com/x'));
    }

    public function testGovUkPayStatusesRefundAndWebhook(): void
    {
        $provider = new GovUkPayProvider($this->client([['payment_id' => 'gp1', '_links' => ['next_url' => ['href' => 'https://pay.gov.test/gp1']]]]), 'api_key');
        $checkout = $provider->createCheckout($this->request());
        $this->assertSame('gp1', $checkout->reference);
        $this->assertSame(1250, json_decode($this->sent[0]['body'], true)['amount'], 'GOV.UK Pay takes pence as a whole number');

        foreach (['success' => PaymentStatus::PAID, 'failed' => PaymentStatus::FAILED, 'cancelled' => PaymentStatus::FAILED, 'error' => PaymentStatus::FAILED, 'started' => PaymentStatus::PENDING, 'submitted' => PaymentStatus::PENDING] as $status => $expected) {
            $this->assertSame($expected, (new GovUkPayProvider($this->client([['state' => ['status' => $status]]]), 'k'))->fetchStatus('gp1'), $status);
        }

        (new GovUkPayProvider($this->client([['refund_summary' => ['amount_available' => 1250]], []]), 'k'))->refund('gp1', 1250, 'GBP');
        $this->assertSame(['amount' => 1250, 'refund_amount_available' => 1250], json_decode($this->sent[1]['body'], true));

        $hook = new GovUkPayProvider($this->client([]), 'k');
        $this->assertSame('gp7', $hook->getWebhookReference(Request::create('/hook', 'POST', [], [], [], [], '{"resource_id":"gp7"}')));
        $this->assertNull($hook->getWebhookReference(Request::create('/hook', 'POST', [], [], [], [], 'not json')));
    }

    public function testPayPalCapturesAnApprovedOrderAndOnlyThenCountsAsPaid(): void
    {
        $token = ['access_token' => 'tok'];
        $provider = new PayPalProvider($this->client([$token, ['status' => 'APPROVED'], ['status' => 'COMPLETED']]), 'id', 'secret', true);
        $this->assertSame(PaymentStatus::PAID, $provider->fetchStatus('ord1'));
        $this->assertSame('https://api-m.sandbox.paypal.com/v1/oauth2/token', $this->sent[0]['url']);
        $this->assertStringEndsWith('/v2/checkout/orders/ord1/capture', $this->sent[2]['url']);

        $created = (new PayPalProvider($this->client([$token, ['status' => 'CREATED']]), 'id', 'secret', false))->fetchStatus('ord1');
        $this->assertSame(PaymentStatus::PENDING, $created, 'an order nobody approved is not paid');
        $this->assertSame('https://api-m.paypal.com/v1/oauth2/token', $this->sent[0]['url']);

        $this->assertSame(PaymentStatus::FAILED, (new PayPalProvider($this->client([$token, ['status' => 'VOIDED']]), 'id', 'secret', true))->fetchStatus('ord1'));
    }

    public function testPayPalCheckoutNeedsAnApprovalLinkAndSendsTheDecimalAmount(): void
    {
        $token = ['access_token' => 'tok'];
        $checkout = (new PayPalProvider($this->client([$token, ['id' => 'ord1', 'links' => [['rel' => 'self', 'href' => 'x'], ['rel' => 'payer-action', 'href' => 'https://paypal.test/pay']]]]), 'i', 's', true))->createCheckout($this->request());
        $this->assertSame('https://paypal.test/pay', $checkout->url);
        $this->assertSame('12.50', json_decode($this->sent[1]['body'], true)['purchase_units'][0]['amount']['value']);

        $this->expectException(\RuntimeException::class);
        (new PayPalProvider($this->client([$token, ['id' => 'ord2', 'links' => [['rel' => 'self', 'href' => 'x']]]]), 'i', 's', true))->createCheckout($this->request());
    }

    public function testPayPalRefundNeedsACapturedPayment(): void
    {
        $token = ['access_token' => 'tok'];
        (new PayPalProvider($this->client([$token, ['purchase_units' => [['payments' => ['captures' => [['id' => 'cap1']]]]]], []]), 'i', 's', true))->refund('ord1', 1250, 'GBP');
        $this->assertStringEndsWith('/v2/payments/captures/cap1/refund', $this->sent[2]['url']);

        $this->expectException(\RuntimeException::class);
        (new PayPalProvider($this->client([$token, ['purchase_units' => [['payments' => []]]]]), 'i', 's', true))->refund('ord1', 1250, 'GBP');
    }

    public function testWebhookBodiesAreOnlyUsedToFindThePayment(): void
    {
        $stripe = new StripeProvider($this->client([]), 'k');
        $this->assertSame('cs_1', $stripe->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '{"data":{"object":{"object":"checkout.session","id":"cs_1"}}}')));
        $this->assertNull($stripe->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '{"data":{"object":{"object":"charge","id":"ch_1"}}}')));
        $this->assertNull($stripe->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '')));
        $this->assertNull($stripe->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '[1,2]')));

        $paypal = new PayPalProvider($this->client([]), 'i', 's', true);
        $this->assertSame('ord9', $paypal->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '{"event_type":"CHECKOUT.ORDER.APPROVED","resource":{"id":"ord9"}}')));
        $this->assertSame('ord8', $paypal->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '{"event_type":"PAYMENT.CAPTURE.COMPLETED","resource":{"id":"cap1","supplementary_data":{"related_ids":{"order_id":"ord8"}}}}')));
        $this->assertNull($paypal->getWebhookReference(Request::create('/h', 'POST', [], [], [], [], '{"event_type":"PAYMENT.CAPTURE.COMPLETED","resource":{"id":"cap1"}}')));
    }
}
