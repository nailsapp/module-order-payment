<?php

/**
 * Migration:   5
 * Started:     16/08/2016
 * Finalised:   16/08/2016
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration5 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` ADD `email` VARCHAR(255)  NULL  DEFAULT NULL  AFTER `customer_id`;");
    }
}
