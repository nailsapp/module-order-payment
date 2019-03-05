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
            if (class_exists('\App\Invoice\Model\Invoice\Email')) {
                return new \App\Invoice\Model\Invoice\Email();
            } else {
                return new \Nails\Invoice\Model\Invoice\Email();
            }
        },
        'InvoiceItem' => function () {
            if (class_exists('\App\Invoice\Model\Invoice\Item')) {
                return new \App\Invoice\Model\Invoice\Item();
            } else {
                return new \Nails\Invoice\Model\Invoice\Item();
            }
        },
        'InvoiceSkin' => function () {
            if (class_exists('\App\Invoice\Model\Invoice\Skin')) {
                return new \App\Invoice\Model\Invoice\Skin();
            } else {
                return new \Nails\Invoice\Model\Invoice\Skin();
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
