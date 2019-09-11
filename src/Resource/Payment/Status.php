<?php

namespace Nails\Invoice\Resource\Payment;

use Nails\Common\Resource;

/**
 * Class Status
 *
 * @package Nails\Invoice\Resource\Payment
 */
class Status extends Resource
{
    /**
     * The status' ID
     *
     * @var string
     */
    public $id;

    /**
     * The status' label
     *
     * @var string
     */
    public $label;
}
