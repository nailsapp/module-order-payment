<?php

/**
 * Complete Request
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Factory
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Factory;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Invoice\Exception\CompleteRequestException;
use Nails\Invoice\Exception\RequestException;

/**
 * Class CompleteRequest
 *
 * @package Nails\Invoice\Factory
 */
class CompleteRequest extends RequestBase
{
    /**
     * Complete the payment
     *
     * @param array $aGetVars  Any $_GET variables passed from the redirect flow
     * @param array $aPostVars Any $_POST variables passed from the redirect flow
     *
     * @return CompleteResponse
     * @throws CompleteRequestException
     * @throws FactoryException
     * @throws ModelException
     * @throws RequestException
     */
    public function execute($aGetVars, $aPostVars)
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

        if (!($oCompleteResponse instanceof CompleteResponse)) {
            throw new CompleteRequestException(
                'Response from driver must be an instance of \Nails\Invoice\Factory\CompleteResponse.',
                1
            );
        }

        //  Handle the response
        if ($oCompleteResponse->isProcessing()) {

            //  Driver has started processing the charge, but it hasn't been confirmed yet
            $this->setPaymentProcessing(
                $oCompleteResponse->getTransactionId(),
                $oCompleteResponse->getFee()
            );

        } elseif ($oCompleteResponse->isComplete()) {

            //  Driver has confirmed that payment has been taken.
            $this->setPaymentComplete(
                $oCompleteResponse->getTransactionId(),
                $oCompleteResponse->getFee()
            );

        } elseif ($oCompleteResponse->isFailed()) {

            /**
             * Payment failed
             */

            //  Update the payment
            $sPaymentClass = get_class($this->oPaymentModel);
            $bResult       = $this->oPaymentModel->update(
                $this->oPayment->id,
                [
                    'status'    => $sPaymentClass::STATUS_FAILED,
                    'fail_msg'  => $oCompleteResponse->getError()->msg,
                    'fail_code' => $oCompleteResponse->getError()->code,
                ]
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
