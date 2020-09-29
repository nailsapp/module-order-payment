<?php

/**
 * Payment model
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Model
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Invoice\Model\Invoice;

use Nails\Common\Model\Base;
use Nails\Invoice\Constants;
use Nails\Invoice\Exception\InvoiceException;
use Nails\Invoice\Resource\Invoice;

/**
 * Class Email
 *
 * @package Nails\Invoice\Model\Invoice
 */
class Email extends Base
{
    /**
     * The table this model represents
     *
     * @var string
     */
    const TABLE = NAILS_DB_PREFIX . 'invoice_email';

    /**
     * The name of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_NAME = 'InvoiceEmail';

    /**
     * The provider of the resource to use (as passed to \Nails\Factory::resource())
     *
     * @var string
     */
    const RESOURCE_PROVIDER = Constants::MODULE_SLUG;

    // --------------------------------------------------------------------------

    /**
     * Email constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->defaultSortColumn = 'created';
        $this
            ->addExpandableField([
                'trigger'   => 'invoice',
                'model'     => 'Invoice',
                'provider'  => Constants::MODULE_SLUG,
                'id_column' => 'inivoice_id',
            ])
            ->addExpandableField([
                'trigger'   => 'email',
                'model'     => 'Email',
                'provider'  => \Nails\Email\Constants::MODULE_SLUG,
                'id_column' => 'email_id',
            ]);
    }

    // --------------------------------------------------------------------------

    /**
     * Sends an email to recipients and records it against an invoice
     *
     * @param string[]                   $aEmails Array of emails
     * @param \Nails\Email\Factory\Email $oEmail
     * @param Invoice                    $oInvoice
     *
     * @throws InvoiceException
     */
    public function sendEmails(array $aEmails, \Nails\Email\Factory\Email $oEmail, Invoice $oInvoice)
    {
        $aEmails = array_unique($aEmails);
        $aEmails = array_filter($aEmails);

        foreach ($aEmails as $sEmail) {
            try {

                $oEmail
                    ->to($sEmail)
                    ->send();

                $aGeneratedEmails = $oEmail->getGeneratedEmails();
                $oLastEmail       = reset($aGeneratedEmails);

                $this->create([
                    'invoice_id' => $oInvoice->id,
                    'email_id'   => $oLastEmail->id,
                    'email_type' => $oLastEmail->type,
                    'recipient'  => $sEmail,
                ]);

            } catch (\Exception $e) {
                throw new InvoiceException($e->getMessage(), null, $e);
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Formats a single object
     *
     * The getAll() method iterates over each returned item with this method so as to
     * correctly format the output. Use this to cast integers and booleans and/or organise data into objects.
     *
     * @param object $oObj      A reference to the object being formatted.
     * @param array  $aData     The same data array which is passed to _getcount_common, for reference if needed
     * @param array  $aIntegers Fields which should be cast as integers if numerical and not null
     * @param array  $aBools    Fields which should be cast as booleans if not null
     * @param array  $aFloats   Fields which should be cast as floats if not null
     *
     * @return void
     */
    protected function formatObject(
        &$oObj,
        array $aData = [],
        array $aIntegers = [],
        array $aBools = [],
        array $aFloats = []
    ) {
        $aIntegers[] = 'invoice_id';
        $aIntegers[] = 'email_id';
        parent::formatObject($oObj, $aData, $aIntegers, $aBools, $aFloats);
    }
}
