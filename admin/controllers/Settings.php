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

use Exception;
use Nails\Admin\Controller\Base;
use Nails\Admin\Factory\Nav;
use Nails\Admin\Helper;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\AppSetting;
use Nails\Common\Service\Database;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Input;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Service\Invoice\Skin;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Settings
 *
 * @package Nails\Admin\Invoice
 */
class Settings extends Base
{
    /**
     * Announces this controller's navGroups
     *
     * @return array|Nav
     * @throws FactoryException
     */
    public static function announce()
    {
        /** @var Nav $oNavGroup */
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

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var AppSetting $oAppSettingService */
        $oAppSettingService = Factory::service('AppSetting');
        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);
        /** @var Skin $oInvoiceSkinService */
        $oInvoiceSkinService = Factory::service('InvoiceSkin', Constants::MODULE_SLUG);

        //  Process POST
        if ($oInput->post()) {

            //  Settings keys
            $sKeyPaymentDriver = $oPaymentDriverService->getSettingKey();
            $sKeyInvoiceSkin   = $oInvoiceSkinService->getSettingKey();

            //  Validation
            /** @var FormValidation $oFormValidation */
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
                    if (!$oAppSettingService->set($aSettings, Constants::MODULE_SLUG)) {
                        throw new NailsException($oAppSettingService->lastError(), 1);
                    }

                    //  Drivers & Skins
                    $oPaymentDriverService->saveEnabled($oInput->post($sKeyPaymentDriver));
                    $oInvoiceSkinService->saveEnabled($oInput->post($sKeyInvoiceSkin));

                    $oDb->trans_commit();
                    $this->data['success'] = 'Invoice &amp; Payment settings were saved.';

                } catch (Exception $e) {
                    $oDb->trans_rollback();
                    $this->data['error'] = 'There was a problem saving settings. ' . $e->getMessage();
                }

            } else {
                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        //  Get data
        $this->data['settings'] = appSetting(null, Constants::MODULE_SLUG, null, true);

        Helper::loadView('index');
    }
}
