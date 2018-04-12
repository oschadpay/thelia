<?php

namespace Oschadpay;

use Oschadpay\Config\ConfigKeys;
use Oschadpay\Service\OSCHADPAY\RequestServiceInterface;
use Thelia\Model\Order;
use Thelia\Module\AbstractPaymentModule;

class Oschadpay extends AbstractPaymentModule
{
    protected static $defaultConfigValues = [
        ConfigKeys::GATEWAY_URL => 'https://api.oschadpay.com.ua/api/checkout/redirect/',
        ConfigKeys::TRANSACTION_VERSION => '1.0',
    ];

    public function pay(Order $order)
    {
        /** @var RequestServiceInterface $OSCHADPAYRequestService */
        $OSCHADPAYRequestService = $this->getContainer()->get('oschadpay.service.oschadpay.request');

        return $this->generateGatewayFormResponse(
            $order,
            $OSCHADPAYRequestService->getGatewayURL(),
            $OSCHADPAYRequestService->getRequestFields($order, $this->getRequest())
        );
    }

    public function isValidPayment()
    {
        return true;
    }

    public static function getConfigValue($variableName, $defaultValue = null, $valueLocale = null)
    {
        if ($defaultValue === null && isset(static::$defaultConfigValues[$variableName])) {
            $defaultValue = static::$defaultConfigValues[$variableName];
        }

        return parent::getConfigValue($variableName, $defaultValue, $valueLocale);
    }
}
