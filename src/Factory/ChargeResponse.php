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

use Stripe\Charge;

class ChargeResponse extends ResponseBase
{
    /**
     * whether the response requires SCA
     *
     * @var bool
     */
    protected $bIsSca = false;

    /**
     * Any data to save for an SCA flow
     *
     * @var array
     */
    protected $aScaData = null;

    /**
     * Whether the response requires a redirect
     *
     * @var bool
     */
    protected $bIsRedirect = false;

    /**
     * Any redirect POST Data
     *
     * @var array
     */
    protected $aRedirectPostData = null;

    /**
     * The URL for redirect flow
     *
     * @var string
     */
    protected $sRedirectUrl = '';

    /**
     * The URL for SCA flow
     *s
     *
     * @var string
     */
    protected $sScaUrl = '';

    /**
     * The URL for successful payment
     *
     * @var string
     */
    protected $sSuccessUrl = '';

    /**
     * The URL for failed payment
     *
     * @var string
     */
    protected $sFailUrl = '';

    /**
     * The URL for where to go after payment is completed
     *
     * @todo (Pablo - 2019-08-02) - Clarify what exactly this is
     *
     * @var string
     */
    protected $sContinueUrl = '';

    // --------------------------------------------------------------------------

    /**
     * Whether the response is a SCA redirect
     *
     * @return bool
     */
    public function isSca(): bool
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
    public function setIsSca(array $aData): ChargeResponse
    {
        if (!$this->bIsLocked) {
            $this->bIsSca   = true;
            $this->aScaData = $aData;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns SCA data
     *
     * @return array
     */
    public function getScaData(): ?array
    {
        return $this->aScaData;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the response is a redirect
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->bIsRedirect;
    }

    // --------------------------------------------------------------------------

    /**
     * Set whether the response is a redirect
     *
     * @param bool $bIsRedirect Whether the response is a redirect
     *
     * @return $this
     */
    public function setIsRedirect(bool $bIsRedirect): ChargeResponse
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
    public function setRedirectUrl(string $sRedirectUrl): ChargeResponse
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
    public function getRedirectUrl(): string
    {
        return $this->sRedirectUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the scaUrl value
     *
     * @param string $sScaUrl The Sca URL
     *
     * @return $this
     */
    public function setScaUrl(string $sScaUrl): ChargeResponse
    {
        if (!$this->bIsLocked) {
            $this->sScaUrl = $sScaUrl;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The URL to redirect to when satisfying an SCA response
     *
     * @return string
     */
    public function getScaUrl(): string
    {
        return $this->sScaUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the sSuccessUrl value
     *
     * @param string $sSuccessUrl The URL to go to on successful payment
     *
     * @return $this
     */
    public function setSuccessUrl(string $sSuccessUrl): ChargeResponse
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
    public function getSuccessUrl(): string
    {
        return $this->sSuccessUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * The URL to redirect to on failed payment
     *
     * @return string
     */
    public function getFailUrl(): string
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
    public function setFailUrl(string $sFailUrl): ChargeResponse
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
    public function setContinueUrl(string $sContinueUrl): ChargeResponse
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
    public function getContinueUrl(): string
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
    public function setRedirectPostData(array $aRedirectPostData): ChargeResponse
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
    public function getRedirectPostData(): ?array
    {
        return $this->aRedirectPostData;
    }
}
