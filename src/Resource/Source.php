<?php

/**
 * This class represents objects dispensed by the Source model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use DateTime;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource\Date;
use Nails\Common\Resource\Entity;
use Nails\Factory;
use stdClass;

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
     * @var stdClass
     */
    public $data;

    /**
     * The source's label
     *
     * @var string
     */
    public $label;

    /**
     * The source's name (e.g. the cardholder)
     *
     * @var string
     */
    public $name;

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
     * @var Date
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

        if ($this->expiry) {
            $this->expiry = Factory::resource('Date', null, ['raw' => $this->expiry]);
        }

        $this->is_expired = $this->isExpired();
        $this->data       = json_decode($this->data) ?? (object) [];
    }

    // --------------------------------------------------------------------------

    /**
     * Determines whether source has expired
     *
     * @param DateTime $oWhen
     *
     * @return bool
     */
    public function isExpired(DateTime $oWhen = null): bool
    {
        if (!$this->expiry) {
            return false;
        }

        /** @var DateTime $oWhen */
        $oWhen = $oWhen ?? Factory::factory('DateTime');
        return $this->expiry->getDateTimeObject() < $oWhen;
    }
}
