<?php

/**
 * Manage payments
 *
 * @package     Nails
 * @subpackage  module-payment-payment
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Payment;

use Nails\Factory;
use Nails\Admin\Helper;
use Nails\Admin\Controller\Base;

class Payment extends Base
{
    protected $oPaymentModel;

    // --------------------------------------------------------------------------

    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:order:payment:manage')) {

            $navGroup = new \Nails\Admin\Nav('Orders &amp; Payments', 'fa-credit-card');
            $navGroup->addAction('Manage payments');

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

        $permissions['manage'] = 'Can manage payments';
        $permissions['create'] = 'Can create payments';
        $permissions['edit']   = 'Can edit payments';
        $permissions['delete'] = 'Can delete payments';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        $this->oPaymentModel = Factory::model('Payment', 'nailsapp/module-order-payment');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse payments
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:order:payment:manage')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Manage Payments';

        // --------------------------------------------------------------------------

        $sTablePrefix = $this->oPaymentModel->getTablePrefix();

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
            $sTablePrefix . '.modified' => 'Modified Date'
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords' => $keywords
        );

        //  Get the items for the page
        $totalRows              = $this->oPaymentModel->count_all($data);
        $this->data['payments'] = $this->oPaymentModel->get_all($page, $perPage, $data);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sortOn, $sortOrder, $perPage, $keywords);
        $this->data['pagination'] = Helper::paginationObject($page, $perPage, $totalRows);

        //  Add a header button
        if (userHasPermission('admin:order:payment:create')) {

             Helper::addHeaderButton(
                'admin/order/payment/create',
                'Create Payment'
            );
        }

        // --------------------------------------------------------------------------

        Helper::loadView('index');
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new payment
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:order:payment:create')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Create Payment';

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oPaymentModel->create($this->getObjectFromPost())) {

                    $this->session->set_flashdata('success', 'Payment created successfully.');
                    redirect('admin/order/payment/index');

                } else {

                    $this->data['error'] = 'Failed to create payment. ' . $this->oPaymentModel->last_error();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        //  Load views
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Edit an payment
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:order:payment:edit')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $this->data['payment'] = $this->oPaymentModel->get_by_id($this->uri->segment(5));

        if (!$this->data['payment']) {

            show_404();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Edit Payment &rsaquo; ' . $this->data['payment']->ref;

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oPaymentModel->update($this->data['payment']->id, $this->getObjectFromPost())) {

                    $this->session->set_flashdata('success', lang('payments_edit_ok'));
                    redirect('admin/order/payment/index');

                } else {

                    $this->data['error'] = 'Failed to update payment. ' . $this->oPaymentModel->last_error();
                }

            } else {

                $this->data['error'] = lang('fv_there_were_errors');
            }
        }

        // --------------------------------------------------------------------------

        //  Load views
        Helper::loadView('edit');
    }

    // --------------------------------------------------------------------------

    /**
     * Validate the POST data
     * @return boolean
     */
    protected function validatePost()
    {
        $this->load->library('form_validation');

        return $this->form_validation->run();
    }

    // --------------------------------------------------------------------------

    /**
     * Get an object generated from the POST data
     * @return array
     */
    protected function getObjectFromPost()
    {
        $aData = array();

        return $aData;
    }

    // --------------------------------------------------------------------------

    /**
     * Delete an payment
     * @return void
     */
    public function delete()
    {
        if (!userHasPermission('admin:order:payment:delete')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oPayment = $this->oPaymentModel->get_by_id($this->uri->segment(5));
        if (!$oPayment) {

            show_404();
        }

        // --------------------------------------------------------------------------

        if ($this->oPaymentModel->delete($oPayment->id)) {

            $sStatus  = 'success';
            $sMessage = 'Payment deleted successfully!';

        } else {

            $sStatus  = 'error';
            $sMessage = 'Payment failed to delete. ' . $this->oPaymentModel->last_error();
        }

        $this->session->set_flashdata($sStatus, $sMessage);
        redirect('admin/order/payment/index');
    }
}
