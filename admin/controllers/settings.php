<?php

/**
 * This class registers some handlers for Invoicing & Payment settings
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Invoice;

use Nails\Factory;
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
        $navGroup = Factory::factory('Nav', 'nailsapp/module-admin');
        $navGroup->setLabel('Settings');
        $navGroup->setIcon('fa-wrench');

        if (userHasPermission('admin:invoice:settings:*')) {
            $navGroup->addAction('Invoicing &amp; Payments');
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
