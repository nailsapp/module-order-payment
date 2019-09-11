<?php

namespace Nails\Invoice\Resource\Customer;

use Nails\Common\Resource;

/**
 * Class Address
 *
 * @package Nails\Invoice\Resource\Customer
 */
class Address extends Resource
{
    /**
     * The first line of the address
     *
     * @var string
     */
    public $line_1 = '';

    /**
     * The second line of the address
     *
     * @var string
     */
    public $line_2 = '';

    /**
     * The address' town
     *
     * @var string
     */
    public $town = '';

    /**
     * The address' county
     *
     * @var string
     */
    public $county = '';

    /**
     * The address' postcode
     *
     * @var string
     */
    public $postcode = '';

    /**
     * The address' country
     *
     * @var string
     */
    public $country = '';
}
