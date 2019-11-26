<?php

/**
 * Manage customers
 *
 * @package     Nails
 * @subpackage  module-invoice
 * @category    AdminController
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Invoice;

use Nails\Admin\Controller\DefaultController;
use Nails\Invoice\Constants;
use Nails\Invoice\Resource;

/**
 * Class Customer
 *
 * @package Nails\Admin\Invoice
 */
class Customer extends DefaultController
{
    const CONFIG_MODEL_NAME     = 'Customer';
    const CONFIG_MODEL_PROVIDER = Constants::MODULE_SLUG;
    const CONFIG_SIDEBAR_GROUP  = 'Invoices &amp; Payments';
    const CONFIG_SIDEBAR_ICON   = 'fa-credit-card';
    const CONFIG_PERMISSION     = 'invoice:customer';
    const CONFIG_INDEX_DATA     = [
        'expand' => ['invoices'],
    ];

    // --------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();

        //  Update existing buttons
        $iDeleteKey     = arraySearchMulti(lang('action_delete'), 'label', $this->aConfig['INDEX_ROW_BUTTONS']);
        $cParentEnabled = clone($this->aConfig['INDEX_ROW_BUTTONS'][$iDeleteKey]['enabled']);

        $this->aConfig['INDEX_ROW_BUTTONS'][$iDeleteKey]['enabled'] = function (Resource\Customer $oCustomer) use ($cParentEnabled) {
            return $cParentEnabled($oCustomer) && $oCustomer->invoices->count === 0;
        };

        //  Additional buttons
        if (userHasPermission('admin:invoice:invoice:create')) {
            $this->aConfig['INDEX_ROW_BUTTONS'][] = [
                'url'   => siteUrl('admin/invoice/invoice/create?customer_id={{id}}'),
                'label' => 'New Invoice',
                'class' => 'btn-success',
            ];
        }

        if (userHasPermission('admin:invoice:invoice:manage')) {
            $this->aConfig['INDEX_ROW_BUTTONS'][] = [
                'url'     => siteUrl('admin/invoice/invoice?customer_id={{id}}'),
                'label'   => 'View Invoices',
                'class'   => 'btn-warning',
                'enabled' => function (Resource\Customer $oCustomer) {
                    return $oCustomer->invoices->count > 0;
                },
            ];
        }

        //  Update cells
        $this->aConfig['INDEX_FIELDS']['Label'] = function (Resource\Customer $oCustomer) {
            $sOut = '';
            if (!empty($oCustomer->first_name)) {
                $sOut .= $oCustomer->first_name . ' ' . $oCustomer->last_name . '<br />';
            }

            if (!empty($oCustomer->billing_email)) {
                $sOut .= mailto($oCustomer->billing_email);
            } else {
                $sOut .= mailto($oCustomer->email);
            }

            return $sOut;
        };
    }
}
