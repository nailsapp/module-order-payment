<?php

/**
 * This class represents objects dispensed by the Tax model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource\Entity;

class Tax extends Entity
{
    /**
     * The tax's label
     *
     * @var string
     */
    public $label = '';

    /**
     * The tax's rate
     *
     * @var int
     */
    public $rate = 0;

    /**
     * The tax's rate as a decimal
     *
     * @var float
     */
    public $rate_decimal = 0;

    // --------------------------------------------------------------------------

    /**
     * Tax constructor.
     *
     * @param array $mObj
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);
        $this->rate_decimal = $this->rate / 100;
    }
}
