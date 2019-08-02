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
use Nails\Common\Service\Asset;
use Nails\Common\Service\FormValidation;
use Nails\Common\Service\Input;
use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Driver\PaymentBase;
use Nails\Invoice\Exception\DriverException;
use Nails\Invoice\Factory\ChargeRequest;
use Nails\Invoice\Service\Invoice\Skin;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Invoice
 */
class Invoice extends Base
{
    /**
     * The default invoice skin to use
     *
     * @type string
     */
    const DEFAULT_INVOICE_SKIN = 'nails/skin-invoice-classic';

    // --------------------------------------------------------------------------

    /**
     * Download a single invoice
     *
     * @param \stdClass $oInvoice The invoice object
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

        /** @var Skin $oInvoiceSkinService */
        $oInvoiceSkinService = Factory::service('InvoiceSkin', 'nails/module-invoice');

        $sEnabledSkin          = $oInvoiceSkinService->getEnabledSlug() ?: self::DEFAULT_INVOICE_SKIN;
        $this->data['invoice'] = $oInvoice;
        $this->data['isPdf']   = true;
        $sHtml                 = $oInvoiceSkinService->view($sEnabledSkin, 'render', $this->data, true);

        $oPdf = Factory::service('Pdf', 'nails/module-pdf');
        $oPdf->setPaperSize('A4', 'portrait');
        $oPdf->load_html($sHtml);

        $oPdf->download('INVOICE-' . $oInvoice->ref . '.pdf');
    }

    // --------------------------------------------------------------------------

    /**
     * View a single invoice
     *
     * @param \stdClass $oInvoice The invoice object
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

        /** @var Skin $oInvoiceSkinService */
        $oInvoiceSkinService = Factory::service('InvoiceSkin', 'nails/module-invoice');

        $sEnabledSkin          = $oInvoiceSkinService->getEnabledSlug() ?: self::DEFAULT_INVOICE_SKIN;
        $this->data['invoice'] = $oInvoice;
        $this->data['isPdf']   = false;

        $oInvoiceSkinService->view($sEnabledSkin, 'render', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Pay a single invoice
     *
     * @param \stdClass $oInvoice The invoice object
     *
     * @return void
     * @throws NailsException
     */
    protected function pay($oInvoice)
    {
        /** @var Asset $oAsset */
        $oAsset = Factory::service('Asset');
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $this->data['oInvoice']       = $oInvoice;
        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        //  Only open invoices can be paid
        if ($oInvoice->state->id !== 'OPEN' && !$oInvoice->is_scheduled) {

            if ($oInvoice->state->id === 'PAID') {

                $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/paid.php');

                Factory::service('View')
                    ->load([
                        'structure/header',
                        'invoice/pay/paid',
                        'structure/footer',
                    ]);
                return;

            } else {
                show404();
            }
        }

        //  If a user ID is specified, then the user must be logged in as that user
        if (!empty($oInvoice->user->id) && $oInvoice->user->id != activeUser('id')) {
            unauthorised();
        }

        $this->data['sUrlCancel'] = $oInput->get('cancel') ?: siteUrl();

        // --------------------------------------------------------------------------

        //  If there are payments against this invoice which are processing, then deny payment
        if ($oInvoice->has_processing_payments) {

            /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
            $oPaymentModel = Factory::model('Payment', 'nails/module-invoice');

            $this->data['aProcessingPayments'] = [];
            foreach ($oInvoice->payments->data as $oPayment) {
                if ($oPayment->status->id === $oPaymentModel::STATUS_PROCESSING) {
                    $this->data['aProcessingPayments'][] = $oPayment;
                }
            }

            $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/hasProcessing.php');

            Factory::service('View')
                ->load([
                    'structure/header',
                    'invoice/pay/hasProcessing',
                    'structure/footer',
                ]);
            return;
        }

        // --------------------------------------------------------------------------

        //  Payment drivers
        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService  = Factory::service('PaymentDriver', 'nails/module-invoice');
        $aDrivers               = $oPaymentDriverService->getEnabled();
        $this->data['aDrivers'] = [];
        foreach ($aDrivers as $oDriver) {

            $oDriverInstance = $oPaymentDriverService->getInstance($oDriver->slug);

            if ($oDriverInstance->isAvailable($oInvoice) && $oDriverInstance->supportsCurrency($oInvoice->currency)) {
                $this->data['aDrivers'][] = $oDriverInstance;
            }
        }

        // --------------------------------------------------------------------------

        if (isLoggedIn()) {
            $oSourceModel = Factory::model('Source', 'nails/module-invoice');
            $aSources     = $oSourceModel->getAll([
                'where' => [

                ],
            ]);
        }

        // --------------------------------------------------------------------------

        if (!empty($this->data['aDrivers']) && $oInput->post()) {

            try {

                $sSelectedDriver = md5($oInput->post('driver'));
                $oSelectedDriver = null;

                foreach ($this->data['aDrivers'] as $oDriver) {
                    if ($sSelectedDriver == md5($oDriver->getSlug())) {
                        $oSelectedDriver = $oDriver;
                        break;
                    }
                }

                if (empty($oSelectedDriver)) {
                    throw new \Nails\Common\Exception\ValidationException('No payment driver selected.');
                }

                //  @todo (Pablo - 2019-07-31) - Think about validation here

                //  Set up ChargeRequest object
                /** @var ChargeRequest $oChargeRequest */
                $oChargeRequest = Factory::factory('ChargeRequest', 'nails/module-invoice');

                $oChargeRequest->setDriver($oSelectedDriver->getSlug());
                $oChargeRequest->setDescription('Payment for invoice #' . $oInvoice->ref);
                $oChargeRequest->setInvoice($oInvoice->id);

                if ($oInput->get('continue')) {
                    $oChargeRequest->setContinueUrl(
                        $oInput->get('continue')
                    );
                }

                //  Let the driver prepare the charge request to its liking
                $oSelectedDriver->prepareChargeRequest(
                    $oChargeRequest,
                    getFromArray($sSelectedDriver, $_POST, [])
                );

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
        }

        // --------------------------------------------------------------------------

        $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/index.php');

        if (!empty($this->data['aDrivers'])) {

            $oAsset->load('../../node_modules/jquery.payment/lib/jquery.payment.min.js', 'nails/module-invoice');
            $oAsset->load('invoice.pay.min.js', 'nails/module-invoice');

            //  Let the drivers load assets
            foreach ($this->data['aDrivers'] as $oDriver) {
                foreach ($oDriver->getCheckoutAssets() as $sCheckoutAsset) {
                    if (is_string($sCheckoutAsset)) {
                        $oAsset->load($sCheckoutAsset, $oDriver->getSlug());
                    } elseif (is_array($sCheckoutAsset)) {
                        $oAsset->load(
                            getFromArray(0, $sCheckoutAsset),
                            getFromArray(1, $sCheckoutAsset),
                            getFromArray(2, $sCheckoutAsset)
                        );
                    }
                }
            }
        }

        // --------------------------------------------------------------------------

        $sFormUrl = current_url();
        if ($oInput->get()) {
            $sFormUrl .= '?';
            foreach ($oInput->get() as $sKey => $sValue) {
                $sFormUrl .= $sKey . '=' . urlencode($sValue) . '&';
            }
            $sFormUrl = substr($sFormUrl, 0, -1);
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Pay Invoice ' . $oInvoice->ref;

        // --------------------------------------------------------------------------

        Factory::service('View')
            ->setData([
                'sFormUrl' => $sFormUrl,
            ])
            ->load([
                'structure/header',
                'invoice/pay/index',
                'structure/footer',
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Remap requests for valid payments to the appropriate controller method
     *
     * @return void
     */
    public function _remap()
    {
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
        $oInvoiceModel = Factory::model('Invoice', 'nails/module-invoice');

        $sInvoiceRef   = $oUri->rsegment(2);
        $sInvoiceToken = $oUri->rsegment(3);
        $sMethod       = $oUri->rsegment(4);

        $oInvoice = $oInvoiceModel->getByRef(
            $sInvoiceRef,
            ['expand' => ['customer', 'items', 'payments', 'refunds']]
        );

        if (empty($oInvoice) || $sInvoiceToken !== $oInvoice->token || !method_exists($this, $sMethod)) {
            show404();
        }

        call_user_func([$this, $sMethod], $oInvoice);
    }
}
