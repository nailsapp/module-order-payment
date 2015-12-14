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
}
