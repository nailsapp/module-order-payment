<?php

/**
 * Migration:  17
 * Started:    12/04/2021
 *
 * @package    Nails
 * @subpackage module-invoice
 * @category   Database Migration
 * @author     Nails Dev Team
 */

namespace Nails\Invoice\Database\Migration;

use Nails\Address;
use Nails\Common\Console\Migrate\Base;
use Nails\Common\Service\Country;
use Nails\Factory;
use Nails\Invoice\Resource\Customer;

class Migration17 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_payment` CHANGE `custom_data` `custom_data` json NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_payment` CHANGE `sca_data` `sca_data` json NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_source` CHANGE `data` `data` json NULL;');
    }
}
