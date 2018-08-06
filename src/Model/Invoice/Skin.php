<?php

/**
 * This model manages the Invoice skins
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model\Invoice;

use Nails\Common\Model\BaseSkin;

class Skin extends BaseSkin
{
    protected $sModule         = 'nailsapp/module-invoice';
    protected $sType           = 'invoice';
    protected $bEnableMultiple = false;
}
