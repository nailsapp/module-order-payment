<?php

/**
 * Returns information about customers
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

class Customer extends Base
{
    /**
     * Search for a customer
     */
    public function getSearch()
    {
        if (!userHasPermission('admin:invoice:customer:manage')) {
            throw new ApiException('You are not authorised to search customers.', 401);
        }

        $oInput         = Factory::service('Input');
        $sKeywords      = $oInput->get('keywords');
        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');

        if (strlen($sKeywords) <= 3) {
            throw new ApiException('Search term must be 3 characters or longer.', 400);
        }

        $oResult = $oCustomerModel->search($sKeywords);
        $aOut    = [];

        foreach ($oResult->data as $oCustomer) {
            $aOut[] = $this->formatCustomer($oCustomer);
        }

        return Factory::factory('ApiResponse', 'nailsapp/module-api')
                      ->setData($aOut);
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a customer by their ID
     *
     * @param  string $iId The customer's ID
     *
     * @return array
     */
    public function getId($iId = null)
    {
        $oInput = Factory::service('Input');
        $iId    = (int) $iId ?: (int) $oInput->get('id');

        if (empty($iId)) {
            throw new ApiException('Invalid Customer ID', 404);
        }

        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
        $oCustomer      = $oCustomerModel->getById($iId);

        if (empty($oCustomer)) {
            throw new ApiException('Invalid Customer ID', 404);
        }

        return Factory::factory('ApiResponse', 'nailsapp/module-api')
                      ->setData($this->formatCustomer($oCustomer));
    }

    // --------------------------------------------------------------------------

    public function formatCustomer($oCustomer)
    {
        return [
            'id'    => $oCustomer->id,
            'label' => $oCustomer->label,
        ];
    }
}
