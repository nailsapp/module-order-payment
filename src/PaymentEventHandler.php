<?php

/**
 * Payment event Hander
 *
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice;

class PaymentEventHandler
{
    //  Payment Events
    const EVENT_PAYMENT_CREATED = 'payment.created';
    const EVENT_PAYMENT_UPDATED = 'payment.updated';

    //  Invoice Events
    const EVENT_INVOICE_CREATED = 'invoice.created';
    const EVENT_INVOICE_UPDATED = 'invoice.updated';
    const EVENT_INVOICE_PAID    = 'invoice.paid';

    // --------------------------------------------------------------------------

    /**
     * Listens for events
     * @param  string $sEvent The event being called
     * @param  mixed  $mData  Any data to pass to the handler
     * @return void
     */
    public function trigger($sEvent, $mData)
    {
    }
}
