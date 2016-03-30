<?php

/**
 * Payment Refund model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model;

use Nails\Factory;
use Nails\Common\Model\Base;
use Nails\Invoice\Exception\PaymentException;

class PaymentRefund extends Base
{
    //  Statuses
    const STATUS_PENDING    = 'PENDING';
    const STATUS_PROCESSING = 'PROCESSING';
    const STATUS_COMPLETE   = 'COMPLETE';
    const STATUS_FAILED     = 'FAILED';

    // --------------------------------------------------------------------------

    /**
     * Currency values
     * @todo  make this way more dynamic
     */
    const CURRENCY_DECIMAL_PLACES = 2;
    const CURRENCY_CODE           = 'GBP';
    const CURRENCY_SYMBOL_HTML    = '&pound;';
    const CURRENCY_SYMBOL_TEXT    = '£';
    const CURRENCY_LOCALISE_VALUE = 100;

    // --------------------------------------------------------------------------

    /**
     * Construct the model
     */
    public function __construct()
    {
        parent::__construct();
        $this->table             = NAILS_DB_PREFIX . 'invoice_payment_refund';
        $this->tablePrefix       = 'pr';
        $this->defaultSortColumn = 'created';
    }

    // --------------------------------------------------------------------------

    /**
     * Returns all the statuses as an array
     * @return array
     */
    public function getStatuses()
    {
        return array(
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_FAILED
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of statsues with human friendly labels
     * @return array
     */
    public function getStatusesHuman()
    {
        return array(
            self::STATUS_PENDING    => 'Pending',
            self::STATUS_PROCESSING => 'Processing',
            self::STATUS_COMPLETE   => 'Complete',
            self::STATUS_FAILED     => 'Failed'
        );
    }

    // --------------------------------------------------------------------------

    /**
     * Retrieve all payments from the databases
     * @param  int     $iPage           The page number to return
     * @param  int     $iPerPage        The number of results per page
     * @param  array   $aData           Data to pass _to getcount_common()
     * @param  boolean $bIncludeDeleted Whether to include deleted results
     * @return array
     */
    public function getAll($iPage = null, $iPerPage = null, $aData = array(), $bIncludeDeleted = false)
    {
        $aItems = parent::getAll($iPage, $iPerPage, $aData, $bIncludeDeleted);

        if (!empty($aItems)) {

            if (!empty($aData['includeAll']) || !empty($aData['includeInvoice'])) {
                $this->getSingleAssociatedItem(
                    $aItems,
                    'invoice_id',
                    'invoice',
                    'Invoice',
                    'nailsapp/module-invoice',
                    array(
                        'includeCustomer' => true,
                        'includeItems'    => true
                    )
                );
            }
        }

        return $aItems;
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new payment refund
     * @param  array   $aData         The data to create the payment refund with
     * @param  boolean $bReturnObject Whether to return the complete payment refund object
     * @return mixed
     */
    public function create($aData = array(), $bReturnObject = false)
    {
        $oDb = Factory::service('Database');

        try {

            $oDb->trans_begin();

            if (empty($aData['ref'])) {
                $aData['ref'] = $this->generateValidRef();
            }

            $oPaymentRefund = parent::create($aData, true);

            if (!$oPaymentRefund) {
                throw new PaymentException('Failed to create payment refund.', 1);
            }

            $oDb->trans_commit();

            //  Trigger the payment.refund.created event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger($sPaymentClass::EVENT_PAYMENT_REFUND_CREATED, $oPaymentRefund);

            return $bReturnObject ? $oPaymentRefund : $oPaymentRefund->id;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Update a payment
     * @param  integer $iPaymentRefundId The ID of the payment refund to update
     * @param  array   $aData            The data to update the payment with
     * @return boolean
     */
    public function update($iPaymentRefundId, $aData = array())
    {
        $oDb = Factory::service('Database');

        try {

            $oDb->trans_begin();

            unset($aData['ref']);

            $bResult = parent::update($iPaymentRefundId, $aData);

            if (!$bResult) {
                throw new PaymentException('Failed to update payment refund.', 1);
            }

            $oDb->trans_commit();

            //  Trigger the payment.updated event
            $oPaymentEventHandler = Factory::model('PaymentEventHandler', 'nailsapp/module-invoice');
            $sPaymentClass        = get_class($oPaymentEventHandler);

            $oPaymentEventHandler->trigger(
                $sPaymentClass::EVENT_PAYMENT_REFUND_UPDATED,
                $this->getById($iPaymentRefundId)
            );

            return $bResult;

        } catch (\Exception $e) {

            $oDb->trans_rollback();
            $this->setError($e->getMessage());
            return false;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a valid invoice ref
     * @return string
     */
    public function generateValidRef()
    {
        Factory::helper('string');

        $oDb  = Factory::service('Database');
        $oNow = Factory::factory('DateTime');

        do {

            $sRef = $oNow->format('Ym') .'-' . strtoupper(random_string('alnum'));
            $oDb->where('ref', $sRef);
            $bRefExists = (bool) $oDb->count_all_results($this->table);

        } while ($bRefExists);

        return $sRef;
    }

    // --------------------------------------------------------------------------

    public function sendReceipt($iPaymentRefundId, $sEmailOverride = null)
    {
        //  @todo
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param  object $oObj      A reference to the object being formatted.
     * @param  array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param  array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param  array  $aBools    Fields which should be cast as booleans if not null
     * @param  array  $aFloats   Fields which should be cast as floats if not null
     * @return void
     */
    protected function formatObject(
        &$oObj,
        $aData = array(),
        $aIntegers = array(),
        $aBools = array(),
        $aFloats = array()
    ) {

        $aIntegers[] = 'payment_id';
        $aIntegers[] = 'amount';
        $aIntegers[] = 'fee';

        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);

        //  Status
        $aStatuses = $this->getStatuses();
        $sStatus   = $oObj->status;

        $oObj->status        = new \stdClass();
        $oObj->status->id    = $sStatus;
        $oObj->status->label = !empty($aStatuses[$sStatus]) ? $aStatuses[$sStatus] : ucfirst(strtolower($sStatus));

        //  Amount
        $iAmount = $oObj->amount;
        $oObj->amount                      = new \stdClass();
        $oObj->amount->base                = $iAmount;
        $oObj->amount->localised           = (float) number_format($oObj->amount->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES, '', '');
        $oObj->amount->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->amount->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);

        $iFee = $oObj->fee;
        $oObj->fee                      = new \stdClass();
        $oObj->fee->base                = $iFee;
        $oObj->fee->localised           = (float) number_format($oObj->fee->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES, '', '');
        $oObj->fee->localised_formatted = self::CURRENCY_SYMBOL_HTML . number_format($oObj->fee->base/self::CURRENCY_LOCALISE_VALUE, self::CURRENCY_DECIMAL_PLACES);
    }
}
