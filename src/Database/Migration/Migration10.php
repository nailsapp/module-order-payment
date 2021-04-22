<?php

/**
 * Migration:   10
 * Started:     06/03/2019
 * Finalised:   06/03/2019
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Database\Migration;

use Nails\Common\Console\Migrate\Base;

class Migration10 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("ALTER TABLE `nails_invoice_invoice` CHANGE `state` `state` ENUM('DRAFT','OPEN','PENDING','PAID_PARTIAL','PAID_PROCESSING','PAID','WRITTEN_OFF', 'CANCELLED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'DRAFT';");
    }
}
