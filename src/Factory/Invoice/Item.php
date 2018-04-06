<?php

/**
 * This is a convenience class for generating invoice line items
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Invoice\Factory\Invoice;

use Nails\Invoice\Model\InvoiceItem;

class Item
{
    /**
     * Stores an array of the getter/setters for the other properties
     * @var array
     */
    protected $aMethods = [];

    /**
     * The item's id
     * @var integer
     */
    protected $iId;

    /**
     * The item's label
     * @var string
     */
    protected $sLabel;

    /**
     * The item's body
     * @var string
     */
    protected $sBody;

    /**
     * The item's unit
     * @var string
     */
    protected $iUnit = InvoiceItem::UNIT_NONE;

    /**
     * The item's tax ID
     * @var integer
     */
    protected $iTaxId;

    /**
     * The item's quantity
     * @var string
     */
    protected $iQuantity = 1;

    /**
     * The item's unit cost
     * @var integer
     */
    protected $iUnitCost = 0;

    /**
     * The item's callback data
     * @var mixed
     */
    protected $mCallbackData;

    // --------------------------------------------------------------------------

    /**
     * Item constructor.
     */
    public function __construct()
    {
        $aVars = get_object_vars($this);
        unset($aVars['aMethods']);
        $aVars = array_keys($aVars);

        foreach ($aVars as $sVar) {
            $sNormalised                          = substr($sVar, 1);
            $this->aMethods['set' . $sNormalised] = $sVar;
            $this->aMethods['get' . $sNormalised] = $sVar;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Mimics setters and getters for class properties
     *
     * @param string $sMethod    The method being called
     * @param array  $aArguments Any passed arguments
     *
     * @return $this
     * @throws \Exception
     */
    public function __call($sMethod, $aArguments)
    {
        if (array_key_exists($sMethod, $this->aMethods)) {
            if (substr($sMethod, 0, 3) === 'set') {
                $this->{$this->aMethods[$sMethod]} = reset($aArguments);
                return $this;
            } else {
                return $this->{$this->aMethods[$sMethod]};
            }
        } else {
            throw new \Exception('Call to undefined method ' . get_called_class() . '::' . $sMethod . '()');
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item as an array
     * @return array
     */
    public function toArray()
    {
        return [
            'id'            => (int) $this->iId ?: null,
            'label'         => $this->sLabel,
            'body'          => $this->sBody,
            'unit'          => $this->iUnit,
            'tax_id'        => (int) $this->iTaxId ?: null,
            'quantity'      => (int) $this->iQuantity ?: 0,
            'unit_cost'     => (int) $this->iUnitCost ?: 0,
            'callback_data' => $this->mCallbackData,
        ];
    }
}
