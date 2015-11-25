<?php

/**
 * View invoices
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

class Invoice extends NAILS_Controller
{
    /**
     * View a single invoice
     * @param  string $sInvoiceToken The invoice token
     * @return void
     */
    protected function viewInvoice($sInvoiceToken)
    {
        dump('View Invoice');
        dump($sInvoiceToken);
    }

    // --------------------------------------------------------------------------

    /**
     * Remaps all requests to the viewInvoice method unless the *real* method exists
     * @return void
     */
    public function _remap()
    {
        $sInvoiceToken = $this->uri->rsegment(2);
        if (method_exists($this, $sInvoiceToken)) {
            $this->{$sInvoiceToken}();
        } else {
            $this->viewInvoice($sInvoiceToken);
        }
    }
}
