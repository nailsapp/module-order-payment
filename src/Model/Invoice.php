<?php

/**
 * Invoice model
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

class Invoice extends Base
{
    /**
     * The table where line items are stored
     * @var string
     */
    protected $tableItem;

    /**
     * A reference to the payment model
     * @var Object
     */
    protected $oPaymentModel;

    /**
     * The various states that an invoice can be in
     */
    const STATE_DRAFT          = 'DRAFT';
    const STATE_OPEN           = 'OPEN';
    const STATE_PARTIALLY_PAID = 'PARTIALLY_PAID';
    const STATE_PAID           = 'PAID';
    const STATE_WRITTEN_OFF    = 'WRITTEN_OFF';

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table       = NAILS_DB_PREFIX . 'invoice_invoice';
        $this->tablePrefix = 'i';
        $this->tableItem   = NAILS_DB_PREFIX . 'invoice_invoice_item';

        $this->oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the invoice states with human friendly names
     * @return array
     */
    public function getStates()
    {
        return array(
            self::STATE_DRAFT => 'Draft',
            self::STATE_OPEN => 'Open',
            self::STATE_PARTIALLY_PAID => 'Partially Paid',
            self::STATE_PAID => 'Paid',
            self::STATE_WRITTEN_OFF => 'Written Off',
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all invoices from the databases
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

        if ($aResults) {
            //  Get the ID's of all the returned invoices
            $aInvoiceIds = array();
            foreach ($aResults as $oRow) {
                $aInvoiceIds[] = $oRow->id;
            }

            //  Get line items for returned invoices
            $this->db->select('id,invoice_id,label,body');
            $this->db->where_in('invoice_id', $aInvoiceIds);
            $this->db->order_by('order');
            $aItems = $this->db->get($this->tableItem)->result();

            //  Get payments for returned invoices
            $aPayments = $this->oPaymentModel->getForInvoices($aInvoiceIds);

            //  Merge line items into the resultset and convert into an Invoice object
            foreach ($aResults as $oRow) {

                //  Line items
                $oRow->items = array();
                foreach ($aItems as $oItemRow) {
                    if ($oItemRow->invoice_id == $oRow->id) {
                        $oItem = Factory::factory('InvoiceItem', 'nailsapp/module-invoice');
                        $oItem->init($oItemRow);
                        $oRow->items[] = $oItem;
                    }
                }

                //  Payments
                $oRow->payments = array();
                foreach ($aPayments as $oPayment) {
                    if ($oPayment->invoice_id == $oRow->id) {
                        $oRow->payments[] = $oPayment;
                    }
                }

                $oInvoice = Factory::factory('Invoice', 'nailsapp/module-invoice');
                $oInvoice->init($oRow);
                $aOut[] = $oInvoice;
            }
        }

        return $aOut;
    }
}
