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

use Nails\Factory;

class Payment extends NAILS_Controller
{
    /**
     * Completes a payment
     * @param  object $oPayment The invoice object
     * @return void
     */
    protected function complete($oPayment)
    {
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $sPaymentClass = get_class($oPaymentModel);

        if ($oPayment->status !== $sPaymentClass::STATUS_PENDING) {
            show_404();
        }

        try {
            //  Set up CompleteRequest object
            $oCompleteRequest = Factory::factory('CompleteRequest', 'nailsapp/module-invoice');

            //  Set the driver to use for the request
            $oCompleteRequest->setDriver($oPayment->driver->slug);

            //  Set the payment we're completing
            $oCompleteRequest->setPayment($oPayment->id);

            //  Attempt completion
            $oResult = $oCompleteRequest->complete(
                $this->input->get(),
                $this->input->post()
            );

            if ($oResult->isOk()) {

                //  Payment was successfull; head to wherever the charge response says to go
                dump('Payment was successful');

            } elseif ($oResult->isFail()) {

                throw new NailsException('Payment failed: ' . $oResult->getError()->user, 1);

            } else {

                throw new NailsException('Payment failed.', 1);
            }

        } catch (\Exception $e) {

            dump('Payment failed');
            dump($e->getMessage());
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Remap requests for valid payments to the appropriate controller method
     * @return void
     */
    public function _remap()
    {
        $iPaymentId    = (int) $this->uri->rsegment(2);
        $sPaymentToken = $this->uri->rsegment(3);
        $sMethod       = $this->uri->rsegment(4);
        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $oPayment      = $oPaymentModel->getById($iPaymentId);

        if (empty($oPayment) || $sPaymentToken !== $oPayment->token || !method_exists($this, $sMethod)) {
            show_404();
        }

        $this->{$sMethod}($oPayment);
    }
}
