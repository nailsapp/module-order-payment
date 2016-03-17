<?php

/**
 * Base Response Model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Invoice\Exception\ResponseException;

class ResponseBase
{
    //  Statuses; these are in line with the Payment statuses
    const STATUS_PENDING    = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_COMPLETE   = 'COMPLETE';
    const STATUS_FAILED     = 'FAILED';

    // --------------------------------------------------------------------------

    //  Locked
    protected $bIsLocked;

    //  Status
    protected $sStatus;

    //  Errors
    protected $sErrorMsg;
    protected $sErrorCode;
    protected $sErrorUser;

    //  Transaction Variables
    protected $sTxnId;
    protected $iFee;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        $this->sStatus = self::STATUS_PENDING;
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
     * Returns the current status of the response
     * @return string
     */
    public function setStatus($sStatus)
    {
        if (!$this->bIsLocked) {

            $aStatuses = $this->getStatuses();
            if (!in_array($sStatus, $aStatuses)) {
                throw new ResponseException('"' . $sStatus . '" is an invalid response status.', 1);
            }

            $this->sStatus = $sStatus;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as PENDING
     */
    public function setStatusPending()
    {
        return $this->setStatus(self::STATUS_PENDING);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as PROCESSING
     */
    public function setStatusProcessing()
    {
        return $this->setStatus(self::STATUS_PROCESSING);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as COMPLETE
     */
    public function setStatusComplete()
    {
        return $this->setStatus(self::STATUS_COMPLETE);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as FAILED
     * @param string $sReasonMsg    The exception message, logged against the payment and not shown to the customer
     * @param integr $iReasonCode   The exception code, logged against the payment and not shown to the customer
     * @param string $sUserFeedback The message to show to the user explaining the error
     */
    public function setStatusFailed($sReasonMsg, $iReasonCode, $sUserFeedback = '')
    {
        $this->sErrorMsg  = trim($sReasonMsg);
        $this->sErrorCode = (int) $iReasonCode;
        $this->sErrorUser = !empty($sUserFeedback) ? trim($sUserFeedback) : $this->sErrorMsg;

        return $this->setStatus(self::STATUS_FAILED);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current status of the response
     * @return string
     */
    public function getStatus()
    {
        return $this->sStatus;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns if the request is pending
     * @return boolean
     */
    public function isPending()
    {
        return $this->getStatus() == self::STATUS_PENDING;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns if the request was successful, but is still in a processing state
     * @return boolean
     */
    public function isProcessing()
    {
        return $this->getStatus() == self::STATUS_PROCESSING;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns if the request was successful and completed fully
     * @return boolean
     */
    public function isComplete()
    {
        return $this->getStatus() == self::STATUS_COMPLETE;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns if the request failed
     * @return boolean
     */
    public function isFailed()
    {
        return $this->getStatus() == self::STATUS_FAILED;
    }

    // --------------------------------------------------------------------------

    /**
     * Return the error messages
     * @return \stdClass
     */
    public function getError()
    {
        $oOut       = new \stdClass();
        $oOut->msg  = $this->sErrorMsg;
        $oOut->code = $this->sErrorCode;
        $oOut->user = $this->sErrorUser;

        return $oOut;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the transaction ID
     * @param string $sTxnId The transaction ID
     */
    public function setTxnId($sTxnId)
    {
        if (!$this->bIsLocked) {
            $this->sTxnId = $sTxnId;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The transaction ID
     * @return string
     */
    public function getTxnId() {
        return $this->sTxnId;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the fee charged by the payment processor
     * @param integer $iFee The fee charged by the payment processor
     */
    public function setFee($iFee)
    {
        if (!$this->bIsLocked) {
            $this->iFee = (int) $iFee;
        }
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * The fee charged by the payment processor
     * @return integer
     */
    public function getFee() {
        return $this->iFee;
    }

    // --------------------------------------------------------------------------

    /**
     * Prevent the object from being altered
     * @return object
     */
    public function lock()
    {
        $this->bIsLocked = true;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Whether the response is locked
     * @return boolean
     */
    public function isLocked() {
        return $this->bIsLocked;
    }
}