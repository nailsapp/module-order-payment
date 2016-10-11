<?php

/**
 * Returns information about invoices
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Api\Invoice;

use Nails\Factory;
use Nails\Api\Controller\Base;

class Invoice extends Base
{
    /**
     * Search for an invoice
     */
    public function getSearch()
    {
        if (!userHasPermission('admin:invoice:invoice:manage')) {

            return array(
                'status' => 401,
                'error' => 'You are not authorised to search invoices.'
            );

        } else {

            $oInput        = Factory::service('Input');
            $sKeywords     = $oInput->get('keywords');
            $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');

            if (strlen($sKeywords) >= 3) {

                $oResult = $oInvoiceModel->search($sKeywords, null, null, array('includeCustomer' => true));
                $aOut    = array();

                foreach ($oResult->data as $oInvoice) {
                    $aOut[] = $this->formatInvoice($oInvoice);
                }

                return array(
                    'data' => $aOut
                );

            } else {

                return array(
                    'status' => 400,
                    'error' => 'Search term must be 3 characters or longer.'
                );
            }
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an invoice by its ID
     * @param  string $iId The invoice's ID
     * @return array
     */
    public function getId($iId = null)
    {
        $oInput = Factory::service('Input');
        $iId    = (int) $iId ?: (int) $oInput->get('id');

        if (empty($iId)) {
            return array(
                'status' => 404
            );
        }

        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oInvoice      = $oInvoiceModel->getById($iId, array('includeCustomer' => true));

        if (empty($oInvoice)) {

            return array(
                'status' => 404
            );

        } else {

            return array(
                'data' => $this->formatInvoice($oInvoice)
            );
        }
    }

    // --------------------------------------------------------------------------

    public function formatInvoice($oInvoice)
    {
        return array(
            'id'       => $oInvoice->id,
            'ref'      => $oInvoice->ref,
            'terms'    => $oInvoice->terms,
            'dated'    => $oInvoice->dated->raw,
            'due'      => $oInvoice->due->raw,
            'paid'     => $oInvoice->paid->raw,
            'state'    => $oInvoice->state,
            'currency' => $oInvoice->currency->code,
            'totals'   => $oInvoice->totals,
            'urls'     => $oInvoice->urls,
            'customer' => empty($oInvoice->customer) ? null : array(
                'id'    => $oInvoice->customer->id,
                'label' => $oInvoice->customer->label,
            )
        );
    }
}
