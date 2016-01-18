<?php

/**
 * Tax rate model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Invoice\Exception\DriverException;

class Card
{
    protected $sToken;
    protected $sName;
    protected $sNumber;
    protected $sExpMonth;
    protected $sExpYear;
    protected $sCvc;

    // --------------------------------------------------------------------------

    /**
     * Set the card's token
     * @param string $sToken The card's token
     */
    public function setToken($sToken)
    {
        $this->sToken = $sToken;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's token
     * @return string The card's token
     */
    public function getToken()
    {
        return $this->sToken;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the cardholder's name
     * @param string $sName The cardholder's name
     */
    public function setName($sName)
    {
        $this->sName = $sName;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the cardholder's Name
     * @return string The cardholder's name
     */
    public function getName()
    {
        return $this->sName;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's number
     * @param string $sNumber The card's number
     */
    public function setNumber($sNumber)
    {
        //  Validate

        $this->sNumber = $sNumber;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's number
     * @return string The card's number
     */
    public function getNumber()
    {
        return $this->sNumber;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry month
     * @param string $sExpiry The card's expiry month
     */
    public function setExpMonth($sExpMonth)
    {
        //  Validate
        $this->sExpMonth = $sExpMonth;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry month
     * @return string The card's expiry month
     */
    public function getExpMonth()
    {
        return $this->sExpMonth;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's expiry year
     * @param string $sExpiry The card's expiry year
     */
    public function setExpYear($sExpYear)
    {
        //  Validate
        $this->sExpYear = $sExpYear;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry year
     * @return string The card's expiry year
     */
    public function getExpYear()
    {
        return $this->sExpYear;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the card's CVC number
     * @param string $sCvc The card's cvc number
     */
    public function setCvc($sCvc)
    {
        //  Validate
        $this->sCvc = $sCvc;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's CVC number
     * @return string The card's CVC number
     */
    public function getCvc()
    {
        return $this->sCvc;
    }

    // --------------------------------------------------------------------------

    public function toArray()
    {
        return array(
            'token'     => $this->getToken(),
            'name'      => $this->getName(),
            'number'    => $this->getNumber(),
            'exp_month' => $this->getExpMonth(),
            'exp_year'  => $this->getExpYear(),
            'cvc'       => $this->getCvc()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to charge the card
     * @param  integer   $iInvoiceId The invoice this charge should be attributed to
     * @param  integer   $iAmount    The amount to charge the card
     * @param  string    $sCurrency  The currency in which to charge
     * @param  string    $sDriver    The payment driver to use
     * @return \stdClass
     */
    public function charge($iInvoiceId, $iAmount, $sCurrency, $sDriver)
    {
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $aDrivers            = $oPaymentDriverModel->getEnabled();

        //  Validate the driver
        $oDriver = false;
        foreach ($aDrivers as $oDriverConfig) {
            if ($oDriverConfig->slug == $sDriver) {
                $oDriver = $oPaymentDriverModel->getInstance($oDriverConfig->slug);
                break;
            }
        }

        if (empty($oDriver)) {
            throw new DriverException('Invalid Payment Driver', 1);
        }

        //  @todo: validate amount
        //  @todo: validate currency

        // --------------------------------------------------------------------------

        //  Create a charge against the invoice
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $iPaymentId    = $oPaymentModel->create(
            array(
                'driver'        => $sDriver,
                'invoice_id'    => $iInvoiceId,
                'currency'      => $sCurrency,
                'currency_base' => $sCurrency,
                'amount'        => $iAmount,
                'amount_base'   => $iAmount
            )
        );

        $oResponse = $oDriver->charge(
            $this->toArray(),
            $iAmount,
            $sCurrency
        );

        //  @todo: validate response

        //  Update the payment
        $oPaymentModel->update(
            $iPaymentId,
            array(
                'status'   => $oResponse->getStatus(),
                'txn_id'   => $oResponse->getTxnId(),
                'fee'      => $oResponse->getFee(),
                'fee_base' => $oResponse->getFee()
            )
        );

        //  Lock the response so it cannot be altered
        $oResponse->lock();

        return $oResponse;
    }
}
