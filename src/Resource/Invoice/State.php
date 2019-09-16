<?php

namespace Nails\Invoice\Resource\Invoice;

use Nails\Common\Resource;

/**
 * Class State
 *
 * @package Nails\Invoice\Resource\Invoice
 */
class State extends Resource
{
    /**
     * The state's ID
     *
     * @var string
     */
    public $id;

    /**
     * The state's label
     *
     * @var string
     */
    public $label;
}
