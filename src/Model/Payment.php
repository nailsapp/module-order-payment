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

use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Factory;
use Nails\Invoice\Events;
use Nails\Invoice\Exception\PaymentException;
use Nails\Invoice\Factory\RefundRequest;
use Nails\Invoice\Factory\RefundResponse;

class Payment extends Base
{
    /**
     * The Currency service
     *
     * @var \Nails\Currency\Service\Currency
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
     * Payment constructor.
     *
     * @throws \Nails\Common\Exception\FactoryException
     * @throws \Nails\Common\Exception\ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_payment';
        $this->tableAlias        = 'p';
        $this->defaultSortColumn = 'created';
        $this->oCurrency         = Factory::service('Currency', 'nails/module-currency');
        $this->searchableFields  = ['id', 'ref', 'description', 'txn_id'];
        $this
            ->addExpandableField([
                'trigger'   => 'invoice',
                'model'     => 'Invoice',
                'provider'  => 'nails/module-invoice',
                'id_column' => 'invoice_id',
            ])
            ->addExpandableField([
                'trigger'   => 'refunds',
                'type'      => self::EXPANDABLE_TYPE_MANY,
                'model'     => 'Refund',
                'provider'  => 'nails/module-invoice',
                'id_column' => 'payment_id',
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the statuses as an array
     *
     * @return array
     */
    public function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_FAILED,
            self::STATUS_REFUNDED,
            self::STATUS_REFUNDED_PARTIAL,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of statsues with human friendly labels
     *
     * @return array
     */
    public function getStatusesHuman()
    {
        return [
            self::STATUS_PENDING          => 'Pending',
            self::STATUS_PROCESSING       => 'Processing',
            self::STATUS_COMPLETE         => 'Complete',
            self::STATUS_FAILED           => 'Failed',
            self::STATUS_REFUNDED         => 'Refunded',
            self::STATUS_REFUNDED_PARTIAL => 'Partially Refunded',
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve payments which relate to a particular set of invoice IDs
     *
     * @param  array $aInvoiceIds The invoice IDs
     *
     * @return array
     */
    public function getForInvoices($aInvoiceIds)
    {
        return $this->getAll([
            'where_in' => [
                ['invoice_id', $aInvoiceIds],
            ],
        ]);
    }

    // --------------------------------------------------------------------------

    /**
     * This method applies the conditionals which are common across the get_*()
     * methods and the count() method.
     *
     * @param  array $data Data passed from the calling method
     *
     * @throws \Nails\Common\Exception\FactoryException
     */
    protected function getCountCommon(array $data = [])
    {
        $oDb           = Factory::service('Database');
        $oInvoiceModel = Factory::model('Invoice', 'nails/module-invoice');
        $oRefundModel  = Factory::model('Refund', 'nails/module-invoice');

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
     *
     * @param  array   $aData         The data to create the payment with
     * @param  boolean $bReturnObject Whether to return the complete payment object
     *
     * @return bool|mixed
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        $oDb = Factory::service('Database');

        try {

            $oDb->trans_begin();

            if (empty($aData['ref'])) {
                $aData['ref'] = $this->generateValidRef();
            }

            $aData['token'] = $this->generateToken();

            if (array_key_exists('custom_data', $aData)) {
                $aData['custom_data'] = json_encode($aData['custom_data']);
            }

            $mPayment = parent::create($aData, $bReturnObject);

            if (!$mPayment) {
                throw new PaymentException('Failed to create payment.');
            }

            $oDb->trans_commit();
            $this->triggerEvent(
                Events::PAYMENT_CREATED,
                [$this->getPaymentForEvent($bReturnObject ? $mPayment->id : $mPayment)]
            );

            return $mPayment;

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
     * @param  integer $iPaymentId The ID of the payment to update
     * @param  array   $aData      The data to update the payment with
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function update($iPaymentId, array $aData = [])
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
                throw new PaymentException('Failed to update payment.');
            }

            $oDb->trans_commit();
            $this->triggerEvent(
                Events::PAYMENT_UPDATED,
                [$this->getPaymentForEvent($iPaymentId)]
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
     *
     * @return string
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function generateValidRef(): string
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
     * Set a payment as PENDING
     *
     * @param  integer $iPaymentId The payment to update
     * @param  array   $aData      Any additional data to save to the transaction
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function setPending($iPaymentId, $aData = []): bool
    {
        $aData['status'] = self::STATUS_PENDING;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as PROCESSING
     *
     * @param  integer $iPaymentId The payment to update
     * @param  array   $aData      Any additional data to save to the transaction
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function setProcessing($iPaymentId, $aData = []): bool
    {
        $aData['status'] = self::STATUS_PROCESSING;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as COMPLETE
     *
     * @param  integer $iPaymentId The payment to update
     * @param  array   $aData      Any additional data to save to the transaction
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function setComplete($iPaymentId, $aData = []): bool
    {
        $aData['status'] = self::STATUS_COMPLETE;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as FAILED
     *
     * @param  integer $iPaymentId The payment to update
     * @param  array   $aData      Any additional data to save to the transaction
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function setFailed($iPaymentId, $aData = []): bool
    {
        $aData['status'] = self::STATUS_FAILED;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as REFUNDED
     *
     * @param  integer $iPaymentId The payment to update
     * @param  array   $aData      Any additional data to save to the transaction
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function setRefunded($iPaymentId, $aData = []): bool
    {
        $aData['status'] = self::STATUS_REFUNDED;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Set a payment as REFUNDED_PARTIAL
     *
     * @param  integer $iPaymentId The payment to update
     * @param  array   $aData      Any additional data to save to the transaction
     *
     * @return bool
     * @throws \Nails\Common\Exception\FactoryException
     */
    public function setRefundedPartial($iPaymentId, $aData = []): bool
    {
        $aData['status'] = self::STATUS_REFUNDED_PARTIAL;
        return $this->update($iPaymentId, $aData);
    }

    // --------------------------------------------------------------------------

    /**
     * Send payment receipt
     *
     * @param  integer $iPaymentId     The ID of the payment
     * @param  string  $sEmailOverride Send to this email instead of the email defined by the invoice object
     *
     * @return bool
     */
    public function sendReceipt($iPaymentId, $sEmailOverride = null): bool
    {
        try {

            $oPayment = $this->getById(
                $iPaymentId,
                [
                    'expand' => [
                        [
                            'invoice',
                            [
                                'expand' => [
                                    'customer',
                                    'items',
                                ],
                            ],
                        ],
                    ],
                ]
            );

            if (empty($oPayment)) {
                throw new PaymentException('Invalid Payment ID');
            }

            if (!in_array($oPayment->status->id, [self::STATUS_PROCESSING, self::STATUS_COMPLETE])) {
                throw new PaymentException('Payment must be in a paid or processing state to send receipt.');
            }

            $oEmail = new \stdClass();

            if ($oPayment->status->id == self::STATUS_COMPLETE) {
                $oEmail->type = 'payment_complete_receipt';
            } else {
                $oEmail->type = 'payment_processing_receipt';
            }

            $oEmail->data = [
                'payment' => $oPayment,
            ];

            if (!empty($sEmailOverride)) {
                //  @todo, validate email address (or addresses if an array)
                $aEmails = explode(',', $sEmailOverride);
            } elseif (!empty($oPayment->invoice->customer->billing_email)) {
                $aEmails = explode(',', $oPayment->invoice->customer->billing_email);
            } elseif (!empty($oPayment->invoice->customer->email)) {
                $aEmails = [$oPayment->invoice->customer->email];
            } elseif (!empty($oPayment->invoice->email)) {
                $aEmails = [$oPayment->invoice->email];
            } else {
                throw new PaymentException('No email address to send the receipt to.');
            }

            $aEmails = array_unique($aEmails);
            $aEmails = array_filter($aEmails);

            $oEmailer           = Factory::service('Emailer', 'nails/module-email');
            $oInvoiceEmailModel = Factory::model('InvoiceEmail', 'nails/module-invoice');

            foreach ($aEmails as $sEmail) {

                $oEmail->to_email = $sEmail;
                $oResult          = $oEmailer->send($oEmail);

                if (!empty($oResult)) {

                    $oInvoiceEmailModel->create(
                        [
                            'invoice_id' => $oPayment->invoice->id,
                            'email_id'   => $oResult->id,
                            'email_type' => $oEmail->type,
                            'recipient'  => $oEmail->to_email,
                        ]
                    );

                } else {
                    throw new PaymentException($oEmailer->lastError());
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
     * Perform a refund
     *
     * @param int    $iPaymentId
     * @param int    $iAmount
     * @param string $sReason
     *
     * @return bool
     * @throws ModelException
     * @throws \Nails\Common\Exception\FactoryException
     * @throws \Nails\Invoice\Exception\RefundRequestException
     * @throws \Nails\Invoice\Exception\RequestException
     */
    public function refund(int $iPaymentId, int $iAmount = null, string $sReason = null): bool
    {
        try {

            //  Validate payment
            $oPayment = $this->getById($iPaymentId, ['expand' => ['invoice']]);
            if (!$oPayment) {
                throw new PaymentException('Invalid payment ID.');
            }

            //  Set up RefundRequest object
            /** @var RefundRequest $oRefundRequest */
            $oRefundRequest = Factory::factory('RefundRequest', 'nails/module-invoice');

            //  Set the driver to use for the request
            $oRefundRequest->setDriver($oPayment->driver->slug);

            //  Describe the charge
            $oRefundRequest->setReason($sReason);

            //  Set the payment we're refunding against
            $oRefundRequest->setPayment($oPayment->id);

            //  Attempt the refund
            /** @var RefundResponse $oRefundResponse */
            $oRefundResponse = $oRefundRequest->execute($iAmount);

            if ($oRefundResponse->isProcessing() || $oRefundResponse->isComplete()) {
                //  It's all good
            } elseif ($oRefundResponse->isFailed()) {
                //  Refund failed, throw an error which will be caught and displayed to the user
                throw new PaymentException('Refund failed: ' . $oRefundResponse->getError()->user);
            } else {
                //Something which we've not accounted for went wrong.
                throw new PaymentException('Refund failed.');
            }

            return true;

        } catch (PaymentException $e) {
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Get a payment in a suitable format for the event triggers
     *
     * @param int $iPaymentId The payment ID
     *
     * @return \stdClass
     * @throws \Nails\Common\Exception\ModelException
     */
    protected function getPaymentForEvent(int $iPaymentId): \stdClass
    {
        $oPayment = $this->getById($iPaymentId);
        if (empty($oPayment)) {
            throw new ModelException('Invalid payment ID');
        }
        return $oPayment;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to getCountCommon, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     *
     * @throws \Nails\Common\Exception\FactoryException
     * @throws \Nails\Currency\Exception\CurrencyException
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
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
        $oPaymentDriverModel = Factory::model('PaymentDriver', 'nails/module-invoice');
        $sDriver             = $oObj->driver;
        $oDriver             = $oPaymentDriverModel->getBySlug($sDriver);

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
        $oObj->amount = (object) [
            'raw'       => $oObj->amount,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->amount / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Amount refunded
        $oObj->amount_refunded = (object) [
            'raw'       => $oObj->amount_refunded,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->amount_refunded / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Fee
        $oObj->fee = (object) [
            'raw'       => $oObj->fee,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->fee / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Fee refunded
        $oObj->fee_refunded = (object) [
            'raw'       => $oObj->fee_refunded,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $oObj->fee_refunded / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Available for refund
        $iAvailableForRefund        = $oObj->amount->raw - $oObj->amount_refunded->raw;
        $oObj->available_for_refund = (object) [
            'raw'       => $iAvailableForRefund,
            'formatted' => $this->oCurrency->format(
                $oCurrency->code, $iAvailableForRefund / pow(10, $oCurrency->decimal_precision)
            ),
        ];

        //  Can this payment be refunded?
        $aValidStates        = [
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_REFUNDED_PARTIAL,
        ];
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
