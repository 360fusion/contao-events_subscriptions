<?php

namespace Codefog\EventsSubscriptionsBundle\Controller;

use Codefog\EventsSubscriptionsBundle\Model\PaymentModel;
use Codefog\EventsSubscriptionsBundle\Model\PaymentProviderModel;
use Codefog\EventsSubscriptionsBundle\Payment\PaymentManager;
use Codefog\EventsSubscriptionsBundle\Payment\ProviderFactory;
use Codefog\EventsSubscriptionsBundle\Payment\WebhookUrl;
use Contao\CoreBundle\Framework\ContaoFramework;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Payment status notifications of the providers. The status is always fetched from the provider's API.
 */
#[Route('/_events_subscriptions/payment/webhook/{id}', name: WebhookUrl::ROUTE, requirements: ['id' => '\d+'], defaults: ['_scope' => 'frontend', '_token_check' => false], methods: ['POST'])]
class PaymentWebhookController
{
    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly LoggerInterface|null $logger = null,
    ) {
    }

    public function __invoke(Request $request, int $id): Response
    {
        $this->framework->initialize();

        if (null === ($providerModel = PaymentProviderModel::findByPk($id))) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }

        try {
            $reference = ProviderFactory::create($providerModel->row())->getWebhookReference($request);

            if (null !== $reference && null !== ($payment = PaymentModel::findOneBy(['provider=?', 'reference=?'], [$id, $reference]))) {
                (new PaymentManager())->sync($payment);
            }
        } catch (\Throwable $e) {
            $this->logger?->error('Payment webhook failed: '.$e->getMessage(), ['exception' => $e]);

            // The provider sends it again later
            return new Response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new Response('', Response::HTTP_OK);
    }
}
