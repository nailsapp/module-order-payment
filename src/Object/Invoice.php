<?php

/**
 * A single invoice
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Object
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Object;

class Invoice
{
    private $id;
    private $ref;
    private $state;
    private $user_id;
    private $user_email;
    private $currency;
    private $total;
    private $tax;
    private $fee;
    private $created;
    private $created_by;
    private $modified;
    private $modified_by;
    private $items;
    private $payments;

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
            $this->ref             = property_exists($oData, 'ref') ? $oData->ref : null;
            $this->state           = property_exists($oData, 'state') ? $oData->state : null;
            $this->user_id         = property_exists($oData, 'user_id') ? $oData->user_id : null;
            $this->user_email      = property_exists($oData, 'user_email') ? $oData->user_email : null;
            $this->currency        = property_exists($oData, 'currency') ? $oData->currency : null;
            $this->total           = property_exists($oData, 'total') ? $oData->total : null;
            $this->tax             = property_exists($oData, 'tax') ? $oData->tax : null;
            $this->fee             = property_exists($oData, 'fee') ? $oData->fee : null;
            $this->additional_text = property_exists($oData, 'additional_text') ? $oData->additional_text : null;
            $this->callback_data   = property_exists($oData, 'callback_data') ? $oData->callback_data : null;
            $this->created         = property_exists($oData, 'created') ? $oData->created : null;
            $this->created_by      = property_exists($oData, 'created_by') ? $oData->created_by : null;
            $this->modified        = property_exists($oData, 'modified') ? $oData->modified : null;
            $this->modified_by     = property_exists($oData, 'modified_by') ? $oData->modified_by : null;
            $this->items           = property_exists($oData, 'items') ? $oData->items : array();
            $this->payments        = property_exists($oData, 'payments') ? $oData->payments : array();
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
