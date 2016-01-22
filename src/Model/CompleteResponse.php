<?php

/**
 * Complete Response model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Invoice\Exception\CompleteResponseException;

class CompleteResponse
{
    //  Statuses
    const STATUS_PENDING = 'PENDING';
    const STATUS_OK      = 'OK';
    const STATUS_FAIL    = 'FAIL';

    // --------------------------------------------------------------------------

    //  Locked
    protected $bIsLocked;

    //  Status
    protected $sStatus;

    //  Successful charge variables
    protected $sTxnId;

    //  Errors
    protected $sErrorMsg;
    protected $sErrorCode;
    protected $sErrorUser;

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
     * Returns the current status of the charge request
     * @return string
     */
    public function setStatus($sStatus)
    {
        if (!$this->bIsLocked) {

            if (!in_array($sStatus, array(self::STATUS_PENDING, self::STATUS_OK, self::STATUS_FAIL))) {
                throw new CompleteResponseException('"' . $sStatus . '" is an invalid complete response status.', 1);
            }

            $this->sStatus = $sStatus;
        }

        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as pending
     */
    public function setStatusPending()
    {
        return $this->setStatus(self::STATUS_PENDING);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as OK
     */
    public function setStatusOk()
    {
        return $this->setStatus(self::STATUS_OK);
    }

    // --------------------------------------------------------------------------

    /**
     * Set the status as failed
     * @param string $sReasonMsg    The exception message, logged against the payment and not shown to the customer
     * @param integr $iReasonCode   The exception code, logged against the payment and not shown to the customer
     * @param string $sUserFeedback The message to show to the user explaining the error
     */
    public function setStatusFail($sReasonMsg, $iReasonCode, $sUserFeedback = '')
    {
        $this->sErrorMsg  = trim($sReasonMsg);
        $this->sErrorCode = (int) $iReasonCode;
        $this->sErrorUser = !empty($sUserFeedback) ? trim($sUserFeedback) : $this->sErrorMsg;
        return $this->setStatus(self::STATUS_FAIL);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns the current status of the charge request
     * @return string
     */
    public function getStatus()
    {
        return $this->sStatus;
    }

    // --------------------------------------------------------------------------

    /**
     * Returns if the request was successful
     * @return boolean
     */
    public function isOk()
    {
        return $this->getStatus() == self::STATUS_OK;
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
     * Returns if the request failed
     * @return boolean
     */
    public function isFail()
    {
        return $this->getStatus() == self::STATUS_FAIL;
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
