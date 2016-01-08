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

class BaseAdmin extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->asset->load('admin.styles.css', 'nailsapp/module-invoice');
    }
}
