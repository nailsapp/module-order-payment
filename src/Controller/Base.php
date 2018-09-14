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

// --------------------------------------------------------------------------

/**
 * Allow the app to add functionality, if needed
 */
if (class_exists('\App\Invoice\Controller\Base')) {
    abstract class BaseMiddle extends \App\Invoice\Controller\Base
    {
    }
} else {
    abstract class BaseMiddle extends \App\Controller\Base
    {
    }
}

// --------------------------------------------------------------------------

abstract class Base extends BaseMiddle
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
            $oAsset->load('nails.min.css', 'nails/common');
            $oAsset->load('invoice.pay.css', 'nails/module-invoice');
        }
    }
}
