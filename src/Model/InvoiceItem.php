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
    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'invoice_invoice_item';
        $this->tablePrefix = 'io';
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

    protected function formatObject($oObj)
    {
        //  Totals
        $oObj->totals = new \stdClass();
        $oObj->totals->sub = $oObj->sub_total;
        $oObj->totals->tax = $oObj->tax_total;
        $oObj->totals->grand = $oObj->grand_total;
        unset($oObj->sub_total);
        unset($oObj->tax_total);
        unset($oObj->grand_total);
    }
}
