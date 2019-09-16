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

use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Model\Invoice;
use Nails\Invoice\Resource\Tax;

/**
 * Class Item
 *
 * @package Nails\Invoice\Factory\Invoice
 */
class Item
{
    /**
     * The item's id
     *
     * @var int|null
     */
    protected $iId = null;

    /**
     * The item's label
     *
     * @var string
     */
    protected $sLabel = '';

    /**
     * The item's body
     *
     * @var string
     */
    protected $sBody = '';

    /**
     * The item's unit
     *
     * @var string
     */
    protected $iUnit = Invoice\Item::UNIT_NONE;

    /**
     * The item's tax ID
     *
     * @var int|null
     */
    protected $iTaxId = null;

    /**
     * The item's quantity
     *
     * @var string
     */
    protected $iQuantity = 1;

    /**
     * The item's unit cost
     *
     * @var int
     */
    protected $iUnitCost = 0;

    /**
     * The item's callback data
     *
     * @var mixed|null
     */
    protected $mCallbackData = null;

    // --------------------------------------------------------------------------

    /**
     * Set the item's ID
     *
     * @param int $iId The Id to set
     *
     * @return Item
     */
    public function setId(int $iId): Item
    {
        $this->iId = $iId;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->iId;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's label
     *
     * @param string $sLabel The item's label
     *
     * @return Item
     */
    public function setLabel(string $sLabel): Item
    {
        $this->sLabel = $sLabel;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's body
     *
     * @param string $sBody The item's body
     *
     * @return Item
     */
    public function setBody(string $sBody): Item
    {
        $this->sBody = $sBody;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's body
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->sBody;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's unit
     *
     * @param string $sUnit The item's unit
     *
     * @return Item
     */
    public function setUnit(string $sUnit): Item
    {
        $this->sUnit = $sUnit;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's unit
     *
     * @return string
     */
    public function getUnit(): string
    {
        return $this->sUnit;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's tax ID
     *
     * @param int|Tax $iTaxId The item's tax ID, or a tax resource
     *
     * @return Item
     */
    public function setTaxId($iTaxId): Item
    {
        if ($iTaxId instanceof Tax) {
            $this->iTaxId = $iTaxId->id;
        } else {
            $this->iTaxId = $iTaxId;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's tax ID
     *
     * @return int|null
     */
    public function getTaxId(): ?int
    {
        return $this->iTaxId;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's quantity
     *
     * @param int $iQuantity The item's quantity
     *
     * @return Item
     */
    public function setQuantity(int $iQuantity): Item
    {
        $this->iQuantity = $iQuantity;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's quantity
     *
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->iQuantity;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's unit cost
     *
     * @param int $iUnitCost The item's unit cost
     *
     * @return Item
     */
    public function setUnitCost(int $iUnitCost): Item
    {
        $this->iUnitCost = $iUnitCost;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's unit cost
     *
     * @return int
     */
    public function getUnitCost(): int
    {
        return $this->iUnitCost;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the item's callback data
     *
     * @param $mValue The item's callback data
     *
     * @return Item
     */
    public function setCallbackData($mValue): Item
    {
        $this->mCallbackData = $mValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the item's callback data
     *
     * @return mixed
     */
    public function getCallbackData()
    {
        return $this->mCallbackData;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item as an array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'            => $this->iId,
            'label'         => $this->sLabel,
            'body'          => $this->sBody,
            'unit'          => $this->iUnit,
            'tax_id'        => $this->iTaxId,
            'quantity'      => $this->iQuantity,
            'unit_cost'     => $this->iUnitCost,
            'callback_data' => $this->mCallbackData,
        ];
    }
}
