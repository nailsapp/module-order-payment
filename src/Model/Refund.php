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

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Common\Resource;
use Nails\Currency;
use Nails\Email;
use Nails\Email\Service\Emailer;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Events;
use Nails\Invoice\Exception\PaymentException;

/**
 * Class Refund
 *
 * @package Nails\Invoice\Model
 */
class Refund extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'invoice_refund';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'Refund';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * The Currency library
     *
     * @var Currency\Service\Currency
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
     * Refund constructor.
     *
     * @throws FactoryException
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this->defaultSortColumn = 'created';
        $this->oCurrency         = Factory::service('Currency', Currency\Constants::MODULE_SLUG);
        $this
            ->addExpandableField([
                'trigger'   => 'invoice',
                'type'      => self::EXPANDABLE_TYPE_SINGLE,
                'property'  => 'invoice',
                'model'     => 'Invoice',
                'provider'  => Constants::MODULE_SLUG,
                'id_column' => 'invoice_id',
            ])
            ->addExpandableField([
                'trigger'   => 'payment',
                'type'      => self::EXPANDABLE_TYPE_SINGLE,
                'property'  => 'payment',
                'model'     => 'Payment',
                'provider'  => Constants::MODULE_SLUG,
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
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of statuses with human friendly labels
     *
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
     * @param array   $aData         The data to create the refund with
     * @param boolean $bReturnObject Whether to return the complete refund object
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

            $mRefund = parent::create($aData, $bReturnObject);

            if (!$mRefund) {
                throw new PaymentException('Failed to create refund.', 1);
            }

            $oDb->trans_commit();
            $this->triggerEvent(
                Events::REFUND_CREATED,
                [$this->getRefundForEvent($bReturnObject ? $mRefund->id : $mRefund)]
            );

            return $mRefund;

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
     * @param integer $iRefundId The ID of the refund to update
     * @param array   $aData     The data to update the payment with
     *
     * @return boolean
     */
    public function update($iRefundId, array $aData = []): bool
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
            $this->triggerEvent(
                Events::REFUND_UPDATED,
                [$this->getRefundForEvent($iRefundId)]
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
     */
    public function generateValidRef()
    {
        Factory::helper('string');

        $oDb  = Factory::service('Database');
        $oNow = Factory::factory('DateTime');

        do {

            $sRef = $oNow->format('Ym') . '-' . strtoupper(random_string('alnum'));
            $oDb->where('ref', $sRef);
            $bRefExists = (bool) $oDb->count_all_results($this->getTableName());

        } while ($bRefExists);

        return $sRef;
    }

    // --------------------------------------------------------------------------

    /**
     * Set a refund as PENDING
     *
     * @param integer $iRefundId The refund to update
     * @param array   $aData     Any additional data to save to the transaction
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
     * @param integer $iRefundId The refund to update
     * @param array   $aData     Any additional data to save to the transaction
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
     * @param integer $iRefundId The refund to update
     * @param array   $aData     Any additional data to save to the transaction
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
     * @param integer $iRefundId The refund to update
     * @param array   $aData     Any additional data to save to the transaction
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
     * @param integer $iRefundId      The ID of the refund
     * @param string  $sEmailOverride The email address to send the email to
     *
     * @return bool
     */
    public function sendReceipt($iRefundId, $sEmailOverride = null)
    {
        try {

            $oRefund = $this->getById(
                $iRefundId,
                [
                    'expand' => [
                        ['invoice', ['expand' => ['customer']]],
                        'payment',
                    ],
                ]
            );

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
                //  @todo (Pablo - 2019-01-20) - validate email address (or addresses if an array)
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

            /** @var Emailer $oEmailer */
            $oEmailer = Factory::service('Emailer', Email\Constants::MODULE_SLUG);
            /** @var \Nails\Invoice\Model\Invoice\Email $oInvoiceEmailModel */
            $oInvoiceEmailModel = Factory::model('InvoiceEmail', Constants::MODULE_SLUG);

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
     * Get a refund in a suitable format for the event triggers
     *
     * @param int $iRefundId The refund ID
     *
     * @return Resource
     * @throws ModelException
     */
    protected function getRefundForEvent(int $iRefundId): Resource
    {
        $oRefund = $this->getById($iRefundId);
        if (empty($oRefund)) {
            throw new ModelException('Invalid refund ID');
        }
        return $oRefund;
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param object $oObj      A reference to the object being formatted.
     * @param array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param array  $aBools    Fields which should be cast as booleans if not null
     * @param array  $aFloats   Fields which should be cast as floats if not null
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
    }
}
