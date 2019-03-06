# Invoice Module
The Invoice & Payments module provides Nails applications with the ability to manage customers, raise invoices, and take payment using a variety of payment gateways, all configured through the `nails/module-admin` interface.

The module has been designed to be generic and not have many opinions on how it should be used; fundamentally it provides the following key concepts: [Customers](customers.md), [Invoices](invoices.md), [Payments](payments.md), and [Refunds](refunds.md) and it is up to the app to determine how to implement it.




## Contents

- [Customers](customers.md)
- [Invoices](invoices.md)
  - [Generating invoices](invoices.md#generating-invoices)
    - [Line Items](invoices.md#line-items)
    - [Callback Data](invoices.md#callback-data)
  - [Creating Charges](invoices.md#creating-charges)
  - [Creating Refunds](invoices.md#creating-refunds)
- [Payments](payments.md)
- [Refunds](refunds.md)
- [Taxes](tax.md)
- [Events](events.md)

