<?php

return array(
    'models' => array(
        'Order' => function () {
            if (class_exists('\App\OrderPayment\Model\Order')) {
                return new \App\OrderPayment\Model\Order();
            } else {
                return new \Nails\OrderPayment\Model\Order();
            }
        },
        'Payment' => function () {
            if (class_exists('\App\OrderPayment\Model\Payment')) {
                return new \App\OrderPayment\Model\Payment();
            } else {
                return new \Nails\OrderPayment\Model\Payment();
            }
        },
        'Processor' => function () {
            if (class_exists('\App\OrderPayment\Model\Processor')) {
                return new \App\OrderPayment\Model\Processor();
            } else {
                return new \Nails\OrderPayment\Model\Processor();
            }
        }
    )
);
