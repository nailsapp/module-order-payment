<?php

/**
 * Make a Payment
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

class Payment extends NAILS_Controller
{
    /**
     * Take payment for a particular invoice
     * @param  string $sInvoiceToken The invoice token
     * @return void
     */
    protected function doPayment($sInvoiceToken)
    {
        dump('Do Payment');
        dump($sInvoiceToken);
    }

    // --------------------------------------------------------------------------

    /**
     * Remaps all requests to the doPayment method unless the *real* method exists
     * @return void
     */
    public function _remap()
    {
        $sInvoiceToken = $this->uri->rsegment(2);
        if (method_exists($this, $sInvoiceToken)) {
            $this->{$sInvoiceToken}();
        } else {
            $this->doPayment($sInvoiceToken);
        }
    }
}