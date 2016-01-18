<?php

/**
 * Payment model
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

class Payment extends Base
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

    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_payment';
        $this->tablePrefix       = 'p';
        $this->defaultSortColumn = 'created';
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all payments from the databases
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

            if (!empty($aData['includeAll']) || !empty($aData['includeInvoice'])) {
                $this->getSingleAssociatedItem($aItems, 'invoice_id', 'invoice', 'Invoice', 'nailsapp/module-invoice');
            }
        }

        return $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Retrive payments which relate to a particular set of invoice IDs
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

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param  array  $data    Data passed from the calling method
     * @return void
     **/
    protected function getCountCommon($data = array())
    {
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');

        $this->db->select($this->tablePrefix . '.*, i.ref invoice_ref, i.state invoice_state');
        $this->db->join($oInvoiceModel->getTableName() . ' i', $this->tablePrefix . '.invoice_id = i.id');
        parent::getCountCommon($data);
    }

    // --------------------------------------------------------------------------

    protected function formatObject($oObj, $aData = array())
    {
        parent::formatObject($oObj, $aData, array('invoice_id', 'amount', 'amount_base', 'fee', 'fee_base'));

        //  Driver
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nailsapp/module-invoice');
        $sDriver = $oObj->driver;
        $oDriver = $oPaymentDriverModel->getBySlug($sDriver);

        if (!empty($oDriver)) {

            $oObj->driver        = new \stdClass();
            $oObj->driver->slug  = $oDriver->slug;
            $oObj->driver->label = $oDriver->name;

        } else {

            $oObj->driver        = new \stdClass();
            $oObj->driver->slug  = $oObj->driver;
            $oObj->driver->label = $oObj->driver;
        }

        //  Amount
        $iAmount = $oObj->amount;
        $oObj->amount                      = new \stdClass();
        $oObj->amount->base                = $iAmount;
        $oObj->amount->localised           = (float) number_format($oObj->amount->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->amount->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->amount->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);

        //  Fee
        $iFee = $oObj->fee;
        $oObj->fee                      = new \stdClass();
        $oObj->fee->base                = $iFee;
        $oObj->fee->localised           = (float) number_format($oObj->fee->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
        $oObj->fee->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->fee->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
    }
}
