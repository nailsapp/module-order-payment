<?php

/**
 * Payment model
 *
 * @package     Nails
 * @subpackage  module-order-payment
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\OrderPayment\Model;

use Nails\Common\Model\Base;

class Payment extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'orderpayment_payment';
        $this->tablePrefix = 'opp';
    }
}
