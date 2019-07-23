<?php

/**
 * Charge Response Model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Factory;

class ChargeResponse extends ResponseBase
{
    //  Redirect variables
    protected $bIsSca;
    protected $aScaData;
    protected $bIsRedirect;
    protected $sRedirectUrl;
    protected $aRedirectPostData;

    //  Urls
    protected $sSuccessUrl;
    protected $sFailUrl;
    protected $sContinueUrl;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->bIsSca      = false;
        $this->bIsRedirect = false;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the response is a SCA redirect
     *
     * @return boolean
     */
    public function isSca()
    {
        return $this->bIsSca;
    }

    // --------------------------------------------------------------------------

    /**
     * Set whether the response is a SCA redirect
     *
     * @param array $aData Any data to save for the SCA flow
     *
     * @return $this
     */
    public function setIsSca(array $aData)
    {
        if (!$this->bIsLocked) {
            $this->bIsSca   = true;
            $this->aScaData = $aData;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    public function getScaData()
    {
        return $this->aScaData;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the response is a redirect
     *
     * @return boolean
     */
    public function isRedirect()
    {
        return $this->bIsRedirect;
    }

    // --------------------------------------------------------------------------

    /**
     * Set whether the response is a redirect
     *
     * @param boolean $bIsRedirect Whether the response is a redirect
     *
     * @return $this
     */
    protected function setIsRedirect($bIsRedirect)
    {
        if (!$this->bIsLocked) {
            $this->bIsRedirect = (bool) $bIsRedirect;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the redirectUrl value
     *
     * @param string $sRedirectUrl The Redirect URL
     *
     * @return $this
     */
    public function setRedirectUrl($sRedirectUrl)
    {
        if (!$this->bIsLocked) {
            $this->sRedirectUrl = $sRedirectUrl;
            $this->setIsRedirect(!empty($sRedirectUrl));
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The URL to redirect to
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->sRedirectUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the sSuccessUrl value
     *
     * @param string $sSuccessUrl The URL to go to on successful payment
     *
     * @return $this
     */
    public function setSuccessUrl($sSuccessUrl)
    {
        if (!$this->bIsLocked) {
            $this->sSuccessUrl = $sSuccessUrl;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The URL to redirect to on successful payment
     *
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->sSuccessUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * The URL to redirect to on failed payment
     *
     * @return string
     */
    public function getFailUrl()
    {
        return $this->sFailUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the sFailUrl value
     *
     * @param string $sFailUrl The URL to go to on failed payment
     *
     * @return $this
     */
    public function setFailUrl($sFailUrl)
    {
        if (!$this->bIsLocked) {
            $this->sFailUrl = $sFailUrl;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the URL to go to when a payment is completed
     *
     * @param string $sContinueUrl the URL to go to when payment is completed
     *
     * @return $this
     */
    public function setContinueUrl($sContinueUrl)
    {
        if (!$this->bIsLocked) {
            $this->sContinueUrl = $sContinueUrl;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the URL to go to when a payment is completed
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->sContinueUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set any data which should be POST'ed to the endpoint
     *
     * @param array $aRedirectPostData The data to post
     *
     * @return $this
     */
    public function setRedirectPostData($aRedirectPostData)
    {
        if (!$this->bIsLocked) {
            $this->aRedirectPostData = $aRedirectPostData;
            $this->setIsRedirect(!empty($aRedirectPostData));
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Any data which should be POST'ed to the endpoint
     *
     * @return string
     */
    public function getRedirectPostData()
    {
        return $this->aRedirectPostData;
    }
}
