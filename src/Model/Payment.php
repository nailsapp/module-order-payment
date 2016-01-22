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
    //  Statuses
    const STATUS_PENDING = 'PENDING';
    const STATUS_OK      = 'OK';
    const STATUS_FAILED  = 'FAILED';

    // --------------------------------------------------------------------------

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
     * Construct the model
     */
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

    /**
     * Create a new payment
     * @param  array   $aData         The data to create the payment with
     * @param  boolean $bReturnObject Whether to return the complete payment object
     * @return mixed
     */
    public function create($aData = array(), $bReturnObject = false)
    {
        try {

            $this->db->trans_begin();

            $aData['token'] = $this->generateValidToken();

            $oPayment = parent::create($aData, true);

            if (!$oPayment) {
                throw new \Exception('Failed to create payment.', 1);
            }

            $this->db->trans_commit();

            //  Trigger the payment.created event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger($sPaymentClass::EVENT_PAYMENT_CREATED, $oPayment);

            return $bReturnObject ? $oPayment : $oPayment->id;

        } catch (\Exception $e) {

            $this->db->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update a payment
     * @param  integer $iPaymentId The ID of the payment to update
     * @param  array   $aData      The data to update the payment with
     * @return boolean
     */
    public function update($iPaymentId, $aData = array())
    {
        try {

            $this->db->trans_begin();

            unset($aData['token']);

            $bResult = parent::update($iPaymentId, $aData);

            if (!$bResult) {
                throw new \Exception('Failed to update payment.', 1);
            }

            $this->db->trans_commit();

            //  Trigger the payment.updated event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger(
                $sPaymentClass::EVENT_PAYMENT_UPDATED,
                $this->getById($iPaymentId)
            );

            return $bResult;

        } catch (\Exception $e) {

            $this->db->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a valid payment token
     * @return string
     */
    public function generateValidToken()
    {
        Factory::helper('string');

        do {

            //  @todo: use more secure token generation, like random_bytes();
            $sToken = md5(microtime(true) . APP_PRIVATE_KEY);
            $this->db->where('token', $sToken);
            $bTokenExists = (bool) $this->db->count_all_results($this->table);

        } while ($bTokenExists);

        return $sToken;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as OK
     * @param  integer  $iPaymentId The Payment to update
     * @return boolean
     */
    public function setOk($iPaymentId)
    {
        return $this->update(
            $iPaymentId,
            array(
                'state' => self::STATUS_OK
            )
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as FAILED
     * @param  integer  $iPaymentId The Payment to update
     * @return boolean
     */
    public function setFailed($iPaymentId)
    {
        return $this->update(
            $iPaymentId,
            array(
                'state' => self::STATUS_FAILED
            )
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Format a payment object
     * @param  \stdClass $oObj  The object to format
     * @param  array     $aData Any data passed to getAll
     * @return void
     */
    protected function formatObject($oObj, $aData = array())
    {
        parent::formatObject($oObj, $aData, array('invoice_id', 'amount'));

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

        //  URLs
        $oObj->urls           = new \stdClass();
        $oObj->urls->complete = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/complete');
    }
}
