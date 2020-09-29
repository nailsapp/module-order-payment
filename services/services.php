<?php

use Nails\Invoice\Factory\ChargeRequest;
use Nails\Invoice\Factory\ChargeResponse;
use Nails\Invoice\Factory\CompleteRequest;
use Nails\Invoice\Factory\CompleteResponse;
use Nails\Invoice\Factory\Invoice\CallbackData;
use Nails\Invoice\Factory\Invoice\PaymentData;
use Nails\Invoice\Factory\RefundRequest;
use Nails\Invoice\Factory\RefundResponse;
use Nails\Invoice\Factory\ScaRequest;
use Nails\Invoice\Factory\ScaResponse;
use Nails\Invoice\Resource\Customer;
use Nails\Invoice\Resource\Customer\Address;
use Nails\Invoice\Resource\Invoice\Email;
use Nails\Invoice\Resource\Invoice\Item;
use Nails\Invoice\Resource\Invoice\Item\Data\Callback;
use Nails\Invoice\Resource\Invoice\Item\Unit;
use Nails\Invoice\Resource\Invoice\Item\UnitCost;
use Nails\Invoice\Resource\Invoice\State;
use Nails\Invoice\Resource\Invoice\Totals;
use Nails\Invoice\Resource\Invoice\Totals\Formatted;
use Nails\Invoice\Resource\Invoice\Totals\Raw;
use Nails\Invoice\Resource\Payment\Urls;
use Nails\Invoice\Resource\Refund;
use Nails\Invoice\Resource\Refund\Amount;
use Nails\Invoice\Resource\Refund\Status;
use Nails\Invoice\Resource\Source;
use Nails\Invoice\Resource\Tax;
use Nails\Invoice\Service\Invoice\Skin;
use Nails\Invoice\Service\PaymentDriver;

return [
    'services'  => [
        'InvoiceSkin'   => function () {
            if (class_exists('\App\Invoice\Service\Invoice\Skin')) {
                return new \App\Invoice\Service\Invoice\Skin();
            } else {
                return new Skin();
            }
        },
        'PaymentDriver' => function () {
            if (class_exists('\App\Invoice\Service\PaymentDriver')) {
                return new \App\Invoice\Service\PaymentDriver();
            } else {
                return new PaymentDriver();
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
        'ChargeRequest'       => function () {
            if (class_exists('\App\Invoice\Factory\ChargeRequest')) {
                return new \App\Invoice\Factory\ChargeRequest();
            } else {
                return new ChargeRequest();
            }
        },
        'ChargeResponse'      => function () {
            if (class_exists('\App\Invoice\Factory\ChargeResponse')) {
                return new \App\Invoice\Factory\ChargeResponse();
            } else {
                return new ChargeResponse();
            }
        },
        'CompleteRequest'     => function () {
            if (class_exists('\App\Invoice\Factory\CompleteRequest')) {
                return new \App\Invoice\Factory\CompleteRequest();
            } else {
                return new CompleteRequest();
            }
        },
        'CompleteResponse'    => function () {
            if (class_exists('\App\Invoice\Factory\CompleteResponse')) {
                return new \App\Invoice\Factory\CompleteResponse();
            } else {
                return new CompleteResponse();
            }
        },
        'Invoice'             => function () {
            if (class_exists('\App\Invoice\Factory\Invoice')) {
                return new \App\Invoice\Factory\Invoice();
            } else {
                return new \Nails\Invoice\Factory\Invoice();
            }
        },
        'InvoiceCallbackData' => function () {
            if (class_exists('\App\Invoice\Factory\Invoice\CallbackData')) {
                return new \App\Invoice\Factory\Invoice\CallbackData();
            } else {
                return new CallbackData();
            }
        },
        'InvoiceItem'         => function () {
            if (class_exists('\App\Invoice\Factory\Invoice\Item')) {
                return new \App\Invoice\Factory\Invoice\Item();
            } else {
                return new \Nails\Invoice\Factory\Invoice\Item();
            }
        },
        'InvoicePaymentData'  => function () {
            if (class_exists('\App\Invoice\Factory\Invoice\PaymentData')) {
                return new \App\Invoice\Factory\Invoice\PaymentData();
            } else {
                return new PaymentData();
            }
        },
        'RefundRequest'       => function () {
            if (class_exists('\App\Invoice\Factory\RefundRequest')) {
                return new \App\Invoice\Factory\RefundRequest();
            } else {
                return new RefundRequest();
            }
        },
        'RefundResponse'      => function () {
            if (class_exists('\App\Invoice\Factory\RefundResponse')) {
                return new \App\Invoice\Factory\RefundResponse();
            } else {
                return new RefundResponse();
            }
        },
        'ScaRequest'          => function () {
            if (class_exists('\App\Invoice\Factory\ScaRequest')) {
                return new \App\Invoice\Factory\ScaRequest();
            } else {
                return new ScaRequest();
            }
        },
        'ScaResponse'         => function () {
            if (class_exists('\App\Invoice\Factory\ScaResponse')) {
                return new \App\Invoice\Factory\ScaResponse();
            } else {
                return new ScaResponse();
            }
        },
    ],
    'resources' => [
        'Customer'                   => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Customer')) {
                return new \App\Invoice\Resource\Customer($mObj);
            } else {
                return new Customer($mObj);
            }
        },
        'CustomerAddress'            => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Customer\Address')) {
                return new \App\Invoice\Resource\Customer\Address($mObj);
            } else {
                return new Address($mObj);
            }
        },
        'Invoice'                    => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice')) {
                return new \App\Invoice\Resource\Invoice($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice($mObj);
            }
        },
        'InvoiceDataCallback'        => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Data\Callback')) {
                return new \App\Invoice\Resource\Invoice\Data\Callback($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Data\Callback($mObj);
            }
        },
        'InvoiceDataPayment'         => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Data\Payment')) {
                return new \App\Invoice\Resource\Invoice\Data\Payment($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Data\Payment($mObj);
            }
        },
        'InvoiceEmail'               => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Email')) {
                return new \App\Invoice\Resource\Invoice\Email($mObj);
            } else {
                return new Email($mObj);
            }
        },
        'InvoiceItem'                => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item')) {
                return new \App\Invoice\Resource\Invoice\Item($mObj);
            } else {
                return new Item($mObj);
            }
        },
        'InvoiceItemDataCallback'    => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Data\Callback')) {
                return new \App\Invoice\Resource\Invoice\Item\Data\Callback($mObj);
            } else {
                return new Callback($mObj);
            }
        },
        'InvoiceItemTotals'          => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Totals')) {
                return new \App\Invoice\Resource\Invoice\Item\Totals($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Item\Totals($mObj);
            }
        },
        'InvoiceItemTotalsFormatted' => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Totals\Formatted')) {
                return new \App\Invoice\Resource\Invoice\Item\Totals\Formatted($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Item\Totals\Formatted($mObj);
            }
        },
        'InvoiceItemTotalsRaw'       => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Totals\Raw')) {
                return new \App\Invoice\Resource\Invoice\Item\Totals\Raw($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Item\Totals\Raw($mObj);
            }
        },
        'InvoiceItemUnit'            => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Unit')) {
                return new \App\Invoice\Resource\Invoice\Item\Unit($mObj);
            } else {
                return new Unit($mObj);
            }
        },
        'InvoiceItemUnitCost'        => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\UnitCost')) {
                return new \App\Invoice\Resource\Invoice\Item\UnitCost($mObj);
            } else {
                return new UnitCost($mObj);
            }
        },
        'InvoiceState'               => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\State')) {
                return new \App\Invoice\Resource\Invoice\State($mObj);
            } else {
                return new State($mObj);
            }
        },
        'InvoiceTotals'              => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals')) {
                return new \App\Invoice\Resource\Invoice\Totals($mObj);
            } else {
                return new Totals($mObj);
            }
        },
        'InvoiceTotalsFormatted'     => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals\Formatted')) {
                return new \App\Invoice\Resource\Invoice\Totals\Formatted($mObj);
            } else {
                return new Formatted($mObj);
            }
        },
        'InvoiceTotalsRaw'           => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals\Raw')) {
                return new \App\Invoice\Resource\Invoice\Totals\Raw($mObj);
            } else {
                return new Raw($mObj);
            }
        },
        'InvoiceUrls'                => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Invoice\Urls')) {
                return new \App\Invoice\Resource\Invoice\Urls($mObj);
            } else {
                return new \Nails\Invoice\Resource\Invoice\Urls($mObj);
            }
        },
        'Payment'                    => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Payment')) {
                return new \App\Invoice\Resource\Payment($mObj);
            } else {
                return new \Nails\Invoice\Resource\Payment($mObj);
            }
        },
        'PaymentAmount'              => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Payment\Amount')) {
                return new \App\Invoice\Resource\Payment\Amount($mObj);
            } else {
                return new \Nails\Invoice\Resource\Payment\Amount($mObj);
            }
        },
        'PaymentDataSca'             => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Payment\Data\Sca')) {
                return new \App\Invoice\Resource\Payment\Data\Sca($mObj);
            } else {
                return new \Nails\Invoice\Resource\Payment\Data\Sca($mObj);
            }
        },
        'PaymentStatus'              => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Payment\Status')) {
                return new \App\Invoice\Resource\Payment\Status($mObj);
            } else {
                return new \Nails\Invoice\Resource\Payment\Status($mObj);
            }
        },
        'PaymentUrls'                => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Payment\Urls')) {
                return new \App\Invoice\Resource\Payment\Urls($mObj);
            } else {
                return new Urls($mObj);
            }
        },
        'Refund'                     => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Refund')) {
                return new \App\Invoice\Resource\Refund($mObj);
            } else {
                return new Refund($mObj);
            }
        },
        'RefundAmount'               => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Refund\Amount')) {
                return new \App\Invoice\Resource\Refund\Amount($mObj);
            } else {
                return new Amount($mObj);
            }
        },
        'RefundStatus'               => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Refund\Status')) {
                return new \App\Invoice\Resource\Refund\Status($mObj);
            } else {
                return new Status($mObj);
            }
        },
        'Source'                     => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Source')) {
                return new \App\Invoice\Resource\Source($mObj);
            } else {
                return new Source($mObj);
            }
        },
        'Tax'                        => function ($mObj) {
            if (class_exists('\App\Invoice\Resource\Tax')) {
                return new \App\Invoice\Resource\Tax($mObj);
            } else {
                return new Tax($mObj);
            }
        },
    ],
];
