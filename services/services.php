<?php

use Nails\Invoice\Factory;
use Nails\Invoice\Model;
use Nails\Invoice\Resource;
use Nails\Invoice\Service;

return [
    'services'  => [
        'InvoiceSkin'   => function (): Service\Invoice\Skin {
            if (class_exists('\App\Invoice\Service\Invoice\Skin')) {
                return new \App\Invoice\Service\Invoice\Skin();
            } else {
                return new Service\Invoice\Skin();
            }
        },
        'PaymentDriver' => function (): Service\PaymentDriver {
            if (class_exists('\App\Invoice\Service\PaymentDriver')) {
                return new \App\Invoice\Service\PaymentDriver();
            } else {
                return new Service\PaymentDriver();
            }
        },
    ],
    'models'    => [
        'Customer'     => function (): Model\Customer {
            if (class_exists('\App\Invoice\Model\Customer')) {
                return new \App\Invoice\Model\Customer();
            } else {
                return new Model\Customer();
            }
        },
        'Invoice'      => function (): Model\Invoice {
            if (class_exists('\App\Invoice\Model\Invoice')) {
                return new \App\Invoice\Model\Invoice();
            } else {
                return new Model\Invoice();
            }
        },
        'InvoiceEmail' => function (): Model\Invoice\Email {
            if (class_exists('\App\Invoice\Model\Invoice\Email')) {
                return new \App\Invoice\Model\Invoice\Email();
            } else {
                return new Model\Invoice\Email();
            }
        },
        'InvoiceItem'  => function (): Model\Invoice\Item {
            if (class_exists('\App\Invoice\Model\Invoice\Item')) {
                return new \App\Invoice\Model\Invoice\Item();
            } else {
                return new Model\Invoice\Item();
            }
        },
        'Payment'      => function (): Model\Payment {
            if (class_exists('\App\Invoice\Model\Payment')) {
                return new \App\Invoice\Model\Payment();
            } else {
                return new Model\Payment();
            }
        },
        'Refund'       => function (): Model\Refund {
            if (class_exists('\App\Invoice\Model\Refund')) {
                return new \App\Invoice\Model\Refund();
            } else {
                return new Model\Refund();
            }
        },
        'Source'       => function (): Model\Source {
            if (class_exists('\App\Invoice\Model\Source')) {
                return new \App\Invoice\Model\Source();
            } else {
                return new Model\Source();
            }
        },
        'Tax'          => function (): Model\Tax {
            if (class_exists('\App\Invoice\Model\Tax')) {
                return new \App\Invoice\Model\Tax();
            } else {
                return new Model\Tax();
            }
        },
    ],
    'factories' => [
        'ChargeRequest'          => function (): Factory\ChargeRequest {
            if (class_exists('\App\Invoice\Factory\ChargeRequest')) {
                return new \App\Invoice\Factory\ChargeRequest();
            } else {
                return new Factory\ChargeRequest();
            }
        },
        'ChargeResponse'         => function (): Factory\ChargeResponse {
            if (class_exists('\App\Invoice\Factory\ChargeResponse')) {
                return new \App\Invoice\Factory\ChargeResponse();
            } else {
                return new Factory\ChargeResponse();
            }
        },
        'CompleteRequest'        => function (): Factory\CompleteRequest {
            if (class_exists('\App\Invoice\Factory\CompleteRequest')) {
                return new \App\Invoice\Factory\CompleteRequest();
            } else {
                return new Factory\CompleteRequest();
            }
        },
        'CompleteResponse'       => function (): Factory\CompleteResponse {
            if (class_exists('\App\Invoice\Factory\CompleteResponse')) {
                return new \App\Invoice\Factory\CompleteResponse();
            } else {
                return new Factory\CompleteResponse();
            }
        },
        'EmailInvoiceSend'       => function (): Factory\Email\Invoice\Send {
            if (class_exists('\App\Invoice\Factory\Email\Invoice\Send')) {
                return new \App\Invoice\Factory\Email\Invoice\Send();
            } else {
                return new Factory\Email\Invoice\Send();
            }
        },
        'EmailPaymentComplete'   => function (): Factory\Email\Payment\Complete {
            if (class_exists('\App\Invoice\Factory\Email\Payment\Complete')) {
                return new \App\Invoice\Factory\Email\Payment\Complete();
            } else {
                return new Factory\Email\Payment\Complete();
            }
        },
        'EmailPaymentProcessing' => function (): Factory\Email\Payment\Processing {
            if (class_exists('\App\Invoice\Factory\Email\Payment\Processing')) {
                return new \App\Invoice\Factory\Email\Payment\Processing();
            } else {
                return new Factory\Email\Payment\Processing();
            }
        },
        'EmailRefundComplete'    => function (): Factory\Email\Refund\Complete {
            if (class_exists('\App\Invoice\Factory\Email\Refund\Complete')) {
                return new \App\Invoice\Factory\Email\Refund\Complete();
            } else {
                return new Factory\Email\Refund\Complete();
            }
        },
        'EmailRefundProcessing'  => function (): Factory\Email\Refund\Processing {
            if (class_exists('\App\Invoice\Factory\Email\Refund\Processing')) {
                return new \App\Invoice\Factory\Email\Refund\Processing();
            } else {
                return new Factory\Email\Refund\Processing();
            }
        },
        'Invoice'                => function (): Factory\Invoice {
            if (class_exists('\App\Invoice\Factory\Invoice')) {
                return new \App\Invoice\Factory\Invoice();
            } else {
                return new Factory\Invoice();
            }
        },
        'InvoiceCallbackData'    => function (): Factory\Invoice\CallbackData {
            if (class_exists('\App\Invoice\Factory\Invoice\CallbackData')) {
                return new \App\Invoice\Factory\Invoice\CallbackData();
            } else {
                return new Factory\Invoice\CallbackData();
            }
        },
        'InvoiceItem'            => function (): Factory\Invoice\Item {
            if (class_exists('\App\Invoice\Factory\Invoice\Item')) {
                return new \App\Invoice\Factory\Invoice\Item();
            } else {
                return new Factory\Invoice\Item();
            }
        },
        'InvoicePaymentData'     => function (): Factory\Invoice\PaymentData {
            if (class_exists('\App\Invoice\Factory\Invoice\PaymentData')) {
                return new \App\Invoice\Factory\Invoice\PaymentData();
            } else {
                return new Factory\Invoice\PaymentData();
            }
        },
        'RefundRequest'          => function (): Factory\RefundRequest {
            if (class_exists('\App\Invoice\Factory\RefundRequest')) {
                return new \App\Invoice\Factory\RefundRequest();
            } else {
                return new Factory\RefundRequest();
            }
        },
        'RefundResponse'         => function (): Factory\RefundResponse {
            if (class_exists('\App\Invoice\Factory\RefundResponse')) {
                return new \App\Invoice\Factory\RefundResponse();
            } else {
                return new Factory\RefundResponse();
            }
        },
        'ScaRequest'             => function (): Factory\ScaRequest {
            if (class_exists('\App\Invoice\Factory\ScaRequest')) {
                return new \App\Invoice\Factory\ScaRequest();
            } else {
                return new Factory\ScaRequest();
            }
        },
        'ScaResponse'            => function (): Factory\ScaResponse {
            if (class_exists('\App\Invoice\Factory\ScaResponse')) {
                return new \App\Invoice\Factory\ScaResponse();
            } else {
                return new Factory\ScaResponse();
            }
        },
    ],
    'resources' => [
        'Customer'                   => function ($mObj): Resource\Customer {
            if (class_exists('\App\Invoice\Resource\Customer')) {
                return new \App\Invoice\Resource\Customer($mObj);
            } else {
                return new Resource\Customer($mObj);
            }
        },
        'Invoice'                    => function ($mObj): Resource\Invoice {
            if (class_exists('\App\Invoice\Resource\Invoice')) {
                return new \App\Invoice\Resource\Invoice($mObj);
            } else {
                return new Resource\Invoice($mObj);
            }
        },
        'InvoiceDataCallback'        => function ($mObj): Resource\Invoice\Data\Callback {
            if (class_exists('\App\Invoice\Resource\Invoice\Data\Callback')) {
                return new \App\Invoice\Resource\Invoice\Data\Callback($mObj);
            } else {
                return new Resource\Invoice\Data\Callback($mObj);
            }
        },
        'InvoiceDataPayment'         => function ($mObj): Resource\Invoice\Data\Payment {
            if (class_exists('\App\Invoice\Resource\Invoice\Data\Payment')) {
                return new \App\Invoice\Resource\Invoice\Data\Payment($mObj);
            } else {
                return new Resource\Invoice\Data\Payment($mObj);
            }
        },
        'InvoiceEmail'               => function ($mObj): Resource\Invoice\Email {
            if (class_exists('\App\Invoice\Resource\Invoice\Email')) {
                return new \App\Invoice\Resource\Invoice\Email($mObj);
            } else {
                return new Resource\Invoice\Email($mObj);
            }
        },
        'InvoiceItem'                => function ($mObj): Resource\Invoice\Item {
            if (class_exists('\App\Invoice\Resource\Invoice\Item')) {
                return new \App\Invoice\Resource\Invoice\Item($mObj);
            } else {
                return new Resource\Invoice\Item($mObj);
            }
        },
        'InvoiceItemDataCallback'    => function ($mObj): Resource\Invoice\Item\Data\Callback {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Data\Callback')) {
                return new \App\Invoice\Resource\Invoice\Item\Data\Callback($mObj);
            } else {
                return new Resource\Invoice\Item\Data\Callback($mObj);
            }
        },
        'InvoiceItemTotals'          => function ($mObj): Resource\Invoice\Item\Totals {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Totals')) {
                return new \App\Invoice\Resource\Invoice\Item\Totals($mObj);
            } else {
                return new Resource\Invoice\Item\Totals($mObj);
            }
        },
        'InvoiceItemTotalsFormatted' => function ($mObj): Resource\Invoice\Item\Totals\Formatted {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Totals\Formatted')) {
                return new \App\Invoice\Resource\Invoice\Item\Totals\Formatted($mObj);
            } else {
                return new Resource\Invoice\Item\Totals\Formatted($mObj);
            }
        },
        'InvoiceItemTotalsRaw'       => function ($mObj): Resource\Invoice\Item\Totals\Raw {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Totals\Raw')) {
                return new \App\Invoice\Resource\Invoice\Item\Totals\Raw($mObj);
            } else {
                return new Resource\Invoice\Item\Totals\Raw($mObj);
            }
        },
        'InvoiceItemUnit'            => function ($mObj): Resource\Invoice\Item\Unit {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\Unit')) {
                return new \App\Invoice\Resource\Invoice\Item\Unit($mObj);
            } else {
                return new Resource\Invoice\Item\Unit($mObj);
            }
        },
        'InvoiceItemUnitCost'        => function ($mObj): Resource\Invoice\Item\UnitCost {
            if (class_exists('\App\Invoice\Resource\Invoice\Item\UnitCost')) {
                return new \App\Invoice\Resource\Invoice\Item\UnitCost($mObj);
            } else {
                return new Resource\Invoice\Item\UnitCost($mObj);
            }
        },
        'InvoiceState'               => function ($mObj): Resource\Invoice\State {
            if (class_exists('\App\Invoice\Resource\Invoice\State')) {
                return new \App\Invoice\Resource\Invoice\State($mObj);
            } else {
                return new Resource\Invoice\State($mObj);
            }
        },
        'InvoiceTotals'              => function ($mObj): Resource\Invoice\Totals {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals')) {
                return new \App\Invoice\Resource\Invoice\Totals($mObj);
            } else {
                return new Resource\Invoice\Totals($mObj);
            }
        },
        'InvoiceTotalsFormatted'     => function ($mObj): Resource\Invoice\Totals\Formatted {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals\Formatted')) {
                return new \App\Invoice\Resource\Invoice\Totals\Formatted($mObj);
            } else {
                return new Resource\Invoice\Totals\Formatted($mObj);
            }
        },
        'InvoiceTotalsRaw'           => function ($mObj): Resource\Invoice\Totals\Raw {
            if (class_exists('\App\Invoice\Resource\Invoice\Totals\Raw')) {
                return new \App\Invoice\Resource\Invoice\Totals\Raw($mObj);
            } else {
                return new Resource\Invoice\Totals\Raw($mObj);
            }
        },
        'InvoiceUrls'                => function ($mObj): Resource\Invoice\Urls {
            if (class_exists('\App\Invoice\Resource\Invoice\Urls')) {
                return new \App\Invoice\Resource\Invoice\Urls($mObj);
            } else {
                return new Resource\Invoice\Urls($mObj);
            }
        },
        'Payment'                    => function ($mObj): Resource\Payment {
            if (class_exists('\App\Invoice\Resource\Payment')) {
                return new \App\Invoice\Resource\Payment($mObj);
            } else {
                return new Resource\Payment($mObj);
            }
        },
        'PaymentAmount'              => function ($mObj): Resource\Payment\Amount {
            if (class_exists('\App\Invoice\Resource\Payment\Amount')) {
                return new \App\Invoice\Resource\Payment\Amount($mObj);
            } else {
                return new Resource\Payment\Amount($mObj);
            }
        },
        'PaymentDataSca'             => function ($mObj): Resource\Payment\Data\Sca {
            if (class_exists('\App\Invoice\Resource\Payment\Data\Sca')) {
                return new \App\Invoice\Resource\Payment\Data\Sca($mObj);
            } else {
                return new Resource\Payment\Data\Sca($mObj);
            }
        },
        'PaymentStatus'              => function ($mObj): Resource\Payment\Status {
            if (class_exists('\App\Invoice\Resource\Payment\Status')) {
                return new \App\Invoice\Resource\Payment\Status($mObj);
            } else {
                return new Resource\Payment\Status($mObj);
            }
        },
        'PaymentUrls'                => function ($mObj): Resource\Payment\Urls {
            if (class_exists('\App\Invoice\Resource\Payment\Urls')) {
                return new \App\Invoice\Resource\Payment\Urls($mObj);
            } else {
                return new Resource\Payment\Urls($mObj);
            }
        },
        'Refund'                     => function ($mObj): Resource\Refund {
            if (class_exists('\App\Invoice\Resource\Refund')) {
                return new \App\Invoice\Resource\Refund($mObj);
            } else {
                return new Resource\Refund($mObj);
            }
        },
        'RefundAmount'               => function ($mObj): Resource\Refund\Amount {
            if (class_exists('\App\Invoice\Resource\Refund\Amount')) {
                return new \App\Invoice\Resource\Refund\Amount($mObj);
            } else {
                return new Resource\Refund\Amount($mObj);
            }
        },
        'RefundStatus'               => function ($mObj): Resource\Refund\Status {
            if (class_exists('\App\Invoice\Resource\Refund\Status')) {
                return new \App\Invoice\Resource\Refund\Status($mObj);
            } else {
                return new Resource\Refund\Status($mObj);
            }
        },
        'Source'                     => function ($mObj): Resource\Source {
            if (class_exists('\App\Invoice\Resource\Source')) {
                return new \App\Invoice\Resource\Source($mObj);
            } else {
                return new Resource\Source($mObj);
            }
        },
        'Tax'                        => function ($mObj): Resource\Tax {
            if (class_exists('\App\Invoice\Resource\Tax')) {
                return new \App\Invoice\Resource\Tax($mObj);
            } else {
                return new Resource\Tax($mObj);
            }
        },
    ],
];
