<?php

namespace Nails\Invoice\Factory\Email\Refund;

use Nails\Common\Exception\FactoryException;
use Nails\Email\Factory\Email;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Factory\Email\Payment\Complete;

/**
 * Class Processing
 *
 * @package Nails\Invoice\Factory\Email\Refund
 */
class Processing extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected $sType = 'refund_processing_receipt';

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
        $oEmail = Factory::factory('EmailRefundComplete', Constants::MODULE_SLUG);
        return $oEmail->getTestData();
    }
}
