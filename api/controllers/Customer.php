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

namespace Nails\Api\Invoice;

use Nails\Factory;
use Nails\Api\Controller\Base;

class Customer extends Base
{
    /**
     * Search for a customer
     */
    public function getSearch()
    {
        if (!userHasPermission('admin:invoice:customer:manage')) {

            return array(
                'status' => 401,
                'error' => 'You are not authorised to search customers.'
            );

        } else {

            $sKeywords      = $this->input->get('keywords');
            $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');

            if (strlen($sKeywords) >= 3) {

                $oResult = $oCustomerModel->search($sKeywords);
                $aOut    = array();

                foreach ($oResult->data as $oCustomer) {
                    $aOut[] = $this->formatCustomer($oCustomer);
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
     * Returns a customer by their ID
     * @param  string $iId The customer's ID
     * @return array
     */
    public function getId($iId = null)
    {
        $iId = (int) $iId ?: (int) $this->input->get('id');

        if (empty($iId)) {
            return array(
                'status' => 404
            );
        }

        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
        $oCustomer      = $oCustomerModel->getById($iId);

        if (empty($oCustomer)) {

            return array(
                'status' => 404
            );

        } else {

            return array(
                'data' => $this->formatCustomer($oCustomer)
            );
        }
    }

    // --------------------------------------------------------------------------

    public function formatCustomer($oCustomer)
    {
        return array(
            'id'    => $oCustomer->id,
            'label' => $oCustomer->label
        );
    }
}
