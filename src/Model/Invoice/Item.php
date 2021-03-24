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

    /**
     * The default column to sort on
     *
     * @var string|null
     */
    const DEFAULT_SORT_COLUMN = 'order';

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
        $this->oCurrency = Factory::service('Currency', Currency\Constants::MODULE_SLUG);
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
            self::UNIT_MINUTE => 'Minute',
            self::UNIT_HOUR   => 'Hour',
            self::UNIT_DAY    => 'Day',
            self::UNIT_WEEK   => 'Week',
            self::UNIT_MONTH  => 'Month',
            self::UNIT_YEAR   => 'Year',
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
        $aIntegers[] = 'order';
        $aIntegers[] = 'tax_id';
        $aIntegers[] = 'unit_cost';

        $aFloats[] = 'quantity';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);
    }
}
