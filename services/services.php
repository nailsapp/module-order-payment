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
        'Tax' => function () {
            if (class_exists('\App\Invoice\Model\Tax')) {
                return new \App\Invoice\Model\Tax();
            } else {
                return new \Nails\Invoice\Model\Tax();
            }
        },
        'Driver' => function () {
            if (class_exists('\App\Invoice\Model\Driver')) {
                return new \App\Invoice\Model\Driver();
            } else {
                return new \Nails\Invoice\Model\Driver();
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
        'InvoiceBuilder' => function () {
            if (class_exists('\App\Invoice\Model\InvoiceBuilder')) {
                return new \App\Invoice\InvoiceBuilder();
            } else {
                return new \Nails\Invoice\InvoiceBuilder();
            }
        }
    )
);
