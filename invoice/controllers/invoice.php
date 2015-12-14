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

use Nails\Factory;

class Invoice extends NAILS_Controller
{
    /**
     * View a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function view($oInvoice)
    {
        dump('View Invoice');
        dump($oInvoice);
    }

    // --------------------------------------------------------------------------

    /**
     * Download a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function download($oInvoice)
    {
        dump('Download Invoice');
        dump($oInvoice);
    }

    // --------------------------------------------------------------------------

    /**
     * Pay a single invoice
     * @param  object $oInvoice The invoice object
     * @return void
     */
    protected function pay($oInvoice)
    {
        dump('Pay Invoice');
        dump($oInvoice);
    }

    // --------------------------------------------------------------------------

    /**
     * Remaps all requests to the viewInvoice method unless the *real* method exists
     * @return void
     */
    public function _remap()
    {
        $sInvoiceRef   = $this->uri->rsegment(2);
        $sInvoiceToken = $this->uri->rsegment(3);
        $sMethod       = $this->uri->rsegment(4) ?: 'view';

        //  @todo verify invoice and token
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oInvoice      = $oInvoiceModel->getByRef($sInvoiceRef);

        if (empty($oInvoice) || $sInvoiceToken !== $oInvoice->token || !method_exists($this, $sMethod)) {
            show_404();
        }

        $this->{$sMethod}($oInvoice);
    }
}
