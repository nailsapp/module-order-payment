<?php

/**
 * Migration:   8
 * Started:     19/04/2018
 * Finalised:   19/04/2018
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nails\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration8 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice_item` ADD `callback_data` TEXT NULL AFTER `grand_total`;");
    }
}
