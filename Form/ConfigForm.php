<?php

namespace Oschadpay\Form;

use Oschadpay\Config\GatewayCurrency;
use Oschadpay\Oschadpay;
use Oschadpay\Config\ConfigKeys;
use Oschadpay\Config\GatewayOschadpayType;
use Thelia\Form\BaseForm;

/**
 * Module configuration form.
 */
class ConfigForm extends BaseForm
{
    public function getName()
    {
        return 'oschadpay_config';
    }

    protected function buildForm()
    {
        $this->formBuilder
            ->add(
                ConfigKeys::MERCHANT_ID,
                'text',
                [
                    'label' => $this->translator->trans('Merchant ID'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::MERCHANT_ID),
                ]
            )
            ->add(
                ConfigKeys::SECRET_KEY,
                'text',
                [
                    'label' => $this->translator->trans('Secret key'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::SECRET_KEY),
                ]
            )
            ->add(
                ConfigKeys::GATEWAY_URL,
                'text',
                [
                    'label' => $this->translator->trans('Payment gateway URL'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::GATEWAY_URL),
                    'required' => false,
                ]
            )
            ->add(
                ConfigKeys::CALLBACK_URL,
                'text',
                [
                    'label' => $this->translator->trans('Gateway callback URL (recommended set empty to generate automatically)'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::CALLBACK_URL),
                    'required' => false,
                ]
            )
            ->add(
                ConfigKeys::RESPONSE_URL,
                'text',
                [
                    'label' => $this->translator->trans('Gateway response URL (recommended set empty to generate automatically)'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::RESPONSE_URL),
                    'required' => false,
                ]
            )
            ->add(
                ConfigKeys::GATEWAY_TYPE,
                'choice',
                [
                    'label' => $this->translator->trans('Gateway type'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::GATEWAY_TYPE),
                    'choices' => [
                        GatewayOschadpayType::RELAY_RESPONSE => $this->translator->trans(
                            'Redirect to payment page'
                        ),
                    ],
                    'required' => false,
                ]
            )
            ->add(
                ConfigKeys::CURRENCY,
                'choice',
                [
                    'label' => $this->translator->trans('Currency'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::CURRENCY),
                    'choices' => [
                         ''=> $this->translator->trans(
                            'choose from shop'
                        ),
                        GatewayCurrency::USD => $this->translator->trans(
                            'USD'
                        ),
                        GatewayCurrency::UAH => $this->translator->trans(
                            'UAH'
                        ),
                        GatewayCurrency::RUB => $this->translator->trans(
                            'RUB'
                        ),
                        GatewayCurrency::EUR => $this->translator->trans(
                            'EUR'
                        ),
                        GatewayCurrency::GBP => $this->translator->trans(
                            'GBP'
                        ),
                        GatewayCurrency::CZK => $this->translator->trans(
                            'CZK'
                        ),
                    ],
                    'required' => false,
                ]
            )
            ->add(
                ConfigKeys::PREAUTH,
                'checkbox',
                [
                    'label' => $this->translator->trans('Enable preauth mode'),
                    'data' => Oschadpay::getConfigValue(ConfigKeys::PREAUTH) == 1,
                    'required' => false,
                ]
            );
    }
}
