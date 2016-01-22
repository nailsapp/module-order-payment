<?php

/**
 * View invoices
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

use Nails\Factory;
use Nails\Common\Exception\NailsException;
use Nails\Invoice\Exception\DriverException;

class Invoice extends NAILS_Controller
{
    /**
     * Download a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function download($oInvoice)
    {
        dump('Download Invoice');
        dump($oInvoice);
    }

    // --------------------------------------------------------------------------

    /**
     * View a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function view($oInvoice)
    {
        $this->data['oInvoice'] = $oInvoice;

        // --------------------------------------------------------------------------

        if ($this->input->get('autosize')) {

            $oAsset = Factory::service('Asset');
            $oAsset->load(
                'iframe-resizer/js/iframeResizer.contentWindow.min.js',
                array('nailsapp/module-invoice', 'BOWER')
            );
        }

        // --------------------------------------------------------------------------

        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        // --------------------------------------------------------------------------

        $this->load->view('structure/header', $this->data);
        $this->load->view('invoice/view/index', $this->data);
        $this->load->view('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Pay a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function pay($oInvoice)
    {
        $this->data['header_override'] = 'structure/header/blank';
        $this->data['footer_override'] = 'structure/footer/blank';
        $this->data['oInvoice']        = $oInvoice;

        $oAsset = Factory::service('Asset');
        $oAsset->load('invoice.pay.css', 'nailsapp/module-invoice');

        //  Only open invoice can be paid
        if ($oInvoice->state->id !== 'OPEN' && !$oInvoice->isScheduled) {
            if ($oInvoice->state->id === 'PAID') {

                $this->load->view('structure/header', $this->data);
                $this->load->view('invoice/pay/paid', $this->data);
                $this->load->view('structure/footer', $this->data);
                return;

            } else {

                show_404();
            }
        }

        //  If a user ID is specified, then the user must be logged in as that user
        if (!empty($oInvoice->user->id) && $oInvoice->user->id != activeUser('id')) {
            unauthorised();
        }

        $this->data['sUrlCancel'] = $this->input->get('cancel') ?: site_url();

        // --------------------------------------------------------------------------

        //  Payment drivers
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $aDrivers            = $oPaymentDriverModel->getEnabled();
        foreach ($aDrivers as $oDriver) {
            $this->data['aDrivers'][] = $oPaymentDriverModel->getInstance($oDriver->slug);
        }

        if (empty($this->data['aDrivers'])) {
            throw new DriverException('No enabled payment drivers', 1);
        }

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            /**
             * Validation works by looking at which driver has been chosen and then
             * validating the respective fields accordingly. If the driver's fields
             * is simply CARD then we validate the cc[] field.
             */

            $oFormValidation = Factory::service('FormValidation');

            $aRules = array(
                'driver'   => array('xss_clean', 'trim', 'required'),
                'cc[name]' => array('xss_clean', 'trim'),
                'cc[num]'  => array('xss_clean', 'trim'),
                'cc[exp]'  => array('xss_clean', 'trim'),
                'cc[cvc]'  => array('xss_clean', 'trim')
            );

            $sSelectedDriver = $this->input->post('driver');
            $oSelectedDriver = null;

            foreach ($this->data['aDrivers'] as $oDriver) {

                $sSlug   = $oDriver->getSlug();
                $aFields = $oDriver->getPaymentFields();

                if ($sSelectedDriver == $sSlug) {

                    $oSelectedDriver = $oDriver;

                    if ($aFields === 'CARD') {

                        $aRules['cc[name]'][] = 'required';
                        $aRules['cc[num]'][]  = 'required';
                        $aRules['cc[exp]'][]  = 'required';
                        $aRules['cc[cvc]'][]  = 'required';

                    } elseif (!empty($aFields)) {

                        foreach ($aFields as $aField) {
                            $aRules[$sSlug . '[' . $aField['key'] . ']'] = array('xss_clean');
                        }
                    }

                    break;
                }
            }

            foreach ($aRules as $sKey => $sRules) {
                $oFormValidation->set_rules($sKey, '', implode('|', array_unique($sRules)));
            }

            if ($oFormValidation->run()) {

                try {

                    //  Set up card object
                    $oChargeRequest = Factory::factory('ChargeRequest', 'nailsapp/module-invoice');

                    //  Set the driver to use for the charge
                    $oChargeRequest->setDriver($sSelectedDriver);

                    //  If the driver expects card data then set it, if it expects custom data then set that
                    $mPaymentFields = $oSelectedDriver->getPaymentFields();

                    if (!empty($mPaymentFields) && $mPaymentFields == 'CARD') {

                        $sName = !empty($_POST['cc']['name']) ? $_POST['cc']['name'] : '';
                        $sNum  = !empty($_POST['cc']['num']) ? $_POST['cc']['num'] : '';
                        $sExp  = !empty($_POST['cc']['exp']) ? $_POST['cc']['exp'] : '';
                        $sCvc  = !empty($_POST['cc']['cvc']) ? $_POST['cc']['cvc'] : '';

                        $aExp   = explode('/', $sExp);
                        $aExp   = array_map('trim', $aExp);
                        $sMonth = !empty($aExp[0]) ? $aExp[0] : null;
                        $sYear  = !empty($aExp[1]) ? $aExp[1] : null;

                        $oChargeRequest->setCardName($sName);
                        $oChargeRequest->setCardNumber($sNum);
                        $oChargeRequest->setCardExpMonth($sMonth);
                        $oChargeRequest->setCardExpYear($sYear);
                        $oChargeRequest->setCardCvc($sCvc);

                    } elseif (!empty($mPaymentFields)) {

                        foreach ($mPaymentFields as $aField) {

                            if (!empty($_POST[$sSelectedDriver][$aField['key']])) {

                                $sValue = $_POST[$sSelectedDriver][$aField['key']];

                            } else {

                                $sValue = null;
                            }
                            $oChargeRequest->setCustom($aField['key'], $sValue);
                        }
                    }

                    //  Attempt payment
                    $oResult = $oChargeRequest->charge(
                        $oInvoice->id,
                        $oInvoice->totals->base->grand,
                        $oInvoice->currency,
                        $oDriver->getSlug()
                    );

                    if ($oResult->isOk()) {

                        //  Payment was successfull; head to wherever the charge response says to go
                        redirect($oResult->getSuccessUrl());

                    } else {

                        throw new NailsException('Payment request failed: ' . $oResult->getError(), 1);
                    }

                } catch (\Exception $e) {

                    $this->data['error'] = $e->getMessage();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $oAsset->load('jquery.payment/lib/jquery.payment.js', array('nailsapp/module-invoice', 'BOWER'));
        $oAsset->load('invoice.pay.min.js', 'nailsapp/module-invoice');

        // --------------------------------------------------------------------------

        $this->load->view('structure/header', $this->data);
        $this->load->view('invoice/pay/index', $this->data);
        $this->load->view('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Remaps all requests to the viewInvoice method unless the *real* method exists
     * @return void
     */
    public function _remap()
    {
        $sInvoiceRef   = $this->uri->rsegment(2);
        $sInvoiceToken = $this->uri->rsegment(3);
        $sMethod       = $this->uri->rsegment(4);

        //  @todo verify invoice and token
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oInvoice      = $oInvoiceModel->getByRef($sInvoiceRef, array('includeItems' => true));

        if (empty($oInvoice) || $sInvoiceToken !== $oInvoice->token || !method_exists($this, $sMethod)) {
            show_404();
        }

        $this->{$sMethod}($oInvoice);
    }
}
