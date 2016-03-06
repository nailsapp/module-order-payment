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

use Nails\Factory;
use Nails\Admin\Helper;
use Nails\Invoice\Controller\BaseAdmin;
use Nails\Common\Exception\NailsException;

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

        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
        $sTablePrefix   = $oCustomerModel->getTablePrefix();

        //  Get pagination and search/sort variables
        $page      = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage   = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn    = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $sTablePrefix . '.organisation';
        $sortOrder = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'asc';
        $keywords  = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = array(
            $sTablePrefix . '.organisation' => 'Organisation',
            $sTablePrefix . '.first_name'   => 'Customer Name',
            $sTablePrefix . '.created'      => 'Created Date',
            $sTablePrefix . '.modified'     => 'Last Modified Date'
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'includeInvoices' => true,
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords'  => $keywords
        );

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

        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');

        if ($this->input->post()) {

            try {

                $this->formValidation();

                if (!$oCustomerModel->create($this->prepPostData())) {
                    throw new NailsException('Failed to create item. ' . $oCustomerModel->lastError(), 1);
                }

                $this->session->set_flashdata('success', 'Item created successfully.');
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

        $itemId             = (int) $this->uri->segment(5);
        $this->data['item'] = $oCustomerModel->getById($itemId, array('includeAll' => true));
        if (empty($this->data['item'])) {
            show_404();
        }

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            try {

                $this->formValidation();

                if (!$oCustomerModel->update($itemId, $this->prepPostData())) {
                    throw new NailsException('Failed to update item. ' . $oCustomerModel->lastError(), 1);
                }

                $this->session->set_flashdata('success', 'Item updated successfully.');
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
        $aRules = array(
            'first_name'               => 'xss_clean|max_length[255]',
            'last_name'                => 'xss_clean|max_length[255]',
            'organisation'             => 'xss_clean|max_length[255]',
            'email'                    => 'xss_clean|max_length[255]|valid_email|required',
            'billing_email'            => 'xss_clean|max_length[255]|valid_email',
            'telephone'                => 'xss_clean|max_length[25]',
            'vat_number'               => 'xss_clean|max_length[25]',
            'billing_address_line_1'   => 'xss_clean|max_length[255]',
            'billing_address_line_2'   => 'xss_clean|max_length[255]',
            'billing_address_town'     => 'xss_clean|max_length[255]',
            'billing_address_county'   => 'xss_clean|max_length[255]',
            'billing_address_postcode' => 'xss_clean|max_length[255]',
            'billing_address_country'  => 'xss_clean|max_length[255]'
        );


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
        $sOrganisation = $this->input->post('organisation');
        $sFirstName    = $this->input->post('first_name');
        $sLastName     = $this->input->post('last_name');
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
        $aData = array(
            'label'                    => '',
            'first_name'               => trim(strip_tags($this->input->post('first_name'))),
            'last_name'                => trim(strip_tags($this->input->post('last_name'))),
            'organisation'             => trim(strip_tags($this->input->post('organisation'))),
            'email'                    => trim(strip_tags($this->input->post('email'))),
            'billing_email'            => trim(strip_tags($this->input->post('billing_email'))),
            'telephone'                => trim(strip_tags($this->input->post('telephone'))),
            'vat_number'               => trim(strip_tags($this->input->post('vat_number'))),
            'billing_address_line_1'   => trim(strip_tags($this->input->post('billing_address_line_1'))),
            'billing_address_line_2'   => trim(strip_tags($this->input->post('billing_address_line_2'))),
            'billing_address_town'     => trim(strip_tags($this->input->post('billing_address_town'))),
            'billing_address_county'   => trim(strip_tags($this->input->post('billing_address_county'))),
            'billing_address_postcode' => trim(strip_tags($this->input->post('billing_address_postcode'))),
            'billing_address_country'  => trim(strip_tags($this->input->post('billing_address_country')))
        );

        //  Compile the label
        if (!empty($aData['organisation'])) {

            $aData['label'] = $aData['organisation'];

        } else {

            $aData['label'] = $aData['first_name'] . ' ' . $aData['last_name'];
        }

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

        $oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');

        $oCustomer = $oCustomerModel->getById($this->uri->segment(5), array('includeInvoices' => true));
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

        $this->session->set_flashdata($sStatus, $sMessage);
        redirect('admin/invoice/customer/index');
    }
}
