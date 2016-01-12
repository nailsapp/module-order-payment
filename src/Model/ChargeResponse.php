<?php

/**
 * Charge Response Model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

class ChargeResponse
{
    //  Locked
    protected $bIsLocked;

    //  Redirect variables
    protected $bIsRedirect;
    protected $sRedirectUrl;

    //  Successful charge variables
    protected $sTxnId;

    // --------------------------------------------------------------------------

    /**
     * Whether the response is a redirect
     * @return boolean
     */
    public function isRedirect() {
        return $this->bIsRedirect;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the redirect URL
     * @param boolean $bIsRedirect Whether the response is a redirect
     */
    public function setIsRedirect($bIsRedirect)
    {
        if (!$this->bIsLocked) {
            $this->bIsRedirect = (bool) $bIsRedirect;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the redirectUrl value
     * @param boolean $bIsRedirect The Redirect URL
     */
    public function setRedirectUrl($sRedirectUrl)
    {
        if (!$this->bIsLocked) {
            $this->sRedirectUrl = $sRedirectUrl;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The URL to redirect to
     * @return string
     */
    public function getRedirectUrl() {
        return $this->sRedirectUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the transaction ID
     * @param boolean $sTxnId The Redirect URL
     */
    public function setTxnId($sTxnId)
    {
        if (!$this->bIsLocked) {
            $this->sTxnId = $sTxnId;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The transaction ID
     * @return string
     */
    public function getTxnId() {
        return $this->sTxnId;
    }

    // --------------------------------------------------------------------------

    /**
     * Prevent the object from being altered
     * @return object
     */
    public function lock()
    {
        $this->bIsLocked = true;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the response is locked
     * @return boolean
     */
    public function isLocked() {
        return $this->bIsLocked;
    }
}
