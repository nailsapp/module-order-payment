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
use Nails\Common\Exception\ValidationException;
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
     * @param array $aData         The data array
     * @param bool  $bReturnObject Whether to return the new object or not
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

        //  If an expiry date is passed, ensure it is valid
        if (array_key_exists('expiry', $aData)) {
            try {
                $oExpiry = new \DateTime($aData['expiry']);
            } catch (\Exception $e) {
                throw new DriverException('"' . $aData['expiry'] . '" is not a valid expiry date.', null, $e);
            }

            $oNow = Factory::factory('DateTime');
            if ($oExpiry < $oNow) {
                throw new DriverException('"expiry" must be a future date.');
            }
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

        //  Ensure data is encoded to a string
        $oResource->data = json_encode($oResource->data);

        $aResource = (array) $oResource;
        unset($aResource['is_expired']);

        return parent::create($aResource, $bReturnObject);
    }

    // --------------------------------------------------------------------------

    /**
     * Updates an existing payment source
     *
     * @param int   $iId   The ID of the object to update
     * @param array $aData The data to update the object with
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function update($iId, array $aData = []): bool
    {
        //  @todo (Pablo - 2019-10-03) - Support passing updates to the driver
        return parent::update($iId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Deletes an existing payment source
     *
     * @param int $iId The ID of the object to deleted
     *
     * @return bool
     * @throws FactoryException
     * @throws ModelException
     */
    public function delete($iId): bool
    {
        //  @todo (Pablo - 2019-10-03) - Support passing deletions to the driver
        return parent::delete($iId);
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the default payment source for a customer
     *
     * @param Resource\Customer|int $mCustomer The customer object or ID
     *
     * @return Resource\Source|null
     * @throws FactoryException
     * @throws ValidationException
     */
    public function getDefault($mCustomer): ?Resource\Source
    {
        $iCustomerId = $this->getCustomerId($mCustomer, __METHOD__);
        $aSources    = $this->getAll([
            'where' => [
                ['customer_id', $iCustomerId],
                ['is_default', true],
            ],
        ]);

        return !empty($aSources) ? reset($aSources) : null;
    }

    // --------------------------------------------------------------------------

    /**
     * Sets the default payment source for a customer
     *
     * @param Resource\Customer|int $mCustomer The customer object or ID
     * @param Resource\Source|int   $mSource   The source object or ID
     *
     * @return bool
     * @throws FactoryException
     * @throws ValidationException
     */
    public function setDefault($mCustomer, $mSource): bool
    {
        $iCustomerId = $this->getCustomerId($mCustomer, __METHOD__);
        $iSourceId   = $this->getSourceId($mSource, __METHOD__);

        if (empty($iCustomerId)) {
            throw new ValidationException('Could not ascertain customer ID.');
        } elseif (empty($iSourceId)) {
            throw new ValidationException('Could not ascertain source ID.');
        }

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
     * @param Resource\Customer|int $mCustomer      The customer object or ID
     * @param bool                  $bRemoveExpired Whether to remove expired payment sources
     *
     * @return Resource\Source[]
     * @throws ModelException
     * @throws ValidationException
     */
    public function getForCustomer($mCustomer, bool $bRemoveExpired = true): array
    {
        $iCustomerId = $this->getCustomerId($mCustomer, __METHOD__);

        if (empty($iCustomerId)) {
            return [];
        }

        /** @var PaymentDriver $oPaymentDriver */
        $oPaymentDriver = Factory::service('PaymentDriver', Constants::MODULE_SLUG);

        return $this->getAll([
            'where'    => array_filter([
                ['customer_id', $iCustomerId],
                $bRemoveExpired ? '(expiry IS NULL OR expiry > CURDATE())' : null,
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
        $aIntegers[] = 'customer_id';
        $aBools[]    = 'is_default';
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the customer ID
     *
     * @param Resource\Customer|int $mCustomer The customer object or ID
     *
     * @return int|null
     * @throws ValidationException
     */
    protected function getCustomerId($mCustomer, $sMethod): ?int
    {
        if ($mCustomer instanceof Resource\Customer) {
            return $mCustomer->id;
        } elseif (is_int($mCustomer)) {
            return $mCustomer;
        } else {
            throw new ValidationException(
                'Invalid type "' . gettype($mCustomer) . '" for customer passed to ' . $sMethod
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the source ID
     *
     * @param Resource\Source|int $mSource The source object or ID
     *
     * @return int
     * @throws ValidationException
     */
    protected function getSourceId($mSource, $sMethod): int
    {
        if ($mSource instanceof Resource\Source) {
            return $mSource->id;
        } elseif (is_int($mSource)) {
            return $mSource;
        } else {
            throw new ValidationException(
                'Invalid type "' . gettype($mSource) . '" for source passed to ' . $sMethod
            );
        }
    }
}
