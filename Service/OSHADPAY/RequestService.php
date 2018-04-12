<?php

namespace Oschadpay\Service\OSCHADPAY;

use Oschadpay\Oschadpay;
use Oschadpay\Config\ConfigKeys;
use Oschadpay\Config\GatewayOschadpayType;
use Symfony\Component\Routing\RouterInterface;
use Thelia\Core\HttpFoundation\Request;
use Thelia\Model\Customer;
use Thelia\Model\Order;
use Thelia\Model\OrderAddress;
use Thelia\Tools\URL;

/**
 * Implementation of the OSCHADPAY (Server Integration Method) payment form request builder.
 */
class RequestService implements RequestServiceInterface
{
    /**
     * Router for this module.
     * @var RouterInterface
     */
    protected $moduleRouter;
    /**
     * URL tools.
     * @var URL
     */
    protected $URLTools;

    /**
     * @param RouterInterface $moduleRouter Router for this module.
     * @param URL $URLTools URL tools.
     */
    public function __construct(
        RouterInterface $moduleRouter,
        URL $URLTools
    )
    {
        $this->moduleRouter = $moduleRouter;
        $this->URLTools = $URLTools;
    }

    public function getGatewayURL()
    {
        return Oschadpay::getConfigValue(ConfigKeys::GATEWAY_URL);
    }

    public function getCallbackURL()
    {
        $callbackURL = Oschadpay::getConfigValue(ConfigKeys::CALLBACK_URL);
        if (empty($callbackURL)) {
            $callbackURL = $this->URLTools->absoluteUrl(
                $this->moduleRouter->generate('oschadpay.front.gateway.callback')
            );
        }

        return $callbackURL;
    }

    public function getResponseURL()
    {
        $ResponseURL = Oschadpay::getConfigValue(ConfigKeys::RESPONSE_URL);
        if (empty($ResponseURL)) {
            $ResponseURL = $this->URLTools->absoluteUrl(
                $this->moduleRouter->generate('oschadpay.front.gateway.callback')
            );
        }

        return $ResponseURL;
    }

    public function getRequestFields(Order $order, Request $httpRequest)
    {
        $request = [];

        $this->addBaseFields($request, $order);

        $customer = $order->getCustomer();
        $this->addCustomerFields($request, $customer);
        
        if(Oschadpay::getConfigValue(ConfigKeys::PREAUTH) === true){
            $this->addPreauthField($request);
        }
        switch (Oschadpay::getConfigValue(ConfigKeys::GATEWAY_TYPE)) {
            case GatewayOschadpayType::RELAY_RESPONSE:
                $this->addRelayResponseFields($request);
                break;
            default:
                $this->addRelayResponseFields($request);
                break;
        }

        $this->addSignatureFields(Oschadpay::getConfigValue(ConfigKeys::MERCHANT_ID), Oschadpay::getConfigValue(ConfigKeys::SECRET_KEY), $request);

        return $request;
    }

    protected function addBaseFields(array &$request, Order $order)
    {
        $request['order_id'] = $order->getId() . '#' . time();
        $request['merchant_id'] = Oschadpay::getConfigValue(ConfigKeys::MERCHANT_ID);
        $request['order_desc'] = '№' . $order->getId();
        $request['amount'] = round($order->getTotalAmount() * 100);
        $request['version'] = Oschadpay::getConfigValue(ConfigKeys::TRANSACTION_VERSION);
        $request['currency'] = Oschadpay::getConfigValue(ConfigKeys::CURRENCY) ? Oschadpay::getConfigValue(ConfigKeys::CURRENCY) : $order->getCurrency()->getCode();
    }

    protected function addPreauthField(array &$request)
    {
        $request['preauth'] = 'Y';
    }

    protected function addCustomerFields(array &$request, Customer $customer)
    {
        $request['sender_email'] = $customer->getEmail();
    }

    protected function addRelayResponseFields(array &$request)
    {
        $request['server_callback_url'] = $this->getCallbackURL();
        $request['response_url'] = $this->getResponseURL();
    }

    protected function addSignatureFields($merchant_id, $password, array &$request)
    {
        $params['merchant_id'] = $merchant_id;
        $params = array_filter($request, 'strlen');
        ksort($params);
        $params = array_values($params);
        array_unshift($params, $password);
        $params = join('|', $params);
        $request['signature'] = (sha1($params));
    }

}
