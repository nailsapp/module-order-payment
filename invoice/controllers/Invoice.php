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

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Exception\ValidationException;
use Nails\Common\Service\Asset;
use Nails\Common\Service\Input;
use Nails\Common\Service\UserFeedback;
use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Driver\PaymentBase;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Factory\ChargeRequest;
use Nails\Invoice\Model\Customer;
use Nails\Invoice\Model\Source;
use Nails\Invoice\Resource;
use Nails\Invoice\Service\Invoice\Skin;
use Nails\Invoice\Service\PaymentDriver;
use Nails\Pdf;

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
            'name'       => appSetting('business_name', Constants::MODULE_SLUG),
            'address'    => appSetting('business_address', Constants::MODULE_SLUG),
            'telephone'  => appSetting('business_telephone', Constants::MODULE_SLUG),
            'email'      => appSetting('business_email', Constants::MODULE_SLUG),
            'vat_number' => appSetting('business_vat_number', Constants::MODULE_SLUG),
        ];

        /** @var Skin $oInvoiceSkinService */
        $oInvoiceSkinService = Factory::service('InvoiceSkin', Constants::MODULE_SLUG);

        $sEnabledSkin          = $oInvoiceSkinService->getEnabledSlug() ?: self::DEFAULT_INVOICE_SKIN;
        $this->data['invoice'] = $oInvoice;
        $this->data['isPdf']   = true;
        $sHtml                 = $oInvoiceSkinService->view($sEnabledSkin, 'render', $this->data, true);

        /** @var Pdf\Service\Pdf $oPdf */
        $oPdf = Factory::service('Pdf', Pdf\Constants::MODULE_SLUG);

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
            'name'       => appSetting('business_name', Constants::MODULE_SLUG),
            'address'    => appSetting('business_address', Constants::MODULE_SLUG),
            'telephone'  => appSetting('business_telephone', Constants::MODULE_SLUG),
            'email'      => appSetting('business_email', Constants::MODULE_SLUG),
            'vat_number' => appSetting('business_vat_number', Constants::MODULE_SLUG),
        ];

        /** @var Skin $oInvoiceSkinService */
        $oInvoiceSkinService = Factory::service('InvoiceSkin', Constants::MODULE_SLUG);

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
     * @throws ValidationException
     */
    protected function pay(Resource\Invoice $oInvoice): void
    {
        /** @var Asset $oAsset */
        $oAsset = Factory::service('Asset');
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
        $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
        /** @var Source $oSourceModel */
        $oSourceModel = Factory::model('Source', Constants::MODULE_SLUG);
        /** @var Customer $oCustomerModel */
        $oCustomerModel = Factory::model('Customer', Constants::MODULE_SLUG);
        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);

        // --------------------------------------------------------------------------

        //  Only open invoices can be paid
        if ($oInvoice->state->id !== $oInvoiceModel::STATE_OPEN && !$oInvoice->is_scheduled) {

            if (in_array($oInvoice->state->id, [$oInvoiceModel::STATE_PAID, $oInvoiceModel::STATE_PAID_PROCESSING])) {

                //  If there are payments against this invoice which are processing, then deny payment
                if ($oInvoice->has_processing_payments) {

                    /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
                    $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);

                    $aProcessingPayments = [];
                    foreach ($oInvoice->payments->data as $oPayment) {
                        if ($oPayment->status->id === $oPaymentModel::STATUS_PROCESSING) {
                            $aProcessingPayments[] = $oPayment;
                        }
                    }

                    $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/hasProcessing.php');

                    Factory::service('View')
                        ->setData([
                            'oInvoice'            => $oInvoice,
                            'aProcessingPayments' => $aProcessingPayments,
                        ])
                        ->load([
                            'structure/header/blank',
                            'invoice/pay/hasProcessing',
                            'structure/footer/blank',
                        ]);
                    return;

                } else {

                    $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/paid.php');

                    Factory::service('View')
                        ->setData([
                            'oInvoice' => $oInvoice,
                        ])
                        ->load([
                            'structure/header/blank',
                            'invoice/pay/paid',
                            'structure/footer/blank',
                        ]);
                    return;
                }

            } else {
                show404();
            }
        }

        // --------------------------------------------------------------------------

        /** @var PaymentBase[] $aEnabledDrivers */
        $aEnabledDrivers = $oPaymentDriverService->getEnabled();
        /** @var PaymentBase[] $aAvailableDrivers */
        $aAvailableDrivers = [];

        foreach ($aEnabledDrivers as $oDriver) {

            $oDriverInstance = $oPaymentDriverService->getInstance($oDriver->slug);

            if ($oDriverInstance->isAvailable($oInvoice) && $oDriverInstance->supportsCurrency($oInvoice->currency)) {
                $aAvailableDrivers[] = $oDriverInstance;
            }
        }

        // --------------------------------------------------------------------------

        //  If the invoice can only be paid using a certain driver then filter
        if (!empty($oInvoice->payment_driver)) {
            $aAvailableDrivers = array_filter(
                $aAvailableDrivers,
                function (PaymentBase $oDriver) use ($oInvoice) {
                    return $oDriver->getSlug() === $oInvoice->payment_driver;
                }
            );
            $aAvailableDrivers = array_values($aAvailableDrivers);
        }

        // --------------------------------------------------------------------------

        $iCustomerId = $oCustomerModel->getCustomerIdForActiveUser();
        if ($iCustomerId) {

            $aSavedPaymentSources = $oSourceModel->getForCustomer(
                $iCustomerId
            );

        } else {
            $aSavedPaymentSources = [];
        }

        // Index the saved payment sources by ID for easy access
        $aSavedPaymentSources = array_combine(
            arrayExtractProperty($aSavedPaymentSources, 'id'),
            $aSavedPaymentSources
        );

        // --------------------------------------------------------------------------

        if (!empty($aAvailableDrivers) && $oInput->post()) {

            try {

                /**
                 * Determine what our payment driver will be, and if a saved source
                 * is being used.
                 */

                $sSelectedDriverSlug = trim($oInput->post('driver'));

                if (empty($sSelectedDriverSlug)) {
                    throw new ValidationException(
                        'No payment source selected'
                    );
                }

                /**
                 * If the user has chosen a saved payment source then the "driver"
                 * will be a numeric ID, validate this belongs to the user.
                 */
                if (!empty($aSavedPaymentSources) && is_numeric($sSelectedDriverSlug)) {

                    if (!array_key_exists($sSelectedDriverSlug, $aSavedPaymentSources)) {
                        throw new ValidationException('Invalid payment source ID.');
                    }

                    /** @var Resource\Source $oSavedPaymentSource */
                    $oSavedPaymentSource = getFromArray($sSelectedDriverSlug, $aSavedPaymentSources);
                    $sSelectedDriverSlug = $oSavedPaymentSource->driver;
                }

                /**
                 * Fetch the driver instance, if none found then we cannot continue.
                 */

                $sSelectedDriver = md5($sSelectedDriverSlug);
                $oSelectedDriver = null;

                foreach ($aAvailableDrivers as $oDriver) {
                    if ($sSelectedDriver === md5($oDriver->getSlug())) {
                        $oSelectedDriver = $oDriver;
                        break;
                    }
                }

                if (empty($oSelectedDriver)) {
                    throw new ValidationException('No payment driver selected.');
                }

                // --------------------------------------------------------------------------

                //  @todo (Pablo - 2019-09-06) - Do we need to validate any user entered data (e.g. driver fields)

                // --------------------------------------------------------------------------

                /**
                 * Set up the charge request, and handle the charger esponse
                 */

                /** @var ChargeRequest $oChargeRequest */
                $oChargeRequest = Factory::factory('ChargeRequest', Constants::MODULE_SLUG);
                $oChargeRequest
                    ->setDriver($oSelectedDriver)
                    ->setDescription('Payment for invoice #' . $oInvoice->ref)
                    ->setInvoice($oInvoice);

                if (!empty($oSavedPaymentSource)) {
                    $oChargeRequest
                        ->setSource($oSavedPaymentSource);
                }

                /** @var Input $oInput */
                $oInput = Factory::service('Input');

                /**
                 * Set the success, error, and cancel URLs. If explicitly defined, use them,
                 * if not default to coming back to this page.
                 */

                if ($oInput->get('url_success')) {
                    $oChargeRequest->setSuccessUrl(
                        $oInput->get('url_success')
                    );
                } else {
                    $oChargeRequest->setSuccessUrl(
                        $oInput->server('REQUEST_URI')
                    );
                }

                if ($oInput->get('url_error')) {
                    $oChargeRequest->setErrorUrl(
                        $oInput->get('url_error')
                    );
                } else {
                    $oChargeRequest->setErrorUrl(
                        $oInput->server('REQUEST_URI')
                    );
                }

                if ($oInput->get('url_cancel')) {
                    $oChargeRequest->setCancelUrl(
                        $oInput->get('url_cancel')
                    );
                }

                //  Let the driver prepare the charge request to its liking
                $oSelectedDriver->prepareChargeRequest(
                    $oChargeRequest,
                    $oInput->post($sSelectedDriver) ?: []
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

            } catch (Exception $e) {

                $sErrorUrl = !empty($oChargeResponse) ? $oChargeResponse->getErrorUrl() : null;
                if (!empty($sErrorUrl)) {

                    /** @var UserFeedback $oUserFeedback */
                    $oUserFeedback = Factory::service('UserFeedback');
                    $oUserFeedback->error($e->getMessage());

                    redirect($sErrorUrl);

                } else {
                    $this->data['error'] = $e->getMessage();
                }
            }
        }

        // --------------------------------------------------------------------------

        $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/index.php');

        if (!empty($aAvailableDrivers)) {

            $oAsset->load('../../node_modules/jquery.payment/lib/jquery.payment.min.js', Constants::MODULE_SLUG);
            $oAsset->load('invoice.pay.min.js', Constants::MODULE_SLUG);

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
                'oInvoice'             => $oInvoice,
                'sUrlCancel'           => siteUrl($oInput->get('url_cancel')) ?: siteUrl(),
                'aDrivers'             => $aAvailableDrivers,
                'aSavedPaymentSources' => $aSavedPaymentSources,
            ])
            ->load([
                'structure/header/blank',
                'invoice/pay/index',
                'structure/footer/blank',
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
        $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);

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
