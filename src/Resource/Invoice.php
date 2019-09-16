<?php

/**
 * This class represents objects dispensed by the Invoice model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource\Date;
use Nails\Common\Resource\DateTime;
use Nails\Common\Resource\Entity;
use Nails\Common\Resource\ExpandableField;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Resource\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Resource\Invoice\Data;
use Nails\Invoice\Resource\Invoice\Item;
use Nails\Invoice\Resource\Invoice\State;
use Nails\Invoice\Resource\Invoice\Totals;
use Nails\Invoice\Resource\Invoice\Urls;

class Invoice extends Entity
{
    /**
     * The invoice's reference
     *
     * @var string
     */
    public $ref;

    /**
     * The invoice's token
     *
     * @var string
     */
    public $token;

    /**
     * The invoice's customer ID
     *
     * @var int
     */
    public $customer_id;

    /**
     * The customer (expandable field)
     *
     * @var Customer
     */
    public $customer;

    /**
     * The invoice's state
     *
     * @var State
     */
    public $state;

    /**
     * The invoice's date
     *
     * @var Date
     */
    public $dated;

    /**
     * The invoice's terms, in days
     *
     * @var int
     */
    public $terms;

    /**
     * The invoice's due date
     *
     * @var Date
     */
    public $due;

    /**
     * The invoice's paid date
     *
     * @var DateTime
     */
    public $paid;

    /**
     * The invoice's email
     *
     * @var string
     */
    public $email;

    /**
     * The invoice's currency
     *
     * @var Currency
     */
    public $currency;

    /**
     * Any additional text
     *
     * @var string
     */
    public $additional_text;

    /**
     * Any callback data
     *
     * @var Data\Callback
     */
    public $callback_data;

    /**
     * Any payment data
     *
     * @var Data\Payment
     */
    public $payment_data;

    /**
     * The payemnt driver
     *
     * @var string|null
     */
    public $payment_driver;

    /**
     * Whether the invoice is scheduled
     *
     * @var bool
     */
    public $is_scheduled;

    /**
     * Whether the invoice is overdue
     *
     * @var bool
     */
    public $is_overdue;

    /**
     * Whether the invoice has processing payments
     *
     * @var bool
     */
    public $has_processing_payments;

    /**
     * The invoice totals
     *
     * @var Totals
     */
    public $totals;

    /**
     * The invoice URLs
     *
     * @var Urls
     */
    public $urls;

    /**
     * The invoice items (expandable field)
     *
     * @var ExpandableField
     */
    public $items;

    // --------------------------------------------------------------------------

    /**
     * Invoice constructor.
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

        /** @var \Nails\Invoice\Model\Invoice $oModel */
        $oModel  = Factory::model('Invoice', Constants::MODULE_SLUG);
        $aStates = $oModel->getStates();

        $this->state = Factory::resource(
            'InvoiceState',
            Constants::MODULE_SLUG,
            (object) [
                'id'    => $mObj->state,
                'label' => $aStates[$mObj->state],
            ]
        );

        // --------------------------------------------------------------------------

        //  Dates and DateTimes
        $this->dated = Factory::resource('Date', null, (object) ['raw' => $mObj->dated]);
        $this->due   = Factory::resource('Date', null, (object) ['raw' => $mObj->due]);
        $this->paid  = Factory::resource('DateTime', null, (object) ['raw' => $mObj->paid]);

        // --------------------------------------------------------------------------

        //  Computed booleans

        /** @var \DateTime $oNow */
        $oNow = Factory::factory('DateTime');

        $this->is_scheduled = false;
        if ($this->state->id == $oModel::STATE_OPEN && $oNow < (new \DateTime($mObj->dated))) {
            $this->is_scheduled = true;
        }

        $this->is_overdue = false;
        if ($this->state->id == $oModel::STATE_OPEN && $oNow > (new \DateTime($mObj->due))) {
            $this->is_overdue = true;
        }

        $this->has_processing_payments = $mObj->processing_payments > 0;
        unset($this->processing_payments);

        // --------------------------------------------------------------------------

        //  Currency
        /** @var \Nails\Currency\Service\Currency $oCurrencyService */
        $oCurrencyService = Factory::service('Currency', \Nails\Currency\Constants::MODULE_SLUG);
        $this->currency   = $oCurrencyService->getByIsoCode($mObj->currency);

        // --------------------------------------------------------------------------

        //  Totals
        $this->totals = Factory::resource(
            'InvoiceTotals',
            Constants::MODULE_SLUG,
            (object) [
                'currency'   => $this->currency,
                'sub'        => (int) $mObj->sub_total,
                'tax'        => (int) $mObj->tax_total,
                'grand'      => (int) $mObj->grand_total,
                'paid'       => (int) $mObj->paid_total,
                'processing' => (int) $mObj->processing_total,
            ]
        );

        unset($this->sub_total);
        unset($this->tax_total);
        unset($this->grand_total);
        unset($this->paid_total);
        unset($this->processing_total);

        // --------------------------------------------------------------------------

        //  URLs
        $this->urls = Factory::resource(
            'InvoiceUrls',
            Constants::MODULE_SLUG,
            (object) [
                'payment'  => siteUrl('invoice/invoice/' . $this->ref . '/' . $this->token . '/pay'),
                'download' => siteUrl('invoice/invoice/' . $this->ref . '/' . $this->token . '/download'),
                'view'     => siteUrl('invoice/invoice/' . $this->ref . '/' . $this->token . '/view'),
            ]
        );

        // --------------------------------------------------------------------------

        //  Data blobs
        $this->callback_data = Factory::resource(
            'InvoiceDataCallback',
            Constants::MODULE_SLUG,
            json_decode($this->callback_data) ?: (object) []
        );

        $this->payment_data = Factory::resource(
            'InvoiceDataPayment',
            Constants::MODULE_SLUG,
            json_decode($this->payment_data) ?: (object) []
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Charges this invoice
     *
     * @param ChargeRequest $oChargeRequest
     *
     * @return ChargeResponse
     * @throws InvoiceException
     */
    public function charge(ChargeRequest $oChargeRequest)
    {
        return $oChargeRequest
            ->setInvoice($this->id)
            ->setDescription('Payment for invoice ' . $this->id)
            ->execute();
    }
}
