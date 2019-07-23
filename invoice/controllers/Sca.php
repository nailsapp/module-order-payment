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

use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Controller\Base;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Sca
 */
class Sca extends Base
{
    public function index()
    {
        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', 'nails/module-invoice');
        /** @var PaymentDriver $oPaymentDriverService */
        $oPaymentDriverService = Factory::service('PaymentDriver', 'nails/module-invoice');

        $oPayment = $oPaymentModel->getByToken($oUri->segment(4));
        if (empty($oPayment) || md5($oPayment->sca_data) !== $oUri->segment(5)) {
            show404();
        }

        //  Clears loaded styles, so load first in case the driver laods anything
        $this->loadStyles(NAILS_APP_PATH . 'application/modules/invoice/views/pay/sca.php');

        try {

            $oDriver  = $oPaymentDriverService->getInstance($oPayment->driver->slug);
            $aScaData = json_decode($oPayment->sca_data, JSON_OBJECT_AS_ARRAY);

            if ($oUri->segment(6) === 'complete') {
                $oResponse = $oDriver->scaComplete($aScaData);
                //  @todo (Pablo - 2019-07-23) - Update payment record and redirect
                dd($oResponse);
            }

            $oDriver->scaRequest(
                $aScaData,
                siteUrl('invoice/payment/sca/' . $oPayment->token . '/' . $oUri->segment(5) . '/complete')
            );

        } catch (\Exception $e) {
            //  @todo (Pablo - 2019-07-23) - Handle errors
            d($e);
        }

        Factory::service('View')
            ->load([
                'structure/header/blank',
                'invoice/pay/sca',
                'structure/footer/blank',
            ]);
    }
}
