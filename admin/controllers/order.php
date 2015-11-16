<?php

/**
 * Manage orders
 *
 * @package     Nails
 * @subpackage  module-order-payment
 * @category    Controller
 * @author      Nails Dev Team
 * @link
 */

namespace Nails\Admin\Order;

use Nails\Factory;
use Nails\Admin\Helper;
use Nails\Admin\Controller\Base;

class Order extends Base
{
    protected $oOrderModel;

    // --------------------------------------------------------------------------

    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:order:order:manage')) {

            $navGroup = new \Nails\Admin\Nav('Orders &amp; Payments', 'fa-credit-card');
            $navGroup->addAction('Manage orders');

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

        $permissions['manage'] = 'Can manage orders';
        $permissions['create'] = 'Can create orders';
        $permissions['edit']   = 'Can edit orders';
        $permissions['delete'] = 'Can delete orders';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        $this->oOrderModel = Factory::model('ORder', 'nailsapp/module-order-payment');
    }

    // --------------------------------------------------------------------------

    /**
     * Browse orders
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:order:order:manage')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Manage Orders';

        // --------------------------------------------------------------------------

        $sTablePrefix = $this->oOrderModel->getTablePrefix();

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
        $totalRows            = $this->oOrderModel->count_all($data);
        $this->data['orders'] = $this->oOrderModel->get_all($page, $perPage, $data);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sortOn, $sortOrder, $perPage, $keywords);
        $this->data['pagination'] = Helper::paginationObject($page, $perPage, $totalRows);

        //  Add a header button
        if (userHasPermission('admin:order:order:create')) {

             Helper::addHeaderButton(
                'admin/order/order/create',
                'Create Order'
            );
        }

        // --------------------------------------------------------------------------

        Helper::loadView('index');
    }

    // --------------------------------------------------------------------------

    /**
     * Create a new order
     * @return void
     */
    public function create()
    {
        if (!userHasPermission('admin:order:order:create')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Create Order';

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oOrderModel->create($this->getObjectFromPost())) {

                    $this->session->set_flashdata('success', 'Order created successfully.');
                    redirect('admin/order/order/index');

                } else {

                    $this->data['error'] = 'Failed to create order. ' . $this->oOrderModel->last_error();
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
     * Edit an order
     * @return void
     */
    public function edit()
    {
        if (!userHasPermission('admin:order:order:edit')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $this->data['order'] = $this->oOrderModel->get_by_id($this->uri->segment(5));

        if (!$this->data['order']) {

            show_404();
        }

        // --------------------------------------------------------------------------

        //  Page Title
        $this->data['page']->title = 'Edit Order &rsaquo; ' . $this->data['order']->ref;

        // --------------------------------------------------------------------------

        if ($this->input->post()) {

            if ($this->validatePost()) {

                if ($this->oOrderModel->update($this->data['order']->id, $this->getObjectFromPost())) {

                    $this->session->set_flashdata('success', lang('orders_edit_ok'));
                    redirect('admin/order/order/index');

                } else {

                    $this->data['error'] = 'Failed to update order. ' . $this->oOrderModel->last_error();
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
     * Delete an order
     * @return void
     */
    public function delete()
    {
        if (!userHasPermission('admin:order:order:delete')) {

            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oOrder = $this->oOrderModel->get_by_id($this->uri->segment(5));
        if (!$oOrder) {

            show_404();
        }

        // --------------------------------------------------------------------------

        if ($this->oOrderModel->delete($oOrder->id)) {

            $sStatus  = 'success';
            $sMessage = 'Order deleted successfully!';

        } else {

            $sStatus  = 'error';
            $sMessage = 'Order failed to delete. ' . $this->oOrderModel->last_error();
        }

        $this->session->set_flashdata($sStatus, $sMessage);
        redirect('admin/order/order/index');
    }
}
