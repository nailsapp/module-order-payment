<?php

/**
 * Manage payments
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

class Payment extends BaseAdmin
{
    protected $oInvoiceModel;
    protected $oPaymentModel;
    protected $oDriverModel;

    // --------------------------------------------------------------------------

    /**
     * Announces this controller's navGroups
     * @return stdClass
     */
    public static function announce()
    {
        if (userHasPermission('admin:invoice:payment:view')) {

            $oNavGroup = Factory::factory('Nav', 'nailsapp/module-admin');
            $oNavGroup->setLabel('Invoices &amp; Payments');
            $oNavGroup->setIcon('fa-credit-card');
            if (userHasPermission('admin:invoice:payment:view')) {
                $oNavGroup->addAction('Manage Payments');
            }

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

        $permissions['view']   = 'Can view payment details';
        $permissions['refund'] = 'Can refund payments';

        return $permissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Browse payments
     * @return void
     */
    public function index()
    {
        if (!userHasPermission('admin:invoice:payment:view')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        //  Set method info
        $this->data['page']->title = 'Manage Payments';

        // --------------------------------------------------------------------------

        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $oInvoiceModel = Factory::model('Invoice', 'nailsapp/module-invoice');
        $oDriverModel  = Factory::model('PaymentDriver', 'nailsapp/module-invoice');

        // --------------------------------------------------------------------------

        $sTablePrefix = $oPaymentModel->getTablePrefix();

        //  Get pagination and search/sort variables
        $page      = $this->input->get('page')      ? $this->input->get('page')      : 0;
        $perPage   = $this->input->get('perPage')   ? $this->input->get('perPage')   : 50;
        $sortOn    = $this->input->get('sortOn')    ? $this->input->get('sortOn')    : $sTablePrefix . '.created';
        $sortOrder = $this->input->get('sortOrder') ? $this->input->get('sortOrder') : 'desc';
        $keywords  = $this->input->get('keywords')  ? $this->input->get('keywords')  : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = array(
            $sTablePrefix . '.created'    => 'Received Date',
            $sTablePrefix . '.driver'     => 'Payment Gateway',
            $sTablePrefix . '.invoice_id' => 'Invoice ID',
            $sTablePrefix . '.txn_id'     => 'Transaction ID',
            $sTablePrefix . '.amount'     => 'Amount',
            $sTablePrefix . '.currency'   => 'Currency'
        );

        // --------------------------------------------------------------------------

        //  Define the filters
        $aCbFilters = array();
        $aOptions   = array();
        $aDrivers   = $oDriverModel->getAll();

        foreach ($aDrivers as $sSlug => $oDriver) {
            $aOptions[] = array(
                $oDriver->name,
                $sSlug,
                true
            );
        }

        $aCbFilters[] = Helper::searchFilterObject(
            $sTablePrefix . '.driver',
            'Gateway',
            $aOptions
        );

        $aCbFilters[] = Helper::searchFilterObject(
            $sTablePrefix . '.status',
            'Status',
            $oPaymentModel->getStatusesHuman()
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = array(
            'sort' => array(
                array($sortOn, $sortOrder)
            ),
            'keywords'  => $keywords,
            'cbFilters' => $aCbFilters
        );

        //  Get the items for the page
        $totalRows                   = $oPaymentModel->countAll($data);
        $this->data['payments']      = $oPaymentModel->getAll($page, $perPage, $data);
        $this->data['invoiceStates'] = $oInvoiceModel->getStates();

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sortOn, $sortOrder, $perPage, $keywords, $aCbFilters);
        $this->data['pagination'] = Helper::paginationObject($page, $perPage, $totalRows);

        // --------------------------------------------------------------------------

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
     * View a single payment
     * @return void
     */
    public function view()
    {
        if (!userHasPermission('admin:invoice:payment:view')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');

        // --------------------------------------------------------------------------

        $this->data['payment'] = $oPaymentModel->getById($this->uri->segment(5), array('includeAll' => true));
        if (!$this->data['payment']) {
            show_404();
        }

        $this->data['page']->title = 'View Payment &rsaquo; ' . $this->data['payment']->ref;

        Helper::loadView('view');
    }

    // --------------------------------------------------------------------------

    /**
     * View a single payment
     * @return void
     */
    public function refund()
    {
        if (!userHasPermission('admin:invoice:payment:refund')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        $oPaymentModel = Factory::model('Payment', 'nailsapp/module-invoice');
        $iPaymentId    = $this->uri->segment(5);
        $sAmount       = $this->input->post('amount') ?: null;
        $sReason       = $this->input->post('reason') ?: null;
        $sRedirect     = urldecode($this->input->post('return_to')) ?: 'invoice/payment/view/' . $iPaymentId;

        // --------------------------------------------------------------------------

        //  Convert the amount to its smallest unit
        //  @todo do this automatically
        $iAmount = intval($sAmount * 100);

        // dumpanddie($iAmount, $sReason);

        // --------------------------------------------------------------------------

        try {

            if (!$oPaymentModel->refund($iPaymentId, $iAmount, $sReason)) {
                throw new NailsException('Failed to refund payment. ' . $oPaymentModel->lastError(), 1);
            }

            $sStatus  = 'success';
            $sMessage = 'Payment refunded successfully.';

        } catch (NailsException $e) {

            $sStatus  = 'error';
            $sMessage = $e->getMessage();
        }

        $oSession = Factory::service('Session', 'nailsapp/module-auth');
        $oSession->set_flashdata($sStatus, $sMessage);
        redirect($sRedirect);
    }
}
