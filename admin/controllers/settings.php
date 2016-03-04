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
     * @return stdClass
     */
    public static function announce()
    {
        $oNavGroup = Factory::factory('Nav', 'nailsapp/module-admin');
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
     * @return array
     */
    public static function permissions()
    {
        $permissions = parent::permissions();

        $permissions['misc']        = 'Can update miscallaneous settings';
        $permissions['driver']      = 'Can update driver settings';
        $permissions['invoiceskin'] = 'Can update the invoice skin';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Manage invoice settings
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:invoice:settings:*')) {
            unauthorised();
        }

        $oDb                 = Factory::service('Database');
        $oAppSettingModel    = Factory::model('AppSetting');
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $oInvoiceSkinModel   = Factory::model('InvoiceSkin', 'nailsapp/module-invoice');

        //  Process POST
        if ($this->input->post()) {

            //  Settings keys
            $sKeyPaymentDriver = $oPaymentDriverModel->getSettingKey();
            $sKeyInvoiceSkin   = $oInvoiceSkinModel->getSettingKey();

            //  Validation
            $oFormValidation = Factory::service('FormValidation');

            $oFormValidation->set_rules('saved_cards_enabled', '', '');
            $oFormValidation->set_rules('saved_addresses_enabled', '', '');
            $oFormValidation->set_rules($sKeyPaymentDriver, '', '');
            $oFormValidation->set_rules($sKeyInvoiceSkin, '', '');

            if ($oFormValidation->run()) {

                try {

                    $aSettings = array(
                        'saved_cards_enabled'     => (bool) $this->input->post('saved_cards_enabled'),
                        'saved_addresses_enabled' => (bool) $this->input->post('saved_addresses_enabled')
                    );

                    $oDb->trans_begin();

                    //  Normal settings
                    if (!$oAppSettingModel->set($aSettings, 'nailsapp/module-invoice')) {
                        throw new NailsException($oAppSettingModel->lastError(), 1);
                    }

                    //  Drivers & Skins
                    $oPaymentDriverModel->saveEnabled($this->input->post($sKeyPaymentDriver));
                    $oInvoiceSkinModel->saveEnabled($this->input->post($sKeyInvoiceSkin));

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
        $this->data['settings'] = appSetting(null, 'nailsapp/module-invoice', true);

        Helper::loadView('index');
    }
}
