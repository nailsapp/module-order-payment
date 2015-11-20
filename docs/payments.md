# Payments
> Documentation is a WIP.

Payments can be taken using any installed driver. A payment is always associated with an invoice. Payments trigger events.


## Drivers
Each payment provider is abstracted using a driver. The driver is responsible for interfacing with the payment gateway.

    @todo: describe how drivers work


## Events
The following events are called throughout the lifetime of a payment.

    @todo: describe how events work


### Event Handler
The payment event handler is responsible for acting upon successfull payments. Whenever a payment is received it will be called and passed any custom data which is stored with the invoice.

It is common for the app to override the event handler so that it can perform actions when a payment is received. This class should be auto-loadable and have the following name:

    \App\Invoice\PaymentEventHandler()

In addition, it should implement the `Nails\Invoice\Interfaces\PaymentEventHandlerInterface` interface.
