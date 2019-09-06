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
use Nails\Invoice\Constants;

/**
 * Class PaymentDriver
 *
 * @package Nails\Invoice\Service
 */
class PaymentDriver extends BaseDriver
{
    protected $sModule = Constants::MODULE_SLUG;
    protected $sType   = 'payment';
}
