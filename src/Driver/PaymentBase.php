<?php

/**
 * Payment driver base
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Interface
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Driver;

use Nails\Common\Driver\Base;
use Nails\Invoice\Interfaces\Driver\Payment;

abstract class PaymentBase extends Base implements Payment
{
    /**
     * Shortcut for requiring basic card details
     *
     * @var string
     */
    const PAYMENT_FIELDS_CARD = 'CARD';
}
