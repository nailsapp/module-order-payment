<?php

/**
 * The class provides a summary of the events fired by this module
 *
 * @package     Nails
 * @subpackage  module-common
 * @category    Events
 * @author      Nails Dev Team
 */

namespace Nails\Invoice;

use Nails\Common\Events\Base;

class Events extends Base
{
    /**
     * Fired when an invoice is created
     *
     * @param \stdClass $oInvoice The newly created invoice
     */
    const INVOICE_CREATED = 'INVOICE:CREATED';

    /**
     * Fired when an invoice is updated
     *
     * @param \stdClass $oInvoice The invoice which was updated
     */
    const INVOICE_UPDATED = 'INVOICE:UPDATED';

    /**
     * Fired when an invoice is marked as fully paid
     *
     * @param \stdClass $oInvoice The invoice which was marked as fully paid
     */
    const INVOICE_PAID = 'INVOICE:PAID';

    /**
     * Fired when an invoice is marked as partially paid
     *
     * @param \stdClass $oInvoice The invoice which was marked as partially paid
     */
    const INVOICE_PAID_PARTIAL = 'INVOICE:PAID:PARTIAL';

    /**
     * Fired when an invoice is marked as paid but with payments processing
     *
     * @param \stdClass $oInvoice The invoice which was marked as paid, but with payments processing
     */
    const INVOICE_PAID_PROCESSING = 'INVOICE:PAID:PROCESSING';

    /**
     * Fired when an invoice is marked as written off
     *
     * @param \stdClass $oInvoice The invoice which was written off
     */
    const INVOICE_WRITTEN_OFF = 'INVOICE:WRITTEN_OFF';

    // --------------------------------------------------------------------------

    /**
     * Fired when a payment is created
     *
     * @param \stdClass $oPayment The newly created payment
     */
    const PAYMENT_CREATED = 'PAYMENT:CREATED';

    /**
     * Fired when a payment is updated
     *
     * @param \stdClass $oPayment The payment which was updated
     */
    const PAYMENT_UPDATED = 'PAYMENT:UPDATED';

    // --------------------------------------------------------------------------

    /**
     * Fired when a refund is created
     *
     * @param \stdClass $oRefund The newly created refund
     */
    const REFUND_CREATED = 'REFUND:CREATED';

    /**
     * Fired when a refund is updated
     *
     * @param \stdClass $oRefund The refund which was updated
     */
    const REFUND_UPDATED = 'REFUND:UPDATED';
}
