<?php

/**
 * This class represents objects dispensed by the Source model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource\Entity;
use Nails\Factory;

/**
 * Class Source
 *
 * @package Nails\Invoice\Resource
 */
class Source extends Entity
{
    /**
     * The source's customer ID
     *
     * @var int
     */
    public $customer_id;

    /**
     * Which driver is responsible for the source
     *
     * @var string
     */
    public $driver;

    /**
     * Any data required by the driver
     *
     * @var string
     */
    public $data;

    /**
     * The source's label
     *
     * @var string
     */
    public $label;

    /**
     * The source's brand
     *
     * @var string
     */
    public $brand;

    /**
     * The source's last four digits
     *
     * @var string
     */
    public $last_four;

    /**
     * The source's expiry date
     *
     * @var Resource\Date
     */
    public $expiry;

    /**
     * Whether the source is the default for the customer or not
     *
     * @var bool
     */
    public $is_default = false;

    /**
     * Whether the source has expired or not
     *
     * @var bool
     */
    public $is_expired = false;

    // --------------------------------------------------------------------------

    /**
     * Source constructor.
     *
     * @param array $mObj The data to populate the resource with
     *
     * @throws FactoryException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        $this->expiry     = Factory::resource('Date', null, ['raw' => $this->expiry]);
        $oNow             = Factory::factory('DateTime');
        $oExpires         = new \DateTime($this->expiry);
        $this->is_expired = $oExpires < $oNow;
    }
}
