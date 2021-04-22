<?php

/**
 * Migration:   15
 * Started:     02/09/2020
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Database\Migration;

use Nails\Address;
use Nails\Common\Console\Migrate\Base;
use Nails\Common\Service\Country;
use Nails\Factory;
use Nails\Invoice\Resource\Customer;

class Migration15 extends Base
{
    /**
     * Execute the migration
     *
     * @return void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` CHANGE `callback_data` `callback_data` json NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice` CHANGE `payment_data` `payment_data` json NULL;');
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_invoice_item` CHANGE `callback_data` `callback_data` json NULL;');
    }
}
