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
    /**
     * The Currency library
     * @var Nails\Currency\Library\Currency
     */
    protected $oCurrency;

    // --------------------------------------------------------------------------

    //  Statuses
    const STATUS_PENDING          = 'PENDING';
    const STATUS_PROCESSING       = 'PROCESSING';
    const STATUS_COMPLETE         = 'COMPLETE';
    const STATUS_FAILED           = 'FAILED';
    const STATUS_REFUNDED         = 'REFUNDED';
    const STATUS_REFUNDED_PARTIAL = 'REFUNDED_PARTIAL';

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_payment';
        $this->tableAlias        = 'p';
        $this->defaultSortColumn = 'created';
        $this->oCurrency         = Factory::service('Currency', 'nailsapp/module-currency');
        $this->searchableFields  = ['id', 'ref', 'description', 'txn_id'];
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
            self::STATUS_FAILED,
            self::STATUS_REFUNDED,
            self::STATUS_REFUNDED_PARTIAL
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
            self::STATUS_PENDING          => 'Pending',
            self::STATUS_PROCESSING       => 'Processing',
            self::STATUS_COMPLETE         => 'Complete',
            self::STATUS_FAILED           => 'Failed',
            self::STATUS_REFUNDED         => 'Refunded',
            self::STATUS_REFUNDED_PARTIAL => 'Partially Refunded'
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
        //  If the first value is an array then treat as if called with getAll(null, null, $aData);
        //  @todo (Pablo - 2017-11-09) - Convert these to expandable fields
        if (is_array($iPage)) {
            $aData = $iPage;
            $iPage = null;
        }

        $aItems = parent::getAll($iPage, $iPerPage, $aData, $bIncludeDeleted);

        if (is_array($iPage)) {

            $aData = $iPage;
            $iPage = null;
        }

        if (!empty($aItems)) {

            if (!empty($aData['includeAll']) || !empty($aData['includeInvoice'])) {
                $this->getSingleAssociatedItem(
                    $aItems,
                    'invoice_id',
                    'invoice',
                    'Invoice',
                    'nailsapp/module-invoice',
                    array(
                        'includeCustomer' => true,
                        'includeItems'    => true
                    )
                );
            }

            if (!empty($aData['includeAll']) || !empty($aData['includeRefunds'])) {
                $this->getManyAssociatedItems(
                    $aItems,
                    'refunds',
                    'payment_id',
                    'Refund',
                    'nailsapp/module-invoice'
                );
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
        $oRefundModel  = Factory::model('Refund', 'nailsapp/module-invoice');

        $oDb->select($this->tableAlias . '.*, i.ref invoice_ref, i.state invoice_state');

        $oDb->select('
            (
                SELECT
                    SUM(amount)
                FROM ' . $oRefundModel->getTableName() . ' r
                WHERE
                r.payment_id = ' . $this->tableAlias . '.id
                AND
                (
                    status = "' . $oRefundModel::STATUS_COMPLETE . '"
                    OR
                    status = "' . $oRefundModel::STATUS_PROCESSING . '"
                )
            ) amount_refunded
        ');
        $oDb->select('
            (
                SELECT
                    SUM(fee)
                FROM ' . $oRefundModel->getTableName() . ' r
                WHERE
                r.payment_id = ' . $this->tableAlias . '.id
                AND
                (
                    status = "' . $oRefundModel::STATUS_COMPLETE . '"
                    OR
                    status = "' . $oRefundModel::STATUS_PROCESSING . '"
                )
            ) fee_refunded
        ');

        $oDb->join($oInvoiceModel->getTableName() . ' i', $this->tableAlias . '.invoice_id = i.id');
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

            if (array_key_exists('custom_data', $aData)) {
                $aData['custom_data'] = json_encode($aData['custom_data']);
            }

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

            if (array_key_exists('custom_data', $aData)) {
                $aData['custom_data'] = json_encode($aData['custom_data']);
            }

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
     * @param  integer  $iPaymentId The payment to update
     * @param  array    $aData      Any additional data to save to the transaction
     * @return boolean
     */
    public function setPending($iPaymentId, $aData = array())
    {
        $aData['status'] = self::STATUS_PENDING;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PROCESSING
     * @param  integer  $iPaymentId The payment to update
     * @param  array    $aData      Any additional data to save to the transaction
     * @return boolean
     */
    public function setProcessing($iPaymentId, $aData = array())
    {
        $aData['status'] = self::STATUS_PROCESSING;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as COMPLETE
     * @param  integer  $iPaymentId The payment to update
     * @param  array    $aData      Any additional data to save to the transaction
     * @return boolean
     */
    public function setComplete($iPaymentId, $aData = array())
    {
        $aData['status'] = self::STATUS_COMPLETE;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as FAILED
     * @param  integer  $iPaymentId The payment to update
     * @param  array    $aData      Any additional data to save to the transaction
     * @return boolean
     */
    public function setFailed($iPaymentId, $aData = array())
    {
        $aData['status'] = self::STATUS_FAILED;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as REFUNDED
     * @param  integer  $iPaymentId The payment to update
     * @param  array    $aData      Any additional data to save to the transaction
     * @return boolean
     */
    public function setRefunded($iPaymentId, $aData = array())
    {
        $aData['status'] = self::STATUS_REFUNDED;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as REFUNDED_PARTIAL
     * @param  integer  $iPaymentId The payment to update
     * @param  array    $aData      Any additional data to save to the transaction
     * @return boolean
     */
    public function setRefundedPartial($iPaymentId, $aData = array())
    {
        $aData['status'] = self::STATUS_REFUNDED_PARTIAL;
        return $this->update($iPaymentId, $aData);
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

            } elseif (!empty($oPayment->invoice->customer->billing_email)) {

                $aEmails = explode(',', $oPayment->invoice->customer->billing_email);

            } elseif (!empty($oPayment->invoice->customer->email)) {

                $aEmails = array($oPayment->invoice->customer->email);

            } elseif (!empty($oPayment->invoice->email)) {

                $aEmails = array($oPayment->invoice->email);

            } else {

                throw new PaymentException('No email address to send the receipt to.', 1);
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

    public function refund($iPaymentId, $iAmount = null, $sReason = null)
    {
        try {

            //  Validate payment
            $oPayment = $this->getById($iPaymentId, array('includeInvoice' => true));
            if (!$oPayment) {
                throw new PaymentException('Invalid payment ID.', 1);
            }

            //  Set up RefundRequest object
            $oRefundRequest = Factory::factory('RefundRequest', 'nailsapp/module-invoice');

            //  Set the driver to use for the request
            $oRefundRequest->setDriver($oPayment->driver->slug);

            //  Describe the charge
            $oRefundRequest->setReason($sReason);

            //  Set the payment we're refunding against
            $oRefundRequest->setPayment($oPayment->id);

            //  Attempt the refund
            $oRefundResponse = $oRefundRequest->execute($iAmount);

            if ($oRefundResponse->isProcessing() || $oRefundResponse->isComplete()) {

                //  It's all good

            } elseif ($oRefundResponse->isFailed()) {

                /**
                 * Refund failed, throw an error which will be caught and displayed to the user
                 */

                throw new PaymentException(
                    'Refund failed: ' . $oRefundResponse->getError()->user,
                    1
                );

            } else {

                /**
                 * Something which we've not accounted for went wrong.
                 */

                throw new PaymentException('Refund failed.', 1);
            }

            return true;

        } catch (PaymentException $e) {

            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     * @return void
     */
    protected function formatObject(
        &$oObj,
        $aData = array(),
        $aIntegers = array(),
        $aBools = array(),
        $aFloats = array()
    ) {

        $aIntegers[] = 'invoice_id';
        $aIntegers[] = 'amount';
        $aIntegers[] = 'amount_refunded';
        $aIntegers[] = 'fee';
        $aIntegers[] = 'fee_refunded';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        //  Status
        $aStatuses = $this->getStatusesHuman();
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

        //  Currency
        $oCurrency      = $this->oCurrency->getByIsoCode($oObj->currency);
        $oObj->currency = $oCurrency;

        //  Amount
        $oObj->amount = (object) array(
            'raw'       => $oObj->amount,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->amount / pow(10, $oCurrency->decimal_precision)
            )
        );

        //  Amount refunded
        $oObj->amount_refunded = (object) array(
            'raw'       => $oObj->amount_refunded,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->amount_refunded / pow(10, $oCurrency->decimal_precision)
            )
        );

        //  Fee
        $oObj->fee = (object) array(
            'raw'       => $oObj->fee,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->fee / pow(10, $oCurrency->decimal_precision)
            )
        );

        //  Fee refunded
        $oObj->fee_refunded = (object) array(
            'raw'       => $oObj->fee_refunded,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->fee_refunded / pow(10, $oCurrency->decimal_precision)
            )
        );

        //  Available for refund
        $iAvailableForRefund = $oObj->amount->raw - $oObj->amount_refunded->raw;
        $oObj->available_for_refund = (object) array(
            'raw'       => $iAvailableForRefund,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $iAvailableForRefund / pow(10, $oCurrency->decimal_precision)
            )
        );

        //  Can this payment be refunded?
        $aValidStates  = array(
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_REFUNDED_PARTIAL
        );
        $oObj->is_refundable = in_array($oObj->status->id, $aValidStates) && $oObj->available_for_refund->raw > 0;

        //  URLs
        $oObj->urls             = new \stdClass();
        $oObj->urls->complete   = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/complete');
        $oObj->urls->thanks     = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/thanks');
        $oObj->urls->processing = site_url('invoice/payment/' . $oObj->id . '/' . $oObj->token . '/processing');
        $oObj->urls->continue   = !empty($oObj->url_continue) ? site_url($oObj->url_continue) : null;

        //  Custom data
        $oObj->custom_data = json_decode($oObj->custom_data);
    }
}
