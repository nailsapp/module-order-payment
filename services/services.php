<?php

return [
    'services'  => [
        'InvoiceSkin'   => function () {
            if (class_exists('\App\Invoice\Service\Invoice\Skin')) {
                return new \App\Invoice\Service\Invoice\Skin();
            } else {
                return new \Nails\Invoice\Service\Invoice\Skin();
            }
        },
        'PaymentDriver' => function () {
            if (class_exists('\App\Invoice\Service\PaymentDriver')) {
                return new \App\Invoice\Service\PaymentDriver();
            } else {
                return new \Nails\Invoice\Service\PaymentDriver();
            }
        },
    ],
    'models'    => [
        'Customer'     => function () {
            if (class_exists('\App\Invoice\Model\Customer')) {
                return new \App\Invoice\Model\Customer();
            } else {
                return new \Nails\Invoice\Model\Customer();
            }
        },
        'Invoice'      => function () {
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
        'InvoiceItem'  => function () {
            if (class_exists('\App\Invoice\Model\Invoice\Item')) {
                return new \App\Invoice\Model\Invoice\Item();
            } else {
                return new \Nails\Invoice\Model\Invoice\Item();
            }
        },
        'Payment'      => function () {
            if (class_exists('\App\Invoice\Model\Payment')) {
                return new \App\Invoice\Model\Payment();
            } else {
                return new \Nails\Invoice\Model\Payment();
            }
        },
        'Refund'       => function () {
            if (class_exists('\App\Invoice\Model\Refund')) {
                return new \App\Invoice\Model\Refund();
            } else {
                return new \Nails\Invoice\Model\Refund();
            }
        },
        'Source'       => function () {
            if (class_exists('\App\Invoice\Model\Source')) {
                return new \App\Invoice\Model\Source();
            } else {
                return new \Nails\Invoice\Model\Source();
            }
        },
        'Tax'          => function () {
            if (class_exists('\App\Invoice\Model\Tax')) {
                return new \App\Invoice\Model\Tax();
            } else {
                return new \Nails\Invoice\Model\Tax();
            }
        },
    ],
    'factories' => [
        'ChargeRequest'    => function () {
            if (class_exists('\App\Invoice\Factory\ChargeRequest')) {
                return new \App\Invoice\Factory\ChargeRequest();
            } else {
                return new \Nails\Invoice\Factory\ChargeRequest();
            }
        },
        'ChargeResponse'   => function () {
            if (class_exists('\App\Invoice\Factory\ChargeResponse')) {
                return new \App\Invoice\Factory\ChargeResponse();
            } else {
                return new \Nails\Invoice\Factory\ChargeResponse();
            }
        },
        'CompleteRequest'  => function () {
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
        'Invoice'          => function () {
            if (class_exists('\App\Invoice\Factory\Invoice')) {
                return new \App\Invoice\Factory\Invoice();
            } else {
                return new \Nails\Invoice\Factory\Invoice();
            }
        },
        'InvoiceItem'      => function () {
            if (class_exists('\App\Invoice\Factory\Invoice\Item')) {
                return new \App\Invoice\Factory\Invoice\Item();
            } else {
                return new \Nails\Invoice\Factory\Invoice\Item();
            }
        },
        'RefundRequest'    => function () {
            if (class_exists('\App\Invoice\Factory\RefundRequest')) {
                return new \App\Invoice\Factory\RefundRequest();
            } else {
                return new \Nails\Invoice\Factory\RefundRequest();
            }
        },
        'RefundResponse'   => function () {
            if (class_exists('\App\Invoice\Factory\RefundResponse')) {
                return new \App\Invoice\Factory\RefundResponse();
            } else {
                return new \Nails\Invoice\Factory\RefundResponse();
            }
        },
        'ScaRequest'       => function () {
            if (class_exists('\App\Invoice\Factory\ScaRequest')) {
                return new \App\Invoice\Factory\ScaRequest();
            } else {
                return new \Nails\Invoice\Factory\ScaRequest();
            }
        },
        'ScaResponse'      => function () {
            if (class_exists('\App\Invoice\Factory\ScaResponse')) {
                return new \App\Invoice\Factory\ScaResponse();
            } else {
                return new \Nails\Invoice\Factory\ScaResponse();
            }
        },
    ],
    'resources' => [
        'Customer'               => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Customer')) {
                return new \App\Invoice\Resource\Customer($mObj);
            } else {
                return new \Nails\Invoice\Resource\Customer($mObj);
            }
        },
        'Invoice'                => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice')) {
                return new \App\Invoice\Resource\Invoice($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice($mObj);
            }
        },
        'InvoiceDataCallback'    => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Data\Callback')) {
                return new \App\Invoice\Resource\Invoice\Data\Callback($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Data\Callback($mObj);
            }
        },
        'InvoiceDataPayment'     => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Data\Payment')) {
                return new \App\Invoice\Resource\Invoice\Data\Payment($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Data\Payment($mObj);
            }
        },
        'InvoiceEmail'           => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Email')) {
                return new \App\Invoice\Resource\Invoice\Email($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Email($mObj);
            }
        },
        'InvoiceItem'            => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item')) {
                return new \App\Invoice\Resource\Invoice\Item($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Item($mObj);
            }
        },
        'InvoiceState'           => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\State')) {
                return new \App\Invoice\Resource\Invoice\State($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\State($mObj);
            }
        },
        'InvoiceTotals'          => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals')) {
                return new \App\Invoice\Resource\Invoice\Totals($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Totals($mObj);
            }
        },
        'InvoiceTotalsFormatted' => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals\Formatted')) {
                return new \App\Invoice\Resource\Invoice\Totals\Formatted($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Totals\Formatted($mObj);
            }
        },
        'InvoiceTotalsRaw'       => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals\Raw')) {
                return new \App\Invoice\Resource\Invoice\Totals\Raw($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Totals\Raw($mObj);
            }
        },
        'InvoiceUrls'            => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Urls')) {
                return new \App\Invoice\Resource\Invoice\Urls($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Urls($mObj);
            }
        },
        'Payment'                => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Payment')) {
                return new \App\Invoice\Resource\Payment($mObj);
            } else {
                return new \Nails\Invoice\Resource\Payment($mObj);
            }
        },
        'Refund'                 => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Refund')) {
                return new \App\Invoice\Resource\Refund($mObj);
            } else {
                return new \Nails\Invoice\Resource\Refund($mObj);
            }
        },
        'Source'                 => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Source')) {
                return new \App\Invoice\Resource\Source($mObj);
            } else {
                return new \Nails\Invoice\Resource\Source($mObj);
            }
        },
        'Tax'                    => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Tax')) {
                return new \App\Invoice\Resource\Tax($mObj);
            } else {
                return new \Nails\Invoice\Resource\Tax($mObj);
            }
        },
    ],
];
