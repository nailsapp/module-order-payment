<?php

/**
 * This model manages the Invoice payment drivers
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Common\Model\BaseDriver;

class PaymentDriver extends BaseDriver
{
    protected $sModule = 'nailsapp/module-invoice';
    protected $sType   = 'payment';
}
