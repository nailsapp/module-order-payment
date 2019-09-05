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
     *
     * @return Void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_payment` ADD `sca_data` TEXT NULL AFTER `custom_data`;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_payment` CHANGE `fail_msg` `fail_msg` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;');
        $this->query('
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_source` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `customer_id` int(11) unsigned NOT NULL,
                `driver` varchar(150) DEFAULT NULL,
                `data` text,
                `label` varchar(150) DEFAULT NULL,
                `brand` varchar(150) DEFAULT NULL,
                `last_four` char(4) NOT NULL DEFAULT "",
                `expiry` date DEFAULT NULL,
                `is_default` tinyint(1) unsigned NOT NULL DEFAULT 0,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `customer_id` (`customer_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_source_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_customer` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_source_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_source_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ');
        $this->query('DROP TABLE `{{NAILS_DB_PREFIX}}user_meta_invoice_card`;');
        $this->query('DROP TABLE `{{NAILS_DB_PREFIX}}user_meta_invoice_address`;');
    }
}
