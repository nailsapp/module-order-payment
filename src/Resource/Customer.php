<?php

/**
 * This class represents objects dispensed by the Customer model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource\Entity;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Resource\Customer\Address;

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
     * @var Address Object
     */
    public $billing_address;

    // --------------------------------------------------------------------------

    /**
     * Customer constructor.
     *
     * @param array $mObj
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        $this->billing_address = Factory::resource(
            'CustomerAddress',
            Constants::MODULE_SLUG,
            (object) [
                'line_1'   => $mObj->billing_address_line_1,
                'line_2'   => $mObj->billing_address_line_2,
                'town'     => $mObj->billing_address_town,
                'county'   => $mObj->billing_address_county,
                'postcode' => $mObj->billing_address_postcode,
                'country'  => $mObj->billing_address_country,
            ]
        );

        unset($this->billing_address_line_1);
        unset($this->billing_address_line_2);
        unset($this->billing_address_town);
        unset($this->billing_address_county);
        unset($this->billing_address_postcode);
        unset($this->billing_address_country);
    }
}
