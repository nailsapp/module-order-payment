<?php

/**
 * Attempts a SCA authorisation
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
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Exception\ChargeRequestException;
use Nails\Invoice\Exception\RequestException;

/**
 * Class ScaRequest
 *
 * @package Nails\Invoice\Factory
 */
class ScaRequest extends RequestBase
{
    /**
     * Executes the SCA request
     *
     * @return ScaResponse
     * @throws FactoryException
     * @throws ModelException
     * @throws ChargeRequestException
     * @throws RequestException
     */
    public function execute(): ScaResponse
    {
        /** @var ScaResponse $oScaResponse */
        $oScaResponse = Factory::factory('ScaResponse', Constants::MODULE_SLUG);
        /** @var \Nails\Invoice\Factory\ChargeRequest $oChargeRequest */
        $oChargeRequest = Factory::factory('ChargeRequest', Constants::MODULE_SLUG);

        if (!$this->getPayment()->isPending()) {
            $oScaResponse
                ->setStatusFailed(
                    'Payment is not in a pending state',
                    null,
                    'Payment has already been processed'
                );

        } else {
            $oScaResponse = $this->oDriver->sca(
                $oScaResponse,
                $this->oPayment->sca_data,
                $oChargeRequest::compileScaUrl($this->oPayment, $this->oPayment->sca_data)
            );
        }

        $oScaResponse->lock();

        // --------------------------------------------------------------------------

        if ($oScaResponse->isComplete()) {
            $this->setPaymentComplete(
                $oScaResponse->getTransactionId(),
                $oScaResponse->getFee()
            );
        } elseif ($oScaResponse->isFailed()) {
            $this->setPaymentFailed(
                $oScaResponse->getErrorMessage(),
                $oScaResponse->getErrorCode()
            );
        }

        // --------------------------------------------------------------------------

        return $oScaResponse;
    }
}
