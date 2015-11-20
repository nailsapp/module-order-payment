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
        'Invoice' => function () {
            if (class_exists('\App\Invoice\Object\Invoice')) {
                return new \App\Invoice\Object\Invoice();
            } else {
                return new \Nails\Invoice\Object\Invoice();
            }
        },
        'InvoiceItem' => function () {
            if (class_exists('\App\Invoice\Object\InvoiceItem')) {
                return new \App\Invoice\Object\InvoiceItem();
            } else {
                return new \Nails\Invoice\Object\InvoiceItem();
            }
        },
        'Payment' => function () {
            if (class_exists('\App\Invoice\Object\Payment')) {
                return new \App\Invoice\Object\Payment();
            } else {
                return new \Nails\Invoice\Object\Payment();
            }
        }
    )
);
