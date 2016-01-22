<?php

/**
 * Handle Payments
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
     * Waits for a payment to complete processing
     * @return void
     */
    public function processing()
    {
        dump('Processing Payment');
    }
}
