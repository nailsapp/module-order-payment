<?php

/**
 * Helper Class for building an invoice object
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;

class InvoiceBuilder
{
    //  Invoice properties
    protected $ref;
    protected $state;
    protected $dated;
    protected $terms;
    protected $user_id;
    protected $user_email;
    protected $currency;
    protected $additional_text;
    protected $items;

    // --------------------------------------------------------------------------

    public function __construct()
    {
        $this->ref             = null;
        $this->state           = null;
        $this->dated           = null;
        $this->terms           = null;
        $this->user_id         = null;
        $this->user_email      = null;
        $this->currency        = null;
        $this->additional_text = null;
        $this->callback_data   = null;
        $this->items           = array();
    }

    // --------------------------------------------------------------------------

    //  @todo build methods for populating the fields
    /**
     * Sets the value of the "ref" property
     * @param Object
     */
    public function setRef($sValue)
    {
        $this->ref = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "ref" property
     * @return String
     */
    public function getRef()
    {
        return $this->ref;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "state" property
     * @param Object
     */
    public function setState($sValue)
    {
        $this->state = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "state" property
     * @return String
     */
    public function getState()
    {
        return $this->state;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "dated" property
     * @param Object
     */
    public function setDated($sValue)
    {
        $this->dated = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "dated" property
     * @return String
     */
    public function getDated()
    {
        return $this->dated;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "terms" property
     * @param Object
     */
    public function setTerms($sValue)
    {
        $this->terms = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "terms" property
     * @return String
     */
    public function getTerms()
    {
        return $this->terms;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "user_id" property
     * @param Object
     */
    public function setUserId($sValue)
    {
        $this->user_id = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "user_id" property
     * @return String
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "user_email" property
     * @param Object
     */
    public function setUserEmail($sValue)
    {
        $this->user_email = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "user_email" property
     * @return String
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "currency" property
     * @param Object
     */
    public function setCurrency($sValue)
    {
        $this->currency = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "currency" property
     * @return String
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "additional_text" property
     * @param Object
     */
    public function setAdditionalText($sValue)
    {
        $this->additional_text = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "additional_text" property
     * @return String
     */
    public function getAdditionalText()
    {
        return $this->additional_text;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the value of the "callback_data" property
     * @param Object
     */
    public function setCallbackData($sValue)
    {
        $this->callback_data = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the value for the "callback_data" property
     * @return String
     */
    public function getCallbackData()
    {
        return $this->callback_data;
    }

    // --------------------------------------------------------------------------

    /**
     * Adds an item to the `items` array
     * @param String $sLabel     The item's label
     * @param String $sBody      The item's body/description
     * @param Integer $iQuantity The number of items
     * @param Integer $iUnitCost The unit cost of the item
     * @param String $sUnit      The unit to apply to the item
     * @param Integer $iTaxId    The tax rate to apply
     */
    public function addItem($sLabel, $sBody, $iQuantity, $iUnitCost, $sUnit, $iTaxId)
    {
        $this->items[] = array(
            'label'     => $sLabel,
            'body'      => $sBody,
            'unit'      => $sUnit,
            'tax_id'    => $iTaxId,
            'quantity'  => $iQuantity,
            'unit_cost' => $iUnitCost,
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Remove an item from the `items` array
     * @param  Integer $iItemIndex The index of the item
     * @return Object
     */
    public function removeItem($iItemIndex)
    {
        unset($this->aItems[$iItemIndex]);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to save the invoice to the database
     * @return Object
     */
    public function save()
    {
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');;

        //  Prepare the object
        $aData = array();

        //  Attempt the save
        return $oInvoiceModel->create($aData, true);
    }
}
