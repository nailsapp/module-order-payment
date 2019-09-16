<?php

namespace Nails\Invoice\Resource\Invoice\Item;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Service\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;

/**
 * Class UnitCost
 *
 * @package Nails\Invoice\Resource\Invoice\Item
 */
class UnitCost extends Resource
{
    /**
     * The raw totals
     *
     * @var int
     */
    public $raw;

    /**
     * The formatted totals
     *
     * @var string
     */
    public $formatted;

    // --------------------------------------------------------------------------

    /**
     * UnitCost constructor.
     *
     * @param array $mObj
     *
     * @throws FactoryException
     * @throws CurrencyException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        /** @var Currency $oCurrencyService */
        $oCurrencyService = Factory::service('Currency', \Nails\Currency\Constants::MODULE_SLUG);

        $this->formatted = $oCurrencyService->format(
            $this->currency,
            $this->raw / pow(10, $this->currency->decimal_precision)
        );

        unset($this->currency);
    }
}
