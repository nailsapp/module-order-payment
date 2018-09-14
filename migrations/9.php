<?php

/**
 * Migration:   9
 * Started:     14/09/2018
 * Finalised:   14/09/2018
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Database Migration
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Database\Migration\Nails\ModuleInvoice;

use Nails\Common\Console\Migrate\Base;

class Migration9 extends Base
{
    /**
     * Execute the migration
     * @return Void
     */
    public function execute()
    {
        $this->query("UPDATE `{{NAILS_DB_PREFIX}}invoice_payment` SET `driver` = REPLACE(`driver`, 'nailsapp', 'nails');");
    }
}
