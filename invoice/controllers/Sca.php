<?php

/**
 * Checkout SCA
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    controller
 * @author      Nails Dev Team
 * @link
 */

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\ModelException;
use Nails\Common\Service\Session;
use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Exception\ChargeRequestException;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Exception\RequestException;
use Nails\Invoice\Factory\ScaRequest;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Sca
 */
class Sca extends Base
{
    /**
     * @throws InvoiceException
     * @throws FactoryException
     * @throws ModelException
     * @throws ChargeRequestException
     * @throws RequestException
     */
    public function index()
    {
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);
        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);

        /** @var \Nails\Invoice\Resource\Payment $oPayment */
        $oPayment = $oPaymentModel->getByToken($oUri->segment(4), ['expand' => ['invoice']]);
        if (empty($oPayment) || md5($oPayment->sca_data) !== $oUri->segment(5)) {
            show404();
        }

        // --------------------------------------------------------------------------

        /** @var ScaRequest $oScaRequest */
        $oScaRequest  = Factory::factory('ScaRequest', Constants::MODULE_SLUG);
        $oScaResponse = $oScaRequest
            ->setPayment($oPayment->id)
            ->setInvoice($oPayment->invoice->id)
            ->setDriver($oPayment->driver)
            ->execute();

        if ($oScaResponse->isComplete()) {
            if (!empty($oPayment->urls->success)) {
                redirect($oPayment->urls->success);
            } else {
                redirect($oPayment->urls->thanks);
            }

        } elseif ($oScaResponse->isRedirect()) {
            redirect($oScaResponse->getRedirectUrl());

        } elseif ($oScaResponse->isFailed()) {

            $oError = $oScaResponse->getError();

            /** @var Session $oSession */
            $oSession = Factory::service('Session');
            $oSession->setFlashData('error', $oError->user);

            if (!empty($oPayment->urls->error)) {
                redirect($oPayment->urls->error);
            } else {

                $sUrl    = $oPayment->invoice->urls->payment;
                $aParams = array_filter([
                    'url_success' => $oPayment->urls->success,
                    'url_error'   => $oPayment->urls->error,
                    'url_cancel'  => $oPayment->urls->cancel,
                ]);
                if (!empty($aParams)) {
                    $sUrl .= '?' . http_build_query($aParams);
                }

                redirect($sUrl);
            }

        } else {
            throw new InvoiceException('Unhandled SCA status');
        }
    }
}
