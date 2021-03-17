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

use Nails\Invoice\Exception\ResponseException;

/**
 * Class ScaResponse
 *
 * @package Nails\Invoice\Factory
 */
class ScaResponse extends ResponseBase
{
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

    /**
     * Whether the request is a redirect with POST data or not
     *
     * @var bool
     */
    protected $bIsRedirectWithPost = false;

    /**
     * The URL to POST to when redirecting with POST data
     *
     * @var string
     */
    protected $sRedirectWithPostUrl = '';

    /**
     * Key value POST data for redirect
     *
     * @var array
     */
    protected $aRedirectWithPostData = [];

    // --------------------------------------------------------------------------

    /**
     * Sets whether the response is a redirect
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setIsRedirect(bool $bValue): self
    {
        if ($this->isLocked()) {
            throw new ResponseException('Response is locked and cannot be modified');
        }

        $this->bIsRedirect = $bValue;
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
     * @param string $sValue The value to set
     *
     * @return $this
     */
    public function setRedirectUrl(string $sValue): self
    {
        if ($this->isLocked()) {
            throw new ResponseException('Response is locked and cannot be modified');
        }

        $this->sRedirectUrl = $sValue;
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
     * Sets whether the response is a redirect with POST data
     *
     * @param bool $bValue The value to set
     *
     * @return $this
     */
    public function setIsRedirectWithPost(bool $bValue): self
    {
        if ($this->isLocked()) {
            throw new ResponseException('Response is locked and cannot be modified');
        }

        $this->bIsRedirectWithPost = $bValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns whether the request is a redirect with POST data or not
     *
     * @return bool
     */
    public function isRedirectWithPost(): bool
    {
        return $this->bIsRedirectWithPost;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the redirect with POST URL
     *
     * @param string $sValue The value to set
     */
    public function setRedirectWithPostUrl(string $sValue): self
    {
        $this->sRedirectWithPostUrl = $sValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the redirect with POST URL
     *
     * @return string
     */
    public function getRedirectWithPostUrl(): string
    {
        return $this->sRedirectWithPostUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the redirect with POST data
     *
     * @param array $aValue The values to set
     */
    public function setRedirectWithPostData(array $aValue): self
    {
        $this->aRedirectWithPostData = $aValue;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the redirect with POST data
     *
     * @return array
     */
    public function getRedirectWithPostData(): array
    {
        return $this->aRedirectWithPostData;
    }
}
