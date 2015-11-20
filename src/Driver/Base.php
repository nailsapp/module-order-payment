<?php

/**
 * Payment driver base
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Interface
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Driver;

use Nails\Invoice\Exception\DriverException;

class Base
{
    protected $sLabel = 'Untitled Driver';

    // --------------------------------------------------------------------------

    /**
     * Return the driver's label
     * @return string
     */
    public function getLabel()
    {
        return $this->sLabel;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the driver's configurable options
     * @return array
     */
    public function getConfig()
    {
        return array();
    }

    // --------------------------------------------------------------------------

    /**
     * Configures the driver using the saved values from getConfig();
     */
    public function setConfig($aConfig)
    {
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Take a payment
     * @return boolean
     */
    public function charge()
    {
        throw new DriverException('Driver must implement the charge() method', 1);
    }

    // --------------------------------------------------------------------------

    /**
     * Issue a refund for a payment
     * @return boolean
     */
    public function refund()
    {
        throw new DriverException('Driver must implement the refund() method', 1);
    }
}
