<?php

/**
 * Order model
 *
 * @package     Nails
 * @subpackage  module-order-payment
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\OrderPayment\Model;

use Nails\Common\Model\Base;

class Order extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'orderpayment_order';
        $this->tablePrefix = 'opo';
    }
}
