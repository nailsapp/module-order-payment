<?php

use Nails\Factory;
use Nails\Invoice\Constants;

/**
 * @var stdClass                          $search
 * @var stdClass                          $pagination
 * @var \Nails\Invoice\Resource\Invoice[] $invoices
 */
/** @var \Nails\Invoice\Model\Invoice $oInvoiceModel */
$oInvoiceModel = Factory::model('Invoice', Constants::MODULE_SLUG);

?>
<div class="group-invoice invoice browse">
    <p>
        Browse invoices which have been raised.
    </p>
    <?=adminHelper('loadSearch', $search)?>
    <?=adminHelper('loadPagination', $pagination)?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="ref">Ref</th>
                    <th class="state">State</th>
                    <th class="customer">Customer</th>
                    <th class="currency">Currency</th>
                    <th class="amount sub">Sub Total</th>
                    <th class="amount tax">Tax</th>
                    <th class="amount grand">Grand Total</th>
                    <th class="datetime">Created</th>
                    <th class="datetime">Modified</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($invoices) {

                    foreach ($invoices as $oInvoice) {

                        ?>
                        <tr>
                            <td class="ref">
                                <?=$oInvoice->ref?>
                            </td>
                            <?php

                            if ($oInvoice->is_overdue) {

                                $sClass = 'danger';
                                $sText  = 'Overdue';
                                $sText  .= '<small>Due: ' . toUserDate($oInvoice->due->raw) . '</small>';

                            } elseif ($oInvoice->is_scheduled) {

                                $sClass = 'warning';
                                $sText  = 'Scheduled';
                                $sText  .= '<small>Sending: ' . toUserDate($oInvoice->dated->raw) . '</small>';

                            } elseif ($oInvoice->state->id == $oInvoiceModel::STATE_OPEN) {

                                $sClass = 'success';
                                $sText  = $oInvoice->state->label;
                                $sText  .= '<small>Due: ' . toUserDate($oInvoice->due->raw) . '</small>';

                            } elseif ($oInvoice->state->id == $oInvoiceModel::STATE_PAID || $oInvoice->state->id == $oInvoiceModel::STATE_PAID_PROCESSING) {

                                $sClass = 'success';
                                $sText  = $oInvoice->state->label;
                                if ($oInvoice->paid->raw) {
                                    $sText .= '<small>Paid: ' . toUserDateTime($oInvoice->paid->raw) . '</small>';
                                }

                            } elseif ($oInvoice->state->id == $oInvoiceModel::STATE_CANCELLED || $oInvoice->state->id == $oInvoiceModel::STATE_WRITTEN_OFF) {

                                $sClass = 'danger';
                                $sText  = $oInvoice->state->label;

                            } else {
                                $sClass = '';
                                $sText  = $oInvoice->state->label;
                            }

                            ?>
                            <td class="state <?=$sClass?>">
                                <?=$sText?>
                            </td>
                            <td class="customer">
                                <?php

                                if (!empty($oInvoice->customer)) {

                                    echo anchor(
                                        'admin/invoice/customer/edit/' . $oInvoice->customer->id,
                                        $oInvoice->customer->label ?: $oInvoice->customer->first_name . ' ' . $oInvoice->customer->last_name
                                    );

                                    echo '<small>';
                                    if (!empty($oInvoice->customer->first_name)) {
                                        echo $oInvoice->customer->first_name . ' ' . $oInvoice->customer->last_name;
                                        echo '<br />';
                                    }

                                    if (!empty($oInvoice->customer->billing_email)) {
                                        echo mailto($oInvoice->customer->billing_email);
                                    } else {
                                        echo mailto($oInvoice->customer->email);
                                    }
                                    echo '</small>';

                                } elseif (!empty($oInvoice->email)) {

                                    echo mailto($oInvoice->email);

                                } else {
                                    echo '<span class="text-muted">Unknown</span>';
                                }

                                ?>
                            </td>
                            <td class="currency">
                                <?=$oInvoice->currency->code?>
                            </td>
                            <td class="amount total">
                                <?=$oInvoice->totals->formatted->sub?>
                            </td>
                            <td class="amount tax">
                                <?=$oInvoice->totals->formatted->tax?>
                            </td>
                            <td class="amount grand">
                                <?=$oInvoice->totals->formatted->grand?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->created)?>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->modified)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:invoice:invoice:edit')) {

                                    if ($oInvoice->state->id == $oInvoiceModel::STATE_DRAFT) {

                                        echo anchor(
                                            'admin/invoice/invoice/edit/' . $oInvoice->id,
                                            lang('action_edit'),
                                            'class="btn btn-xs btn-primary"'
                                        );

                                    } elseif (in_array($oInvoice->state->id, [
                                        'WRITTEN_OFF',
                                        'PAID',
                                        'PAID_PROCESING',
                                    ])) {

                                        echo anchor(
                                            'admin/invoice/invoice/view/' . $oInvoice->id,
                                            lang('action_view'),
                                            'class="btn btn-xs btn-default"'
                                        );

                                        echo anchor(
                                            $oInvoice->urls->download,
                                            lang('action_download'),
                                            'class="btn btn-xs btn-primary" target="_blank"'
                                        );

                                    } else {

                                        echo anchor(
                                            'admin/invoice/invoice/view/' . $oInvoice->id,
                                            lang('action_view'),
                                            'class="btn btn-xs btn-default"'
                                        );

                                        echo anchor(
                                            $oInvoice->urls->download,
                                            lang('action_download'),
                                            'class="btn btn-xs btn-primary" target="_blank"'
                                        );

                                        if ($oInvoice->state->id == $oInvoiceModel::STATE_OPEN || $oInvoice->state->id == $oInvoiceModel::STATE_PAID_PARTIAL) {
                                            echo anchor(
                                                $oInvoice->urls->payment,
                                                'Pay',
                                                'class="btn btn-xs btn-primary" target="_blank"'
                                            );
                                        }

                                        $aValidPayments = array_filter(
                                            $oInvoice->payments->data,
                                            function ($oPayment) {
                                                return $oPayment->status->id !== 'FAILED';
                                            }
                                        );

                                        if (empty($aValidPayments)) {
                                            echo anchor(
                                                'admin/invoice/invoice/make_draft/' . $oInvoice->id,
                                                'Make Draft',
                                                'class="btn btn-xs btn-warning"'
                                            );
                                            if (in_array($oInvoice->state->id, [$oInvoiceModel::STATE_OPEN])) {
                                                echo anchor(
                                                    'admin/invoice/invoice/write_off/' . $oInvoice->id,
                                                    'Write Off',
                                                    'class="btn btn-xs btn-danger confirm" data-body="Write invoice ' . $oInvoice->ref . ' off?"'
                                                );
                                            }
                                        }
                                    }
                                }

                                if (userHasPermission('admin:invoice:invoice:delete') && $oInvoice->state->id == $oInvoiceModel::STATE_DRAFT) {
                                    echo anchor(
                                        'admin/invoice/invoice/delete/' . $oInvoice->id,
                                        lang('action_delete'),
                                        'class="btn btn-xs btn-danger confirm" data-body="You cannot undo this action"'
                                    );
                                }

                                echo anchor(
                                    'admin/invoice/invoice/resend/' . $oInvoice->id,
                                    'Re-send',
                                    'class="btn btn-xs btn-default"'
                                );

                                ?>
                            </td>
                        </tr>
                        <?php
                    }

                } else {
                    ?>
                    <tr>
                        <td colspan="10" class="no-data">
                            No Invoices Found
                        </td>
                    </tr>
                    <?php
                }

                ?>
            </tbody>
        </table>
    </div>
    <?=adminHelper('loadPagination', $pagination)?>
</div>
