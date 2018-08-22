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

use Nails\Admin\Helper;
use Nails\Common\Exception\NailsException;
use Nails\Factory;
use Nails\Invoice\Controller\BaseAdmin;

class Customer extends BaseAdmin
{
    protected $oCustomerModel;

    // --------------------------------------------------------------------------

    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:invoice:customer:manage')) {

            $oNavGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $oNavGroup->setLabel('Invoices &amp; Payments');
            $oNavGroup->setIcon('fa-credit-card');
            $oNavGroup->addAction('Manage Customers');

            return $oNavGroup;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extra permissions for this controller
     * @return array
     */
    public static function permissions()
    {
        $permissions = parent::permissions();

        $permissions['manage'] = 'Can manage customers';
        $permissions['create'] = 'Can create customers';
        $permissions['edit']   = 'Can edit customers';
        $permissions['delete'] = 'Can delete customers';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Construct the controller
     */
    public function __construct()
    {
        parent::__construct();
        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse customers
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:invoice:customer:manage')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Manage Customers';

        // --------------------------------------------------------------------------

        $oInput         = Factory::service('Input');
        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
        $sTableAlias    = $oCustomerModel->getTableAlias();

        //  Get pagination and search/sort variables
        $page      = $oInput->get('page') ? $oInput->get('page') : 0;
        $perPage   = $oInput->get('perPage') ? $oInput->get('perPage') : 50;
        $sortOn    = $oInput->get('sortOn') ? $oInput->get('sortOn') : $sTableAlias . '.organisation';
        $sortOrder = $oInput->get('sortOrder') ? $oInput->get('sortOrder') : 'asc';
        $keywords  = $oInput->get('keywords') ? $oInput->get('keywords') : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = [
            $sTableAlias . '.organisation' => 'Organisation',
            $sTableAlias . '.first_name'   => 'Customer Name',
            $sTableAlias . '.created'      => 'Created Date',
            $sTableAlias . '.modified'     => 'Last Modified Date',
        ];

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = [
            'expand'   => ['invoices'],
            'sort'     => [
                [$sortOn, $sortOrder],
            ],
            'keywords' => $keywords,
        ];

        //  Get the items for the page
        $totalRows               = $oCustomerModel->countAll($data);
        $this->data['customers'] = $oCustomerModel->getAll($page, $perPage, $data);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sortOn, $sortOrder, $perPage, $keywords);
        $this->data['pagination'] = Helper::paginationObject($page, $perPage, $totalRows);

        // --------------------------------------------------------------------------

        //  Add a header button
        if (userHasPermission('admin:invoice:customer:create')) {

            Helper::addHeaderButton(
                'admin/invoice/customer/create',
                'Create Customer'
            );
        }

        // --------------------------------------------------------------------------

        Helper::loadView('index');
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new customer
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:invoice:customer:create')) {
            unauthorised();
        }

        $oInput = Factory::service('Input');
        if ($oInput->post()) {

            try {

                $this->formValidation();
                $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
                if (!$oCustomerModel->create($this->prepPostData())) {
                    throw new NailsException('Failed to create item. ' . $oCustomerModel->lastError(), 1);
                }

                $oSession = Factory::service('Session', 'nailsapp/module-auth');
                $oSession->setFlashData('success', 'Item created successfully.');
                redirect('admin/invoice/customer');

            } catch (\Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        }

        $this->data['page']->title = 'Create customer';
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Edit an existing customer
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:invoice:customer:edit')) {
            unauthorised();
        }

        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
        $oUri           = Factory::service('Uri');

        $itemId             = (int) $oUri->segment(5);
        $this->data['item'] = $oCustomerModel->getById(
            $itemId,
            ['expand' => $oCustomerModel::EXPAND_ALL]
        );

        if (empty($this->data['item'])) {
            show_404();
        }

        // --------------------------------------------------------------------------

        $oInput = Factory::service('Input');
        if ($oInput->post()) {

            try {

                $this->formValidation();

                if (!$oCustomerModel->update($itemId, $this->prepPostData())) {
                    throw new NailsException('Failed to update item. ' . $oCustomerModel->lastError(), 1);
                }

                $oSession = Factory::service('Session', 'nailsapp/module-auth');
                $oSession->setFlashData('success', 'Item updated successfully.');
                redirect('admin/invoice/customer');

            } catch (\Exception $e) {
                $this->data['error'] = $e->getMessage();
            }
        }

        // --------------------------------------------------------------------------

        $this->data['page']->title = 'Edit customer';
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Runs form validation
     * @return void
     */
    protected function formValidation()
    {
        $aRules = [
            'first_name'               => 'max_length[255]',
            'last_name'                => 'max_length[255]',
            'organisation'             => 'max_length[255]',
            'email'                    => 'max_length[255]|valid_email|required',
            'billing_email'            => 'max_length[255]|valid_email',
            'telephone'                => 'max_length[25]',
            'vat_number'               => 'max_length[25]',
            'billing_address_line_1'   => 'max_length[255]',
            'billing_address_line_2'   => 'max_length[255]',
            'billing_address_town'     => 'max_length[255]',
            'billing_address_county'   => 'max_length[255]',
            'billing_address_postcode' => 'max_length[255]',
            'billing_address_country'  => 'max_length[255]',
        ];

        $oFormValidation = Factory::service('FormValidation');
        foreach ($aRules as $sKey => $sRule) {
            $oFormValidation->set_rules($sKey, '', $sRule);
        }

        $oFormValidation->set_message('required', lang('fv_required'));
        $oFormValidation->set_message('max_length', lang('fv_max_length'));
        $oFormValidation->set_message('valid_email', lang('fv_valid_email'));

        if (!$oFormValidation->run()) {
            throw new NailsException(lang('fv_there_were_errors'), 1);
        }

        //  First/Last name is required if no organisation is provided
        $oInput        = Factory::service('Input');
        $sOrganisation = $oInput->post('organisation');
        $sFirstName    = $oInput->post('first_name');
        $sLastName     = $oInput->post('last_name');
        if (empty($sOrganisation) && (empty($sFirstName) || empty($sLastName))) {
            throw new NailsException('First name and surname are required if not providing an organisation.', 1);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Gets the object data from the $_POST array
     * @return array
     */
    protected function prepPostData()
    {
        $oInput = Factory::service('Input');
        $aData  = [
            'first_name'               => trim(strip_tags($oInput->post('first_name'))),
            'last_name'                => trim(strip_tags($oInput->post('last_name'))),
            'organisation'             => trim(strip_tags($oInput->post('organisation'))),
            'email'                    => trim(strip_tags($oInput->post('email'))),
            'billing_email'            => trim(strip_tags($oInput->post('billing_email'))),
            'telephone'                => trim(strip_tags($oInput->post('telephone'))),
            'vat_number'               => trim(strip_tags($oInput->post('vat_number'))),
            'billing_address_line_1'   => trim(strip_tags($oInput->post('billing_address_line_1'))),
            'billing_address_line_2'   => trim(strip_tags($oInput->post('billing_address_line_2'))),
            'billing_address_town'     => trim(strip_tags($oInput->post('billing_address_town'))),
            'billing_address_county'   => trim(strip_tags($oInput->post('billing_address_county'))),
            'billing_address_postcode' => trim(strip_tags($oInput->post('billing_address_postcode'))),
            'billing_address_country'  => trim(strip_tags($oInput->post('billing_address_country'))),
        ];

        return $aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete a customer
     * @return void
     */
    public function delete()
    {
        if (!userHasPermission('admin:invoice:customer:delete')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oUri           = Factory::service('Uri');
        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
        $oCustomer      = $oCustomerModel->getById(
            $oUri->segment(5),
            ['expand' => ['invoices']]
        );
        if (!$oCustomer) {
            show_404();
        }

        if ($oCustomer->invoices->count) {

            $sStatus  = 'error';
            $sMessage = 'Cannot delete a customer who has invoices.';

        } else {

            if ($oCustomerModel->delete($oCustomer->id)) {

                $sStatus  = 'success';
                $sMessage = 'Invoice deleted successfully!';

            } else {

                $sStatus  = 'error';
                $sMessage = 'Invoice failed to delete. ' . $this->oInvoiceModel->lastError();
            }
        }

        $oSession = Factory::service('Session', 'nailsapp/module-auth');
        $oSession->setFlashData($sStatus, $sMessage);
        redirect('admin/invoice/customer/index');
    }
}
