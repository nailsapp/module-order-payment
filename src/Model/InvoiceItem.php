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

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Common\Model\Base;

class InvoiceItem extends Base
{
    /**
     * Currency values
     * @todo  make this way more dynamic
     */
    const CURRENCY_DECIMAL_PLACES = 2;
    const CURRENCY_CODE           = 'GBP';
    const CURRENCY_SYMBOL_HTML    = '&pound;';
    const CURRENCY_SYMBOL_TEXT    = '£';
    const CURRENCY_LOCALISE_VALUE = 100;

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

    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'invoice_invoice_item';
        $this->tablePrefix = 'io';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item quantity units with human friendly names
     * @return array
     */
    public function getUnits()
    {
        return array(
            self::UNIT_NONE   => 'None',
            self::UNIT_MINUTE => 'Minutes',
            self::UNIT_HOUR   => 'Hours',
            self::UNIT_DAY    => 'Days',
            self::UNIT_WEEK   => 'Weeks',
            self::UNIT_MONTH  => 'Months',
            self::UNIT_YEAR   => 'Years'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all items from the databases
     * @param  int     $iPage           The page number to return
     * @param  int     $iPerPage        The number of results per page
     * @param  array   $aData           Data to pass _to getcount_common()
     * @param  boolean $bIncludeDeleted Whether to include deleted results
     * @return array
     */
    public function getAll($iPage = null, $iPerPage = null, $aData = array(), $bIncludeDeleted = false)
    {
        $aItems = parent::getAll($iPage, $iPerPage, $aData, $bIncludeDeleted);

        if (!empty($aItems)) {
            $this->getsingleAssociatedItem($aItems, 'tax_id', 'tax', 'Tax', 'nailsapp/module-invoice');
        }

        return $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrive items which relate to a particular set of invoice IDs
     * @param  array $aInvoiceIds The invoice IDs
     * @return array
     */
    public function getForInvoices($aInvoiceIds)
    {
        $aData = array(
            'where_in' => array(
                array('invoice_id', $aInvoiceIds)
            )
        );

        return $this->getAll(null, null, $aData);
    }

    // --------------------------------------------------------------------------

    protected function formatObject($oObj, $aData)
    {
        parent::formatObject($oObj, $aData, array('invoice_id', 'tax_id', 'unit_cost'), array(), array('quantity'));

        //  Unit Cost
        $iUnitCost = $oObj->unit_cost;
        $oObj->unit_cost                      = new \stdClass();
        $oObj->unit_cost->base                = $iUnitCost;
        $oObj->unit_cost->localised           = (float) number_format($oObj->unit_cost->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->unit_cost->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->unit_cost->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);

        //  Totals
        $oObj->totals              = new \stdClass();
        $oObj->totals->base        = new \stdClass();
        $oObj->totals->base->sub   = (int) $oObj->sub_total;
        $oObj->totals->base->tax   = (int) $oObj->tax_total;
        $oObj->totals->base->grand = (int) $oObj->grand_total;

        //  Localise to the User's preference; perform any currency conversions as required
        $oObj->totals->localised        = new \stdClass();
        $oObj->totals->localised->sub   = (float) number_format($oObj->totals->base->sub/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->totals->localised->tax   = (float) number_format($oObj->totals->base->tax/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->totals->localised->grand = (float) number_format($oObj->totals->base->grand/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);

        $oObj->totals->localised_formatted        = new \stdClass();
        $oObj->totals->localised_formatted->sub   = self::CURRENCY_SYMBOL_HTML . number_format($oObj->totals->base->sub/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->totals->localised_formatted->tax   = self::CURRENCY_SYMBOL_HTML . number_format($oObj->totals->base->tax/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->totals->localised_formatted->grand = self::CURRENCY_SYMBOL_HTML . number_format($oObj->totals->base->grand/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);

        unset($oObj->sub_total);
        unset($oObj->tax_total);
        unset($oObj->grand_total);

        //  Units
        $sUnit  = $oObj->unit;
        $aUnits = $this->getUnits();

        $oObj->unit        = new \stdClass();
        $oObj->unit->id    = $sUnit;
        $oObj->unit->label = $aUnits[$sUnit];
    }
}
