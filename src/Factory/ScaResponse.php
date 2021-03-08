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
}
