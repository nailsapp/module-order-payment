<?php

/**
 * Migration:   16
 * Started:     12/02/2021
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

class Migration16 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_source` ADD `billing_address_id` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `driver`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_source` ADD FOREIGN KEY (`billing_address_id`) REFERENCES `{{NAILS_DB_PREFIX}}address` (`id`) ON DELETE RESTRICT;');
    }
}
