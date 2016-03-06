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

use Nails\Factory;
use Nails\Common\Model\Base;

class Customer extends Base
{
    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_customer';
        $this->tablePrefix       = 'c';
        $this->defaultSortColumn = 'first_name';
        $this->destructiveDelete = false;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all customers from the databases
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

            if (!empty($aData['includeAll']) || !empty($aData['includeInvoices'])) {
                $this->getManyAssociatedItems(
                    $aItems,
                    'invoices',
                    'customer_id',
                    'Invoice',
                    'nailsapp/module-invoice'
                );
            }
        }

        return $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats the business object
     * @param object $oObj An object containing business data
     * @return void
     */
    protected function formatObject(
        &$oObj,
        $aData = array(),
        $aIntegers = array(),
        $aBools = array(),
        $aFloats = array()
    ) {
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        //  Address
        $aAddress = array(
            $oObj->billing_address_line_1,
            $oObj->billing_address_line_2,
            $oObj->billing_address_town,
            $oObj->billing_address_county,
            $oObj->billing_address_postcode,
            $oObj->billing_address_country
        );
        $aAddress = array_filter($aAddress);

        $oObj->billing_address           = new \stdClass();
        $oObj->billing_address->line_1   = $oObj->billing_address_line_1;
        $oObj->billing_address->line_2   = $oObj->billing_address_line_2;
        $oObj->billing_address->town     = $oObj->billing_address_town;
        $oObj->billing_address->county   = $oObj->billing_address_county;
        $oObj->billing_address->postcode = $oObj->billing_address_postcode;
        $oObj->billing_address->country  = $oObj->billing_address_country;

        unset($oObj->billing_address_line_1);
        unset($oObj->billing_address_line_2);
        unset($oObj->billing_address_town);
        unset($oObj->billing_address_county);
        unset($oObj->billing_address_postcode);
        unset($oObj->billing_address_country);
    }
}
