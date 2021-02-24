<?php

/**
 * Migration:   1
 * Started:     04/03/2016
 * Finalised:   17/03/2016
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nails\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;
use Nails\Invoice\Constants;

class Migration1 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("UPDATE `{{NAILS_DB_PREFIX}}app_setting` SET `key` = 'enabled_driver_payment' WHERE `key` = 'enabled_payment_drivers' AND `grouping` = '" . Constants::MODULE_SLUG . "';");
    }
}
