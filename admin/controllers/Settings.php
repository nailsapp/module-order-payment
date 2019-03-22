<?php

/**
 * This class registers some handlers for Invoicing & Payment settings
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Invoice;

use Nails\Factory;
use Nails\Admin\Helper;
use Nails\Invoice\Controller\BaseAdmin;
use Nails\Common\Exception\NailsException;

class Settings extends BaseAdmin
{
    /**
     * Announces this controller's navGroups
     *
     * @return stdClass
     */
    public static function announce()
    {
        $oNavGroup = Factory::factory('Nav', 'nails/module-admin');
        $oNavGroup->setLabel('Settings');
        $oNavGroup->setIcon('fa-wrench');

        if (userHasPermission('admin:invoice:settings:*')) {
            $oNavGroup->addAction('Invoices &amp; Payments');
        }

        return $oNavGroup;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of permissions which can be configured for the user
     *
     * @return array
     */
    public static function permissions(): array
    {
        $aPermissions = parent::permissions();

        $aPermissions['misc']        = 'Can update miscallaneous settings';
        $aPermissions['driver']      = 'Can update driver settings';
        $aPermissions['invoiceskin'] = 'Can update the invoice skin';

        return $aPermissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Manage invoice settings
     *
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:invoice:settings:*')) {
            unauthorised();
        }

        $oInput              = Factory::service('Input');
        $oDb                 = Factory::service('Database');
        $oAppSettingService  = Factory::service('AppSetting');
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nails/module-invoice');
        $oInvoiceSkinModel   = Factory::model('InvoiceSkin', 'nails/module-invoice');

        //  Process POST
        if ($oInput->post()) {

            //  Settings keys
            $sKeyPaymentDriver = $oPaymentDriverModel->getSettingKey();
            $sKeyInvoiceSkin   = $oInvoiceSkinModel->getSettingKey();

            //  Validation
            $oFormValidation = Factory::service('FormValidation');

            $oFormValidation->set_rules('business_name', '', '');
            $oFormValidation->set_rules('business_address', '', '');
            $oFormValidation->set_rules('business_phone', '', '');
            $oFormValidation->set_rules('business_email', '', 'valid_email');
            $oFormValidation->set_rules('business_vat_number', '', '');
            $oFormValidation->set_rules('business_', '', '');
            $oFormValidation->set_rules('default_additional_text', '', '');
            $oFormValidation->set_rules('default_payment_terms', '', '');
            $oFormValidation->set_rules('saved_cards_enabled', '', '');
            $oFormValidation->set_rules('saved_addresses_enabled', '', '');
            $oFormValidation->set_rules($sKeyPaymentDriver, '', '');
            $oFormValidation->set_rules($sKeyInvoiceSkin, '', '');

            $oFormValidation->set_message('valid_email', lang('fv_valid_email'));

            if ($oFormValidation->run()) {

                try {

                    $aSettings = [
                        'business_name'           => trim(strip_tags($oInput->post('business_name'))),
                        'business_address'        => trim(strip_tags($oInput->post('business_address'))),
                        'business_phone'          => trim(strip_tags($oInput->post('business_phone'))),
                        'business_email'          => trim(strip_tags($oInput->post('business_email'))),
                        'business_vat_number'     => trim(strip_tags($oInput->post('business_vat_number'))),
                        'default_additional_text' => trim(strip_tags($oInput->post('default_additional_text'))),
                        'default_payment_terms'   => (int) $oInput->post('default_payment_terms'),
                        'saved_cards_enabled'     => (bool) $oInput->post('saved_cards_enabled'),
                        'saved_addresses_enabled' => (bool) $oInput->post('saved_addresses_enabled'),
                    ];

                    $oDb->trans_begin();

                    //  Normal settings
                    if (!$oAppSettingService->set($aSettings, 'nails/module-invoice')) {
                        throw new NailsException($oAppSettingService->lastError(), 1);
                    }

                    //  Drivers & Skins
                    $oPaymentDriverModel->saveEnabled($oInput->post($sKeyPaymentDriver));
                    $oInvoiceSkinModel->saveEnabled($oInput->post($sKeyInvoiceSkin));

                    $oDb->trans_commit();
                    $this->data['success'] = 'Invoice &amp; Payment settings were saved.';

                } catch (\Exception $e) {
                    $oDb->trans_rollback();
                    $this->data['error'] = 'There was a problem saving settings. ' . $e->getMessage();
                }

            } else {
                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        //  Get data
        $this->data['settings'] = appSetting(null, 'nails/module-invoice', true);

        Helper::loadView('index');
    }
}
