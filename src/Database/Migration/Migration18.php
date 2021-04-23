<?php

/**
 * Migration: 18
 * Started:   23/04/2021
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

class Migration18 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_email` ADD `error` VARCHAR(255) NULL DEFAULT NULL AFTER `recipient`;');
    }
}
