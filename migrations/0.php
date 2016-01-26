<?php

/**
 * Migration:   0
 * Started:     16/01/2016
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
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_email` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `invoice_id` int(11) unsigned NOT NULL,
                `email_id` int(11) unsigned DEFAULT NULL,
                `email_type` varchar(50) DEFAULT NULL,
                `recipient` varchar(255) DEFAULT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `order_id` (`invoice_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                KEY `email_id` (`email_id`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_email_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_invoice` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_email_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_email_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_email_ibfk_4` FOREIGN KEY (`email_id`) REFERENCES `{{NAILS_DB_PREFIX}}email_archive` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `ref` char(15) NOT NULL DEFAULT '',
                `token` char(32) NOT NULL,
                `state` enum('DRAFT','OPEN','PENDING','PAID_PARTIAL','PAID_PROCESSING','PAID','WRITTEN_OFF') NOT NULL DEFAULT 'DRAFT',
                `dated` date NOT NULL,
                `terms` int(11) unsigned NOT NULL DEFAULT '0',
                `due` date NOT NULL,
                `paid` datetime DEFAULT NULL,
                `user_id` int(11) unsigned DEFAULT NULL,
                `user_email` varchar(255) DEFAULT NULL,
                `currency` char(3) NOT NULL DEFAULT '',
                `sub_total` int(11) unsigned NOT NULL DEFAULT '0',
                `tax_total` int(11) unsigned NOT NULL DEFAULT '0',
                `grand_total` int(11) unsigned NOT NULL DEFAULT '0',
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
                `label` varchar(255) NOT NULL,
                `body` text,
                `order` int(11) unsigned NOT NULL DEFAULT '0',
                `unit` enum('NONE','MINUTE','HOUR','DAY','WEEK','MONTH','YEAR') NOT NULL DEFAULT 'NONE',
                `tax_id` int(11) unsigned DEFAULT NULL,
                `quantity` decimal(10,3) unsigned NOT NULL DEFAULT '1.000',
                `unit_cost` int(11) NOT NULL DEFAULT '0',
                `sub_total` int(11) unsigned NOT NULL DEFAULT '0',
                `tax_total` int(11) unsigned NOT NULL DEFAULT '0',
                `grand_total` int(11) unsigned NOT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `invoice_id` (`invoice_id`),
                KEY `tax_id` (`tax_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_item_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_invoice` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_item_ibfk_2` FOREIGN KEY (`tax_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_tax` (`id`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_item_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_invoice_item_ibfk_4` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");

        $this->query("
            CREATE TABLE `{{NAILS_DB_PREFIX}}invoice_payment` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `ref` char(15) NOT NULL DEFAULT '',
                `token` char(32) NOT NULL,
                `driver` varchar(150) NOT NULL DEFAULT '',
                `invoice_id` int(11) unsigned DEFAULT NULL,
                `description` varchar(255) DEFAULT NULL,
                `status` enum('PENDING','PROCESSING','COMPLETE','FAILED') NOT NULL DEFAULT 'PENDING',
                `txn_id` varchar(255) DEFAULT NULL,
                `fail_msg` varchar(255) DEFAULT NULL,
                `fail_code` int(11) DEFAULT NULL,
                `currency` char(3) NOT NULL DEFAULT '',
                `amount` int(11) NOT NULL DEFAULT '0',
                `url_continue` varchar(255) DEFAULT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `order_id` (`invoice_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}invoice_payment_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `{{NAILS_DB_PREFIX}}invoice_invoice` (`id`) ON DELETE SET NULL,
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
                `user_id` int(11) unsigned NOT NULL,
                `token` varchar(150) DEFAULT NULL,
                `label` varchar(150) DEFAULT NULL,
                `last_four` char(4) NOT NULL DEFAULT '',
                `expiry` date DEFAULT NULL,
                `created` datetime NOT NULL,
                `created_by` int(11) unsigned DEFAULT NULL,
                `modified` datetime NOT NULL,
                `modified_by` int(11) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `created_by` (`created_by`),
                KEY `modified_by` (`modified_by`),
                CONSTRAINT `{{NAILS_DB_PREFIX}}user_meta_invoice_card_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE CASCADE,
                CONSTRAINT `{{NAILS_DB_PREFIX}}user_meta_invoice_card_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL,
                CONSTRAINT `{{NAILS_DB_PREFIX}}user_meta_invoice_card_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `{{NAILS_DB_PREFIX}}user` (`id`) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
    }
}
