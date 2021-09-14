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

use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Input;
use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Factory\CompleteRequest;

/**
 * Class Payment
 */
class Payment extends Base
{
    /**
     * Completes a payment
     *
     * @param \Nails\Invoice\Resource\Payment $oPayment The invoice object
     *
     * @return void
     */
    protected function complete(\Nails\Invoice\Resource\Payment $oPayment)
    {
        /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);
        /** @var Input $oInput */
        $oInput = Factory::service('Input');

        $this->data['oPayment'] = $oPayment;
        $this->data['oInvoice'] = $oPayment->invoice;

        if ($oPayment->status->id === $oPaymentModel::STATUS_FAILED) {

            //  Payments which FAILED should be ignored
            show404();

        } elseif ($oPayment->status->id === $oPaymentModel::STATUS_COMPLETE) {

            //  Payment is already complete
            if ($oPayment->urls->success) {
                redirect($oPayment->urls->success);
            } else {
                redirect($oPayment->urls->thanks);
            }

        } elseif ($oPayment->status->id === $oPaymentModel::STATUS_PROCESSING) {

            //  Payment is already complete and is being processed
            if ($oPayment->urls->success) {
                redirect($oPayment->urls->success);
            } else {
                redirect($oPayment->urls->processing);
            }

        } else {

            try {

                //  Set up CompleteRequest object
                /** @var CompleteRequest $oCompleteRequest */
                $oCompleteRequest = Factory::factory('CompleteRequest', Constants::MODULE_SLUG);

                //  Set the driver to use for the request
                $oCompleteRequest->setDriver($oPayment->driver);

                //  Set the payment we're completing
                $oCompleteRequest->setPayment($oPayment->id);

                //  Set the invoice we're completing
                $oCompleteRequest->setInvoice($oPayment->invoice->id);

                //  Set the success URL, if there is one
                $oCompleteRequest->setSuccessUrl($oPayment->urls->success);

                //  Attempt completion
                $oCompleteResponse = $oCompleteRequest->execute(
                    $oInput->get(),
                    $oInput->post()
                );

                if ($oCompleteResponse->isProcessing()) {

                    //  Payment was successful but has not been confirmed
                    if ($oCompleteRequest->getSuccessUrl()) {
                        redirect($oCompleteRequest->getSuccessUrl());
                    } else {
                        redirect($oPayment->urls->processing);
                    }

                } elseif ($oCompleteResponse->isComplete()) {

                    //  Payment has completed fully
                    if ($oCompleteRequest->getSuccessUrl()) {
                        redirect($oCompleteRequest->getSuccessUrl());
                    } else {
                        redirect($oPayment->urls->thanks);
                    }

                } elseif ($oCompleteResponse->isFailed()) {
                    throw new NailsException('Payment failed: ' . $oCompleteResponse->getErrorMessageUser());
                } else {
                    throw new NailsException('Payment failed.');
                }

            } catch (Exception $e) {
                $this->oUserFeedback->error($e->getMessage());
                redirect($oPayment->invoice->urls->payment);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Shows a thank you page
     *
     * @param \Nails\Invoice\Resource\Payment $oPayment The invoice object
     *
     * @return void
     */
    protected function thanks(\Nails\Invoice\Resource\Payment $oPayment)
    {
        if ($oPayment->status->id === 'PROCESSING') {
            redirect($oPayment->urls->processing);
        } elseif ($oPayment->status->id !== 'COMPLETE') {
            show404();
        }

        $this->data['oPayment']       = $oPayment;
        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        // --------------------------------------------------------------------------

        $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/thanks/index.php');

        Factory::service('View')
            ->load([
                'structure/header',
                'invoice/thanks/index',
                'structure/footer',
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Shows a thank you page which informs the user that their payment is processing
     *
     * @param \Nails\Invoice\Resource\Payment $oPayment The invoice object
     *
     * @return void
     */
    protected function processing(\Nails\Invoice\Resource\Payment $oPayment)
    {
        if ($oPayment->status->id === 'COMPLETE') {
            redirect($oPayment->urls->thanks);
        } elseif ($oPayment->status->id !== 'PROCESSING') {
            show404();
        }

        $this->data['oPayment']       = $oPayment;
        $this->data['headerOverride'] = 'structure/header/blank';
        $this->data['footerOverride'] = 'structure/footer/blank';

        // --------------------------------------------------------------------------

        $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/thanks/processing.php');

        Factory::service('View')
            ->load([
                'structure/header',
                'invoice/thanks/processing',
                'structure/footer',
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Remap requests for valid payments to the appropriate controller method
     *
     * @return void
     */
    public function _remap()
    {
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);

        $iPaymentId    = (int) $oUri->rsegment(2);
        $sPaymentToken = $oUri->rsegment(3);
        $sMethod       = $oUri->rsegment(4);
        $oPayment      = $oPaymentModel->getById($iPaymentId, ['expand' => ['invoice']]);

        if (empty($oPayment) || $sPaymentToken !== $oPayment->token || !method_exists($this, $sMethod)) {
            show404();
        }

        $this->{$sMethod}($oPayment);
    }
}
