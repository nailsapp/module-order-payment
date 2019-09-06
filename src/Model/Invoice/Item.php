<?php

/**
 * Invoice Item model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model\Invoice;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;

/**
 * Class Item
 *
 * @package Nails\Invoice\Model\Invoice
 */
class Item extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'invoice_invoice_item';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'InvoiceItem';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * The Currency service
     *
     * @var Currency\Service\Currency
     */
    protected $oCurrency;

    // --------------------------------------------------------------------------

    /**
     * The various item quantity units
     */
    const UNIT_NONE   = 'NONE';
    const UNIT_MINUTE = 'MINUTE';
    const UNIT_HOUR   = 'HOUR';
    const UNIT_DAY    = 'DAY';
    const UNIT_WEEK   = 'WEEK';
    const UNIT_MONTH  = 'MONTH';
    const UNIT_YEAR   = 'YEAR';

    // --------------------------------------------------------------------------

    /**
     * Item constructor.
     *
     * @throws FactoryException
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this->defaultSortColumn = 'order';
        $this->oCurrency         = Factory::service('Currency', Currency\Constants::MODULE_SLUG);
        $this
            ->addExpandableField([
                'trigger'   => 'invoice',
                'model'     => 'Invoice',
                'provider'  => Constants::MODULE_SLUG,
                'id_column' => 'invoice_id',
            ])
            ->addExpandableField([
                'trigger'     => 'tax',
                'model'       => 'Tax',
                'provider'    => Constants::MODULE_SLUG,
                'id_column'   => 'tax_id',
                'auto_expand' => true,
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item quantity units with human friendly names
     *
     * @return string[]
     */
    public function getUnits()
    {
        return [
            self::UNIT_NONE   => 'None',
            self::UNIT_MINUTE => 'Minutes',
            self::UNIT_HOUR   => 'Hours',
            self::UNIT_DAY    => 'Days',
            self::UNIT_WEEK   => 'Weeks',
            self::UNIT_MONTH  => 'Months',
            self::UNIT_YEAR   => 'Years',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve items which relate to a particular set of invoice IDs
     *
     * @param array $aInvoiceIds The invoice IDs
     *
     * @return array
     */
    public function getForInvoices($aInvoiceIds)
    {
        return $this->getAll([
            'where_in' => [
                ['invoice_id', $aInvoiceIds],
            ],
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param object $oObj      A reference to the object being formatted.
     * @param array  $aData     The same data array which is passed to getCountCommon, for reference if needed
     * @param array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param array  $aBools    Fields which should be cast as booleans if not null
     * @param array  $aFloats   Fields which should be cast as floats if not null
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
        $aIntegers[] = 'invoice_id';
        $aIntegers[] = 'tax_id';
        $aIntegers[] = 'unit_cost';

        $aFloats[] = 'quantity';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        //  Currency
        $oCurrency = $this->oCurrency->getByIsoCode($oObj->currency);
        unset($oObj->currency);

        //  Unit cost
        $oObj->unit_cost = (object) [
            'raw'       => $oObj->unit_cost,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->unit_cost / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Totals
        $oObj->totals = (object) [
            'raw'       => (object) [
                'sub'   => (int) $oObj->sub_total,
                'tax'   => (int) $oObj->tax_total,
                'grand' => (int) $oObj->grand_total,
            ],
            'formatted' => (object) [
                'sub'   => $this->oCurrency->format(
                    $oCurrency->code, $oObj->sub_total / pow(10, $oCurrency->decimal_precision)
                ),
                'tax'   => $this->oCurrency->format(
                    $oCurrency->code, $oObj->tax_total / pow(10, $oCurrency->decimal_precision)
                ),
                'grand' => $this->oCurrency->format(
                    $oCurrency->code, $oObj->grand_total / pow(10, $oCurrency->decimal_precision)
                ),
            ],
        ];

        unset($oObj->sub_total);
        unset($oObj->tax_total);
        unset($oObj->grand_total);

        //  Units
        $sUnit  = $oObj->unit;
        $aUnits = $this->getUnits();

        $oObj->unit = (object) [
            'id'    => $sUnit,
            'label' => $aUnits[$sUnit],
        ];

        //  Callback data
        $oObj->callback_data = json_decode($oObj->callback_data);
    }
}
