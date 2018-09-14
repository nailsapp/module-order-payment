<?php

namespace Nails\Invoice\DataExport\Source;

use Nails\Admin\Interfaces\DataExport\Source;
use Nails\Factory;

class Invoices implements Source
{
    public function getLabel()
    {
        return 'Invoices';
    }

    // --------------------------------------------------------------------------

    public function getFileName()
    {
        return 'invoices';
    }

    // --------------------------------------------------------------------------

    public function getDescription()
    {
        return 'Export invoices';
    }

    // --------------------------------------------------------------------------

    public function getOptions()
    {
        $oInvoiceModel = Factory::model('Invoice', 'nails/module-invoice');
        return [
            [
                'key'     => 'state',
                'label'   => 'State',
                'type'    => 'dropdown',
                'class'   => 'select2',
                'options' => [
                    ''                                    => 'All',
                    $oInvoiceModel::STATE_DRAFT           => 'Draft',
                    $oInvoiceModel::STATE_OPEN            => 'Open',
                    $oInvoiceModel::STATE_PAID_PARTIAL    => 'Partially Paid',
                    $oInvoiceModel::STATE_PAID_PROCESSING => 'Payments processing',
                    $oInvoiceModel::STATE_PAID            => 'Paid',
                    $oInvoiceModel::STATE_WRITTEN_OFF     => 'Written Off',
                ],
            ],
            [
                'key'   => 'date_start',
                'label' => 'Date Start',
                'info'  => 'This value is inclusive',
                'type'  => 'date',
            ],
            [
                'key'   => 'date_end',
                'label' => 'Date End',
                'info'  => 'This value is inclusive',
                'type'  => 'date',
            ],
        ];
    }

    // --------------------------------------------------------------------------

    public function isEnabled()
    {
        return true;
    }

    // --------------------------------------------------------------------------

    public function execute($aOptions = [])
    {
        $oDb               = Factory::service('Database');
        $oInvoiceModel     = Factory::model('Invoice', 'nails/module-invoice');
        $oInvoiceItemModel = Factory::model('InvoiceItem', 'nails/module-invoice');

        $sState     = getFromArray('state', $aOptions);
        $sDateStart = getFromArray('date_start', $aOptions);
        $sDateEnd   = getFromArray('date_end', $aOptions);

        $sTableInvoice     = $oInvoiceModel->getTableName();
        $sTableInvoiceItem = $oInvoiceItemModel->getTableName();

        $sSqlInvoice     = 'SELECT `i`.* FROM `' . $sTableInvoice . '` `i`';
        $sSqlInvoiceItem = 'SELECT `ii`.* FROM `' . $sTableInvoiceItem . '` `ii` LEFT JOIN `' . $sTableInvoice . '` `i` ON `i`.`id` = `ii`.`invoice_id`';

        $aConditionals = [];

        if (!empty($sDateStart)) {
            $aConditionals[] = '`i`.`dated` >= "' . $sDateStart . '"';
        }

        if (!empty($sDateEnd)) {
            $aConditionals[] = '`i`.`dated` <= "' . $sDateEnd . '"';
        }

        if (!empty($sState)) {
            $aConditionals[] = '`i`.`state` = "' . $sState . '"';
        }

        if (!empty($aConditionals)) {
            $sSqlInvoice     .= ' WHERE ' . implode(' AND ', $aConditionals);
            $sSqlInvoiceItem .= ' WHERE ' . implode(' AND ', $aConditionals);
        }

        return [
            Factory::factory('DataExportSourceResponse', 'nails/module-admin')
                   ->setLabel('Table: ' . $sTableInvoice)
                   ->setFilename('invoice')
                   ->setFields(arrayExtractProperty($oDb->query('DESCRIBE ' . $sTableInvoice)->result(), 'Field'))
                   ->setSource($oDb->query($sSqlInvoice)),
            Factory::factory('DataExportSourceResponse', 'nails/module-admin')
                   ->setLabel('Table: ' . $sTableInvoiceItem)
                   ->setFilename('invoice_item')
                   ->setFields(arrayExtractProperty($oDb->query('DESCRIBE ' . $sTableInvoiceItem)->result(), 'Field'))
                   ->setSource($oDb->query($sSqlInvoiceItem)),
        ];
    }
}
