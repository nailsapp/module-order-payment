<?php

/**
 * This class represents objects dispensed by the Customer model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Address;
use Nails\Common\Helper\Model\Expand;
use Nails\Common\Resource\Entity;
use Nails\Factory;

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

    // --------------------------------------------------------------------------

    /**
     * Returns associated customer addresses
     *
     * @return Address\Resource\Address[]
     */
    public function addresses(): array
    {
        /** @var Address\Model\Address\Associated $oModel */
        $oModel = Factory::model('AddressAssociated', Address\Constants::MODULE_SLUG);

        $aAddresses = $oModel->getAll([
            new Expand('address'),
            'where' => [
                ['associated_type', self::class],
                ['associated_id', $this->id],
            ],
        ]);

        return array_map(function (Address\Resource\Address\Associated $oAssociation) {
            return $oAssociation->address;
        }, $aAddresses);
    }
}
