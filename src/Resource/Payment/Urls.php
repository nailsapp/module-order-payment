<?php

namespace Nails\Invoice\Resource\Payment;

use Nails\Common\Resource;

/**
 * Class Urls
 *
 * @package Nails\Invoice\Resource\Payment
 */
class Urls extends Resource
{
    /**
     * The payment's complete URL
     *
     * @var string
     */
    public $complete;

    /**
     * The payment's thanks URL
     *
     * @var string
     */
    public $thanks;

    /**
     * The payment's processing URL
     *
     * @var string
     */
    public $processing;

    /**
     * The payment's success URL
     *
     * @var string
     */
    public $success;

    /**
     * The payment's error URL
     *
     * @var string
     */
    public $error;

    /**
     * The payment's cancel URL
     *
     * @var string
     */
    public $cancel;
}
