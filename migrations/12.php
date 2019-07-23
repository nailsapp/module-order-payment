<?php

/**
 * Migration:   12
 * Started:     23/07/2019
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nails\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration12 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_payment` ADD `sca_data` TEXT NULL AFTER `custom_data`;');
    }
}
