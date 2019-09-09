<?php

/**
 * This model handles interactions with the app's "nails_invoice_source" table.
 *
 * @package  Nails\Invoice\Model
 * @category model
 */

namespace Nails\Invoice\Model;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Common\Service\Database;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Driver\PaymentBase;
use Nails\Invoice\Exception\DriverException;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Resource;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Source
 *
 * @package Nails\Invoice\Model
 */
class Source extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'invoice_source';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Source';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * Creates a new payment source. Delegates to the payment driver.
     *
     * @param array $aData         the data array
     * @param bool  $bReturnObject Whether top return the new object or not
     *
     * @return mixed
     * @throws DriverException
     * @throws FactoryException
     * @throws ModelException
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        if (!array_key_exists('driver', $aData)) {
            throw new DriverException('"driver" is a required field');
        } elseif (!array_key_exists('customer_id', $aData)) {
            throw new DriverException('"customer_id" is a required field');
        }

        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);
        /** @var PaymentBase $oDriver */
        $oDriver = $oPaymentDriverService->getInstance($aData['driver']);

        if (empty($oDriver)) {
            throw new DriverException('"' . $aData['driver'] . '" is not a valid payment driver.');
        }

        /** @var Resource\Source $oResource */
        $oResource = Factory::resource('Source', Constants::MODULE_SLUG, [
            'customer_id' => $aData['customer_id'],
            'driver'      => $aData['driver'],
        ]);

        unset($aData['driver']);
        unset($aData['customer_id']);

        $oDriver->createSource($oResource, $aData);

        if (empty($oResource->label) && !empty($oResource->brand) && !empty($oResource->last_four)) {
            $oResource->label = $oResource->brand . ' ending ' . $oResource->last_four;
        } elseif (empty($oResource->label)) {
            $oResource->label = 'Payment Source';
        }

        return parent::create((array) $oResource, $bReturnObject);
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the default payment source for a customer
     *
     * @param int $iCustomerId The customer ID
     * @param int $iSourceId   The source ID
     *
     * @return bool
     * @throws FactoryException
     */
    public function setDefault(int $iCustomerId, int $iSourceId): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        $oDb->trans_begin();
        try {

            $oDb->set('is_default', false);
            $oDb->where('customer_id', $iCustomerId);
            $oDb->where('id !=', $iSourceId);
            if (!$oDb->update($this->getTableName())) {
                throw new InvoiceException(
                    'Failed to set default payment source; could not unset previous sources.'
                );
            }

            $oDb->set('is_default', true);
            $oDb->where('customer_id', $iCustomerId);
            $oDb->where('id', $iSourceId);
            if (!$oDb->update($this->getTableName())) {
                throw new InvoiceException(
                    'Failed to set default payment source; could not set desired source.'
                );
            }

            $oDb->trans_commit();

            return true;

        } catch (\Exception $e) {
            $oDb->trans_rollback();
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns payment sources for a particular customer
     *
     * @param int|null $iCustomerId    The customer ID
     * @param bool     $bRemoveExpired Whether to remove expired payment sources
     *
     * @return Source[]
     * @throws ModelException
     */
    public function getForCustomer(int $iCustomerId = null, bool $bRemoveExpired = true)
    {
        if (empty($iCustomerId)) {
            return [];
        }

        /** @var PaymentDriver $oPaymentDriver */
        $oPaymentDriver = Factory::service('PaymentDriver', Constants::MODULE_SLUG);

        return $this->getAll([
            'where'    => array_filter([
                ['customer_id', $iCustomerId],
                $bRemoveExpired ? ['expiry > ', 'CURDATE()', false] : null,
            ]),
            'where_in' => [
                ['driver', $oPaymentDriver->getEnabledSlug()],
            ],
            'sort'     => [
                ['is_default', 'desc'],
                ['created', 'desc'],
            ],
        ]);
    }

    // --------------------------------------------------------------------------

    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);
        $oObj->expiry = Factory::resource('Date', null, ['raw' => $oObj->expiry]);
    }
}
