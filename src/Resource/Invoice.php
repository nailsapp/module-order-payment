<?php

/**
 * This class represents objects dispensed by the Invoice model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource;
use Nails\Currency\Resource\Currency;
use stdClass;

class Invoice extends Resource
{
    /**
     * The invoice's ID
     *
     * @var int
     */
    public $id;

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
     * @var mixed|null
     */
    public $callback_data;

    /**
     * The invoice's created date
     *
     * @var Resource\DateTime
     */
    public $created;

    /**
     * The invoice's creator
     *
     * @var int|Resource|null
     */
    public $created_by;

    /**
     * The invoice's modification date
     *
     * @var Resource\DateTime
     */
    public $modified;

    /**
     * The invoice's modifier
     *
     * @var int|Resource|null
     */
    public $modified_by;
}
