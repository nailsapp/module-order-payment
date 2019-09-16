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

namespace Nails\Invoice\Api\Controller;

use Nails\Api\Controller\Base;
use Nails\Api\Exception\ApiException;
use Nails\Factory;
use Nails\Invoice\Constants;

/**
 * Class Invoice
 *
 * @package Nails\Invoice\Api\Controller
 */
class Invoice extends Base
{
    /**
     * Search for an invoice
     */
    public function getSearch()
    {
        if (!userHasPermission('admin:invoice:invoice:manage')) {
            throw new ApiException('You are not authorised to search invoices.', 401);
        }

        $oInput        = Factory::service('Input');
        $sKeywords     = $oInput->get('keywords');
        $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);

        if (strlen($sKeywords) >= 3) {
            throw new ApiException('Search term must be 3 characters or longer.', 400);
        }

        $oResult = $oInvoiceModel->search($sKeywords, null, null, ['expand' => ['customer']]);
        $aOut    = [];

        foreach ($oResult->data as $oInvoice) {
            $aOut[] = $this->formatInvoice($oInvoice);
        }

        return Factory::factory('ApiResponse', 'nails/module-api')
            ->setData($aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an invoice by its ID
     *
     * @param string $iId The invoice's ID
     *
     * @return array
     */
    public function getId($iId = null)
    {
        $oInput = Factory::service('Input');
        $iId    = (int) $iId ?: (int) $oInput->get('id');

        if (empty($iId)) {
            throw new ApiException('Invalid Invoice ID', 404);
        }

        $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);
        $oInvoice      = $oInvoiceModel->getById($iId, ['expand' => ['customer']]);

        if (empty($oInvoice)) {
            throw new ApiException('Invalid Invoice ID', 404);
        }

        return Factory::factory('ApiResponse', 'nails/module-api')
            ->setData($this->formatInvoice($oInvoice));
    }

    // --------------------------------------------------------------------------

    public function formatInvoice($oInvoice)
    {
        return [
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
            'customer' => empty($oInvoice->customer) ? null : [
                'id'    => $oInvoice->customer->id,
                'label' => $oInvoice->customer->label,
            ],
        ];
    }
}
