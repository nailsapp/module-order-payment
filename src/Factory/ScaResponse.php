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
     * Whether the request is failed or not
     *
     * @var bool
     */
    protected $bIsFail = false;

    /**
     * The error message
     *
     * @var string
     */
    protected $sErrorMessage = '';

    /**
     * The error code
     *
     * @var string
     */
    protected $sErrorCode = '';

    /**
     * The redirect URL
     *
     * @var string
     */
    protected $sRedirectUrl = '';

    /**
     * The transaction ID
     *
     * @var string
     */
    protected $sTransactionId = '';

    /**
     * The transaction fee
     *
     * @var int
     */
    protected $iTransactionFee = 0;

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
     * Sets whether the resposne is failed
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setIsFail(bool $bValue): self
    {
        if (!$this->bIsLocked) {
            $this->bIsFail = $bValue;
            $this->setIsPending(false);
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the request is failed or not
     *
     * @return bool
     */
    public function isFail(): bool
    {
        return $this->bIsFail;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the error message
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setErrorMessage(string $sValue): self
    {
        if (!$this->bIsLocked) {
            $this->sErrorMessage = $sValue;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns any error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->sErrorMessage;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the error code
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setErrorCode(string $sValue): self
    {
        if (!$this->bIsLocked) {
            $this->sErrorCode = $sValue;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns any error code
     *
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->sErrorCode;
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

    // --------------------------------------------------------------------------

    /**
     * Sets the transaction ID
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setTransactionId(string $sValue): self
    {
        if (!$this->bIsLocked) {
            $this->sTransactionId = $sValue;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the transaction ID
     *
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->sTransactionId;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the transaction fee
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setTransactionFee(int $iValue): self
    {
        if (!$this->bIsLocked) {
            $this->iTransactionFee = $iValue;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the transaction fee
     *
     * @return int
     */
    public function getTransactionFee(): int
    {
        return $this->iTransactionFee;
    }
}
