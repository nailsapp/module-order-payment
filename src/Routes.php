<?php

/**
 * Generates Invoice routes
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice;

use Nails\Common\Interfaces\RouteGenerator;

/**
 * Class Routes
 *
 * @package Nails\Invoice
 */
class Routes implements RouteGenerator
{
    /**
     * Returns an array of routes for this module
     *
     * @return array
     */
    public static function generate()
    {
        return [
            'invoice/payment/sca/(.+)/(.+)' => 'invoice/sca',
        ];
    }
}
