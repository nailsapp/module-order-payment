<?php

/**
 * A single payment
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Object
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Object;

class Payment
{
    private $id;
    private $processor;
    private $invoice_id;
    private $invoice_ref;
    private $invoice_state;
    private $transaction_ref;
    private $currency;
    private $currency_base;
    private $amount;
    private $amount_base;
    private $fee;
    private $fee_base;
    private $created;
    private $created_by;
    private $modified;
    private $modified_by;

    // --------------------------------------------------------------------------

    /**
     * Initialise the invoice with data from the database
     * @param  object $aData The invoice row
     * @return object
     */
    public function init($oData)
    {
        if (is_null($this->id)) {

            $this->id              = property_exists($oData, 'id') ? $oData->id : null;
            $this->processor       = property_exists($oData, 'processor') ? $oData->processor : null;
            $this->invoice_id      = property_exists($oData, 'invoice_id') ? $oData->invoice_id : null;
            $this->invoice_ref     = property_exists($oData, 'invoice_ref') ? $oData->invoice_ref : null;
            $this->invoice_state   = property_exists($oData, 'invoice_state') ? $oData->invoice_state : null;
            $this->transaction_ref = property_exists($oData, 'transaction_ref') ? $oData->transaction_ref : null;
            $this->currency        = property_exists($oData, 'currency') ? $oData->currency : null;
            $this->currency_base   = property_exists($oData, 'currency_base') ? $oData->currency_base : null;
            $this->amount          = property_exists($oData, 'amount') ? $oData->amount : null;
            $this->amount_base     = property_exists($oData, 'amount_base') ? $oData->amount_base : null;
            $this->fee             = property_exists($oData, 'fee') ? $oData->fee : null;
            $this->fee_base        = property_exists($oData, 'fee_base') ? $oData->fee_base : null;
            $this->created         = property_exists($oData, 'created') ? $oData->created : null;
            $this->created_by      = property_exists($oData, 'created_by') ? $oData->created_by : null;
            $this->modified        = property_exists($oData, 'modified') ? $oData->modified : null;
            $this->modified_by     = property_exists($oData, 'modified_by') ? $oData->modified_by : null;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Fetch a property
     * @param  string $sName the proeprty to Fetch
     * @return mixed
     */
    public function __get($sName)
    {
        if (property_exists($this, $sName)) {
            return $this->{$sName};
        }
        user_error('Invalid property: ' . __CLASS__ . '->' . $sName);
    }
}
