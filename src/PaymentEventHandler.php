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

    //  Refund events
    const EVENT_PAYMENT_REFUND_CREATED = 'payment.refund.created';
    const EVENT_PAYMENT_REFUND_UPDATED = 'payment.refund.updated';

    //  Invoice Events
    const EVENT_INVOICE_CREATED         = 'invoice.created';
    const EVENT_INVOICE_UPDATED         = 'invoice.updated';
    const EVENT_INVOICE_PAID            = 'invoice.paid';
    const EVENT_INVOICE_PAID_PARTIAL    = 'invoice.paid.partial';
    const EVENT_INVOICE_PAID_PROCESSING = 'invoice.paid.processing';

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
