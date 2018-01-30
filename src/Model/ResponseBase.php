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
     * ResponseBase constructor.
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
     * Returns the current status of the response
     *
     * @param string $sStatus The status to set
     *
     * @throws ResponseException
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
     * @return string
     */
    public function setStatusPending()
    {
        return $this->setStatus(self::STATUS_PENDING);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as PROCESSING
     * @return string
     */
    public function setStatusProcessing()
    {
        return $this->setStatus(self::STATUS_PROCESSING);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as COMPLETE
     * @return string
     */
    public function setStatusComplete()
    {
        return $this->setStatus(self::STATUS_COMPLETE);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as FAILED
     *
     * @param string $sReasonMsg    The exception message, logged against the payment and not shown to the customer
     * @param string $sReasonCode   The exception code, logged against the payment and not shown to the customer
     * @param string $sUserFeedback The message to show to the user explaining the error
     *
     * @return string
     */
    public function setStatusFailed($sReasonMsg, $sReasonCode, $sUserFeedback = '')
    {
        $this->sErrorMsg  = trim($sReasonMsg);
        $this->sErrorCode = trim($sReasonCode);
        $this->sErrorUser = trim($sUserFeedback);

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
     *
     * @param string $sTxnId The transaction ID
     *
     * @return $this
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
    public function getTxnId()
    {
        return $this->sTxnId;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the fee charged by the payment processor
     *
     * @param integer $iFee The fee charged by the payment processor
     *
     * @return $this
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
    public function getFee()
    {
        return $this->iFee;
    }

    // --------------------------------------------------------------------------

    /**
     * Prevent the object from being altered
     * @return $this
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
    public function isLocked()
    {
        return $this->bIsLocked;
    }
}
