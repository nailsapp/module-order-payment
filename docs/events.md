# Events
> Documentation is a WIP.

Various events will be called throughout the lifetime of invoices and payments.

## Listening for Events
To listen for events, override the `Nails\Invoice\PaymentEventHandler` class by creating a custom version at
`App\Invoice\PaymentEventHandler` and extending the parent. At minimum it should contain a single public method:
`trigger($sEvent, $mData)` which is designed to be used as a router to delegate events to more focused methods, or
classes.

This will be passed two parameters: `$sEvent`, a string which is the event code and `$mData` which will be data
relevant to the event being called.


## Events

### `invoice.created`
Called when a new invoice is created and is passed the complete invoice object.

### `invoice.updated`
Called when an existing invoice is updated and is passed the complete invoice object.

### `invoice.paid`
Called when the system detects that an invoice has been completely paid.

### `payment.created`
Called when a new payment is created and is passed the complete payment object.

### `payment.updated`
Called when an existing payment is updated and is passed the complete payment object.
