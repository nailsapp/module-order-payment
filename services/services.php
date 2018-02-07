<?php

return [
    'models'    => [
        'Customer' => function () {
            if (class_exists('\App\Invoice\Model\Customer')) {
                return new \App\Invoice\Model\Customer();
            } else {
                return new \Nails\Invoice\Model\Customer();
            }
        },
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
        'InvoiceSkin' => function () {
            if (class_exists('\App\Invoice\Model\InvoiceSkin')) {
                return new \App\Invoice\Model\InvoiceSkin();
            } else {
                return new \Nails\Invoice\Model\InvoiceSkin();
            }
        },
        'Payment' => function () {
            if (class_exists('\App\Invoice\Model\Payment')) {
                return new \App\Invoice\Model\Payment();
            } else {
                return new \Nails\Invoice\Model\Payment();
            }
        },
        'Refund' => function () {
            if (class_exists('\App\Invoice\Model\Refund')) {
                return new \App\Invoice\Model\Refund();
            } else {
                return new \Nails\Invoice\Model\Refund();
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
        },
    ],
    'factories' => [
        'ChargeRequest' => function () {
            if (class_exists('\App\Invoice\Factory\ChargeRequest')) {
                return new \App\Invoice\Factory\ChargeRequest();
            } else {
                return new \Nails\Invoice\Factory\ChargeRequest();
            }
        },
        'ChargeResponse' => function () {
            if (class_exists('\App\Invoice\Factory\ChargeResponse')) {
                return new \App\Invoice\Factory\ChargeResponse();
            } else {
                return new \Nails\Invoice\Factory\ChargeResponse();
            }
        },
        'CompleteRequest' => function () {
            if (class_exists('\App\Invoice\Factory\CompleteRequest')) {
                return new \App\Invoice\Factory\CompleteRequest();
            } else {
                return new \Nails\Invoice\Factory\CompleteRequest();
            }
        },
        'CompleteResponse' => function () {
            if (class_exists('\App\Invoice\Factory\CompleteResponse')) {
                return new \App\Invoice\Factory\CompleteResponse();
            } else {
                return new \Nails\Invoice\Factory\CompleteResponse();
            }
        },
        'Invoice' => function () {
            if (class_exists('\App\Invoice\Factory\Invoice')) {
                return new \App\Invoice\Factory\Invoice();
            } else {
                return new \Nails\Invoice\Factory\Invoice();
            }
        },
        'InvoiceItem' => function () {
            if (class_exists('\App\Invoice\Factory\Invoice\Item')) {
                return new \App\Invoice\Factory\Invoice\Item();
            } else {
                return new \Nails\Invoice\Factory\Invoice\Item();
            }
        },
        'RefundRequest' => function () {
            if (class_exists('\App\Invoice\Factory\RefundRequest')) {
                return new \App\Invoice\Factory\RefundRequest();
            } else {
                return new \Nails\Invoice\Factory\RefundRequest();
            }
        },
        'RefundResponse' => function () {
            if (class_exists('\App\Invoice\Factory\RefundResponse')) {
                return new \App\Invoice\Factory\RefundResponse();
            } else {
                return new \Nails\Invoice\Factory\RefundResponse();
            }
        },
    ],
];
