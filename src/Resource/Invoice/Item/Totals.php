<?php

namespace Nails\Invoice\Resource\Invoice\Item;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Resource\Invoice\Totals\Item\Formatted;
use Nails\Invoice\Resource\Invoice\Totals\Item\Raw;

/**
 * Class Totals
 *
 * @package Nails\Invoice\Resource\Invoice\Item
 */
class Totals extends Resource
{
    /**
     * The raw totals
     *
     * @var Raw
     */
    public $raw;

    /**
     * The formatted totals
     *
     * @var Formatted
     */
    public $formatted;

    // --------------------------------------------------------------------------

    /**
     * Totals constructor.
     *
     * @param array $mObj
     *
     * @throws FactoryException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        $this->raw = Factory::resource(
            'InvoiceItemTotalsRaw',
            Constants::MODULE_SLUG,
            [
                'sub'   => (int) $mObj->sub,
                'tax'   => (int) $mObj->tax,
                'grand' => (int) $mObj->grand,
            ]
        );

        $this->formatted = Factory::resource(
            'InvoiceItemTotalsFormatted',
            Constants::MODULE_SLUG,
            [
                'currency' => $this->currency,
                'sub'      => (int) $mObj->sub,
                'tax'      => (int) $mObj->tax,
                'grand'    => (int) $mObj->grand,
            ]
        );

        unset($this->sub);
        unset($this->tax);
        unset($this->grand);
        unset($this->currency);
    }
}
