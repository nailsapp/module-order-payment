<?php

/**
 * Migration:   6
 * Started:     11/10/2016
 * Finalised:   11/10/2016
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nailsapp\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration6 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice_item` ADD `currency` CHAR(3)  NOT NULL  DEFAULT '' AFTER `order`;");
        $this->query("UPDATE `{{NAILS_DB_PREFIX}}invoice_invoice_item` AS `it` SET `it`.`currency` = (SELECT `i`.`currency` FROM `{{NAILS_DB_PREFIX}}invoice_invoice` AS `i` WHERE `i`.`id` = `it`.`invoice_id`);");
    }
}
