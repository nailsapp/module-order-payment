<?php

/**
 * A single invoice line item
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Object
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Object;

class InvoiceItem
{
    private $id;
    private $quantity;
    private $units;
    private $tax;
    private $label;
    private $body;

    // --------------------------------------------------------------------------

    /**
     * Initialise the item with data from the database
     * @param  object $aData The item row
     * @return object
     */
    public function init($oData)
    {
        if (is_null($this->id)) {

            $this->id       = property_exists($oData, 'id') ? $oData->id : null;
            $this->quantity = property_exists($oData, 'quantity') ? $oData->quantity : null;
            $this->units    = property_exists($oData, 'units') ? $oData->units : null;
            $this->tax      = property_exists($oData, 'tax') ? $oData->tax : null;
            $this->label    = property_exists($oData, 'label') ? $oData->label : null;
            $this->body     = property_exists($oData, 'body') ? $oData->body : null;
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
