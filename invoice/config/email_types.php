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

use Nails\Invoice\Constants;

$config['email_types'] = [
    (object) [
        'slug'            => 'send_invoice',
        'name'            => 'Invoice & Payments: Send Invoice',
        'description'     => 'Email sent when admin creates a new invoice',
        'can_unsubscribe' => false,
        'template_header' => '',
        'template_body'   => 'invoice/email/send_invoice',
        'template_footer' => '',
        'default_subject' => 'Invoice {{invoice.ref}}',
        'factory'         => Constants::MODULE_SLUG . '::EmailInvoiceSend',
    ],
    (object) [
        'slug'            => 'payment_complete_receipt',
        'name'            => 'Invoice & Payments: Payment Receipt (complete)',
        'description'     => 'Email sent when a payment is completed',
        'can_unsubscribe' => false,
        'template_header' => '',
        'template_body'   => 'invoice/email/payment_complete_receipt',
        'template_footer' => '',
        'default_subject' => 'Thank you for your payment - Invoice {{invoice.ref}}',
        'factory'         => Constants::MODULE_SLUG . '::EmailPaymentComplete',
    ],
    (object) [
        'slug'            => 'payment_processing_receipt',
        'name'            => 'Invoice & Payments: Payment Receipt (Processing)',
        'description'     => 'Email sent when a payment is processing',
        'can_unsubscribe' => false,
        'template_header' => '',
        'template_body'   => 'invoice/email/payment_processing_receipt',
        'template_footer' => '',
        'default_subject' => 'We are processing your payment - Invoice {{invoice.ref}}',
        'factory'         => Constants::MODULE_SLUG . '::EmailPaymentProcessing',
    ],
    (object) [
        'slug'            => 'refund_complete_receipt',
        'name'            => 'Invoice & Payments: Refund Receipt',
        'description'     => 'Email sent when a refund is sent',
        'can_unsubscribe' => false,
        'template_header' => '',
        'template_body'   => 'invoice/email/refund_complete_receipt',
        'template_footer' => '',
        'default_subject' => 'You have been refunded',
    ],
    (object) [
        'slug'            => 'refund_processing_receipt',
        'name'            => 'Invoice & Payments: Refund Receipt (Processing)',
        'description'     => 'Email sent when a refund is processing',
        'can_unsubscribe' => false,
        'template_header' => '',
        'template_body'   => 'invoice/email/refund_processing_receipt',
        'template_footer' => '',
        'default_subject' => 'We are processing your refund',
    ],
];
