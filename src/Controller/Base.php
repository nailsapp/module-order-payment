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

use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Asset;
use Nails\Factory;
use Nails\Invoice\Constants;

// --------------------------------------------------------------------------

/**
 * Allow the app to add functionality, if needed
 */
if (!class_exists('\App\Invoice\Controller\Base')) {
    abstract class BaseMiddle extends \App\Controller\Base
    {
    }
} else {
    abstract class BaseMiddle extends \App\Invoice\Controller\Base
    {
        public function __construct()
        {
            /** @phpstan-ignore-next-line */
            if (!classExtends(parent::class, \App\Controller\Base::class)) {
                throw new NailsException(sprintf(
                    'Class %s must extend %s',
                    /** @phpstan-ignore-next-line */
                    parent::class,
                    \App\Controller\Base::class
                ));
            }
            /** @phpstan-ignore-next-line */
            parent::__construct();
        }
    }
}

// --------------------------------------------------------------------------

/**
 * Class Base
 *
 * @package Nails\Invoice\Controller
 */
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
            /** @var Asset $oAsset */
            $oAsset = Factory::service('Asset');
            $oAsset->clear();
            $oAsset->load('https://code.jquery.com/jquery-2.2.4.min.js');
            $oAsset->load('nails.min.css', \Nails\Common\Constants::MODULE_SLUG);
            $oAsset->load('invoice.pay.min.css', Constants::MODULE_SLUG);
        }
    }
}
