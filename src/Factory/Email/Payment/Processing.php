<?php

namespace Nails\Invoice\Factory\Email\Payment;

use Nails\Common\Exception\FactoryException;
use Nails\Email\Factory\Email;
use Nails\Factory;
use Nails\Invoice\Constants;

/**
 * Class Processing
 *
 * @package Nails\Invoice\Factory\Email\Payment
 */
class Processing extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected static $sType = 'payment_processing_receipt';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return array
     * @throws FactoryException
     */
    public function getTestData(): array
    {
        /** @var Complete $oEmail */
        $oEmail = Factory::factory('EmailPaymentComplete', Constants::MODULE_SLUG);
        return $oEmail->getTestData();
    }
}
