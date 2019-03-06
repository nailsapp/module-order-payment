# Invoice Module › Events

This module exposes the following events through the [Nails Events Service](https://github.com/nails/common/blob/master/docs/intro/events.md) in the `nails/module-invoice` namespace.

> Remember you can see all events available to the application using `nails events`


- [Invoices](#invoices)
    - [Nails\Invoice\Events::INVOICE_CREATED](#invoice-created)
    - [Nails\Invoice\Events::INVOICE_UPDATED](#invoice-updated)
    - [Nails\Invoice\Events::INVOICE_PAID](#invoice-paid)
    - [Nails\Invoice\Events::INVOICE\_PAID\_PARTIAL](#invoice-paid-partial)
    - [Nails\Invoice\Events::INVOICE\_PAID\_PROCESSING](#invoice-paid-processing)
    - [Nails\Invoice\Events::INVOICE\_WRITTEN\_OFF](#invoice-written-off)
- [Payments](#payments)
    - [Nails\Invoice\Events::PAYMENT_CREATED](#payment-created)
    - [Nails\Invoice\Events::PAYMENT_UPDATED](#payment-updated)
- [Refunds](#refunds)
    - [Nails\Invoice\Events::REFUND_CREATED](#refund-created)
    - [Nails\Invoice\Events::REFUND_UPDATED](#refund-updated)



## Invoices

<a name="invoice-created"></a>
### `Nails\Invoice\Events::INVOICE_CREATED`

Fired when an invoice is created.

**Receives:**

> ```
> \stdClass $oInvoice The newly created Invoice
> ```


<a name="invoice-updated"></a>
### `Nails\Invoice\Events::INVOICE_UPDATED`

Fired when an invoice is updated

**Receives:**

> ```
> \stdClass $oInvoice The invoice which was updated
> ```


<a name="invoice-paid"></a>
### `Nails\Invoice\Events::INVOICE_PAID`

Fired when an invoice is marked as fully paid

**Receives:**

> ```
> \stdClass $oInvoice The invoice which was marked as fully paid
> ```


<a name="invoice-paid-partial"></a>
### `Nails\Invoice\Events::INVOICE_PAID_PARTIAL`

Fired when an invoice is marked as partially paid

**Receives:**

> ```
> \stdClass $oInvoice The invoice which was marked as partially paid
> ```


<a name="invoice-paid-processing"></a>
### `Nails\Invoice\Events::INVOICE_PAID_PROCESSING`

 Fired when an invoice is marked as paid but with payments processing

**Receives:**

> ```
> \stdClass $oInvoice The invoice which was marked as paid, but with payments processing
> ```


<a name="invoice-written-off"></a>
### `Nails\Invoice\Events::INVOICE_WRITTEN_OFF`

Fired when an invoice is marked as written off

**Receives:**

> ```
> \stdClass $oInvoice The invoice which was written off
> ```




## Payments

<a name="payment-created"></a>
### `Nails\Invoice\Events::PAYMENT_CREATED`

Fired when a payment is created.

**Receives:**

> ```
> \stdClass $oPayment The newly created payment
> ```


<a name="payment-updated"></a>
### `Nails\Invoice\Events::PAYMENT_UPDATED`

Fired when a payment is updated.

**Receives:**

> ```
> \stdClass $oPayment The payment which was updated
> ```





## Refunds

<a name="refund-created"></a>
### `Nails\Invoice\Events::REFUND_CREATED`

Fired when a refund is created.

**Receives:**

> ```
> \stdClass $oRefund The newly created refund
> ```


<a name="refund-updated"></a>
### `Nails\Invoice\Events::REFUND_UPDATED`

Fired when a refund is updated.

**Receives:**

> ```
> \stdClass $oRefund The refund which was updated
> ```
