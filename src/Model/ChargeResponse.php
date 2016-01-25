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

use Nails\Invoice\Model\ResponseBase;
use Nails\Invoice\Exception\ChargeResponseException;

class ChargeResponse extends ResponseBase
{
    //  Redirect variables
    protected $bIsRedirect;
    protected $sRedirectUrl;
    protected $aRedirectPostData;

    //  Urls
    protected $sSuccessUrl;
    protected $sFailUrl;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->bIsRedirect = false;
    }

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
     * Set whether the response is a redirect
     * @param boolean $bIsRedirect Whether the response is a redirect
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
     * @param string $sRedirectUrl The Redirect URL
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
     * @return string
     */
    public function getRedirectUrl() {
        return $this->sRedirectUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the sSuccessUrl value
     * @param string $sSuccessUrl The URL to go to on successful payment
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
     * The URL to redirect to on failed payment
     * @return string
     */
    public function getFailUrl() {
        return $this->sFailUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the sFailUrl value
     * @param string $sFailUrl The URL to go to on failed payment
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
     * The URL to redirect to on successsful payment
     * @return string
     */
    public function getSuccessUrl() {
        return $this->sSuccessUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set any data which should be POST'ed to the endpoint
     * @param array $aRedirectPostData The data to post
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
     * @return string
     */
    public function getRedirectPostData() {
        return $this->aRedirectPostData;
    }
}
