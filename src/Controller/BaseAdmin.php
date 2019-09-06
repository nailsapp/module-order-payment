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
use Nails\Common\Exception\FactoryException;
use Nails\Common\Service\Asset;
use Nails\Factory;
use Nails\Invoice\Constants;

/**
 * Class BaseAdmin
 *
 * @package Nails\Invoice\Controller
 */
class BaseAdmin extends Base
{
    /**
     * BaseAdmin constructor.
     *
     * @throws FactoryException
     */
    public function __construct()
    {
        parent::__construct();
        /** @var Asset $oAsset */
        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.min.css', Constants::MODULE_SLUG);
    }
}
