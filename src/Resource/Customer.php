<?php

/**
 * This class represents objects dispensed by the Customer model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource\Entity;
use stdClass;

/**
 * Class Customer
 *
 * @package Nails\Invoice\Resource
 */
class Customer extends Entity
{
    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $organisation;

    /**
     * @var string
     */
    public $first_name;

    /**
     * @var string
     */
    public $last_name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $billing_email;

    /**
     * @var string
     */
    public $telephone;

    /**
     * @var string
     */
    public $vat_number;

    /**
     * @var bool
     */
    public $is_deleted;

    /**
     * @var stdClass Object
     */
    public $billing_address;
}
