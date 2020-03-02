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

use Nails\Admin\Controller\Base;
use Nails\Admin\Factory\Nav;
use Nails\Admin\Helper;
use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Service\Input;
use Nails\Common\Service\Session;
use Nails\Common\Service\Uri;
use Nails\Factory;
use Nails\Invoice\Constants;
use Nails\Invoice\Service\PaymentDriver;

/**
 * Class Payment
 *
 * @package Nails\Admin\Invoice
 */
class Payment extends Base
{
    /**
     * Announces this controller's navGroups
     *
     * @return array|Nav
     * @throws FactoryException
     */
    public static function announce()
    {
        if (userHasPermission('admin:invoice:payment:view')) {

            /** @var Nav $oNavGroup */
            $oNavGroup = Factory::factory('Nav', 'nails/module-admin')
                ->setLabel('Invoices &amp; Payments')
                ->setIcon('fa-credit-card');

            if (userHasPermission('admin:invoice:payment:view')) {
                $oNavGroup->addAction('Manage Payments');
            }

            return $oNavGroup;
        }
    }

    // --------------------------------------------------------------------------

    /**
     * Returns an array of extra permissions for this controller
     *
     * @return array
     */
    public static function permissions(): array
    {
        $aPermissions = parent::permissions();

        $aPermissions['view']   = 'Can view payment details';
        $aPermissions['refund'] = 'Can refund payments';

        return $aPermissions;
    }

    // --------------------------------------------------------------------------

    /**
     * Browse payments
     *
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

        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var PaymentDriver $oDriverService */
        $oDriverService = Factory::service('PaymentDriver', Constants::MODULE_SLUG);
        /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);
        /** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
        $oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);

        // --------------------------------------------------------------------------

        $sTableAlias = $oPaymentModel->getTableAlias();

        //  Get pagination and search/sort variables
        $iPage      = $oInput->get('page') ? $oInput->get('page') : 0;
        $iPerPage   = $oInput->get('perPage') ? $oInput->get('perPage') : 50;
        $sSortOn    = $oInput->get('sortOn') ? $oInput->get('sortOn') : $sTableAlias . '.created';
        $sSortOrder = $oInput->get('sortOrder') ? $oInput->get('sortOrder') : 'desc';
        $sKeywords  = $oInput->get('keywords') ? $oInput->get('keywords') : '';

        // --------------------------------------------------------------------------

        //  Define the sortable columns
        $sortColumns = [
            $sTableAlias . '.created'        => 'Received Date',
            $sTableAlias . '.driver'         => 'Payment Gateway',
            $sTableAlias . '.invoice_id'     => 'Invoice ID',
            $sTableAlias . '.transaction_id' => 'Transaction ID',
            $sTableAlias . '.amount'         => 'Amount',
            $sTableAlias . '.currency'       => 'Currency',
        ];

        // --------------------------------------------------------------------------

        //  Define the filters
        $aCbFilters = [];
        $aOptions   = [];
        $aDrivers   = $oDriverService->getAll();

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

        //  Define the $aData variable for the queries
        $aData = [
            'expand'    => [
                'invoice',
            ],
            'sort'      => [
                [$sSortOn, $sSortOrder],
            ],
            'keywords'  => $sKeywords,
            'cbFilters' => $aCbFilters,
        ];

        //  Get the items for the page
        $totalRows              = $oPaymentModel->countAll($aData);
        $this->data['payments'] = $oPaymentModel->getAll($iPage, $iPerPage, $aData);

        //  Set Search and Pagination objects for the view
        $this->data['search']     = Helper::searchObject(true, $sortColumns, $sSortOn, $sSortOrder, $iPerPage, $sKeywords, $aCbFilters);
        $this->data['pagination'] = Helper::paginationObject($iPage, $iPerPage, $totalRows);

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
     *
     * @return void
     */
    public function view()
    {
        if (!userHasPermission('admin:invoice:payment:view')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var \Nails\Invoice\Model\Payment $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);

        // --------------------------------------------------------------------------

        $this->data['payment'] = $oPaymentModel->getById(
            $oUri->segment(5),
            ['expand' => $oPaymentModel::EXPAND_ALL]
        );

        if (!$this->data['payment']) {
            show404();
        }

        $this->data['page']->title = 'View Payment &rsaquo; ' . $this->data['payment']->ref;

        Helper::loadView('view');
    }

    // --------------------------------------------------------------------------

    /**
     * View a single payment
     *
     * @return void
     */
    public function refund()
    {
        if (!userHasPermission('admin:invoice:payment:refund')) {
            unauthorised();
        }

        // --------------------------------------------------------------------------

        /** @var Uri $oUri */
        $oUri = Factory::service('Uri');
        /** @var Input $oInput */
        $oInput = Factory::service('Input');
        /** @var \Nails\Invoice\Model\Paymentt $oPaymentModel */
        $oPaymentModel = Factory::model('Payment', Constants::MODULE_SLUG);

        $iPaymentId = $oUri->segment(5);
        $sAmount    = preg_replace('/[^0-9\.]/', '', $oInput->post('amount')) ?: null;
        $sReason    = $oInput->post('reason') ?: null;
        $sRedirect  = urldecode($oInput->post('return_to')) ?: 'invoice/payment/view/' . $iPaymentId;

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

        /** @var Session $oSession */
        $oSession = Factory::service('Session');
        $oSession->setFlashData($sStatus, $sMessage);
        redirect($sRedirect);
    }
}
