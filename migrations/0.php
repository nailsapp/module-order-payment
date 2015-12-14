<?php

/**
 * Migration:   0
 * Started:     16/01/2015
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

class Migration0 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `ref` char(20) NOT NULL DEFAULT '',
                `state` enum('DRAFT','OPEN','PENDING','PARTIALLY_PAID','PAID','WRITTEN_OFF') NOT NULL DEFAULT 'DRAFT',
                `dated` date NOT NULL,
                `terms` int(11) unsigned NOT NULL DEFAULT '0',
                `due` date NOT NULL,
                `user_id` int(11) unsigned DEFAULT NULL,
                `user_email` varchar(255) DEFAULT NULL,
                `currency` char(3) NOT NULL DEFAULT '',
                `total` int(11) unsigned NOT NULL DEFAULT '0',
                `tax` int(11) unsigned NOT NULL DEFAULT '0',
                `discount` int(11) unsigned NOT NULL DEFAULT '0',
                `additional_text` text,
                `callback_data` text,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_invoice_item` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `invoice_id` int(11) unsigned NOT NULL,
                `order` int(11) unsigned NOT NULL DEFAULT '0',
                `quantity` int(11) unsigned DEFAULT NULL,
                `units` enum('NONE','MINUTE','HOUR','DAY','WEEK','MONTH','YEAR') NOT NULL DEFAULT 'NONE',
                `tax` int(11) unsigned DEFAULT NULL,
                `label` varchar(255) NOT NULL DEFAULT '',
                `body` text,
                PRIMARY KEY (`id`),
                KEY `invoice_id` (`invoice_id`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_invoice` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_item_ibfk_2` FOREIGN KEY (`tax_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_tax` (`id`) ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_payment` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `processor` varchar(150) NOT NULL DEFAULT '',
                `invoice_id` int(11) unsigned NOT NULL,
                `transaction_ref` varchar(50) DEFAULT NULL,
                `currency` char(3) NOT NULL DEFAULT '',
                `currency_base` char(3) NOT NULL DEFAULT '',
                `amount` int(11) unsigned NOT NULL DEFAULT '0',
                `amount_base` int(11) unsigned NOT NULL DEFAULT '0',
                `fee` int(11) unsigned NOT NULL DEFAULT '0',
                `fee_base` int(11) unsigned DEFAULT '0',
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `order_id` (`invoice_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_payment_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_invoice` (`id`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_payment_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_payment_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_tax` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `label` varchar(150) NOT NULL DEFAULT '',
                `rate` int(11) unsigned DEFAULT '0',
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_tax_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_tax_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}user_meta_invoice_address` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}user_meta_invoice_card` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
