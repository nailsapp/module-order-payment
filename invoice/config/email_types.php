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
        'slug'             => 'payment_complete_receipt',
        'name'             => 'Invoice & Payments: Payment Receipt (complete)',
        'description'      => 'Email sent when a payment is completed',
        'isUnsubscribable' => false,
        'template_header'  => '',
        'template_body'    => 'invoice/email/payment_complete_receipt',
        'template_footer'  => '',
        'default_subject'  => 'Thank you for your payment - Invoice {{payment.invoice.ref}}'
    ),
    (object) array(
        'slug'             => 'payment_processing_receipt',
        'name'             => 'Invoice & Payments: Payment Receipt (Processing)',
        'description'      => 'Email sent when a payment is processing',
        'isUnsubscribable' => false,
        'template_header'  => '',
        'template_body'    => 'invoice/email/payment_processing_receipt',
        'template_footer'  => '',
        'default_subject'  => 'We are processing your payment - Invoice {{payment.invoice.ref}}'
    ),
    (object) array(
        'slug'             => 'refund_complete_receipt',
        'name'             => 'Invoice & Payments: Refund Receipt',
        'description'      => 'Email sent when a refund is sent',
        'isUnsubscribable' => false,
        'template_header'  => '',
        'template_body'    => 'invoice/email/refund_complete_receipt',
        'template_footer'  => '',
        'default_subject'  => 'You have been refunded'
    ),
    (object) array(
        'slug'             => 'refund_processing_receipt',
        'name'             => 'Invoice & Payments: Refund Receipt (Processing)',
        'description'      => 'Email sent when a refund is processing',
        'isUnsubscribable' => false,
        'template_header'  => '',
        'template_body'    => 'invoice/email/refund_processing_receipt',
        'template_footer'  => '',
        'default_subject'  => 'We are processing your refund'
    )
);
