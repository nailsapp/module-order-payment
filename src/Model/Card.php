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
    protected $sExpiry;
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
     * Set the card's expiry date
     * @param string $sExpiry The card's expiry date
     */
    public function setExpiry($sExpiry)
    {
        //  Validate
        $this->sExpiry = $sExpiry;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the card's expiry date'
     * @return string The card's expiry date'
     */
    public function getExpiry()
    {
        return $this->sExpiry;
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
            'token'  => $this->getToken(),
            'name'   => $this->getName(),
            'number' => $this->getNumber(),
            'expiry' => $this->getExpiry(),
            'cvc'    => $this->getCvc()
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Attempts to charge the card
     * @param  integer   $iAmount   The amount to charge the card
     * @param  string    $sCurrency The currency in which to charge
     * @param  string    $sDriver   The payment driver to use
     * @return \stdClass
     */
    public function charge($iAmount, $sCurrency, $sDriver)
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

        $oResponse = $oDriver->charge(
            $this->toArray(),
            $iAmount,
            $sCurrency
        );

        //  @todo: validate response

        //  Lock the response so it cannot be altered
        $oResponse->lock();

        return $oResponse;
    }
}
