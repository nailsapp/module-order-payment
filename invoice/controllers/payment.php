<?php

/**
 * Handle Payments
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

use App\Controller\Base;
use Nails\Common\Exception\NailsException;
use Nails\Factory;

class Payment extends Base
{
    /**
     * Completes a payment
     *
     * @param  \stdClass $oPayment The invoice object
     *
     * @return void
     */
    protected function complete($oPayment)
    {
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');

        $this->data['oPayment'] = $oPayment;
        $this->data['oInvoice'] = $oPayment->invoice;

        if ($oPayment->status->id === $oPaymentModel::STATUS_FAILED) {
            //  Payments which FAILED should be ignored
            show_404();

        } elseif ($oPayment->status->id === $oPaymentModel::STATUS_COMPLETE) {

            //  Payment is already complete
            redirect($oPayment->urls->thanks);

        } elseif ($oPayment->status->id === $oPaymentModel::STATUS_PROCESSING) {

            //  Payment is already complete and is being processed
            redirect($oPayment->urls->processing);

        } else {

            try {

                //  Set up CompleteRequest object
                $oCompleteRequest = Factory::factory('CompleteRequest', 'nailsapp/module-invoice');

                //  Set the driver to use for the request
                $oCompleteRequest->setDriver($oPayment->driver->slug);

                //  Set the payment we're completing
                $oCompleteRequest->setPayment($oPayment->id);

                //  Set the invoice we're completing
                $oCompleteRequest->setInvoice($oPayment->invoice->id);

                //  Set the complete URL, if there is one
                $oCompleteRequest->setContinueUrl($oPayment->urls->continue);

                //  Attempt completion
                $oInput            = Factory::service('Input');
                $oCompleteResponse = $oCompleteRequest->execute(
                    $oInput->get(),
                    $oInput->post()
                );

                if ($oCompleteResponse->isProcessing()) {

                    //  Payment was successful but has not been confirmed
                    if ($oCompleteRequest->getContinueUrl()) {
                        redirect($oCompleteRequest->getContinueUrl());
                    } else {
                        redirect($oPayment->urls->processing);
                    }

                } elseif ($oCompleteResponse->isComplete()) {

                    //  Payment has completed fully
                    if ($oCompleteRequest->getContinueUrl()) {
                        redirect($oCompleteRequest->getContinueUrl());
                    } else {
                        redirect($oPayment->urls->thanks);
                    }

                } elseif ($oCompleteResponse->isFailed()) {
                    throw new NailsException('Payment failed: ' . $oCompleteResponse->getError()->user, 1);
                } else {
                    throw new NailsException('Payment failed.', 1);
                }

            } catch (\Exception $e) {
                $oSession = Factory::service('Session', 'nailsapp/module-auth');
                $oSession->set_flashdata('error', $e->getMessage());
                redirect($oPayment->invoice->urls->payment);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Shows a thank you page
     *
     * @param  \stdClass $oPayment The invoice object
     *
     * @return void
     */
    protected function thanks($oPayment)
    {
        if ($oPayment->status->id === 'PROCESSING') {
            redirect($oPayment->urls->processing);
        } elseif ($oPayment->status->id !== 'COMPLETE') {
            show_404();
        }

        $this->data['oPayment']       = $oPayment;
        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        // --------------------------------------------------------------------------

        $oView = Factory::service('View');
        $oView->load('structure/header', $this->data);
        $oView->load('invoice/thanks/index', $this->data);
        $oView->load('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Shows a thank you page which informs the user that their payment is processing
     *
     * @param  \stdClass $oPayment The invoice object
     *
     * @return void
     */
    protected function processing($oPayment)
    {
        if ($oPayment->status->id === 'COMPLETE') {
            redirect($oPayment->urls->thanks);
        } elseif ($oPayment->status->id !== 'PROCESSING') {
            show_404();
        }

        $this->data['oPayment']       = $oPayment;
        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        // --------------------------------------------------------------------------

        $oView = Factory::service('View');
        $oView->load('structure/header', $this->data);
        $oView->load('invoice/thanks/processing', $this->data);
        $oView->load('structure/footer', $this->data);
    }

    // --------------------------------------------------------------------------

    /**
     * Remap requests for valid payments to the appropriate controller method
     * @return void
     */
    public function _remap()
    {
        $oUri          = Factory::service('Uri');
        $iPaymentId    = (int) $oUri->rsegment(2);
        $sPaymentToken = $oUri->rsegment(3);
        $sMethod       = $oUri->rsegment(4);
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $oPayment      = $oPaymentModel->getById($iPaymentId, ['includeInvoice' => true]);

        if (empty($oPayment) || $sPaymentToken !== $oPayment->token || !method_exists($this, $sMethod)) {
            show_404();
        }

        $this->{$sMethod}($oPayment);
    }
}
