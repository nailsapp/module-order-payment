<?php

/**
 * Complete Response
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Factory;

class CompleteResponse extends ResponseBase
{
    //  Urls
    protected $sContinueUrl;

    // --------------------------------------------------------------------------

    /**
     * Set the URL to go to when a payment is completed
     * @param string $sContinueUrl the URL to go to when payment is completed
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
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->sContinueUrl;
    }
}
