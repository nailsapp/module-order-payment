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

use Nails\Common\Model\Base;
use Nails\Factory;
use Nails\Invoice\Exception\PaymentException;

class Refund extends Base
{
    /**
     * The Currency library
     * @var Nails\Currency\Service\Currency
     */
    protected $oCurrency;

    // --------------------------------------------------------------------------

    //  Statuses
    const STATUS_PENDING    = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_COMPLETE   = 'COMPLETE';
    const STATUS_FAILED     = 'FAILED';

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_refund';
        $this->tableAlias        = 'pr';
        $this->defaultSortColumn = 'created';
        $this->oCurrency         = Factory::service('Currency', 'nailsapp/module-currency');

        $this->addExpandableField([
            'trigger'   => 'invoice',
            'type'      => self::EXPANDABLE_TYPE_SINGLE,
            'property'  => 'invoice',
            'model'     => 'Invoice',
            'provider'  => 'nailsapp/module-invoice',
            'id_column' => 'invoice_id',
        ]);
        $this->addExpandableField([
            'trigger'   => 'payment',
            'type'      => self::EXPANDABLE_TYPE_SINGLE,
            'property'  => 'payment',
            'model'     => 'Payment',
            'provider'  => 'nailsapp/module-invoice',
            'id_column' => 'payment_id',
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the statuses as an array
     * @return array
     */
    public function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_FAILED,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of statuses with human friendly labels
     * @return array
     */
    public function getStatusesHuman()
    {
        return [
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETE   => 'Complete',
            self::STATUS_FAILED     => 'Failed',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new refund
     *
     * @param  array   $aData         The data to create the refund with
     * @param  boolean $bReturnObject Whether to return the complete refund object
     *
     * @return mixed
     */
    public function create(array $aData = [], $bReturnObject = false)
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
     *
     * @param  integer $iRefundId The ID of the refund to update
     * @param  array   $aData     The data to update the payment with
     *
     * @return boolean
     */
    public function update($iRefundId, array $aData = [])
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

            $sRef = $oNow->format('Ym') . '-' . strtoupper(random_string('alnum'));
            $oDb->where('ref', $sRef);
            $bRefExists = (bool) $oDb->count_all_results($this->table);

        } while ($bRefExists);

        return $sRef;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as PENDING
     *
     * @param  integer $iRefundId The refund to update
     * @param  array   $aData     Any additional data to save to the transaction
     *
     * @return boolean
     */
    public function setPending($iRefundId, $aData = [])
    {
        $aData['status'] = self::STATUS_PENDING;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as PROCESSING
     *
     * @param  integer $iRefundId The refund to update
     * @param  array   $aData     Any additional data to save to the transaction
     *
     * @return boolean
     */
    public function setProcessing($iRefundId, $aData = [])
    {
        $aData['status'] = self::STATUS_PROCESSING;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as COMPLETE
     *
     * @param  integer $iRefundId The refund to update
     * @param  array   $aData     Any additional data to save to the transaction
     *
     * @return boolean
     */
    public function setComplete($iRefundId, $aData = [])
    {
        $aData['status'] = self::STATUS_COMPLETE;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as FAILED
     *
     * @param  integer $iRefundId The refund to update
     * @param  array   $aData     Any additional data to save to the transaction
     *
     * @return boolean
     */
    public function setFailed($iRefundId, $aData = [])
    {
        $aData['status'] = self::STATUS_FAILED;
        return $this->update($iRefundId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Sends refund receipt email
     *
     * @param  integer $iRefundId      The ID of the refund
     * @param  string  $sEmailOverride The email address to send the email to
     * @return bool
     */
    public function sendReceipt($iRefundId, $sEmailOverride = null)
    {
        try {

            $oRefund = $this->getById($iRefundId, ['expand' => ['invoice', 'payment']]);

            if (empty($oRefund)) {
                throw new PaymentException('Invalid Payment ID', 1);
            }

            if (!in_array($oRefund->status->id, [self::STATUS_PROCESSING, self::STATUS_COMPLETE])) {
                throw new PaymentException('Refund must be in a paid or processing state to send receipt.', 1);
            }

            $oEmail = new \stdClass();

            if ($oRefund->status->id == self::STATUS_COMPLETE) {

                $oEmail->type = 'refund_complete_receipt';

            } else {

                $oEmail->type = 'refund_processing_receipt';
            }

            $oEmail->data = [
                'refund' => $oRefund,
            ];

            if (!empty($sEmailOverride)) {

                //  @todo, validate email address (or addresses if an array)
                $aEmails = explode(',', $sEmailOverride);

            } elseif (!empty($oRefund->invoice->customer->billing_email)) {

                $aEmails = explode(',', $oRefund->invoice->customer->billing_email);

            } elseif (!empty($oRefund->invoice->customer->email)) {

                $aEmails = [$oRefund->invoice->customer->email];

            } else {

                throw new PaymentException('No email address to send the receipt to.', 1);
            }

            $aEmails = array_unique($aEmails);
            $aEmails = array_filter($aEmails);

            $oEmailer           = Factory::service('Emailer', 'nailsapp/module-email');
            $oInvoiceEmailModel = Factory::model('InvoiceEmail', 'nailsapp/module-invoice');

            foreach ($aEmails as $sEmail) {

                $oEmail->to_email = $sEmail;
                $oResult          = $oEmailer->send($oEmail);

                if (!empty($oResult)) {

                    $oInvoiceEmailModel->create(
                        [
                            'invoice_id' => $oRefund->invoice->id,
                            'email_id'   => $oResult->id,
                            'email_type' => $oEmail->type,
                            'recipient'  => $oEmail->to_email,
                        ]
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
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
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

        //  Currency
        $oCurrency = $this->oCurrency->getByIsoCode($oObj->currency);
        unset($oObj->currency);

        //  Amount
        $oObj->amount = (object) [
            'raw'       => $oObj->amount,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->amount / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Fee
        $oObj->fee = (object) [
            'raw'       => $oObj->fee,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->fee / pow(10, $oCurrency->decimal_precision)
            ),
        ];
    }
}
