<?php

/**
 * This class provides some common Invoice controller functionality
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Controller;

use Nails\Factory;

class Base extends \App\Controller\Base
{
    /**
     * Loads Invoice styles if supplied view does not exist
     *
     * @param string $sView The view to test
     */
    protected function loadStyles($sView)
    {
        //  Test if a view has been provided by the app
        if (!is_file($sView)) {
            $oAsset = Factory::service('Asset');
            $oAsset->clear();
            $oAsset->load('https://code.jquery.com/jquery-2.2.4.min.js');
            $oAsset->load('nails.min.css', 'nailsapp/common');
            $oAsset->load('invoice.pay.css', 'nailsapp/module-invoice');
        }
    }
}
