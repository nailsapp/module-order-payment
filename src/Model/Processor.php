<?php

/**
 * Payment PRocessor model
 *
 * @package     Nails
 * @subpackage  module-order-payment
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\OrderPayment\Model;

use Nails\OrderPayment\Exception\DriverException;

class Processor
{
    private $aDrivers;

    // --------------------------------------------------------------------------

    /**
     * Get all the payment proseccor drivers
     * @return array
     */
    public function getAll()
    {
        if (is_null($this->aDrivers)) {

            $aComponents = _NAILS_GET_DRIVERS('order-payment');

            foreach ($aComponents as $oDriver) {

                if (!empty($oDriver->moduleData->className)) {

                    $sClassName = $oDriver->moduleData->className;

                } else {

                    throw new DriverException('Driver name missing from driver "' . $oDriver->name . '"', 1);
                }

                $sDriverClass    = '\Nails\OrderPayment\Driver\\' . $sClassName;
                $oDriverInstance = new $sDriverClass();

                if (!($oDriverInstance instanceof \Nails\OrderPayment\Driver\Base)) {

                    throw new DriverException(
                        'Driver "' . $oDriver->name . '" must extend \Nails\OrderPayment\Driver\Base',
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
