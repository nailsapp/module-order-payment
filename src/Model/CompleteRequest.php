<?php

/**
 * Complete Request model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Invoice\Model\RequestBase;
use Nails\Invoice\Exception\CompleteRequestException;

class CompleteRequest extends RequestBase
{
    protected $sContinueUrl;

    // --------------------------------------------------------------------------

    /**
     * Set the URL to go to when a payment is completed
     * @param string $sContinueUrl the URL to go to when payment is completed
     */
    public function setContinueUrl($sContinueUrl)
    {
        $this->sContinueUrl = $sContinueUrl;
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Get the URL to go to when a payment is completed
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->sContinueUrl;
    }

    // --------------------------------------------------------------------------

    /**
     * Complete the payment
     * @param  array $aGetVars  Any $_GET variables passed from the redirect flow
     * @param  array $aPostVars Any $_POST variables passed from the redirect flow
     * @return \Nails\Invoice\Model\CompleteResponse
     */
    public function complete($aGetVars, $aPostVars)
    {
        //  Ensure we have a driver
        if (empty($this->oDriver)) {
            throw new CompleteRequestException('No driver selected.', 1);
        }

        //  Ensure we have a payment
        if (empty($this->oPayment)) {
            throw new CompleteRequestException('No payment selected.', 1);
        }

        if (empty($this->oInvoice)) {
            throw new CompleteRequestException('No invoice selected.', 1);
        }

        //  Execute the completion
        $oCompleteResponse = $this->oDriver->complete(
            $this->oPayment,
            $this->oInvoice,
            $aGetVars,
            $aPostVars
        );

        //  Validate driver response
        if (empty($oCompleteResponse)) {
            throw new CompleteRequestException('Response from driver was empty.', 1);
        }

        if (!($oCompleteResponse instanceof \Nails\Invoice\Model\CompleteResponse)) {
            throw new CompleteRequestException(
                'Response from driver must be an instance of \Nails\Invoice\Model\CompleteResponse.',
                1
            );
        }

        //  Handle the response
        if ($oCompleteResponse->isProcessing()) {

            //  Driver has started processing the charge, but it hasn't been confirmed yet
            $this->setPaymentProcessing(
                $oCompleteResponse->getTxnId()
            );

        } elseif ($oCompleteResponse->isComplete()) {

            //  Driver has confirmed that payment has been taken.
            $this->setPaymentComplete(
                $oCompleteResponse->getTxnId()
            );

        } elseif ($oCompleteResponse->isFailed()) {

            /**
             * Payment failed
             */

            //  Update the payment
            $sPaymentClass = get_class($this->oPaymentModel);
            $bResult       = $this->oPaymentModel->update(
                $this->oPayment->id,
                array(
                    'status'    => $sPaymentClass::STATUS_FAILED,
                    'fail_msg'  => $oCompleteResponse->getError()->msg,
                    'fail_code' => $oCompleteResponse->getError()->code
                )
            );

            if (empty($bResult)) {
                throw new CompleteRequestException('Failed to update existing payment.', 1);
            }
        }

        //  Lock the response so it cannot be altered
        $oCompleteResponse->lock();

        return $oCompleteResponse;
    }
}
