<?php

/**
 * Migration:   14
 * Started:     17/07/2020
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Database\Migration;

use Nails\Address;
use Nails\Common\Console\Migrate\Base;
use Nails\Common\Service\Country;
use Nails\Factory;
use Nails\Invoice\Resource\Customer;
use PDO;

class Migration14 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` ADD COLUMN `billing_address_id` int(11) unsigned NULL AFTER `payment_driver`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` ADD COLUMN `delivery_address_id` int(11) unsigned NULL AFTER `billing_address_id`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` ADD FOREIGN KEY (`billing_address_id`) REFERENCES `{{NAILS_DB_PREFIX}}address` (`id`) ON DELETE RESTRICT;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` ADD FOREIGN KEY (`delivery_address_id`) REFERENCES `{{NAILS_DB_PREFIX}}address` (`id`) ON DELETE RESTRICT;');

        /** @var Country $oCountryService */
        $oCountryService = Factory::service('Country');
        /** @var Address\Model\Address $oAddressModel */
        $oAddressModel = Factory::model('Address', Address\Constants::MODULE_SLUG);
        /** @var Address\Model\Address\Associated $oAddressAssociatedModel */
        $oAddressAssociatedModel = Factory::model('AddressAssociated', Address\Constants::MODULE_SLUG);

        $aCountries = [];
        foreach ($oCountryService->getCountries() as $oCountry) {
            $aCountries[trim(strtolower($oCountry->name))]   = $oCountry->iso;
            $aCountries[trim(strtolower($oCountry->native))] = $oCountry->iso;
        }

        $oResult = $this->query('
            SELECT
                id,
                billing_address_line_1,
                billing_address_line_2,
                billing_address_town,
                billing_address_county,
                billing_address_postcode,
                billing_address_country
            FROM {{NAILS_DB_PREFIX}}invoice_customer
            WHERE
                billing_address_line_1 != ""
                AND billing_address_town != ""
                AND billing_address_country != "";
        ');

        $oQueryAddress = $this->prepare('
            INSERT INTO `' . $oAddressModel->getTableName() . '` (
                country,
                line_1,
                line_2,
                town,
                region,
                postcode,
                created,
                modified
            )
            VALUES (
                :country,
                :line_1,
                :line_2,
                :town,
                :county,
                :postcode,
                NOW(),
                NOW()
            );
        ');

        $oQueryAssociation = $this->prepare('
            INSERT INTO `' . $oAddressAssociatedModel->getTableName() . '` (
                address_id,
                associated_type,
                associated_id,
                is_default,
                created,
                modified
            )
            VALUES (
                :address_id,
                :class,
                :customer_id,
                1,
                NOW(),
                NOW()
            );
        ');

        while ($oCustomer = $oResult->fetch(PDO::FETCH_OBJ)) {

            $sCountry = getFromArray(trim(strtolower($oCustomer->billing_address_country)), $aCountries);

            //  Country must be set
            if (!empty($sCountry)) {

                $oQueryAddress->execute([
                    'country'  => $sCountry,
                    'line_1'   => $oCustomer->billing_address_line_1,
                    'line_2'   => $oCustomer->billing_address_line_2,
                    'town'     => $oCustomer->billing_address_town,
                    'county'   => $oCustomer->billing_address_county,
                    'postcode' => $oCustomer->billing_address_postcode,
                ]);

                $oQueryAssociation->execute([
                    'address_id'  => $this->lastInsertId(),
                    'class'       => Customer::class,
                    'customer_id' => $oCustomer->id,
                ]);
            }
        }

        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_customer` DROP COLUMN `billing_address_line_1`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_customer` DROP COLUMN `billing_address_line_2`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_customer` DROP COLUMN `billing_address_town`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_customer` DROP COLUMN `billing_address_county`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_customer` DROP COLUMN `billing_address_postcode`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_customer` DROP COLUMN `billing_address_country`;');
    }
}
