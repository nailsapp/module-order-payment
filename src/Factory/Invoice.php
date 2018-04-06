<?php

/**
 * This is a convenience class for generating invoices
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 */

namespace Nails\Invoice\Factory;

use Nails\Common\Traits\ErrorHandling;
use Nails\Factory;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Factory\Invoice\Item;

class Invoice
{
    use ErrorHandling;

    // --------------------------------------------------------------------------

    /**
     * Stores an array of the getter/setters for the other properties
     * @var array
     */
    protected $aMethods = [];

    /**
     * The invoice's ID
     * @var integer
     */
    protected $iId;

    /**
     * The invoice's ref
     * @var string
     */
    protected $sRef;

    /**
     * The invoice's state
     * @var string
     */
    protected $sState = \Nails\Invoice\Model\Invoice::STATE_OPEN;

    /**
     * The invoice's dated date
     * @var string
     */
    protected $sDated;

    /**
     * The invoice's terms
     * @var integer
     */
    protected $iTerms;

    /**
     * The invoice's customer ID
     * @var integer
     */
    protected $iCustomerId;

    /**
     * The invoice's email
     * @var string
     */
    protected $sEmail;

    /**
     * The invoice's currency
     * @var string
     */
    protected $sCurrency;

    /**
     * The invoice's additional text
     * @var string
     */
    protected $sAdditionalText;

    /**
     * The invoice's callback data
     * @var mixed
     */
    protected $mCallbackData;

    /**
     * The invoice's items
     * @var string
     */
    protected $aItems;

    // --------------------------------------------------------------------------

    /**
     * Invoice constructor.
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
                if (empty(!$this->iId)) {
                    throw new InvoiceException('Invoice has been saved and cannot be modified.');
                }
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
     * Add an item to the invoice
     *
     * @param Item $oItem the item to add
     *
     * @return $this
     * @throws InvoiceException
     */
    public function addItem(Item $oItem)
    {
        if (empty(!$this->iId)) {
            throw new InvoiceException('Invoice has been saved and cannot be modified.');
        }
        $this->aItems[] = $oItem;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Add multiple items to the invoice
     *
     * @param array $aItems The array of items to add
     *
     * @return $this
     */
    public function addItems(array $aItems)
    {
        foreach ($aItems as $oItem) {
            $this->addItem($oItem);
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Saves a new invoice
     * @return \stdClass
     * @throws InvoiceException
     */
    public function save()
    {
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        if (empty($this->iId)) {
            $oInvoice = $oInvoiceModel->create($this->toArray(), true);
            if (empty($oInvoice)) {
                throw new InvoiceException($oInvoiceModel->lastError());
            }
            $this->iId  = $oInvoice->id;
            $this->sRef = $oInvoice->ref;
        } else {
            $oInvoice = $oInvoiceModel->getById($this->iId);
        }
        return $oInvoice;
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an invoice
     * @return $this
     * @throws InvoiceException
     */
    public function delete()
    {
        if (!empty($this->iId)) {
            $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
            if (!$oInvoiceModel->delete($this->iId)) {
                throw new InvoiceException('Failed to delete invoice.');
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes an invoice off
     * @return $this
     * @throws InvoiceException
     */
    public function writeOff()
    {
        if (!empty($this->iId)) {
            $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
            if (!$oInvoiceModel->setWrittenOff($this->iId)) {
                throw new InvoiceException('Failed to write off invoice.');
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Charges an invoice
     *
     * @param ChargeRequest $oChargeRequest
     *
     * @return ChargeResponse
     * @throws InvoiceException
     */
    public function charge(ChargeRequest $oChargeRequest)
    {
        if (empty($this->iId)) {
            $oInvoice = $this->save();
        } else {
            $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
            $oInvoice      = $oInvoiceModel->getById($this->iId);
        }

        $oChargeRequest->setInvoice($this->iId);
        $oChargeRequest->setDescription('Payment for invoice ' . $this->sRef);
        return $oChargeRequest->execute(
            $oInvoice->totals->raw->grand,
            $oInvoice->currency->code
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item as an array
     * @return array
     */
    public function toArray()
    {
        return [
            'ref'             => $this->sRef,
            'state'           => $this->sState,
            'dated'           => $this->sDated,
            'terms'           => (int) $this->iTerms ?: null,
            'customer_id'     => (int) $this->iCustomerId ?: null,
            'email'           => $this->sEmail,
            'currency'        => $this->sCurrency,
            'additional_text' => $this->sAdditionalText,
            'callback_data'   => $this->mCallbackData,
            'items'           => (array) $this->aItems,
        ];
    }
}
