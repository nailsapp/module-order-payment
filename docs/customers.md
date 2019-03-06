# Invoice Module › Customers

Customers are a representation of an individual or an organisation who can recieve an invoice. At minimum, a customer will have a name and an email address. All invoices raised must be attributed to a customer.

> It is important to note that a customer does not have a 1:1 relationship with users. This is a deliberate action so that it is possible to send invoices to people who are not registered on the site. If you require this functionality, then consider lsitening to the `nails/module-auth` module's `USER_CREATED` event and create a customer on the fly for each user which is added.

Customers can be created in the admin interface, or by using the `Customer` model.

```php
$oModel      = Factor::model('Customer', 'nails/module-invoice');
$iCustomerId = $oModel->create([
    'first_name'   => 'Claire',
    'last_name'    => 'Green',
    'organisation' => 'Claire Green Ltd.',
    'email'        => 'claire.green@example.com'
]);
```

The `organisation` field or the `first_name` and `last_name` field must be supplied. If an `organisation` is supplied then that value will be mapped to the object's `label` property; if not, then `first_name` and `last_name` will ve mapped instead.

