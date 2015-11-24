<?php

/**
 * Manage invoices
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
use Nails\Admin\Controller\Base;

class Invoice extends Base
{
    protected $oInvoiceModel;
    protected $oTaxModel;

    // --------------------------------------------------------------------------

    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:invoice:invoice:manage')) {

            $navGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $navGroup->setLabel('Invoices &amp; Payments');
            $navGroup->setIcon('fa-credit-card');
            $navGroup->addAction('Manage Invoices', 'index', array(), 0);

            return $navGroup;
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

        $permissions['manage'] = 'Can manage invoices';
        $permissions['create'] = 'Can create invoices';
        $permissions['edit']   = 'Can edit invoices';
        $permissions['delete'] = 'Can delete invoices';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        $this->oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $this->oTaxModel     = Factory::model('Tax', 'nailsapp/module-invoice');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse invoices
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:invoice:invoice:manage')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Manage Invoices';

        // --------------------------------------------------------------------------

        $sTablePrefix = $this->oInvoiceModel->getTablePrefix();

        //  Get pagination and search/sort variables
        $page      = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage   = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn    = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $sTablePrefix . '.created';
        $sortOrder = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'desc';
        $keywords  = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = array(
            $sTablePrefix . '.created'  => 'Created Date',
            $sTablePrefix . '.modified' => 'Modified Date',
            $sTablePrefix . '.state'    => 'Invoice State',
        );

        // --------------------------------------------------------------------------

        //  Define the filters
        $aCbFilters = array();
        $aOptions   = array();
        $aStates    = $this->oInvoiceModel->getStates();

        foreach ($aStates as $sState => $sLabel) {
            $aOptions[] = array(
                $sLabel,
                $sState,
                true
            );
        }

        $aCbFilters[] = Helper::searchFilterObject(
            $sTablePrefix . '.state',
            'State',
            $aOptions
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords' => $keywords,
            'cbFilters' => $aCbFilters
        );

        //  Get the items for the page
        $totalRows                   = $this->oInvoiceModel->count_all($data);
        $this->data['invoices']      = $this->oInvoiceModel->get_all($page, $perPage, $data);
        $this->data['invoiceStates'] = $aStates;

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sortOn, $sortOrder, $perPage, $keywords, $aCbFilters);
        $this->data['pagination'] = Helper::paginationObject($page, $perPage, $totalRows);

        //  Add a header button
        if (userHasPermission('admin:invoice:invoice:create')) {

             Helper::addHeaderButton(
                'admin/invoice/invoice/create',
                'Create Invoice'
            );
        }

        // --------------------------------------------------------------------------

        Helper::loadView('index');
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new invoice
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:invoice:invoice:create')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Create Invoice';

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oInvoiceModel->create($this->getObjectFromPost())) {

                    $this->session->set_flashdata('success', 'Invoice created successfully.');
                    redirect('admin/invoice/invoice/index');

                } else {

                    $this->data['error'] = 'Failed to create invoice. ' . $this->oInvoiceModel->lastError();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        //  Page data
        $aItemUnits = $this->oInvoiceModel->getItemUnits();
        $aTaxes     = $this->oTaxModel->get_all();

        $this->data['invoiceStates'] = $this->oInvoiceModel->getStates();

        // --------------------------------------------------------------------------

        $this->asset->load('nails.admin.module.invoice.css', 'NAILS');
        $this->asset->load('knockout/dist/knockout.js', 'NAILS-BOWER');
        $this->asset->load('nails.admin.invoice.invoice.edit.min.js', 'NAILS');
        $this->asset->inline(
            'ko.applyBindings(
                new invoiceEdit(
                    ' . json_encode($aItemUnits) . ',
                    ' . json_encode($aTaxes) . ',
                    []
                )
            );',
            'JS'
        );

        //  Load views
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Edit an invoice
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:invoice:invoice:edit')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $this->data['invoice'] = $this->oInvoiceModel->get_by_id($this->uri->segment(5));

        if (!$this->data['invoice'] || $this->data['invoice']->state != 'DRAFT') {
            show_404();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Edit Invoice &rsaquo; ' . $this->data['invoice']->ref;

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oInvoiceModel->update($this->data['invoice']->id, $this->getObjectFromPost())) {

                    $this->session->set_flashdata('success', lang('invoices_edit_ok'));
                    redirect('admin/invoice/invoice/index');

                } else {

                    $this->data['error'] = 'Failed to update invoice. ' . $this->oInvoiceModel->lastError();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        //  Page data
        $aItemUnits = $this->oInvoiceModel->getItemUnits();
        $aTaxes     = $this->oTaxModel->get_all();

        $this->data['invoiceStates'] = $this->oInvoiceModel->getStates();

        // --------------------------------------------------------------------------

        $this->asset->load('nails.admin.module.invoice.css', 'NAILS');
        $this->asset->load('knockout/dist/knockout.js', 'NAILS-BOWER');
        $this->asset->load('nails.admin.invoice.invoice.edit.min.js', 'NAILS');
        $this->asset->inline(
            'ko.applyBindings(
                new invoiceEdit(
                    ' . json_encode($aItemUnits) . ',
                    ' . json_encode($aTaxes) . ',
                    ' . json_encode($this->data['invoice']->items) . '
                )
            );',
            'JS'
        );

        //  Load views
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * View an invoice
     * @return void
     */
    public function view()
    {
        if (!userHasPermission('admin:invoice:invoice:edit')) {

            unauthorised();
        }

        $this->data['invoice'] = $this->oInvoiceModel->get_by_id($this->uri->segment(5));
        if (!$this->data['invoice'] || $this->data['invoice']->state == 'DRAFT') {
            show_404();
        }

        $this->data['page']->title = 'View Invoice &rsaquo; ' . $this->data['invoice']->ref;

        Helper::loadView('view');
    }

    // --------------------------------------------------------------------------

    /**
     * Validate the POST data
     * @return boolean
     */
    protected function validatePost()
    {
        $oFormValidation = Factory::service('FormValidation');

        $aRules = array(
            'ref'             => 'xss_clean|trim|required',
            'state'           => 'xss_clean|trim|required',
            'dated'           => 'xss_clean|trim|valid_date',
            'terms'           => 'xss_clean|trim|is_natural',
            'user_id'         => 'xss_clean|trim',
            'user_email'      => 'xss_clean|trim|valid_email',
            'additional_text' => 'xss_clean|trim',
            'items'           => 'xss_clean'
        );

        $aRulesFV = array();
        foreach ($aRules as $sKey => $sRules) {
            $aRulesFV[] = array(
                'field' => $sKey,
                'label' => '',
                'rules' => $sRules
            );
        }

        $oFormValidation->set_rules($aRulesFV);

        $oFormValidation->set_message('required', lang('fv_required'));

        return $oFormValidation->run();
    }

    // --------------------------------------------------------------------------

    /**
     * Get an object generated from the POST data
     * @return array
     */
    protected function getObjectFromPost()
    {
        dumpanddie($_POST);
        $aData = array();

        return $aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete an invoice
     * @return void
     */
    public function delete()
    {
        if (!userHasPermission('admin:invoice:invoice:delete')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oInvoice = $this->oInvoiceModel->get_by_id($this->uri->segment(5));
        if (!$oInvoice) {

            show_404();
        }

        // --------------------------------------------------------------------------

        if ($this->oInvoiceModel->delete($oInvoice->id)) {

            $sStatus  = 'success';
            $sMessage = 'Invoice deleted successfully!';

        } else {

            $sStatus  = 'error';
            $sMessage = 'Invoice failed to delete. ' . $this->oInvoiceModel->lastError();
        }

        $this->session->set_flashdata($sStatus, $sMessage);
        redirect('admin/invoice/invoice/index');
    }
}
