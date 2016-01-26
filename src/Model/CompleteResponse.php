<?php

/**
 * Complete Response model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Invoice\Model\ResponseBase;

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
