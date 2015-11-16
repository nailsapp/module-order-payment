<?php

/**
 * This class registers some handlers for Order & Payment settings
 *
 * @package     Nails
 * @subpackage  module-order-payment
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Order;

use Nails\Admin\Helper;
use Nails\Admin\Controller\Base;

class Settings extends Base
{
    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        $navGroup = new \Nails\Admin\Nav('Settings', 'fa-wrench');

        if (userHasPermission('admin:order:settings:*')) {
            $navGroup->addAction('Orders &amp; Payments');
        }

        return $navGroup;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of permissions which can be configured for the user
     * @return array
     */
    public static function permissions()
    {
        $permissions = parent::permissions();

        $permissions['misc']   = 'Can update miscallaneous settings';
        $permissions['driver'] = 'Can update driver settings';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Manage Email settings
     * @return void
     */
    public function index()
    {
        Helper::loadView('index');
    }
}
