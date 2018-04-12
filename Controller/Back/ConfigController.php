<?php

namespace Oschadpay\Controller\Back;

use Oschadpay\Oschadpay;
use Oschadpay\Config\ConfigKeys;
use Symfony\Component\HttpFoundation\Response;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;

/**
 * Back-office module configuration controller.
 */
class ConfigController extends BaseAdminController
{
    /**
     * Configuration fields to save directly.
     * @var array
     */
    protected $fieldsToSave = [
        ConfigKeys::MERCHANT_ID,
        ConfigKeys::SECRET_KEY,
        ConfigKeys::GATEWAY_URL,
        ConfigKeys::CALLBACK_URL,
        ConfigKeys::GATEWAY_TYPE,
        ConfigKeys::RESPONSE_URL,
        ConfigKeys::CURRENCY,
        ConfigKeys::PREAUTH,
    ];

    /**
     * Save the module configuration.
     * @return Response
     */
    public function saveAction()
    {
        $authResponse = $this->checkAuth(AdminResources::MODULE, Oschadpay::getModuleCode(), AccessManager::UPDATE);
        if (null !== $authResponse) {
            return $authResponse;
        }

        $baseForm = $this->createForm('oschadpay.form.config');

        $form = $this->validateForm($baseForm, 'POST');

        foreach ($this->fieldsToSave as $field) {
            Oschadpay::setConfigValue($field, $form->get($field)->getData());
        }

        if ($this->getRequest()->get('save_mode') === 'close') {
            return $this->generateRedirectFromRoute('admin.module');
        } else {
            return $this->generateRedirectFromRoute(
                'admin.module.configure',
                [],
                [
                    'module_code' => Oschadpay::getModuleCode(),
                ]
            );
        }
    }
}
