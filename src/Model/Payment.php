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
use Nails\Invoice\Exception\PaymentException;

class Payment extends Base
{
    //  Statuses
    const STATUS_PENDING    = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_COMPLETE   = 'COMPLETE';
    const STATUS_FAILED     = 'FAILED';

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
     * Returns all the statuses as an array
     * @return array
     */
    public function getStatuses()
    {
        return array(
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_FAILED
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of statsues with human friendly labels
     * @return array
     */
    public function getStatusesHuman()
    {
        return array(
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETE   => 'Complete',
            self::STATUS_FAILED     => 'Failed'
        );
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
        $oDb           = Factory::service('Database');
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');

        $oDb->select($this->tablePrefix . '.*, i.ref invoice_ref, i.state invoice_state');
        $oDb->join($oInvoiceModel->getTableName() . ' i', $this->tablePrefix . '.invoice_id = i.id');
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
        $oDb = Factory::service('Database');

        try {

            $oDb->trans_begin();

            if (empty($aData['ref'])) {
                $aData['ref'] = $this->generateValidRef();
            }

            $aData['token'] = $this->generateValidToken();

            $oPayment = parent::create($aData, true);

            if (!$oPayment) {
                throw new PaymentException('Failed to create payment.', 1);
            }

            $oDb->trans_commit();

            //  Trigger the payment.created event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger($sPaymentClass::EVENT_PAYMENT_CREATED, $oPayment);

            return $bReturnObject ? $oPayment : $oPayment->id;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
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
        $oDb = Factory::service('Database');

        try {

            $oDb->trans_begin();

            unset($aData['ref']);
            unset($aData['token']);

            $bResult = parent::update($iPaymentId, $aData);

            if (!$bResult) {
                throw new PaymentException('Failed to update payment.', 1);
            }

            $oDb->trans_commit();

            //  Trigger the payment.updated event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger(
                $sPaymentClass::EVENT_PAYMENT_UPDATED,
                $this->getById($iPaymentId)
            );

            return $bResult;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a valid invoice ref
     * @return string
     */
    public function generateValidRef()
    {
        Factory::helper('string');

        $oDb  = Factory::service('Database');
        $oNow = Factory::factory('DateTime');

        do {

            $sRef = $oNow->format('Ym') .'-' . strtoupper(random_string('alnum'));
            $oDb->where('ref', $sRef);
            $bRefExists = (bool) $oDb->count_all_results($this->table);

        } while ($bRefExists);

        return $sRef;
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a valid payment token
     * @return string
     */
    public function generateValidToken()
    {
        $oDb = Factory::service('Database');

        do {

            //  @todo: use more secure token generation, like random_bytes();
            $sToken = md5(microtime(true) . APP_PRIVATE_KEY);
            $oDb->where('token', $sToken);
            $bTokenExists = (bool) $oDb->count_all_results($this->table);

        } while ($bTokenExists);

        return $sToken;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PENDING
     * @param  integer  $iPaymentId The Payment to update
     * @return boolean
     */
    public function setPending($iPaymentId)
    {
        return $this->update(
            $iPaymentId,
            array(
                'state' => self::STATUS_PENDING
            )
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PROCESSING
     * @param  integer  $iPaymentId The Payment to update
     * @return boolean
     */
    public function setProcessing($iPaymentId)
    {
        return $this->update(
            $iPaymentId,
            array(
                'state' => self::STATUS_PROCESSING
            )
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as COMPLETE
     * @param  integer  $iPaymentId The Payment to update
     * @return boolean
     */
    public function setComplete($iPaymentId)
    {
        return $this->update(
            $iPaymentId,
            array(
                'state' => self::STATUS_COMPLETE
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
     * Send payment receipt
     * @param  integer $iPaymentId     The ID of the payment
     * @param  string  $sEmailOverride Send to this email instead of the email defined by the invoice object
     * @return boolean
     */
    public function sendReceipt($iPaymentId, $sEmailOverride = null)
    {
        try {

            $oPayment = $this->getById($iPaymentId, array('includeInvoice' => true));

            if (empty($oPayment)) {
                throw new PaymentException('Invalid Payment ID', 1);
            }

            if (!in_array($oPayment->status->id, array(self::STATUS_PROCESSING, self::STATUS_COMPLETE))) {
                throw new PaymentException('Payment must be in a paid or processing state to send receipt.', 1);
            }

            $oEmail = new \stdClass();

            if ($oPayment->status->id == self::STATUS_COMPLETE) {

                $oEmail->type = 'payment_complete_receipt';

            } else {

                $oEmail->type = 'payment_processing_receipt';
            }

            $oEmail->data = array(
                'payment' => $oPayment
            );

            if (!empty($sEmailOverride)) {

                //  @todo, validate email address (or addresses if an array)
                $aEmails = explode(',', $sEmailOverride);

            } elseif (!empty($oPayment->invoice->user_email)) {

                $aEmails = explode(',', $oPayment->invoice->user_email);

            } elseif (!empty($oPayment->invoice->user->email)) {

                $aEmails = array($oPayment->invoice->user->email);

            } else {

                throw new PaymentException('No email address to send the invoice to.', 1);
            }

            $aEmails = array_unique($aEmails);
            $aEmails = array_filter($aEmails);

            $oEmailer           = Factory::service('Emailer', 'nailsapp/module-email');
            $oInvoiceEmailModel = Factory::model('InvoiceEmail', 'nailsapp/module-invoice');

            foreach ($aEmails as $sEmail) {

                $oEmail->to_email = $sEmail;
                $oResult = $oEmailer->send($oEmail);

                if (!empty($oResult)) {

                    $oInvoiceEmailModel->create(
                        array(
                            'invoice_id' => $oPayment->invoice->id,
                            'email_id'   => $oResult->id,
                            'email_type' => $oEmail->type,
                            'recipient'  => $oEmail->to_email
                        )
                    );

                } else {

                    throw new PaymentException($oEmailer->lastError(), 1);
                }
            }

        } catch (\Exception $e) {

            $this->setError($e->getMessage());
            return false;
        }

        return true;
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

        //  Status
        $aStatuses = $this->getStatuses();
        $sStatus   = $oObj->status;

        $oObj->status        = new \stdClass();
        $oObj->status->id    = $sStatus;
        $oObj->status->label = !empty($aStatuses[$sStatus]) ? $aStatuses[$sStatus] : ucfirst(strtolower($sStatus));

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
        $oObj->urls             = new \stdClass();
        $oObj->urls->complete   = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/complete');
        $oObj->urls->thanks     = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/thanks');
        $oObj->urls->processing = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/processing');
        $oObj->urls->continue   = !empty($oObj->url_continue) ? site_url($oObj->url_continue) : null;
    }
}
