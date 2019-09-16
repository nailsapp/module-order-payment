<?php

/**
 * Sca Response Model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Factory;

/**
 * Class ScaResponse
 *
 * @package Nails\Invoice\Factory
 */
class ScaResponse extends ResponseBase
{
    /**
     * Whether the request is pending or not
     *
     * @var bool
     */
    protected $bIsPending = true;

    /**
     * Whether the request is complete or not
     *
     * @var bool
     */
    protected $bIsComplete = false;

    /**
     * Whether the request is a redirect or not
     *
     * @var bool
     */
    protected $bIsRedirect = false;

    /**
     * The redirect URL
     *
     * @var string
     */
    protected $sRedirectUrl = '';

    // --------------------------------------------------------------------------

    /**
     * Sets whether the resposne is pending
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setIsPending(bool $bValue): self
    {
        if (!$this->bIsLocked) {
            $this->bIsPending = $bValue;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the request is pending or not
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->bIsPending;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether the resposne is complete
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setIsComplete(bool $bValue): self
    {
        if (!$this->bIsLocked) {
            $this->bIsComplete = $bValue;
            $this->setIsPending(false);
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the request is complete or not
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->bIsComplete;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets whether the resposne is a redirect
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setIsRedirect(bool $bValue): self
    {
        if (!$this->bIsLocked) {
            $this->bIsRedirect = $bValue;
            $this->setIsPending(false);
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the request is a redirect or not
     *
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->bIsRedirect;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the redirect URL
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setRedirectUrl(string $sValue): self
    {
        if (!$this->bIsLocked) {
            $this->sRedirectUrl = $sValue;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the redirect URL
     *
     * @return string
     */
    public function getRedirectUrl(): string
    {
        return $this->sRedirectUrl;
    }
}
