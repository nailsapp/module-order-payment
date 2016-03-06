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
        $this->oCustomerModel = Factory::model('Customer', 'nailsapp/module-invoice');
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

        $sTablePrefix = $this->oCustomerModel->getTablePrefix();

        //  Get pagination and search/sort variables
        $page      = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage   = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn    = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $sTablePrefix . '.organisation';
        $sortOrder = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'desc';
        $keywords  = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = array(
            $sTablePrefix . '.organisation' => 'Organisation',
            $sTablePrefix . '.first_name'   => 'Customer Name',
            $sTablePrefix . '.email'        => 'Customer Email',
            $sTablePrefix . '.created'      => 'Created Date',
            $sTablePrefix . '.modified'     => 'Last Modified Date'
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords'  => $keywords
        );

        //  Get the items for the page
        $totalRows               = $this->oCustomerModel->countAll($data);
        $this->data['customers'] = $this->oCustomerModel->getAll($page, $perPage, $data);

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
}
