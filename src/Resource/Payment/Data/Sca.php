<?php

namespace Nails\Invoice\Resource\Payment\Data;

use Nails\Common\Resource;
use Nails\Config;
use Nails\Invoice\Resource\ArbitraryData;

/**
 * Class Status
 *
 * @package Nails\Invoice\Resource\Payment\Data
 */
class Sca extends ArbitraryData
{
    /**
     * Generate a hash of this data
     *
     * @return string
     */
    public function hash(): string
    {
        return md5($this . Config::get('PRIVATE_KEY'));
    }
}
