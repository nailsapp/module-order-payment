<?php

namespace Nails\Invoice\Resource\Invoice;

use Nails\Common\Resource;

/**
 * Class Urls
 *
 * @package Nails\Invoice\Resource\Invoice
 */
class Urls extends Resource
{
    /**
     * The invoice's payment URL
     *
     * @var string
     */
    public $payment;

    /**
     * The invoice's download URL
     *
     * @var string
     */
    public $download;

    /**
     * The invoice's view URL
     *
     * @var string
     */
    public $view;
}
