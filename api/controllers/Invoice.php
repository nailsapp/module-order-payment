<?php

/**
 * Admin API end points: Invoice
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Api\Invoice;

use Nails\Factory;

class Invoice extends \Nails\Api\Controller\Base
{
    public static $requiresAuthentication = true;
    protected $oInvoiceModel;

    // --------------------------------------------------------------------------

    /**
     * Construct the controller
     */
    public function __construct($oApiRouter)
    {
        parent::__construct($oApiRouter);
        $this->oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
    }

    // --------------------------------------------------------------------------

    /**
     * Generates a valid invoice ref
     * @return array
     */
    public function getGenerateRef()
    {
        if (!userHasPermission('admin:invoice:invoice:create') && !userHasPermission('admin:invoice:invoice:edit')) {

            return array(
                'status' => 401,
                'error'  => 'You do not have permission to create invoices.'
            );

        } else {

            return array(
                'ref' => $this->oInvoiceModel->generateValidRef()
            );
        }
    }
}
