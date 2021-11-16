<?php

/**
 * Migration: 19
 * Started:   16/11/2021
 *
 * @package    Nails
 * @subpackage module-invoice
 * @category   Database Migration
 * @author     Nails Dev Team
 */

namespace Nails\Invoice\Database\Migration;

use Nails\Common\Console\Migrate\Base;

class Migration19 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_payment` CHANGE `status` `status` ENUM(\'PENDING\',\'SENT_FOR_AUTH\',\'PROCESSING\',\'COMPLETE\',\'FAILED\',\'REFUNDED\',\'REFUNDED_PARTIAL\') NOT NULL DEFAULT \'PENDING\';');
    }
}
