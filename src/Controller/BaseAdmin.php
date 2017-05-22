<?php

/**
 * This class provides some common Invoice controller functionality in admin
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Controller;

use Nails\Admin\Controller\Base;
use Nails\Factory;

class BaseAdmin extends Base
{
    public function __construct()
    {
        parent::__construct();
        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.styles.css', 'nailsapp/module-invoice');
    }
}
