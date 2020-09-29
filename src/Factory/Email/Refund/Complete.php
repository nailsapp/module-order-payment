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
    protected $sType = 'refund_complete_receipt';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return array
     */
    public function getTestData(): array
    {
        return [
            'refund'  => [
                'id'     => 123,
                'ref'    => '1234-ABCDEF',
                'reason' => 'Donec ullamcorper nulla non metus auctor fringilla.',
                'amount' => '£12.00',
            ],
            'payment' => [
                'id'     => 123,
                'ref'    => '1234-ABCDEF',
                'amount' => '£12.00',
            ],
            'invoice' => [
                'id'  => 123,
                'ref' => '1234-ABCDEF',
            ],
        ];
    }
}
