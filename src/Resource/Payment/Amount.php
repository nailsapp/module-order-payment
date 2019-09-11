<?php

namespace Nails\Invoice\Resource\Payment;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Resource;
use Nails\Currency\Constants;
use Nails\Currency\Exception\CurrencyException;
use Nails\Currency\Service\Currency;
use Nails\Factory;

/**
 * Class Amount
 *
 * @package Nails\Invoice\Resource\Payment
 */
class Amount extends Resource
{
    /**
     * The amount' raw value
     *
     * @var string
     */
    public $raw;

    /**
     * The amount' formatted value
     *
     * @var string
     */
    public $formatted;

    // --------------------------------------------------------------------------

    /**
     * Amount constructor.
     *
     * @param array $mObj
     *
     * @throws FactoryException
     * @throws CurrencyException
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        /** @var Currency $oCurrency */
        $oCurrency = Factory::service('Currency', Constants::MODULE_SLUG);

        $this->formatted = $oCurrency->format(
            $this->currency,
            $this->raw / pow(10, $this->currency->decimal_precision)
        );

        unset($this->currency);
    }
}
