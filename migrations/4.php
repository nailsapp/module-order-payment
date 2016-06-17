<?php

/**
 * Migration:   4
 * Started:     17/06/2016
 * Finalised:   17/06/2016
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration4 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` ADD `written_off` DATETIME  NULL  AFTER `paid`;");
    }
}
