<?php

namespace Oschadpay\Controller\Front;

use Oschadpay\Oschadpay;
use Oschadpay\Config\ConfigKeys;
use Oschadpay\Config\GatewayOschadpayType;
use Oschadpay\Service\OSCHADPAY\ResponseServiceInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Thelia\Core\HttpFoundation\Response;
use Thelia\Core\HttpKernel\Exception\RedirectException;
use Thelia\Module\BasePaymentModuleController;

/**
 * Controller for requests from the payment gateway.
 */
class GatewayController extends BasePaymentModuleController
{
    protected function getModuleCode()
    {
        return Oschadpay::getModuleCode();
    }

    /**
     * Process the callback from the payment gateway.
     * @return Response|null The rendered page for non-redirection responses (when relay response is configured).
     * @throws RedirectException For redirection responses (when receipt link is configured).
     */
    public function callbackAction()
    {
        $response = $this->getRequest()->request->all();
        /** @var ResponseServiceInterface $OSCHADPAYResponseService */
        $OSCHADPAYResponseService = $this->getContainer()->get('oschadpay.service.oschadpay.response');

        if ($OSCHADPAYResponseService->isResponseSignatureValid($response) !== true) {
            throw new AccessDeniedHttpException('Invalid response hash.');
        }
        $order_id = explode('#',$response['order_id'])[0];

        $order = $this->getOrder($order_id);
        if ($order === null) {
            throw new NotFoundHttpException('Order not found.');
        }
        if (round($order->getTotalAmount()*100) != $response['amount']) {
            throw new AccessDeniedHttpException('Invalid amount.');
        }
        $orderPaid = $OSCHADPAYResponseService->payOrderFromResponse($response, $order);

        if ($orderPaid) {
            $this->redirectToSuccessPage($order->getId());
        } else {
            $this->redirectToFailurePage($order->getId(), $this->getTranslator()->trans('Payment error.'));
        }

        return null;
    }
}
