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

use Exception;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Model\Base;
use Nails\Common\Service\Database;
use Nails\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Events;
use Nails\Invoice\Exception\PaymentException;
use Nails\Invoice\Factory\Email\Refund\Complete;
use Nails\Invoice\Factory\Email\Refund\Processing;

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

    /**
     * The default column to sort on
     *
     * @var string|null
     */
    const DEFAULT_SORT_COLUMN = 'created';

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
        $this->oCurrency = Factory::service('Currency', Currency\Constants::MODULE_SLUG);
        $this
            ->hasOne('invoice', 'Invoice', Constants::MODULE_SLUG)
            ->hasOne('payment', 'Payment', Constants::MODULE_SLUG);
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
     * @param array $aData         The data to create the refund with
     * @param bool  $bReturnObject Whether to return the complete refund object
     *
     * @return mixed
     */
    public function create(array $aData = [], $bReturnObject = false)
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        try {

            $oDb->transaction()->start();

            if (empty($aData['ref'])) {
                $aData['ref'] = $this->generateValidRef();
            }

            $mRefund = parent::create($aData, $bReturnObject);

            if (!$mRefund) {
                throw new PaymentException('Failed to create refund.');
            }

            $oDb->transaction()->commit();
            $this->triggerEvent(
                Events::REFUND_CREATED,
                [$this->getRefundForEvent($bReturnObject ? $mRefund->id : $mRefund)]
            );

            return $mRefund;

        } catch (Exception $e) {
            $oDb->transaction()->rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update a payment
     *
     * @param int   $iRefundId The ID of the refund to update
     * @param array $aData     The data to update the payment with
     *
     * @return bool
     */
    public function update($iRefundId, array $aData = []): bool
    {
        /** @var Database $oDb */
        $oDb = Factory::service('Database');

        try {

            $oDb->transaction()->start();

            unset($aData['ref']);

            $bResult = parent::update($iRefundId, $aData);

            if (!$bResult) {
                throw new PaymentException('Failed to update refund.');
            }

            $oDb->transaction()->commit();
            $this->triggerEvent(
                Events::REFUND_UPDATED,
                [$this->getRefundForEvent($iRefundId)]
            );

            return $bResult;

        } catch (Exception $e) {
            $oDb->transaction()->rollback();
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

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var \DataTime $oNow */
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
     * @param int   $iRefundId The refund to update
     * @param array $aData     Any additional data to save to the transaction
     *
     * @return bool
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
     * @param int   $iRefundId The refund to update
     * @param array $aData     Any additional data to save to the transaction
     *
     * @return bool
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
     * @param int   $iRefundId The refund to update
     * @param array $aData     Any additional data to save to the transaction
     *
     * @return bool
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
     * @param int   $iRefundId The refund to update
     * @param array $aData     Any additional data to save to the transaction
     *
     * @return bool
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
     * @param int         $iRefundId      The ID of the refund
     * @param string|null $sEmailOverride The email address to send the email to
     *
     * @return bool
     */
    public function sendReceipt($iRefundId, $sEmailOverride = null)
    {
        try {

            /** @var \Nails\Invoice\Resource\Refund $oRefund */
            $oRefund = $this->getById(
                $iRefundId,
                [
                    'expand' => [
                        'invoice',
                        'payment',
                    ],
                ]
            );

            if (empty($oRefund)) {
                throw new PaymentException('Invalid Payment ID');
            }

            if (!in_array($oRefund->status->id, [self::STATUS_PROCESSING, self::STATUS_COMPLETE])) {
                throw new PaymentException('Refund must be in a paid or processing state to send receipt.');
            }

            /** @var Complete|Processing $oEmail */
            $oEmail = $oRefund->status->id == self::STATUS_COMPLETE
                ? Factory::factory('EmailRefundComplete', Constants::MODULE_SLUG)
                : Factory::factory('EmailRefundProcessing', Constants::MODULE_SLUG);

            $oEmail->data([
                'refund'  => [
                    'id'     => $oRefund->id,
                    'ref'    => $oRefund->ref,
                    'reason' => $oRefund->reason,
                    'amount' => html_entity_decode($oRefund->amount->formatted),
                ],
                'payment' => [
                    'id'     => $oRefund->payment->id,
                    'ref'    => $oRefund->payment->ref,
                    'amount' => html_entity_decode($oRefund->payment->amount->formatted),
                ],
                'invoice' => [
                    'id'  => $oRefund->invoice->id,
                    'ref' => $oRefund->invoice->ref,
                ],
            ]);

            if (!empty($sEmailOverride)) {
                //  @todo (Pablo - 2019-01-20) - validate email address (or addresses if an array)
                $aEmails = explode(',', $sEmailOverride);

            } elseif (!empty($oRefund->invoice->customer->billing_email)) {
                $aEmails = explode(',', $oRefund->invoice->customer->billing_email);

            } elseif (!empty($oRefund->invoice->customer->email)) {
                $aEmails = [$oRefund->invoice->customer->email];

            } else {
                $aEmails = [];
            }

            /** @var Invoice\Email $oInvoiceEmailModel */
            $oInvoiceEmailModel = Factory::model('InvoiceEmail', Constants::MODULE_SLUG);
            $oInvoiceEmailModel->sendEmails(
                $aEmails,
                $oEmail,
                $oRefund->invoice
            );

        } catch (Exception $e) {
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
     * @return \Nails\Invoice\Resource\Refund
     * @throws ModelException
     */
    protected function getRefundForEvent(int $iRefundId): \Nails\Invoice\Resource\Refund
    {
        /** @var \Nails\Invoice\Resource\Refund $oRefund */
        $oRefund = $this->getById($iRefundId);
        if (empty($oRefund)) {
            throw new ModelException('Invalid refund ID');
        }
        return $oRefund;
    }
}
