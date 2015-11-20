<?php

/**
 * Driver model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Invoice\Exception\DriverException;

class Driver
{
    /**
     * An array of discovered drivers
     * @var array
     */
    private $aDrivers;

    // --------------------------------------------------------------------------

    /**
     * Get all the payment proseccor drivers
     * @return array
     */
    public function getAll()
    {
        if (is_null($this->aDrivers)) {

            $aComponents = _NAILS_GET_DRIVERS('invoice');

            foreach ($aComponents as $oDriver) {

                if (!empty($oDriver->moduleData->className)) {

                    $sClassName = $oDriver->moduleData->className;

                } else {

                    throw new DriverException('Driver name missing from driver "' . $oDriver->name . '"', 1);
                }

                $sDriverClass    = '\Nails\Invoice\Driver\\' . $sClassName;
                $oDriverInstance = new $sDriverClass();

                if (!($oDriverInstance instanceof \Nails\Invoice\Driver\Base)) {

                    throw new DriverException(
                        'Driver "' . $oDriver->name . '" must extend \Nails\Invoice\Driver\Base',
                        2
                    );

                } else {

                    $this->aDrivers[$oDriver->name] = $oDriverInstance;
                }
            }
        }

        return $this->aDrivers;
    }

    // --------------------------------------------------------------------------

    /**
     * Return a single instance of a processor driver
     * @param  string $sSlug The driver's slug
     * @return object
     */
    public function getBySlug($sSlug)
    {
        $oOut     = null;
        $aDrivers = $this->getAll();

        foreach ($aDrivers as $aDriver) {
            if ($sSlug == $aDriver['slug']) {
                $oOut = $aDriver['instance'];
            }
        }

        return $oOut;
    }
}
