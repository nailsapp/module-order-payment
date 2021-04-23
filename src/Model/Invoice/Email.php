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

use Nails\Common\Exception\ModelException;
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

    /**
     * The default column to sort on
     *
     * @var string|null
     */
    const DEFAULT_SORT_COLUMN = 'created';

    // --------------------------------------------------------------------------

    /**
     * Email constructor.
     */
    /**
     * Email constructor.
     *
     * @throws ModelException
     */
    public function __construct()
    {
        parent::__construct();
        $this
            ->hasOne('invoice', 'Invoice', Constants::MODULE_SLUG)
            ->hasOne('email', 'Email', \Nails\Email\Constants::MODULE_SLUG);
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
                $this->create([
                    'invoice_id' => $oInvoice->id,
                    'email_type' => $oLastEmail->type,
                    'recipient'  => $sEmail,
                    'error'      => $e->getMessage(),
                ]);
                throw new InvoiceException($e->getMessage(), null, $e);
            }
        }
    }
}
