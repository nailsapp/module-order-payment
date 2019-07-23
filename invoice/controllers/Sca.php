<?php

/**
 * Payment Strong Customer Authentication (SCA)
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

        $oPayment = $oPaymentModel->getByToken($oUri->segment(4));
        if (empty($oPayment) || md5($oPayment->sca_data) !== $oUri->segment(5)) {
            show404();
        }

        d($oPayment);
    }
}
