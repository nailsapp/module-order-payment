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

use Nails\Auth\Service\Session;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Asset;
use Nails\Common\Service\Input;
use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Factory\ChargeRequest;
use Nails\Invoice\Model\Customer;
use Nails\Invoice\Model\Source;
use Nails\Invoice\Resource;
use Nails\Invoice\Service\Invoice\Skin;
use Nails\Invoice\Service\PaymentDriver;
use Nails\Pdf\Service\Pdf;

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
     * @param Resource\Invoice $oInvoice The invoice object
     *
     * @throws FactoryException
     * @throws NailsException
     */
    protected function download(Resource\Invoice $oInvoice): void
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

        /** @var Pdf $oPdf */
        $oPdf = Factory::service('Pdf', 'nails/module-pdf');

        $oPdf->setPaperSize('A4', 'portrait');
        $oPdf->load_html($sHtml);

        $oPdf->download('INVOICE-' . $oInvoice->ref . '.pdf');
    }

    // --------------------------------------------------------------------------

    /**
     * View a single invoice
     *
     * @param Resource\Invoice $oInvoice The invoice object
     *
     * @throws FactoryException
     * @throws NailsException
     */
    protected function view(Resource\Invoice $oInvoice): void
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
     * @param Resource\Invoice $oInvoice The invoice object
     *
     * @throws FactoryException
     * @throws ModelException
     */
    protected function pay(Resource\Invoice $oInvoice): void
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
        $oPaymentDriverService = Factory::service('PaymentDriver', 'nails/module-invoice');
        $aEnabledDrivers       = $oPaymentDriverService->getEnabled();
        $aAvailableDrivers     = [];

        foreach ($aEnabledDrivers as $oDriver) {

            $oDriverInstance = $oPaymentDriverService->getInstance($oDriver->slug);

            if ($oDriverInstance->isAvailable($oInvoice) && $oDriverInstance->supportsCurrency($oInvoice->currency)) {
                $aAvailableDrivers[] = $oDriverInstance;
            }
        }

        // --------------------------------------------------------------------------

        if (isLoggedIn()) {
            /** @var Source $oSourceModel */
            $oSourceModel = Factory::model('Source', 'nails/module-invoice');
            /** @var Customer $oCustomerModel */
            $oCustomerModel = Factory::model('Customer', 'nails/module-invoice');

            $aSavedPaymentSources = $oSourceModel->getForCustomer(
                $oCustomerModel->getCustomerIdforActiveUser('customer_id')
            );

        } else {
            $aSavedPaymentSources = [];
        }

        // --------------------------------------------------------------------------

        if (!empty($aAvailableDrivers) && $oInput->post()) {

            dd($_POST);

            try {

                $sSelectedDriver = md5($oInput->post('driver'));
                $oSelectedDriver = null;

                foreach ($aAvailableDrivers as $oDriver) {
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

                /** @var Input $oInput */
                $oInput = Factory::service('Input');

                if ($oInput->get('url_success')) {
                    //  Explicity defined success URL
                    $oChargeRequest->setSuccessUrl(
                        $oInput->get('url_success')
                    );
                } else {
                    //  Come back to the exact same page
                    $oChargeRequest->setSuccessUrl(
                        $oInput->server('REQUEST_URI')
                    );
                }

                if ($oInput->get('url_error')) {
                    //  Explicity defined error URL
                    $oChargeRequest->setErrorUrl(
                        $oInput->get('url_error')
                    );
                } else {
                    //  Come back to the exact same page
                    $oChargeRequest->setErrorUrl(
                        $oInput->server('REQUEST_URI')
                    );
                }

                if ($oInput->get('url_cancel')) {
                    //  Explicity defined cancel URL
                    $oChargeRequest->setCancelUrl(
                        $oInput->get('url_cancel')
                    );
                } else {
                    //  Come back to the exact same page
                    $oChargeRequest->setCancelUrl(
                        $oInput->server('REQUEST_URI')
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
                     * Payment was successful (but potentially unconfirmed).
                     */
                    redirect($oChargeResponse->getSuccessUrl());

                } elseif ($oChargeResponse->isFailed()) {

                    /**
                     * Payment failed, throw an error which will be caught and displayed to the user
                     */
                    throw new InvoiceException('Payment failed: ' . $oChargeResponse->getErrorMessageUser());

                } else {

                    /**
                     * Something which we've not accounted for went wrong.
                     */
                    throw new InvoiceException('Payment failed.');
                }

            } catch (\Exception $e) {

                $sErrorUrl = $oChargeResponse->getErrorUrl();
                if (!empty($sErrorUrl)) {

                    /** @var Session $oSession */
                    $oSession = Factory::service('Session', 'nails/module-auth');
                    $oSession->setFlashData('error', $e->getMessage());

                    redirect($oChargeResponse->getErrorUrl());
                } else {
                    $this->data['error'] = $e->getMessage();
                }
            }
        }

        // --------------------------------------------------------------------------

        $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/index.php');

        if (!empty($aAvailableDrivers)) {

            $oAsset->load('../../node_modules/jquery.payment/lib/jquery.payment.min.js', 'nails/module-invoice');
            $oAsset->load('invoice.pay.min.js', 'nails/module-invoice');

            //  Let the drivers load assets
            foreach ($aAvailableDrivers as $oDriver) {
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
            $sFormUrl .= '?' . http_build_query($oInput->get());
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Pay Invoice ' . $oInvoice->ref;

        // --------------------------------------------------------------------------

        Factory::service('View')
            ->setData([
                'sFormUrl'             => $sFormUrl,
                'sUrlCancel'           => $oInput->get('url_cancel') ?: siteUrl(),
                'aDrivers'             => $aAvailableDrivers,
                'aSavedPaymentSources' => $aSavedPaymentSources,
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
     * @throws FactoryException
     * @throws ModelException
     */
    public function _remap(): void
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
