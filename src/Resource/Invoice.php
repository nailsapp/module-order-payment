<?php

/**
 * This class represents objects dispensed by the Invoice model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource\Entity;
use Nails\Currency\Resource\Currency;
use stdClass;

class Invoice extends Entity
{
    /**
     * The invoice's reference
     *
     * @var string
     */
    public $ref;

    /**
     * The invoice's token
     *
     * @var string
     */
    public $token;

    /**
     * The invoice's state
     *
     * @var stdClass
     */
    public $state;

    /**
     * The invoice's date
     *
     * @var Resource\DateTime
     */
    public $dated;

    /**
     * The invoice's terms, in days
     *
     * @var int
     */
    public $terms;

    /**
     * The invoice's due date
     *
     * @var Resource\DateTime
     */
    public $due;

    /**
     * The invoice's paid date
     *
     * @var Resource\DateTime
     */
    public $paid;

    /**
     * The invoice's email
     *
     * @var string
     */
    public $email;

    /**
     * The invoice's currency
     *
     * @var Currency
     */
    public $currency;

    /**
     * Any additional text
     *
     * @var string
     */
    public $additional_text;

    /**
     * Any callback data
     *
     * @var stdClass
     */
    public $callback_data;

    /**
     * Any payment data
     *
     * @var stdClass
     */
    public $payment_data;

    /**
     * The payemnt driver
     *
     * @var string|null
     */
    public $payment_driver;
}
