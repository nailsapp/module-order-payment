# Invoice Module › Invoices

- [Generating invoices](#generating-invoices)
  - [Line Items](#line-items)
  - [Callback Data](#callback-data)
- [Creating Charges](#creating-charges)
- [Creating Refunds](#creating-refunds)



## Generating invoces

Generating invoices should be done using the `Invoice` Factory object provided by `nails/module-invoice`. It provides a convinient API for creating, manipulating, and ultimately [charging](#creating-charges) an invoice.

For those who want some copy/pase code to dive right in – a simple example of how to generate an invoice:

```php
//	Generate a new invoice object
$oInvoice = Factory::factory('Invoice', 'nails/module-invoice');
$oInvoice
    //	Define the customer to which to attribute the invoice
    ->setCustomerId(activeUser('customer_id'))
    //	Define the currency we're charging in, all values will be in this currency
    ->setCurrency('GBP')
    //	Add a line item
    ->addItem(
        Factory::factory('InvoiceItem', 'nails/module-invoice')
		    //	Give the item a label
            ->setLabel('Treasure Island')
		    //	A body can be used to give additional detail about what has been purchased
    		->setBody('A story of adventure by Robert Louis Stephenson')
		    //	Define the unit this item is sold in
		    ->setUnit(Nails\Invoice\Model\Invoice\Item::UNIT_NONE)
		    // Define the cost of each unit; use the currency's smallest unit
            ->setUnitCost(1000)
		    //	Define how many units are being bought
            ->setQuantity(1)
    		//	Define which tax band to apply to this item
            ->setTaxId($iVatBandId)
   			//	Define any callback data
            ->setCallbackData([
                'foo' => 'bar'
            ])
    );

/**
 * Here we can call the Invoice object's save() method if we wish to commit this 
 * invoice to the database. This method returns an instance of an invoice as if
 * it were retrived from the Invoice model.
 *
 * Note this is NOT the same object we have been working with here.
 *
 * Alternatively, we might create a ChargeRequest object and attempt a charge
 * against the user's payment source.
 **/
```



### Line Items

Like on a normal, paper, invoice a line item represents a single type of product, or unit. They encapsulate the item's description, unit type, unit cost, tax band, as well as the number of units being bought. A unit's cost can also be negative to represent a discount.

If applicable, a tax band can be added to a line item. The total tax collected will be calculated automatically.

```php
Factory::factory('InvoiceItem', 'nails/module-invoice')
    //	Give the item a label
    ->setLabel('Treasure Island')
    //	A body can be used to give additional detail about what has been purchased
    ->setBody('A story of adventure by Robert Louis Stephenson')
    //	Define the unit this item is sold in
    ->setUnit(Nails\Invoice\Model\Invoice\Item::UNIT_NONE)
    // Define the cost of each unit; use the currency's smallest unit
    ->setUnitCost(1000)
    //	Define how many units are being bought
    ->setQuantity(1)
    //	Define which tax band to apply to this item
    ->setTaxId($iVatBandId)
    //	Define any callback data
    ->setCallbackData([
        'foo' => 'bar'
    ]);
```





### Callback Data

Callback data can be applied to both invoices and individual line items. This is an opportunity to attach arbritray data to an object for retrieval later (for exampe, in one of the module's various [events](events.md)).

#### Invoices

Specify invoice callback data when creating the invoice object:

```
$oInvoice = Factory::factory('Invoice', 'nails/module-invoice');
$oInvoice->setCallbackData(['foo' => 'bar']);
```

This data will be avaialble on the invoice's `callback_data` property when retrieved via the Invoice model. 

> **Note**: Internally it is stored as a JSON object, and will be decoded on retrieval - so bear in mind how data might differ from how it is entered (e.g. an associative array will become a `\stdClass`).



#### Line items

Specify line item callback data when creating the line item object:

```
$oLineItem = Factory::factory('InvoiceItem', 'nails/module-invoice')
$oLineItem->setCallbackData(['foo' => 'bar']);
```

This data will be avaialble on the line items `callback_data` property when retrieved via the `Invoice` model, or the `InvoiceItem` model. 

> **Note**: Internally it is stored as a JSON object, and will be decoded on retrieval - so bear in mind how data might differ from how it is entered (e.g. an associative array will become a `\stdClass`).





## Creating Charges

Charges are neatly wrapped up into `ChargeRequest` objects. These objects represent a single payment attempt and provide a unified API for dealing with the response from the payment driver.

Once again, a learn-by-example code snippet:

```php
//	Using $oInvoice from the above example

//	Create a new ChargeRequest object
$oChargeRequest = Factory::factory('ChargeRequest', 'nails/module-invoice');
$oChargeRequest
    //	Define which payment driver we'd like to use for this payment
    ->setDriver('nails/driver-invoice-stripe')
    //	Set any custom data we (or the driver) might require
    ->setCustomData('source_id', $oPaymentSource->stripe_token);

/**
 * Pass the ChargeRequest object into the Invoice object's charge() method to
 * execute. This will create a new payment record and return a ChargeResponse
 * object, the payment record will be udpated automatcially with any success or
 * failure messages, as well as transaction references.
 */
$oChargeResponse = $oInvoice->charge($oChargeRequest);

//	Handle the result of the payment
if ($oChargeResponse->isPending()) {
    
    //	The charge was successful, but is pending completion
    //	Example: Payment requires verification by a third party
    $this->handlePendingPayment($oInvoice, $oChargeResponse);
    
} elseif ($oChargeResponse->isProcessing()) {
    
    //	The charge was successful, but is proccessing
    //	Example: Payment is a direct debit which will be complete in the future
    $this->handleProcessingPayment($oInvoice, $oChargeResponse);
    
} elseif ($oChargeResponse->isFailed()) {
    
    //	Payment was a failure, write the invoice off and show a user-friendly error
    $oInvoice->writeOff();
    throw new Nails\Invoice\Exception\PaymentException(
        'Payment failed. ' . $oChargeResponse->getError()->user
    );

} elseif ($oChargeResponse->isRedirect()) {
    
	/**
	 * Payment requires a redirect flow and the request was made with the
	 * setAutoRedirect(false) configuration
	 **/
    $this->handleRedirectPayment($oInvoice, $oChargeResponse);
    
} elseif ($oChargeResponse->isComplete()) {
    
    //	Payment was a success!
    $this->handleCompletePayment($oInvoice, $oChargeResponse);
    
} else {
    //	And for good measure, handle undefined behaviour
    throw new Nails\Invoice\Exception\PaymentException(
        'Unhandled charge response behaviour'
    );
}
```



> @todo - Write up some of the more advanced ways of handling charges, specifically the redirect routes



## Creating Refunds

> @todo - write up how to issue refunds