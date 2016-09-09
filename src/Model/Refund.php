<?php

/**
 * Payment Refund model
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

class Refund extends Base
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
        $this->table             = NAILS_DB_PREFIX . 'invoice_refund';
        $this->tableAlias       = 'pr';
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

            if (!empty($aData['includeAll']) || !empty($aData['includePayment'])) {
                $this->getSingleAssociatedItem(
                    $aItems,
                    'payment_id',
                    'payment',
                    'Payment',
                    'nailsapp/module-invoice'
                );
            }
        }

        return $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new refund
     * @param  array   $aData         The data to create the refund with
     * @param  boolean $bReturnObject Whether to return the complete refund object
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

            $oRefund = parent::create($aData, true);

            if (!$oRefund) {
                throw new PaymentException('Failed to create refund.', 1);
            }

            $oDb->trans_commit();

            //  Trigger the payment.refund.created event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger($sPaymentClass::EVENT_PAYMENT_REFUND_CREATED, $oRefund);

            return $bReturnObject ? $oRefund : $oRefund->id;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update a payment
     * @param  integer $iRefundId The ID of the refund to update
     * @param  array   $aData     The data to update the payment with
     * @return boolean
     */
    public function update($iRefundId, $aData = array())
    {
        $oDb = Factory::service('Database');

        try {

            $oDb->trans_begin();

            unset($aData['ref']);

            $bResult = parent::update($iRefundId, $aData);

            if (!$bResult) {
                throw new PaymentException('Failed to update refund.', 1);
            }

            $oDb->trans_commit();

            //  Trigger the payment.updated event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger(
                $sPaymentClass::EVENT_PAYMENT_REFUND_UPDATED,
                $this->getById($iRefundId)
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
     * Set a refund as PENDING
     * @param  integer  $iRefundId The refund to update
     * @param  array    $aData     Any additional data to save to the transaction
     * @return boolean
     */
    public function setPending($iRefundId, $aData = array())
    {
        $aData['status'] = self::STATUS_PENDING;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as PROCESSING
     * @param  integer  $iRefundId The refund to update
     * @param  array    $aData     Any additional data to save to the transaction
     * @return boolean
     */
    public function setProcessing($iRefundId, $aData = array())
    {
        $aData['status'] = self::STATUS_PROCESSING;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as COMPLETE
     * @param  integer  $iRefundId The refund to update
     * @param  array    $aData     Any additional data to save to the transaction
     * @return boolean
     */
    public function setComplete($iRefundId, $aData = array())
    {
        $aData['status'] = self::STATUS_COMPLETE;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as FAILED
     * @param  integer  $iRefundId The refund to update
     * @param  array    $aData     Any additional data to save to the transaction
     * @return boolean
     */
    public function setFailed($iRefundId, $aData = array())
    {
        $aData['status'] = self::STATUS_FAILED;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    public function sendReceipt($iRefundId, $sEmailOverride = null)
    {
        try {

            $oRefund = $this->getById($iRefundId, array('includeInvoice' => true, 'includePayment' => true));

            if (empty($oRefund)) {
                throw new PaymentException('Invalid Payment ID', 1);
            }

            if (!in_array($oRefund->status->id, array(self::STATUS_PROCESSING, self::STATUS_COMPLETE))) {
                throw new PaymentException('Refund must be in a paid or processing state to send receipt.', 1);
            }

            $oEmail = new \stdClass();

            if ($oRefund->status->id == self::STATUS_COMPLETE) {

                $oEmail->type = 'refund_complete_receipt';

            } else {

                $oEmail->type = 'refund_processing_receipt';
            }

            $oEmail->data = array(
                'refund' => $oRefund
            );

            if (!empty($sEmailOverride)) {

                //  @todo, validate email address (or addresses if an array)
                $aEmails = explode(',', $sEmailOverride);

            } elseif (!empty($oRefund->invoice->customer->billing_email)) {

                $aEmails = explode(',', $oRefund->invoice->customer->billing_email);

            } elseif (!empty($oRefund->invoice->customer->email)) {

                $aEmails = array($oRefund->invoice->customer->email);

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
                            'invoice_id' => $oRefund->invoice->id,
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

        $aIntegers[] = 'payment_id';
        $aIntegers[] = 'amount';
        $aIntegers[] = 'fee';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        //  Status
        $aStatuses = $this->getStatusesHuman();
        $sStatus   = $oObj->status;

        $oObj->status        = new \stdClass();
        $oObj->status->id    = $sStatus;
        $oObj->status->label = !empty($aStatuses[$sStatus]) ? $aStatuses[$sStatus] : ucfirst(strtolower($sStatus));

        //  Amount
        $iAmount = $oObj->amount;
        $oObj->amount                      = new \stdClass();
        $oObj->amount->base                = $iAmount;
        $oObj->amount->localised           = (float) number_format($oObj->amount->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES, '', '');
        $oObj->amount->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->amount->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);

        $iFee = $oObj->fee;
        $oObj->fee                      = new \stdClass();
        $oObj->fee->base                = $iFee;
        $oObj->fee->localised           = (float) number_format($oObj->fee->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES, '', '');
        $oObj->fee->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->fee->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
    }
}
