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

use Nails\Address;
use Nails\Admin\Controller\DefaultController;
use Nails\Admin\Factory\Model\Field\DynamicTable;
use Nails\Common\Service\Country;
use Nails\Common\Service\Database;
use Nails\Factory;
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

        //  Ignore label
        $this->aConfig['CREATE_IGNORE_FIELDS'][] = 'label';
        $this->aConfig['EDIT_IGNORE_FIELDS'][]   = 'label';

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

        // --------------------------------------------------------------------------

        /** @var Country $oCountryService */
        $oCountryService = Factory::service('Country');

        /** @var DynamicTable $oField */
        $oField = Factory::factory('ModelFieldDynamicTable', 'nails/module-admin');
        $oField
            ->setKey('addresses')
            ->setLabel('Addresses')
            ->setFieldset('Addresses')
            ->setColumns([
                'Details' => implode('', [
                    '<input type="hidden" name="addresses[{{index}}][id]" value="{{id}}">' .
                    '<input type="text" name="addresses[{{index}}][line_1]" placeholder="Line 1" value="{{line_1}}">',
                    '<input type="text" name="addresses[{{index}}][line_2]" placeholder="Line 2" value="{{line_2}}">',
                    '<input type="text" name="addresses[{{index}}][line_3]" placeholder="Line 3" value="{{line_3}}">',
                    '<input type="text" name="addresses[{{index}}][town]" placeholder="Town" value="{{town}}">',
                    '<input type="text" name="addresses[{{index}}][region]" placeholder="Region" value="{{region}}">',
                    '<input type="text" name="addresses[{{index}}][postcode]" placeholder="Postcode" value="{{postcode}}">',
                    form_dropdown(
                        'addresses[{{index}}][country]',
                        ['Select Country'] + $oCountryService->getCountriesFlat(),
                        null,
                        'data-dynamic-table-value="{{country.iso}}"'
                    ),
                ]),
            ]);

        $this->aConfig['FIELDS']['addresses'] = $oField;
    }

    // --------------------------------------------------------------------------

    protected function loadEditViewData(\Nails\Common\Resource $oItem = null): void
    {
        $oItem->addresses = $oItem->addresses();
        parent::loadEditViewData($oItem);
    }

    // --------------------------------------------------------------------------

    protected function getPostObject(): array
    {
        $aData = parent::getPostObject();
        unset($aData['addresses']);
        return $aData;
    }

    // --------------------------------------------------------------------------

    protected function afterCreateAndEdit(
        $sMode,
        \Nails\Common\Resource $oNewItem,
        \Nails\Common\Resource $oOldItem = null
    ): void {

        parent::afterCreateAndEdit($sMode, $oNewItem, $oOldItem);

        /** @var Database $oDb */
        $oDb = Factory::service('Database');
        /** @var Address\Model\Address $oAddressModel */
        $oAddressModel = Factory::model('Address', Address\Constants::MODULE_SLUG);
        /** @var Address\Model\Address\Associated $oAddressAssociatedModel */
        $oAddressAssociatedModel = Factory::model('AddressAssociated', Address\Constants::MODULE_SLUG);

        /** @var array[] $aAddresses */
        $aAddresses = getFromArray('addresses', parent::getPostObject(), []);
        /** @var int[] $aAddressIds */
        $aAddressIds = [];

        //  Update/create addresses
        foreach ($aAddresses as $aAddress) {
            if (!empty($aAddress['id'])) {
                $oAddressModel->update($aAddress['id'], $aAddress);
                $aAddressIds[] = (int) $aAddress['id'];
            } else {
                $aAddressIds[] = $oAddressModel->create($aAddress);
            }
        }

        //  Delete old associations (old addresses are left as they might be in use)
        $oAddressAssociatedModel->deleteWhere([
            'address_id NOT IN (' . implode(',', $aAddressIds) . ')',
            ['associated_type', Resource\Customer::class],
            ['associated_id', $oNewItem->id],
        ]);

        $aExistingAssociations = $oAddressAssociatedModel->getAll([
            'where' => [
                ['associated_type', Resource\Customer::class],
                ['associated_id', $oNewItem->id],
            ],
        ]);
        $aExistingAddressIds   = arrayExtractProperty($aExistingAssociations, 'address_id');

        //  Insert new associations
        foreach ($aAddressIds as $iAddressId) {
            if (!in_array($iAddressId, $aExistingAddressIds)) {
                $oAddressAssociatedModel->create([
                    'address_id'      => $iAddressId,
                    'associated_type' => Resource\Customer::class,
                    'associated_id'   => $oNewItem->id,
                ]);
            }
        }
    }
}

