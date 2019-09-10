<?php

namespace Nails\Invoice\Resource\Invoice\Totals;

use Nails\Common\Resource;

/**
 * Class Raw
 *
 * @package Nails\Invoice\Resource\Invoice
 */
class Raw extends Resource
{
    /**
     * The sub total
     *
     * @var int
     */
    public $sub;

    /**
     * The tax total
     *
     * @var int
     */
    public $tax;

    /**
     * The grand total
     *
     * @var int
     */
    public $grand;

    /**
     * The paid total
     *
     * @var int
     */
    public $paid;

    /**
     * The processing total
     *
     * @var int
     */
    public $processing;
}
