<?php

/**
 * This class represents objects dispensed by the Source model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource;

class Source extends Resource
{
    /**
     * The ID of the source
     *
     * @var int
     */
    public $id;

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
     * @var string
     */
    public $expiry;

    /**
     * Whether the source is the default for the customer or not
     *
     * @var bool
     */
    public $is_default;

    /**
     * The source's creation date
     *
     * @var string
     */
    public $created;

    /**
     * The source's creator's ID
     *
     * @var string
     */
    public $created_by;

    /**
     * The source's modification date
     *
     * @var string
     */
    public $modified;

    /**
     * The source's modifier's ID
     *
     * @var string
     */
    public $modified_by;
}
