<?php

namespace Nails\Invoice\Resource\Invoice;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Resource\Invoice\Totals\Formatted;
use Nails\Invoice\Resource\Invoice\Totals\Raw;

/**
 * Class Totals
 *
 * @package Nails\Invoice\Resource\Invoice
 */
class Totals extends Resource
{
    /**
     * The currency
     *
     * @var \Nails\Currency\Resource\Currency
     */
    public $currency;

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
            'InvoiceTotalsRaw',
            Constants::MODULE_SLUG,
            [
                'sub'        => (int) $mObj->sub,
                'tax'        => (int) $mObj->tax,
                'grand'      => (int) $mObj->grand,
                'paid'       => (int) $mObj->paid,
                'processing' => (int) $mObj->processing,
            ]
        );

        $this->formatted = Factory::resource(
            'InvoiceTotalsFormatted',
            Constants::MODULE_SLUG,
            [
                'currency'   => $this->currency,
                'sub'        => (int) $mObj->sub,
                'tax'        => (int) $mObj->tax,
                'grand'      => (int) $mObj->grand,
                'paid'       => (int) $mObj->paid,
                'processing' => (int) $mObj->processing,
            ]
        );

        unset($this->sub);
        unset($this->tax);
        unset($this->grand);
        unset($this->paid);
        unset($this->processing);
        unset($this->currency);
    }
}
