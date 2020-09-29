<?php

namespace Nails\Invoice\Factory\Email\Refund;

use Nails\Email\Factory\Email;

/**
 * Class Complete
 *
 * @package Nails\Invoice\Factory\Email\Refund
 */
class Complete extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected static $sType = 'refund_complete_receipt';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return array
     */
    public function getTestData(): array
    {
        //  @todo (Pablo 29/09/2020) - implement method
        return [];
    }
}
