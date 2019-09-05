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
