<?php

namespace Nails\Invoice\Factory\Email\Invoice;

use DateTime;
use Nails\Common\Exception\FactoryException;
use Nails\Email\Factory\Email;
use Nails\Factory;

/**
 * Class Send
 *
 * @package Nails\Invoice\Factory\Email\Invoice
 */
class Send extends Email
{
    /**
     * The email's type
     *
     * @var string
     */
    protected $sType = 'send_invoice';

    // --------------------------------------------------------------------------

    /**
     * Returns test data to use when sending test emails
     *
     * @return array
     * @throws FactoryException
     */
    public function getTestData(): array
    {
        /** @var DateTime $now */
        $now = Factory::factory('DateTime');

        return [
            'invoice' => [
                'id'       => 123,
                'ref'      => '1234-ABCDEF',
                'due'      => $now->format('l jS F Y'),
                'dated'    => $now->format('l jS F Y'),
                'customer' => [
                    'id'    => 123,
                    'label' => 'Jenny Jones',
                ],
                'address'  => [
                    'billing'  => [
                        'label'    => 'Jenny Jones',
                        'line_1'   => 'Jones House',
                        'line_2'   => '123 Main Street',
                        'town'     => 'London',
                        'postcode' => 'E1 1AB',
                        'country'  => 'GB',
                    ],
                    'delivery' => [
                        'label'    => 'Jenny Jones',
                        'line_1'   => 'Jones House',
                        'line_2'   => '123 Main Street',
                        'town'     => 'London',
                        'postcode' => 'E1 1AB',
                        'country'  => 'GB',
                    ],
                ],
                'urls'     => [
                    'view'     => 'https://example.com',
                    'payment'  => 'https://example.com',
                    'download' => 'https://example.com',
                ],
                'totals'   => [
                    'sub'   => '£10.00',
                    'tax'   => '£2.00',
                    'grand' => '£12.00',
                ],
                'items'    => [
                    [
                        'id'       => 123,
                        'label'    => 'A Line Item',
                        'body'     => 'Etiam porta sem malesuada magna mollis euismod.',
                        'quantity' => 1,
                        'totals'   => [
                            'sub'   => '£5.00',
                            'tax'   => '£1.00',
                            'grand' => '£6.00',
                        ],
                    ],
                    [
                        'id'       => 123,
                        'label'    => 'A Line Item',
                        'body'     => 'Etiam porta sem malesuada magna mollis euismod.',
                        'quantity' => 1,
                        'totals'   => [
                            'sub'   => '£5.00',
                            'tax'   => '£1.00',
                            'grand' => '£6.00',
                        ],
                    ],
                ],
            ],
        ];
    }
}
