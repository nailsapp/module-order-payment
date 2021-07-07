<?php

/**
 * This class represents objects dispensed by the Payment model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Resource\Entity;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Service\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Driver\PaymentBase;
use Nails\Invoice\Resource\Payment\Amount;
use Nails\Invoice\Resource\Payment\Data\Sca;
use Nails\Invoice\Resource\Payment\Status;
use Nails\Invoice\Resource\Payment\Urls;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Payment
 *
 * @package Nails\Invoice\Resource
 */
class Payment extends Entity
{
    /**
     * The payment's reference
     *
     * @var string
     */
    public $ref;

    /**
     * The payment's token
     *
     * @var string
     */
    public $token;

    /**
     * The payment's driver
     *
     * @var PaymentBase
     */
    public $driver;

    /**
     * The payment's invoice ID
     *
     * @var int
     */
    public $invoice_id;

    /**
     * The invoice (expandable field)
     *
     * @var Invoice
     */
    public $invoice;

    /**
     * The payment's source ID
     *
     * @var int
     */
    public $source_id;

    /**
     * The payment source used (expandable field)
     *
     * @var Source
     */
    public $source;

    /**
     * The payment's description
     *
     * @var string
     */
    public $description;

    /**
     * The payment's status
     *
     * @var Status
     */
    public $status;

    /**
     * The payment's transaction ID
     *
     * @var string
     */
    public $transaction_id;

    /**
     * The payment's failure message
     *
     * @var string
     */
    public $fail_msg;

    /**
     * The payment's failure code
     *
     * @var string
     */
    public $fail_code;

    /**
     * The payment's currency
     *
     * @var \Nails\Currency\Resource\Currency
     */
    public $currency;

    /**
     * The payment's amount
     *
     * @var Amount
     */
    public $amount;

    /**
     * The payment's amount which has been refunded
     *
     * @var Amount
     */
    public $amount_refunded;

    /**
     * The payment's fee
     *
     * @var Amount
     */
    public $fee;

    /**
     * The payment's fee which has been refunded
     *
     * @var Amount
     */
    public $fee_refunded;

    /**
     * The available amount to be refunded
     *
     * @var Amount
     */
    public $available_for_refund;

    /**
     * Whether the payment is refundable
     *
     * @var bool
     */
    public $is_refundable;

    /**
     * Whether the customer was present
     *
     * @var bool
     */
    public $customer_present;

    /**
     * The payment's custom data
     *
     * @var Invoice\Data\Payment
     */
    public $custom_data;

    /**
     * The payment's SCA data
     *
     * @var Sca
     */
    public $sca_data;

    /**
     * The payment's URLs
     *
     * @var Urls
     */
    public $urls;

    // --------------------------------------------------------------------------

    /**
     * Payment constructor.
     *
     * @param array $mObj
     *
     * @throws FactoryException
     * @throws CurrencyException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        // --------------------------------------------------------------------------

        /** @var \Nails\Invoice\Model\Payment $oModel */
        $oModel    = Factory::model('Payment', Constants::MODULE_SLUG);
        $aStatuses = $oModel->getStatusesHuman();

        $this->status = Factory::resource(
            'PaymentStatus',
            Constants::MODULE_SLUG,
            (object) [
                'id'    => $mObj->status,
                'label' => $aStatuses[$mObj->status],
            ]
        );

        // --------------------------------------------------------------------------

        //  Driver
        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);
        $this->driver          = $oPaymentDriverService->getInstance($mObj->driver);

        // --------------------------------------------------------------------------

        //  Currency
        /** @var Currency $oCurrency */
        $oCurrency      = Factory::service('Currency', \Nails\Currency\Constants::MODULE_SLUG);
        $this->currency = $oCurrency->getByIsoCode($mObj->currency);

        // --------------------------------------------------------------------------

        //  Amounts and values
        $this->amount = Factory::resource(
            'PaymentAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $mObj->amount,
            ]
        );

        $this->amount_refunded = Factory::resource(
            'PaymentAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $mObj->amount_refunded,
            ]
        );

        $this->fee = Factory::resource(
            'PaymentAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $mObj->fee,
            ]
        );

        $this->fee_refunded = Factory::resource(
            'PaymentAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $mObj->fee_refunded,
            ]
        );

        $this->available_for_refund = Factory::resource(
            'PaymentAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $this->amount->raw - $this->amount_refunded->raw,
            ]
        );

        // --------------------------------------------------------------------------

        //  Computed booleans
        $aValidStates = [
            $oModel::STATUS_PROCESSING,
            $oModel::STATUS_COMPLETE,
            $oModel::STATUS_REFUNDED_PARTIAL,
        ];

        $this->is_refundable = in_array($this->status->id, $aValidStates) && $this->available_for_refund->raw > 0;

        // --------------------------------------------------------------------------

        //  URLs
        $this->urls = Factory::resource(
            'PaymentUrls',
            Constants::MODULE_SLUG,
            (object) [
                'complete'   => siteUrl('invoice/payment/' . $this->id . '/' . $this->token . '/complete'),
                'thanks'     => siteUrl('invoice/payment/' . $this->id . '/' . $this->token . '/thanks'),
                'processing' => siteUrl('invoice/payment/' . $this->id . '/' . $this->token . '/processing'),
                'success'    => !empty($mObj->url_success) ? siteUrl($mObj->url_success) : null,
                'error'      => !empty($mObj->url_error) ? siteUrl($mObj->url_error) : null,
                'cancel'     => !empty($mObj->url_cancel) ? siteUrl($mObj->url_cancel) : null,
            ]
        );

        unset($this->url_success);
        unset($this->url_error);
        unset($this->url_cancel);

        // --------------------------------------------------------------------------

        //  Custom Data
        //  @todo (Pablo - 2019-09-11) - Rename this to payment_data
        //  @todo (Pablo - 2019-09-11) - Move the data object into the Payment namespace?
        $this->custom_data = Factory::resource(
            'InvoiceDataPayment',
            Constants::MODULE_SLUG,
            json_decode($this->custom_data) ?: (object) []
        );

        //  SCA Data
        $this->sca_data = Factory::resource(
            'PaymentDataSca',
            Constants::MODULE_SLUG,
            json_decode($this->sca_data) ?: (object) []
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the payment source used for the payment, if known
     *
     * @return Source|null
     * @throws FactoryException
     * @throws ModelException
     */
    public function source(): ?Source
    {
        if (empty($this->source) && !empty($this->source_id)) {
            /** @var \Nails\Invoice\Model\Source $oModel */
            $oModel       = Factory::model('Source', Constants::MODULE_SLUG);
            $this->source = $oModel->getById($this->source_id);
        }

        return $this->source;
    }
}
