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
use Nails\Invoice\Controller\BaseAdmin;

class Invoice extends BaseAdmin
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

            $oNavGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $oNavGroup->setLabel('Invoices &amp; Payments');
            $oNavGroup->setIcon('fa-credit-card');
            $oNavGroup->addAction('Manage Invoices', 'index', array(), 0);

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

        $this->oInvoiceModel     = Factory::model('Invoice', 'nailsapp/module-invoice');
        $this->oInvoiceItemModel = Factory::model('InvoiceItem', 'nailsapp/module-invoice');
        $this->oTaxModel         = Factory::model('Tax', 'nailsapp/module-invoice');
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

        $sTableAlias = $this->oInvoiceModel->getTableAlias();

        //  Get pagination and search/sort variables
        $page      = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage   = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn    = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $sTableAlias . '.created';
        $sortOrder = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'desc';
        $keywords  = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = array(
            $sTableAlias . '.created'  => 'Created Date',
            $sTableAlias . '.modified' => 'Modified Date',
            $sTableAlias . '.state'    => 'Invoice State',
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
            $sTableAlias . '.state',
            'State',
            $aOptions
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'includeCustomer' => true,
            'includePayments' => true,
            'keywords'        => $keywords,
            'cbFilters'       => $aCbFilters
        );

        //  Get the items for the page
        $totalRows                   = $this->oInvoiceModel->countAll($data);
        $this->data['invoices']      = $this->oInvoiceModel->getAll($page, $perPage, $data);

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

                $oInvoice = $this->oInvoiceModel->create($this->getObjectFromPost(), true);

                if (!empty($oInvoice)) {

                    //  Send invoice if needed
                    $this->sendInvoice($oInvoice);

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
        $aItemUnits = $this->oInvoiceItemModel->getUnits();
        $aTaxes     = $this->oTaxModel->getAll();

        $this->data['invoiceStates'] = $this->oInvoiceModel->getSelectableStates();

        // --------------------------------------------------------------------------

        //  Invoice Items
        if ($this->input->post()) {

            $aItems = $this->input->post('items') ?: array();
            //  Tidy up post data as expected by JS
            foreach ($aItems as &$aItem) {

                $aItem['label'] = html_entity_decode($aItem['label'], ENT_QUOTES, 'UTF-8');
                $aItem['body']  = html_entity_decode($aItem['body'], ENT_QUOTES, 'UTF-8');

                $sUnitCost                     = $aItem['unit_cost'];
                $aItem['unit_cost']            = new \stdClass();
                $aItem['unit_cost']->localised = new \stdClass();
                $aItem['unit_cost']->localised = !empty($sUnitCost) ? (float) $sUnitCost : null;

                $aItem['tax'] = new \stdClass();
                $aItem['tax']->id = !empty($aItem['tax_id']) ? (int) $aItem['tax_id'] : null;

                $sUnit             = !empty($aItem['unit']) ? $aItem['unit'] : null;
                $aItem['unit']     = new \stdClass();
                $aItem['unit']->id = $sUnit;
            }

        } else {

            $aItems = array();
        }

        // --------------------------------------------------------------------------

        $oAsset = Factory::service('Asset');
        $oAsset->load('invoice.edit.min.js', 'nailsapp/module-invoice');
        $oAsset->inline(
            'ko.applyBindings(
                new invoiceEdit(
                    ' . json_encode($aItemUnits) . ',
                    ' . json_encode($aTaxes) . ',
                    ' . json_encode($aItems) . '
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

        $iInvoiceId = (int) $this->uri->segment(5);
        $this->data['invoice'] = $this->oInvoiceModel->getById($iInvoiceId, array('includeAll' => true));

        if (!$this->data['invoice'] || $this->data['invoice']->state->id != 'DRAFT') {
            show_404();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Edit Invoice &rsaquo; ' . $this->data['invoice']->ref;

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oInvoiceModel->update($this->data['invoice']->id, $this->getObjectFromPost())) {

                    //  Send invoice if needed
                    $oInvoice = $this->oInvoiceModel->getById($this->data['invoice']->id);
                    $this->sendInvoice($oInvoice);

                    $this->session->set_flashdata('success', 'Invoice was saved successfully.');
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
        $aItemUnits = $this->oInvoiceItemModel->getUnits();
        $aTaxes     = $this->oTaxModel->getAll();

        $this->data['invoiceStates'] = $this->oInvoiceModel->getSelectableStates();

        // --------------------------------------------------------------------------

        //  Invoice Items
        if ($this->input->post()) {

            $aItems = $this->input->post('items') ?: array();
            //  Tidy up post data as expected by JS
            foreach ($aItems as &$aItem) {

                $aItem['label'] = html_entity_decode($aItem['label'], ENT_QUOTES, 'UTF-8');
                $aItem['body']  = html_entity_decode($aItem['body'], ENT_QUOTES, 'UTF-8');

                $sUnitCost                     = $aItem['unit_cost'];
                $aItem['unit_cost']            = new \stdClass();
                $aItem['unit_cost']->localised = new \stdClass();
                $aItem['unit_cost']->localised = !empty($sUnitCost) ? (float) $sUnitCost : null;

                $aItem['tax'] = new \stdClass();
                $aItem['tax']->id = !empty($aItem['tax_id']) ? (int) $aItem['tax_id'] : null;

                $sUnit             = !empty($aItem['unit']) ? $aItem['unit'] : null;
                $aItem['unit']     = new \stdClass();
                $aItem['unit']->id = $sUnit;
            }

        } else {

            $aItems = $this->data['invoice']->items->data;
        }

        // --------------------------------------------------------------------------

        $oAsset = Factory::service('Asset');
        $oAsset->load('invoice.edit.min.js', 'nailsapp/module-invoice');
        $oAsset->inline(
            'ko.applyBindings(
                new invoiceEdit(
                    ' . json_encode($aItemUnits) . ',
                    ' . json_encode($aTaxes) . ',
                    ' . json_encode($aItems) . '
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

        $iInvoiceId = (int) $this->uri->segment(5);
        $this->data['invoice'] = $this->oInvoiceModel->getById($iInvoiceId, array('includeAll' => true));

        if (!$this->data['invoice'] || $this->data['invoice']->state->id == 'DRAFT') {
            show_404();
        }

        $this->data['page']->title = 'View Invoice &rsaquo; ' . $this->data['invoice']->ref;

        $oAsset = Factory::service('Asset');
        $oAsset->load('admin.invoice.view.min.js', 'nailsapp/module-invoice');

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
            'ref'             => 'xss_clean|trim',
            'state'           => 'xss_clean|trim|required',
            'dated'           => 'xss_clean|trim|required|valid_date',
            'terms'           => 'xss_clean|trim|is_natural',
            'customer_id'     => 'xss_clean|trim',
            'additional_text' => 'xss_clean|trim',
            'items'           => ''
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
        $oFormValidation->set_message('valid_date', lang('fv_valid_date'));
        $oFormValidation->set_message('is_natural', lang('fv_is_natural'));
        $oFormValidation->set_message('valid_email', lang('fv_valid_email'));

        return $oFormValidation->run();
    }

    // --------------------------------------------------------------------------

    /**
     * Get an object generated from the POST data
     * @return array
     */
    protected function getObjectFromPost()
    {
        $aData = array(
            'ref'             => $this->input->post('ref') ?: null,
            'state'           => $this->input->post('state') ?: null,
            'dated'           => $this->input->post('dated') ?: null,
            'terms'           => (int) $this->input->post('terms') ?: 0,
            'customer_id'     => (int) $this->input->post('customer_id') ?: null,
            'additional_text' => $this->input->post('additional_text') ?: null,
            'items'           => array(),
            'currency'        => 'GBP'
        );

        if ($this->input->post('items')) {
            foreach ($this->input->post('items') as $aItem) {

                //  @todo convert to pence using a model
                $aData['items'][] = array(
                    'id'        => array_key_exists('id', $aItem) ? $aItem['id'] : null,
                    'quantity'  => array_key_exists('quantity', $aItem) ? $aItem['quantity'] : null,
                    'unit'      => array_key_exists('unit', $aItem) ? $aItem['unit'] : null,
                    'label'     => array_key_exists('label', $aItem) ? $aItem['label'] : null,
                    'body'      => array_key_exists('body', $aItem) ? $aItem['body'] : null,
                    'unit_cost' => array_key_exists('unit_cost', $aItem) ? intval($aItem['unit_cost']*100) : null,
                    'tax_id'    => array_key_exists('tax_id', $aItem) ? $aItem['tax_id'] : null
                );
            }
        }

        if ($this->uri->rsegment(5) == 'edit') {
            unset($aData['ref']);
        }

        return $aData;
    }

    // --------------------------------------------------------------------------

    protected function sendInvoice($oInvoice)
    {
        if (empty($oInvoice)) {
            return false;
        }

        $sInvoiceClass = get_class($this->oInvoiceModel);

        if ($oInvoice->state->id !== $sInvoiceClass::STATE_OPEN) {
            return false;
        }

        $oNow   = Factory::factory('DateTime');
        $oDated = new \DateTime($oInvoice->dated->raw);

        if ($oNow->format('Y-m-d') != $oDated->format('Y-m-d')) {
            return false;
        }

        return $this->oInvoiceModel->send($oInvoice->id);
    }

    // --------------------------------------------------------------------------

    /**
     * Make an invoice a draft
     * @return void
     */
    public function make_draft()
    {
        if (!userHasPermission('admin:invoice:invoice:edit')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oInvoice = $this->oInvoiceModel->getById($this->uri->segment(5));
        if (!$oInvoice) {
            show_404();
        }

        // --------------------------------------------------------------------------

        //  Allow getting a constant
        $oInvoiceModel = $this->oInvoiceModel;

        $aData = array(
            'state' => $oInvoiceModel::STATE_DRAFT,
        );
        if ($this->oInvoiceModel->update($oInvoice->id, $aData)) {

            $sStatus  = 'success';
            $sMessage = 'Invoice updated successfully!';

        } else {

            $sStatus  = 'error';
            $sMessage = 'Invoice failed to update invoice. ' . $this->oInvoiceModel->lastError();
        }

        $this->session->set_flashdata($sStatus, $sMessage);
        redirect('admin/invoice/invoice/edit/' . $oInvoice->id);
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

        $oInvoice = $this->oInvoiceModel->getById($this->uri->segment(5));
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
