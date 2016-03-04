<?php

/**
 * Migration:   1
 * Started:     04/03/2016
 * Finalised:
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration1 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("UPDATE `nails_app_setting` SET `key` = 'enabled_driver_payment' WHERE `key` = 'enabled_payment_drivers' AND `grouping` = 'nailsapp/module-invoice';");
    }
}
