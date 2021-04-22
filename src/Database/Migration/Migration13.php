<?php

/**
 * Migration:   13
 * Started:     03/10/2019
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Database\Migration;

use Nails\Common\Console\Migrate\Base;

class Migration13 extends Base
{
    /**
     * Execute the migration
     *
     * @return Void
     */
    public function execute()
    {
        $this->query('ALTER TABLE `{{NAILS_DB_PREFIX}}invoice_source` ADD `name` VARCHAR(150) NULL DEFAULT NULL AFTER `label`;');
    }
}
