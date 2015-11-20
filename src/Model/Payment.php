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
    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'invoice_payment';
        $this->tablePrefix = 'p';
    }

    // --------------------------------------------------------------------------

    /**
     * Retrive payments which relate to a aprticular set of invoice IDs
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

        return $this->get_all(null, null, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all payments from the databases
     * @param  int     $page           The page number to return
     * @param  int     $perPage        The number of results per page
     * @param  array   $data           Data to pass _to getcount_common()
     * @param  boolean $includeDeleted Whether to include deleted results
     * @param  string  $_caller        Internal flag of which emthod called this method
     * @return array
     */
    public function get_all($page = null, $perPage = null, $data = array(), $includeDeleted = false, $_caller = 'GET_ALL')
    {
        $aResults = parent::get_all($page, $perPage, $data, $includeDeleted, $_caller);
        $aOut     =  array();

        //  Merge line items into the resultset and convert into an Invoice object
        foreach ($aResults as $oRow) {

            $oPayment = Factory::factory('Payment', 'nailsapp/module-invoice');
            $oPayment->init($oRow);
            $aOut[] = $oPayment;
        }
        return $aOut;
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     * @param  array  $data    Data passed from the calling method
     * @param  string $_caller The name of the calling method
     * @return void
     **/
    protected function _getcount_common($data = array(), $_caller = null)
    {
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');

        $this->db->select($this->tablePrefix . '.*, i.ref invoice_ref, i.state invoice_state');
        $this->db->join($oInvoiceModel->getTableName() . ' i', $this->tablePrefix . '.invoice_id = i.id');
        parent::_getcount_common($data, $_caller);
    }
}
