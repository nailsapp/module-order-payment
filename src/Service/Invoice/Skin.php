<?php

/**
 * This service manages the Invoice skins
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Service
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Service\Invoice;

use Nails\Common\Model\BaseSkin;
use Nails\Invoice\Constants;

/**
 * Class Skin
 *
 * @package Nails\Invoice\Service\Invoice
 */
class Skin extends BaseSkin
{
    protected $sModule         = Constants::MODULE_SLUG;
    protected $sType           = 'invoice';
    protected $bEnableMultiple = false;
}
