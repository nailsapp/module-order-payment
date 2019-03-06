<?php

/**
 * Manages customer entities
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Common\Model\Base;
use Nails\Invoice\Exception\InvoiceException;

class Customer extends Base
{
    /**
     * Customer constructor.
     *
     * @throws \Nails\Common\Exception\ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this->table              = NAILS_DB_PREFIX . 'invoice_customer';
        $this->defaultSortColumn  = 'first_name';
        $this->destructiveDelete  = false;
        $this->searchableFields[] = 'email';
        $this->searchableFields[] = 'billing_email';

        $this->addExpandableField([
            'trigger'   => 'invoices',
            'type'      => self::EXPANDABLE_TYPE_MANY,
            'property'  => 'invoices',
            'model'     => 'Invoice',
            'provider'  => 'nails/module-invoice',
            'id_column' => 'customer_id',
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     *
     * @param array $aData Data passed from the calling method
     *
     * @return void
     */
    protected function getCountCommon(array $aData = [])
    {
        //  If there's a search term, then we better get %LIKING%
        if (!empty($aData['keywords'])) {

            if (empty($aData['or_like'])) {
                $aData['or_like'] = [];
            }

            $iKeywordAsId = (int) preg_replace('/[^0-9]/', '', $aData['keywords']);

            if ($iKeywordAsId) {
                $aData['or_like'][] = [
                    'column' => $this->getTableAlias() . '.id',
                    'value'  => $iKeywordAsId,
                ];
            }

            $aData['or_like'][] = [
                'column' => $this->getTableAlias() . '.label',
                'value'  => $aData['keywords'],
            ];
        }

        // --------------------------------------------------------------------------

        //  Let the parent method handle sorting, etc
        parent::getCountCommon($aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new customer
     *
     * @param  array   $aData         The data to create the customer with
     * @param  boolean $bReturnObject Whether to return the complete customer object
     *
     * @return mixed
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        try {

            if (empty($aData['organisation']) && empty($aData['first_name']) && empty($aData['last_name'])) {
                throw new InvoiceException('"organisation", "first_name" or "last_name" must be supplied.', 1);
            }

            //  Compile the label
            $aData['label'] = $this->compileLabel($aData);

            return parent::create($aData, $bReturnObject);

        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update an existing customer
     *
     * @param  integer $iCustomerId The ID of the customer to update
     * @param  array   $aData       The data to update the customer with
     *
     * @return mixed
     */
    public function update($iCustomerId, array $aData = [])
    {
        try {

            $sKeyExistsLabel = array_key_exists('label', $aData);
            $sKeyExistsOrg   = array_key_exists('organisation', $aData);
            $sKeyExistsFirst = array_key_exists('first_name', $aData);
            $sKeyExistsLast  = array_key_exists('last_name', $aData);

            if ($sKeyExistsOrg && $sKeyExistsFirst && $sKeyExistsLast) {
                if (empty($aData['organisation']) && empty($aData['first_name']) && empty($aData['last_name'])) {
                    throw new InvoiceException('"organisation", "first_name" and "last_name" cannot all be empty.', 1);
                }
            }

            //  Only compile the label if the label isn't defined and any of the other fields are present
            if (!$sKeyExistsLabel && ($sKeyExistsOrg || $sKeyExistsFirst || $sKeyExistsLast)) {
                $aData['label'] = $this->compileLabel($aData);
            }

            return parent::update($iCustomerId, $aData);

        } catch (\Exception $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the customer label
     *
     * @param  array $aData The data passed to create() or update()
     *
     * @return string
     */
    protected function compileLabel($aData)
    {
        if (!empty($aData['organisation'])) {
            return trim($aData['organisation']);
        } else {
            return implode(
                ' ',
                array_filter([
                    !empty($aData['first_name']) ? trim($aData['first_name']) : '',
                    !empty($aData['last_name']) ? trim($aData['last_name']) : '',
                ])
            );
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to _getCountCommon, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        $oObj->billing_address = (object) [
            'line_1'   => $oObj->billing_address_line_1,
            'line_2'   => $oObj->billing_address_line_2,
            'town'     => $oObj->billing_address_town,
            'county'   => $oObj->billing_address_county,
            'postcode' => $oObj->billing_address_postcode,
            'country'  => $oObj->billing_address_country,
        ];

        unset($oObj->billing_address_line_1);
        unset($oObj->billing_address_line_2);
        unset($oObj->billing_address_town);
        unset($oObj->billing_address_county);
        unset($oObj->billing_address_postcode);
        unset($oObj->billing_address_country);
    }
}
