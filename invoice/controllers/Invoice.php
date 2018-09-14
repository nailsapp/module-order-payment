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

use Nails\Common\Exception\NailsException;
use Nails\Factory;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Exception\DriverException;

class Invoice extends Base
{
    /**
     * The default invoice skin to use
     * @type string
     */
    const DEFAULT_INVOICE_SKIN = 'nails/skin-invoice-classic';

    // --------------------------------------------------------------------------

    /**
     * Download a single invoice
     *
     * @param  \stdClass $oInvoice The invoice object
     *
     * @return void
     */
    protected function download($oInvoice)
    {
        //  Business details
        $this->data['business'] = (object) [
            'name'       => appSetting('business_name', 'nails/module-invoice'),
            'address'    => appSetting('business_address', 'nails/module-invoice'),
            'telephone'  => appSetting('business_telephone', 'nails/module-invoice'),
            'email'      => appSetting('business_email', 'nails/module-invoice'),
            'vat_number' => appSetting('business_vat_number', 'nails/module-invoice'),
        ];

        $oInvoiceSkinModel     = Factory::model('InvoiceSkin', 'nails/module-invoice');
        $sEnabledSkin          = $oInvoiceSkinModel->getEnabledSlug() ?: self::DEFAULT_INVOICE_SKIN;
        $this->data['invoice'] = $oInvoice;
        $this->data['isPdf']   = true;
        $sHtml                 = $oInvoiceSkinModel->view($sEnabledSkin, 'render', $this->data, true);

        $oPdf = Factory::service('Pdf', 'nails/module-pdf');
        $oPdf->setPaperSize('A4', 'portrait');
        $oPdf->load_html($sHtml);

        $oPdf->download('INVOICE-' . $oInvoice->ref . '.pdf');
    }

    // --------------------------------------------------------------------------

    /**
     * View a single invoice
     *
     * @param  \stdClass $oInvoice The invoice object
     *
     * @return void
     */
    protected function view($oInvoice)
    {
        //  Business details
        $this->data['business'] = (object) [
            'name'       => appSetting('business_name', 'nails/module-invoice'),
            'address'    => appSetting('business_address', 'nails/module-invoice'),
            'telephone'  => appSetting('business_telephone', 'nails/module-invoice'),
            'email'      => appSetting('business_email', 'nails/module-invoice'),
            'vat_number' => appSetting('business_vat_number', 'nails/module-invoice'),
        ];

        $oInvoiceSkinModel     = Factory::model('InvoiceSkin', 'nails/module-invoice');
        $sEnabledSkin          = $oInvoiceSkinModel->getEnabledSlug() ?: self::DEFAULT_INVOICE_SKIN;
        $this->data['invoice'] = $oInvoice;
        $this->data['isPdf']   = false;

        $oInvoiceSkinModel->view($sEnabledSkin, 'render', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Pay a single invoice
     *
     * @param  \stdClass $oInvoice The invoice object
     *
     * @return void
     * @throws NailsException
     */
    protected function pay($oInvoice)
    {
        $oAsset = Factory::service('Asset');
        $oInput = Factory::service('Input');

        $this->data['oInvoice']       = $oInvoice;
        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        //  Only open invoice can be paid
        if ($oInvoice->state->id !== 'OPEN' && !$oInvoice->is_scheduled) {

            if ($oInvoice->state->id === 'PAID') {

                $oView = Factory::service('View');
                $this->loadStyles(APPPATH . 'modules/invoice/views/pay/paid.php');
                $oView->load('structure/header', $this->data);
                $oView->load('invoice/pay/paid', $this->data);
                $oView->load('structure/footer', $this->data);
                return;

            } else {

                show_404();
            }
        }

        //  If a user ID is specified, then the user must be logged in as that user
        if (!empty($oInvoice->user->id) && $oInvoice->user->id != activeUser('id')) {
            unauthorised();
        }

        $this->data['sUrlCancel'] = $oInput->get('cancel') ?: site_url();

        // --------------------------------------------------------------------------

        //  If there are payments against this invoice which are processing, then deny payment
        if ($oInvoice->has_processing_payments) {

            $oPaymentModel = Factory::model('Payment', 'nails/module-invoice');

            $this->data['aProcessingPayments'] = [];
            foreach ($oInvoice->payments->data as $oPayment) {
                if ($oPayment->status->id === $oPaymentModel::STATUS_PROCESSING) {
                    $this->data['aProcessingPayments'][] = $oPayment;
                }
            }

            $oView = Factory::service('View');
            $this->loadStyles(APPPATH . 'modules/invoice/views/pay/hasProcessing.php');
            $oView->load('structure/header', $this->data);
            $oView->load('invoice/pay/hasProcessing', $this->data);
            $oView->load('structure/footer', $this->data);
            return;
        }

        // --------------------------------------------------------------------------

        //  Payment drivers
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nails/module-invoice');
        $aDrivers            = $oPaymentDriverModel->getEnabled();
        foreach ($aDrivers as $oDriver) {

            $oDriverInstance = $oPaymentDriverModel->getInstance($oDriver->slug);

            if ($oDriverInstance->isAvailable($oInvoice)) {
                $this->data['aDrivers'][] = $oDriverInstance;
            }
        }

        if (empty($this->data['aDrivers'])) {
            throw new DriverException('No enabled payment drivers', 1);
        }

        // --------------------------------------------------------------------------

        if ($oInput->post()) {

            /**
             * Validation works by looking at which driver has been chosen and then
             * validating the respective fields accordingly. If the driver's fields
             * is simply CARD then we validate the cc[] field.
             */

            $oFormValidation = Factory::service('FormValidation');

            $aRules = [
                'driver'   => ['', 'trim', 'required'],
                'cc[name]' => ['', 'trim'],
                'cc[num]'  => ['', 'trim'],
                'cc[exp]'  => ['', 'trim'],
                'cc[cvc]'  => ['', 'trim'],
            ];

            $sSelectedDriver = $oInput->post('driver');
            $oSelectedDriver = null;

            foreach ($this->data['aDrivers'] as $oDriver) {

                $sSlug   = $oDriver->getSlug();
                $aFields = $oDriver->getPaymentFields();

                if ($sSelectedDriver == $sSlug) {

                    $oSelectedDriver = $oDriver;

                    if ($aFields === $oSelectedDriver::PAYMENT_FIELDS_CARD) {

                        $aRules['cc[name]'][] = 'required';
                        $aRules['cc[num]'][]  = 'required';
                        $aRules['cc[exp]'][]  = 'required';
                        $aRules['cc[cvc]'][]  = 'required';

                    } elseif (!empty($aFields)) {
                        foreach ($aFields as $aField) {
                            $aRules[$sSlug . '[' . $aField['key'] . ']'] = [''];
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

                    //  Set up ChargeRequest object
                    $oChargeRequest = Factory::factory('ChargeRequest', 'nails/module-invoice');

                    //  Set the driver to use for the request
                    $oChargeRequest->setDriver($sSelectedDriver);

                    //  Describe the charge
                    $oChargeRequest->setDescription('Payment for invoice #' . $oInvoice->ref);

                    //  Set the invoice we're charging against
                    $oChargeRequest->setInvoice($oInvoice->id);

                    //  If the driver expects card data then set it, if it expects custom data then set that
                    $mPaymentFields = $oSelectedDriver->getPaymentFields();

                    if (!empty($mPaymentFields) && $mPaymentFields == $oSelectedDriver::PAYMENT_FIELDS_CARD) {

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
                            $oChargeRequest->setCustomField($aField['key'], $sValue);
                        }
                    }

                    //  Attempt payment
                    $oChargeResponse = $oChargeRequest->execute(
                        $oInvoice->totals->raw->grand,
                        $oInvoice->currency->code
                    );

                    //  Handle response
                    if ($oChargeResponse->isProcessing() || $oChargeResponse->isComplete()) {

                        /**
                         * Payment was successful (but potentially unconfirmed). Send the user off to
                         * complete the request.
                         */

                        redirect($oChargeResponse->getSuccessUrl());

                    } elseif ($oChargeResponse->isFailed()) {

                        /**
                         * Payment failed, throw an error which will be caught and displayed to the user
                         */

                        throw new NailsException(
                            'Payment failed: ' . $oChargeResponse->getError()->user,
                            1
                        );

                    } else {

                        /**
                         * Something which we've not accounted for went wrong.
                         */

                        throw new NailsException('Payment failed.', 1);
                    }

                } catch (\Exception $e) {
                    $this->data['error'] = $e->getMessage();
                }

            } else {
                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        $oAsset->load('jquery.payment/lib/jquery.payment.min.js', ['nails/module-invoice', 'BOWER']);
        $oAsset->load('invoice.pay.min.js', 'nails/module-invoice');

        // --------------------------------------------------------------------------

        $oView = Factory::service('View');
        $this->loadStyles(APPPATH . 'modules/invoice/views/pay/index.php');
        $oView->load('structure/header', $this->data);
        $oView->load('invoice/pay/index', $this->data);
        $oView->load('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Remap requests for valid payments to the appropriate controller method
     * @return void
     */
    public function _remap()
    {
        $oUri          = Factory::service('Uri');
        $sInvoiceRef   = $oUri->rsegment(2);
        $sInvoiceToken = $oUri->rsegment(3);
        $sMethod       = $oUri->rsegment(4);
        $oInvoiceModel = Factory::model('Invoice', 'nails/module-invoice');
        $oInvoice      = $oInvoiceModel->getByRef(
            $sInvoiceRef,
            ['expand' => ['customer', 'items', 'payments', 'refunds']]
        );

        if (empty($oInvoice) || $sInvoiceToken !== $oInvoice->token || !method_exists($this, $sMethod)) {
            show_404();
        }

        call_user_func([$this, $sMethod], $oInvoice);
    }
}
