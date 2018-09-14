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

use Nails\Admin\Helper;
use Nails\Common\Exception\NailsException;
use Nails\Factory;
use Nails\Invoice\Controller\BaseAdmin;

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

            $oNavGroup = Factory::factory('Nav', 'nails/module-admin');
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

        $oInput        = Factory::service('Input');
        $oPaymentModel = Factory::model('Payment', 'nails/module-invoice');
        $oInvoiceModel = Factory::model('Invoice', 'nails/module-invoice');
        $oDriverModel  = Factory::model('PaymentDriver', 'nails/module-invoice');

        // --------------------------------------------------------------------------

        $sTableAlias = $oPaymentModel->getTableAlias();

        //  Get pagination and search/sort variables
        $page      = $oInput->get('page') ? $oInput->get('page') : 0;
        $perPage   = $oInput->get('perPage') ? $oInput->get('perPage') : 50;
        $sortOn    = $oInput->get('sortOn') ? $oInput->get('sortOn') : $sTableAlias . '.created';
        $sortOrder = $oInput->get('sortOrder') ? $oInput->get('sortOrder') : 'desc';
        $keywords  = $oInput->get('keywords') ? $oInput->get('keywords') : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = [
            $sTableAlias . '.created'    => 'Received Date',
            $sTableAlias . '.driver'     => 'Payment Gateway',
            $sTableAlias . '.invoice_id' => 'Invoice ID',
            $sTableAlias . '.txn_id'     => 'Transaction ID',
            $sTableAlias . '.amount'     => 'Amount',
            $sTableAlias . '.currency'   => 'Currency',
        ];

        // --------------------------------------------------------------------------

        //  Define the filters
        $aCbFilters = [];
        $aOptions   = [];
        $aDrivers   = $oDriverModel->getAll();

        foreach ($aDrivers as $sSlug => $oDriver) {
            $aOptions[] = [
                $oDriver->name,
                $sSlug,
                true,
            ];
        }

        $aCbFilters[] = Helper::searchFilterObject(
            $sTableAlias . '.driver',
            'Gateway',
            $aOptions
        );

        $aCbFilters[] = Helper::searchFilterObject(
            $sTableAlias . '.status',
            'Status',
            $oPaymentModel->getStatusesHuman()
        );

        // --------------------------------------------------------------------------

        //  Define the $data variable for the queries
        $data = [
            'sort'      => [
                [$sortOn, $sortOrder],
            ],
            'keywords'  => $keywords,
            'cbFilters' => $aCbFilters,
        ];

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

        $oUri          = Factory::service('Uri');
        $oPaymentModel = Factory::model('Payment', 'nails/module-invoice');

        // --------------------------------------------------------------------------

        $this->data['payment'] = $oPaymentModel->getById(
            $oUri->segment(5),
            ['expand' => $oPaymentModel::EXPAND_ALL]
        );

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

        $oUri          = Factory::service('Uri');
        $oInput        = Factory::service('Input');
        $oPaymentModel = Factory::model('Payment', 'nails/module-invoice');
        $iPaymentId    = $oUri->segment(5);
        $sAmount       = $oInput->post('amount') ?: null;
        $sReason       = $oInput->post('reason') ?: null;
        $sRedirect     = urldecode($oInput->post('return_to')) ?: 'invoice/payment/view/' . $iPaymentId;

        try {

            $oPayment = $oPaymentModel->getById($iPaymentId);
            if (empty($oPaymentModel)) {
                throw new NailsException('Invalid payment ID.');
            }

            // --------------------------------------------------------------------------

            //  Convert the amount to its smallest unit
            $iAmount = intval($sAmount * pow(10, $oPayment->currency->decimal_precision));

            // --------------------------------------------------------------------------

            if (!$oPaymentModel->refund($iPaymentId, $iAmount, $sReason)) {
                throw new NailsException('Failed to refund payment. ' . $oPaymentModel->lastError(), 1);
            }

            $sStatus  = 'success';
            $sMessage = 'Payment refunded successfully.';

        } catch (NailsException $e) {
            $sStatus  = 'error';
            $sMessage = $e->getMessage();
        }

        $oSession = Factory::service('Session', 'nails/module-auth');
        $oSession->setFlashData($sStatus, $sMessage);
        redirect($sRedirect);
    }
}
