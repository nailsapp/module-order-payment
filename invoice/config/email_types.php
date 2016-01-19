<?php

/**
 * This config file defines email types for this module.
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Config
 * @author      Nails Dev Team
 * @link
 */

$config['email_types'] = array(
    (object) array(
        'slug'             => 'send_invoice',
        'name'             => 'Invoice & Payments: Send Invoice',
        'description'      => 'Email sent when admin creates a new invoice',
        'isUnsubscribable' => false,
        'template_header'  => '',
        'template_body'    => 'invoice/email/send_invoice',
        'template_footer'  => '',
        'default_subject'  => 'Invoice {{invoice.ref}}'
    ),
    (object) array(
        'slug'             => 'invoice_paid_receipt',
        'name'             => 'Invoice & Payments: Invoice Paid Receipt',
        'description'      => 'Email sent with invoice attached',
        'isUnsubscribable' => false,
        'template_header'  => '',
        'template_body'    => 'invoice/email/invoice_paid_receipt',
        'template_footer'  => '',
        'default_subject'  => 'Thank you for your payment - Invoice {{invoice.ref}}'
    )
);
