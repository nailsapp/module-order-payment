<?php

/**
 * This service manages the Invoice payment drivers
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Service;

use Nails\Common\Model\BaseDriver;

class PaymentDriver extends BaseDriver
{
    protected $sModule = 'nails/module-invoice';
    protected $sType   = 'payment';
}
