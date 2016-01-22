<?php

return array(
    'models' => array(
        'Invoice' => function () {
            if (class_exists('\App\Invoice\Model\Invoice')) {
                return new \App\Invoice\Model\Invoice();
            } else {
                return new \Nails\Invoice\Model\Invoice();
            }
        },
        'InvoiceEmail' => function () {
            if (class_exists('\App\Invoice\Model\InvoiceEmail')) {
                return new \App\Invoice\Model\InvoiceEmail();
            } else {
                return new \Nails\Invoice\Model\InvoiceEmail();
            }
        },
        'InvoiceItem' => function () {
            if (class_exists('\App\Invoice\Model\InvoiceItem')) {
                return new \App\Invoice\Model\InvoiceItem();
            } else {
                return new \Nails\Invoice\Model\InvoiceItem();
            }
        },
        'Payment' => function () {
            if (class_exists('\App\Invoice\Model\Payment')) {
                return new \App\Invoice\Model\Payment();
            } else {
                return new \Nails\Invoice\Model\Payment();
            }
        },
        'PaymentDriver' => function () {
            if (class_exists('\App\Invoice\Model\PaymentDriver')) {
                return new \App\Invoice\Model\PaymentDriver();
            } else {
                return new \Nails\Invoice\Model\PaymentDriver();
            }
        },
        'Tax' => function () {
            if (class_exists('\App\Invoice\Model\Tax')) {
                return new \App\Invoice\Model\Tax();
            } else {
                return new \Nails\Invoice\Model\Tax();
            }
        },
        'PaymentEventHandler' => function () {
            if (class_exists('\App\Invoice\PaymentEventHandler')) {
                return new \App\Invoice\PaymentEventHandler();
            } else {
                return new \Nails\Invoice\PaymentEventHandler();
            }
        }
    ),
    'factories' => array(
        'ChargeRequest' => function () {
            if (class_exists('\App\Invoice\Model\ChargeRequest')) {
                return new \App\Invoice\Model\ChargeRequest();
            } else {
                return new \Nails\Invoice\Model\ChargeRequest();
            }
        },
        'ChargeResponse' => function () {
            if (class_exists('\App\Invoice\Model\ChargeResponse')) {
                return new \App\Invoice\Model\ChargeResponse();
            } else {
                return new \Nails\Invoice\Model\ChargeResponse();
            }
        },
        'CompleteRequest' => function () {
            if (class_exists('\App\Invoice\Model\CompleteRequest')) {
                return new \App\Invoice\Model\CompleteRequest();
            } else {
                return new \Nails\Invoice\Model\CompleteRequest();
            }
        },
        'CompleteResponse' => function () {
            if (class_exists('\App\Invoice\Model\CompleteResponse')) {
                return new \App\Invoice\Model\CompleteResponse();
            } else {
                return new \Nails\Invoice\Model\CompleteResponse();
            }
        },
        'RefundRequest' => function () {
            if (class_exists('\App\Invoice\Model\RefundRequest')) {
                return new \App\Invoice\Model\RefundRequest();
            } else {
                return new \Nails\Invoice\Model\RefundRequest();
            }
        },
        'RefundResponse' => function () {
            if (class_exists('\App\Invoice\Model\RefundResponse')) {
                return new \App\Invoice\Model\RefundResponse();
            } else {
                return new \Nails\Invoice\Model\RefundResponse();
            }
        }
    )
);
