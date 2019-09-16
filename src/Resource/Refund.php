<?php

/**
 * This class represents objects dispensed by the Refund model
 *
 * @package  Nails\Invoice\Resource
 * @category resource
 */

namespace Nails\Invoice\Resource;

use Nails\Common\Resource\Entity;
use Nails\Currency\Service\Currency;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Resource\Refund\Status;

class Refund extends Entity
{
    /**
     * The refund's payment ID
     *
     * @var int
     */
    public $payment_id;

    /**
     * The refund's invoice ID
     *
     * @var int
     */
    public $invoice_id;

    /**
     * The refund's ref
     *
     * @var string
     */
    public $ref;

    /**
     * The refund's reason
     *
     * @var string
     */
    public $reason;

    /**
     * The refund's status
     *
     * @var Status
     */
    public $status;

    /**
     * The refund's transaction ID
     *
     * @var string
     */
    public $transaction_id;

    /**
     * The refund's fail message
     *
     * @var string
     */
    public $fail_msg;

    /**
     * The refund's fail code
     *
     * @var string
     */
    public $fail_code;

    /**
     * @var \Nails\Currency\Resource\Currency
     */
    public $currency;


    public $amount;
    public $fee;

    // --------------------------------------------------------------------------

    /**
     * Refund constructor.
     *
     * @param array $mObj
     */
    public function __construct($mObj = [])
    {
        parent::__construct($mObj);

        // --------------------------------------------------------------------------

        /** @var \Nails\Invoice\Model\Refund $oModel */
        $oModel    = Factory::model('Refund', Constants::MODULE_SLUG);
        $aStatuses = $oModel->getStatusesHuman();

        $this->status = Factory::resource(
            'RefundStatus',
            Constants::MODULE_SLUG,
            (object) [
                'id'    => $mObj->status,
                'label' => $aStatuses[$mObj->status],
            ]
        );

        // --------------------------------------------------------------------------

        //  Currency
        /** @var Currency $oCurrency */
        $oCurrency      = Factory::service('Currency', \Nails\Currency\Constants::MODULE_SLUG);
        $this->currency = $oCurrency->getByIsoCode($mObj->currency);

        // --------------------------------------------------------------------------

        //  Amounts and values
        $this->amount = Factory::resource(
            'RefundAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $mObj->amount,
            ]
        );

        $this->fee = Factory::resource(
            'RefundAmount',
            Constants::MODULE_SLUG,
            (object) [
                'currency' => $this->currency,
                'raw'      => $mObj->fee,
            ]
        );
    }
}
