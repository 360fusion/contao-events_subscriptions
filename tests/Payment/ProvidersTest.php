<?php

declare(strict_types=1);

namespace Codefog\EventsSubscriptionsBundle\Tests\Payment;

use Codefog\EventsSubscriptionsBundle\Payment\GovUkPayProvider;
use Codefog\EventsSubscriptionsBundle\Payment\MollieProvider;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentRequest;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentStatus;
use Codefog\EventsSubscriptionsBundle\Payment\PayPalProvider;
use Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory;
use Codefog\EventsSubscriptionsBundle\Payment\StripeProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

class ProvidersTest extends TestCase
{
    /**
     * @var list<array{method: string, url: string, options: array}>
     */
    private array $requests = [];

    public function testStripeCheckoutStatusAndRefund(): void
    {
        $provider = new StripeProvider($this->client([
            ['id' => 'cs_test_1', 'url' => 'https://checkout.stripe.com/c/pay/cs_test_1'],
            ['payment_status' => 'paid', 'status' => 'complete'],
            ['payment_status' => 'unpaid', 'status' => 'expired'],
            ['payment_intent' => 'pi_1'],
            ['id' => 're_1'],
        ]), 'sk_test_x');

        $checkout = $provider->createCheckout($this->request());
        $this->assertSame('https://checkout.stripe.com/c/pay/cs_test_1', $checkout->url);
        $this->assertSame('cs_test_1', $checkout->reference);
        $this->assertSame('https://api.stripe.com/v1/checkout/sessions', $this->requests[0]['url']);
        parse_str($this->requests[0]['options']['body'], $body);
        $this->assertSame('1250', $body['line_items'][0]['price_data']['unit_amount']);
        $this->assertSame('gbp', $body['line_items'][0]['price_data']['currency']);
        $this->assertSame('token123', $body['client_reference_id']);
        $this->assertSame('ann@nhs.net', $body['customer_email']);

        $this->assertSame(PaymentStatus::PAID, $provider->fetchStatus('cs_test_1'));
        $this->assertSame(PaymentStatus::FAILED, $provider->fetchStatus('cs_test_1'));

        $provider->refund('cs_test_1', 1250, 'GBP');
        parse_str($this->requests[4]['options']['body'], $body);
        $this->assertSame(['payment_intent' => 'pi_1', 'amount' => '1250'], $body);

        $this->assertSame('cs_test_1', $provider->getWebhookReference(new Request(content: json_encode(['data' => ['object' => ['object' => 'checkout.session', 'id' => 'cs_test_1']]]))));
        $this->assertNull($provider->getWebhookReference(new Request(content: json_encode(['data' => ['object' => ['object' => 'charge', 'id' => 'ch_1']]]))));
    }

    public function testPayPalCapturesApprovedOrders(): void
    {
        $provider = new PayPalProvider($this->client([
            ['access_token' => 'tok'],
            ['id' => 'ORDER1', 'links' => [['rel' => 'self', 'href' => 'x'], ['rel' => 'approve', 'href' => 'https://www.sandbox.paypal.com/checkoutnow?token=ORDER1']]],
            ['status' => 'APPROVED'],
            ['status' => 'COMPLETED'],
            ['purchase_units' => [['payments' => ['captures' => [['id' => 'CAP1']]]]]],
            ['id' => 'REF1'],
        ]), 'id', 'secret', true);

        $checkout = $provider->createCheckout($this->request());
        $this->assertSame('ORDER1', $checkout->reference);
        $this->assertStringStartsWith('https://api-m.sandbox.paypal.com/v1/oauth2/token', $this->requests[0]['url']);
        $this->assertSame('12.50', json_decode($this->requests[1]['options']['body'], true)['purchase_units'][0]['amount']['value']);

        $this->assertSame(PaymentStatus::PAID, $provider->fetchStatus('ORDER1'));
        $this->assertSame('https://api-m.sandbox.paypal.com/v2/checkout/orders/ORDER1/capture', $this->requests[3]['url']);

        $provider->refund('ORDER1', 1250, 'GBP');
        $this->assertSame('https://api-m.sandbox.paypal.com/v2/payments/captures/CAP1/refund', $this->requests[5]['url']);

        $this->assertSame('ORDER1', $provider->getWebhookReference(new Request(content: json_encode(['event_type' => 'PAYMENT.CAPTURE.COMPLETED', 'resource' => ['id' => 'CAP1', 'supplementary_data' => ['related_ids' => ['order_id' => 'ORDER1']]]]))));
    }

    public function testMollieSkipsTheWebhookOnLocalSites(): void
    {
        $provider = new MollieProvider($this->client([
            ['id' => 'tr_1', '_links' => ['checkout' => ['href' => 'https://www.mollie.com/checkout/tr_1']]],
            ['status' => 'canceled'],
            ['id' => 're_1'],
        ]), 'test_x');

        $this->assertSame('tr_1', $provider->createCheckout($this->request('https://events.test/hook'))->reference);
        $this->assertArrayNotHasKey('webhookUrl', json_decode($this->requests[0]['options']['body'], true));
        $this->assertSame(PaymentStatus::FAILED, $provider->fetchStatus('tr_1'));

        $provider->refund('tr_1', 999, 'GBP');
        $this->assertSame(['currency' => 'GBP', 'value' => '9.99'], json_decode($this->requests[2]['options']['body'], true)['amount']);

        $this->assertSame('tr_1', $provider->getWebhookReference(new Request(request: ['id' => 'tr_1'])));
    }

    public function testGovUkPay(): void
    {
        $provider = new GovUkPayProvider($this->client([
            ['payment_id' => 'pay1', '_links' => ['next_url' => ['href' => 'https://card.payments.service.gov.uk/secure/x']]],
            ['state' => ['status' => 'success']],
            ['refund_summary' => ['amount_available' => 1250]],
            [],
        ]), 'api_test_x');

        $checkout = $provider->createCheckout($this->request());
        $this->assertSame('pay1', $checkout->reference);
        $this->assertSame(1250, json_decode($this->requests[0]['options']['body'], true)['amount']);
        $this->assertSame(PaymentStatus::PAID, $provider->fetchStatus('pay1'));

        $provider->refund('pay1', 1250, 'GBP');
        $this->assertSame(['amount' => 1250, 'refund_amount_available' => 1250], json_decode($this->requests[3]['options']['body'], true));
    }

    public function testFactory(): void
    {
        $this->assertInstanceOf(PayPalProvider::class, ProviderFactory::create(['type' => 'paypal', 'clientId' => 'a', 'clientSecret' => 'b', 'live' => '']));
        $this->expectException(\InvalidArgumentException::class);
        ProviderFactory::create(['type' => 'nope']);
    }

    private function request(string $webhookUrl = 'https://events.example/_events_subscriptions/payment/webhook/1'): PaymentRequest
    {
        return new PaymentRequest(1250, 'GBP', 'Sepsis Study Day', 'token123', 'https://events.example/event?payment=return&booking=token123', $webhookUrl, 'ann@nhs.net');
    }

    private function client(array $responses): MockHttpClient
    {
        return new MockHttpClient(function (string $method, string $url, array $options) use (&$responses): MockResponse {
            $this->requests[] = ['method' => $method, 'url' => $url, 'options' => $options];

            return new MockResponse(json_encode(array_shift($responses)));
        });
    }
}
