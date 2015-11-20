<?php

/**
 * Invoice Item model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Common\Model\Base;

class InvoiceItem extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'invoicing_invoice_item';
        $this->tablePrefix = 'ii';
    }
}
