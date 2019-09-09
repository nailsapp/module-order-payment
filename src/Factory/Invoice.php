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

use Nails\Common\Resource\Date;
use Nails\Common\Resource\DateTime;
use Nails\Common\Traits\ErrorHandling;
use Nails\Currency\Resource\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Factory\Invoice\Item;
use Nails\Invoice\Resource\Customer;

/**
 * Class Invoice
 *
 * @package Nails\Invoice\Factory
 */
class Invoice
{
    use ErrorHandling;

    // --------------------------------------------------------------------------

    /**
     * The invoice's ID
     *
     * @var int
     */
    protected $iId = null;

    /**
     * The invoice's ref
     *
     * @var string
     */
    protected $sRef = '';

    /**
     * The invoice's state
     *
     * @var string
     */
    protected $sState = \Nails\Invoice\Model\Invoice::STATE_OPEN;

    /**
     * The invoice's dated date
     *
     * @var string
     */
    protected $sDated = '';

    /**
     * The invoice's terms
     *
     * @var int
     */
    protected $iTerms = 0;

    /**
     * The invoice's customer ID
     *
     * @var int
     */
    protected $iCustomerId = null;

    /**
     * The invoice's email
     *
     * @var string
     */
    protected $sEmail = '';

    /**
     * The invoice's currency
     *
     * @var string
     */
    protected $sCurrency = '';

    /**
     * The invoice's additional text
     *
     * @var string
     */
    protected $sAdditionalText = '';

    /**
     * The invoice's callback data
     *
     * @var mixed
     */
    protected $mCallbackData = null;

    /**
     * The invoice's items
     *
     * @var array
     */
    protected $aItems = [];

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's ID
     *
     * @param int $iId The invoice's ID
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setId(int $iId): Invoice
    {
        $this->ensureNotSaved();
        $this->iId = $iId;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's ID
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->iId;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's ref
     *
     * @param string $sRef The invoice's ref
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setRef(string $sRef): Invoice
    {
        $this->ensureNotSaved();
        $this->sRef = $sRef;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's ref
     *
     * @return string
     */
    public function getRef(): string
    {
        return $this->sRef;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's state
     *
     * @param string $sState The invoice's state
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setState(string $sState): Invoice
    {
        $this->ensureNotSaved();
        $this->sState = $sState;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's state
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->sState;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's dated date
     *
     * @param \DateTime|Date|string $mDate The invoice's dated date
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setDated($mDate): Invoice
    {
        $this->ensureNotSaved();

        if ($mDate instanceof \DateTime) {
            $this->sDated = $mDate->format('Y-m-d H:i:s');
        } elseif ($mDate instanceof Date) {
            $this->sDated = (string) $mDate;
        } elseif (is_string($mDate)) {
            $this->sDated = $mDate;
        } else {
            throw new InvoiceException(
                'Invalid data type (' . gettype($mDate) . ') passed to ' . __METHOD__
            );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's dated date
     *
     * @return string
     */
    public function getDated()
    {
        return $this->sDated;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's terms
     *
     * @param int $iTerms The invoice's terms
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setTerms(int $iTerms): Invoice
    {
        $this->ensureNotSaved();
        $this->iTerms = $iTerms;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's terms
     *
     * @return mixed
     */
    public function getTerms()
    {
        return $this->iTerms;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's Customer ID
     *
     * @param int|Customer $mCustomer The invoice's Customer ID
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setCustomerId($mCustomer): Invoice
    {
        $this->ensureNotSaved();

        if ($mCustomer instanceof Customer) {
            $this->iCustomerId = $mCustomer->id;
        } elseif (is_int($mCustomer)) {
            $this->iCustomerId = $mCustomer;
        } else {
            throw new InvoiceException(
                'Invalid data type (' . gettype($mCustomer) . ') passed to ' . __METHOD__
            );
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's Customer ID
     *
     * @return int
     */
    public function getCustomerId(): ?int
    {
        return $this->iCustomerId;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's email
     *
     * @param string $sEmail The invoice's email
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setEmail(string $sEmail): Invoice
    {
        $this->ensureNotSaved();
        $this->sEmail = $sEmail;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's email
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->sEmail;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's currency
     *
     * @param $mCurrency The invoice's currency
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setCurrency($mCurrency): Invoice
    {
        $this->ensureNotSaved();

        if ($mCurrency instanceof Currency) {
            $this->sCurrency = $mCurrency->code;
        } elseif (is_string($mCurrency)) {
            $this->sCurrency = $mCurrency;
        } else {
            throw new InvoiceException(
                'Invalid data type (' . gettype($mCurrency) . ') passed to ' . __METHOD__
            );
        }

        $this->sCurrency = $mCurrency;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's currency
     *
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->sCurrency;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's additional text
     *
     * @param string $sAdditionalText The invoice's additional text
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setAdditionalText(string $sAdditionalText): Invoice
    {
        $this->ensureNotSaved();
        $this->sAdditionalText = $sAdditionalText;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's additional text
     *
     * @return string
     */
    public function getAdditionalText(): string
    {
        return $this->sAdditionalText;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's callback data
     *
     * @param $mCallbackData The invoice's callback data
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setCallbackData($mCallbackData): Invoice
    {
        $this->ensureNotSaved();
        $this->mCallbackData = $mCallbackData;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's callback data
     *
     * @return mixed
     */
    public function getCallbackData()
    {
        return $this->mCallbackData;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the invoice's items
     *
     * @param $aItems The invoice's items
     *
     * @return $this
     * @throws InvoiceException
     */
    public function setItems($aItems): Invoice
    {
        $this->ensureNotSaved();
        $this->aItems = $aItems;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice's items
     *
     * @return array
     */
    public function getItems(): array
    {
        return $this->aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Throws an exception if the item's ID has been set
     *
     * @throws InvoiceException
     */
    protected function ensureNotSaved()
    {
        if (!empty($this->iId)) {
            throw new InvoiceException('Invoice has been saved and cannot be modified.');
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
     * @throws InvoiceException
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
            throw new InvoiceException('Call to undefined method ' . get_called_class() . '::' . $sMethod . '()');
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
     *
     * @return \stdClass
     * @throws InvoiceException
     */
    public function save()
    {
        /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
        $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
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
     *
     * @return $this
     * @throws InvoiceException
     */
    public function delete()
    {
        if (!empty($this->iId)) {
            /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
            $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
            if (!$oInvoiceModel->delete($this->iId)) {
                throw new InvoiceException('Failed to delete invoice.');
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Writes an invoice off
     *
     * @return $this
     * @throws InvoiceException
     */
    public function writeOff()
    {
        if (!empty($this->iId)) {
            /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
            $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
            if (!$oInvoiceModel->setWrittenOff($this->iId)) {
                throw new InvoiceException('Failed to write off invoice.');
            }
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Cancels an invoice
     *
     * @return $this
     * @throws InvoiceException
     */
    public function cancel()
    {
        if (!empty($this->iId)) {
            /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
            $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
            if (!$oInvoiceModel->setCancelled($this->iId)) {
                throw new InvoiceException('Failed to cancel invoice.');
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
            /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
            $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
            $oInvoice      = $oInvoiceModel->getById($this->iId);
        }

        return $oChargeRequest
            ->setInvoice($this->iId)
            ->setDescription('Payment for invoice ' . $this->sRef)
            ->execute(
                $oInvoice->totals->raw->grand,
                $oInvoice->currency->code
            );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item as an array
     *
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
