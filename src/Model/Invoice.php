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

    /**
     * The various item quantity units
     */
    const ITEM_UNIT_NONE  = 'NONE';
    const ITEM_UNIT_HOUR  = 'HOUR';
    const ITEM_UNIT_DAY   = 'DAY';
    const ITEM_UNIT_WEEK  = 'WEEK';
    const ITEM_UNIT_MONTH = 'MONTH';
    const ITEM_UNIT_YEAR  = 'YEAR';

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
            self::STATE_DRAFT          => 'Draft',
            self::STATE_OPEN           => 'Open',
            self::STATE_PARTIALLY_PAID => 'Partially Paid',
            self::STATE_PAID           => 'Paid',
            self::STATE_WRITTEN_OFF    => 'Written Off'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the item quantity units with human friendly names
     * @return array
     */
    public function getItemUnits()
    {
        return array(
            self::ITEM_UNIT_NONE  => 'None',
            self::ITEM_UNIT_HOUR  => 'Hours',
            self::ITEM_UNIT_DAY   => 'Days',
            self::ITEM_UNIT_WEEK  => 'Weeks',
            self::ITEM_UNIT_MONTH => 'Months',
            self::ITEM_UNIT_YEAR  => 'Years'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all invoices from the databases
     * @param  int     $page           The page number to return
     * @param  int     $perPage        The number of results per page
     * @param  array   $data           Data to pass _to getcount_common()
     * @param  boolean $includeDeleted Whether to include deleted results
     * @return array
     */
    public function getAll($page = null, $perPage = null, $data = array(), $includeDeleted = false)
    {
        $aInvoices = parent::getAll($page, $perPage, $data, $includeDeleted);

        if ($aInvoices) {

            //  Get the ID's of all the returned invoices
            $aInvoiceIds = array();
            foreach ($aInvoices as $oInvoice) {
                $aInvoiceIds[] = $oInvoice->id;
            }

            //  Get line items for returned invoices
            $this->db->select('id,invoice_id,label,body');
            $this->db->where_in('invoice_id', $aInvoiceIds);
            $this->db->order_by('order');
            $aItems = $this->db->get($this->tableItem)->result();

            //  Get payments for returned invoices
            $aPayments = $this->oPaymentModel->getForInvoices($aInvoiceIds);

            //  Merge line items into the resultset and convert into an Invoice object
            foreach ($aInvoices as $oInvoice) {

                //  Line items
                $oInvoice->items = array();
                foreach ($aItems as $oItem) {
                    if ($oItem->invoice_id == $oInvoice->id) {
                        $oInvoice->items[] = $oItem;
                    }
                }

                //  Payments
                $oInvoice->payments = array();
                foreach ($aPayments as $oPayment) {
                    if ($oPayment->invoice_id == $oInvoice->id) {
                        $oInvoice->payments[] = $oPayment;
                    }
                }
            }
        }

        return $aInvoices;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a valid invoice ref
     * @return string
     */
    public function generateValidRef()
    {
        Factory::helper('string');

        $oNow = Factory::factory('DateTime');

        do {

            $sRef = $oNow->format('Ym') .'-' . strtoupper(random_string('alnum'));
            $this->db->where('ref', $sRef);
            $bRefExists = (bool) $this->db->count_all_results($this->table);

        } while ($bRefExists);

        return $sRef;
    }
}
