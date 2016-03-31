<div class="group-invoice invoice view">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Dates</strong>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="header">Dated</td>
                            <td><?=toUserDate($invoice->dated->raw)?></td>
                        </tr>
                        <tr>
                            <td class="header">Terms</td>
                            <td><?=$invoice->terms?> Days</td>
                        </tr>
                        <tr>
                            <td class="header">Due</td>
                            <td><?=toUserDate($invoice->due->raw)?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Customer</strong>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="header">Organisation</td>
                            <td>
                                <?=anchor('admin/invoice/customer/edit/' . $invoice->customer->id, $invoice->customer->label)?>
                            </td>
                        </tr>
                        <tr>
                            <td class="header">Sent To</td>
                            <td>
                                <?php

                                if (!empty($invoice->customer->billing_email)) {
                                    echo mailto($invoice->customer->billing_email);
                                } else {
                                    echo mailto($invoice->customer->email);
                                }

                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-5">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Notes</strong>
                </div>
                <div class="panel-body">
                    <?=$invoice->additional_text ?: '<span class="text-muted">No additional text</span>'?>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Line Items &amp; Totals</strong>
        </div>
        <?php

        if ($invoice->items->count > 0) {

            ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th class="text-center">Unit Cost</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Sub Total</th>
                            <th class="text-center">Tax</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($invoice->items->data as $oItem) {

                            ?>
                            <tr>
                                <td>
                                    <?=$oItem->label?>
                                    <?=$oItem->body ? '<small>' . $oItem->body . '</small>' : ''?>
                                </td>
                                <td class="text-center"><?=$oItem->unit_cost->localised_formatted?></td>
                                <td class="text-center">
                                    <?=$oItem->quantity?> <?=$oItem->unit->label?>
                                </td>
                                <td class="text-center"><?=$oItem->totals->localised_formatted->sub?></td>
                                <td class="text-center">
                                    <?=$oItem->totals->localised_formatted->tax?>
                                    <small>
                                        at <?=$oItem->tax ? $oItem->tax->rate : 0?>%
                                </td>
                                <td class="text-center"><?=$oItem->totals->localised_formatted->grand?></td>
                            </tr>
                            <?php
                        }

                        ?>
                    </tbody>
                    <tfoot class="invoice-total" data-bind="visible: items().length">
                        <tr class="total-row">
                            <td colspan="7" class="text-right">
                                <strong>Sub Total:</strong>
                                <span><?=$invoice->totals->localised_formatted->sub?></span>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="7" class="text-right">
                                <strong>Tax:</strong>
                                <span><?=$invoice->totals->localised_formatted->tax?></span>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="7" class="text-right">
                                <strong>Grand Total:</strong>
                                <span><?=$invoice->totals->localised_formatted->grand?></span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php

        } else {

            ?>
            <div class="panel-body">
                No line items recorded on this invoice.
            </div>
            <?php
        }

        ?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Associated Payments</strong>
        </div>
        <?php

        if (userHasPermission('admin:invoice:payment:view') && $invoice->payments->count > 0) {

            ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Status</th>
                            <th>Gateway</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Created</th>
                            <th>Modified</th>
                            <th class="actions">Actions</th>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($invoice->payments->data as $oPayment) {

                            ?>
                            <tr>
                                <td class="text-center"><?=$oPayment->id?></td>
                                <td class="text-center"><?=$oPayment->status->label?></td>
                                <td><?=$oPayment->driver->label?></td>
                                <td><?=$oPayment->txn_id?></td>
                                <td>
                                    <?php

                                    echo $oPayment->amount->localised_formatted;
                                    if ($oPayment->amount_refunded->base) {
                                        echo '<small>';
                                        echo 'Refunded: ' . $oPayment->amount_refunded->localised_formatted;
                                        echo '</small>';
                                    }

                                    ?>
                                </td>
                                <td>
                                    <?php

                                    echo $oPayment->fee->localised_formatted;
                                    if ($oPayment->fee_refunded->base) {
                                        echo '<small>';
                                        echo 'Refunded: ' . $oPayment->fee_refunded->localised_formatted;
                                        echo '</small>';
                                    }

                                    ?>
                                </td>
                                <?=adminHelper('loadDateTimeCell', $oPayment->created)?>
                                <?=adminHelper('loadDateTimeCell', $oPayment->modified)?>
                                <td class="actions">
                                    <?php

                                    echo anchor(
                                        'admin/invoice/payment/view/' . $oPayment->id,
                                        'View',
                                        'class="btn btn-xs btn-default"'
                                    );

                                    if ($oPayment->is_refundable && userHasPermission('admin:invoice:payment:refund')) {

                                        $aAttr = array(
                                            'class="btn btn-xs btn-danger js-confirm-refund"',
                                            'data-max="' . $oPayment->available_for_refund->localised . '"',
                                            'data-max-formatted="' . $oPayment->available_for_refund->localised_formatted . '"',
                                            'data-return-to="' . urlencode(current_url()) . '"',
                                        );

                                        echo anchor(
                                            'admin/invoice/payment/refund/' . $oPayment->id,
                                            'Refund',
                                            implode(' ', $aAttr)
                                        );
                                    }

                                    ?>
                                </td>
                            </tr>
                            <?php

                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <?php

        } else {

            ?>
            <div class="panel-body text-muted">
                No Associated Payments
            </div>
            <?php
        }

        ?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Associated Refunds</strong>
        </div>
        <?php

        if (userHasPermission('admin:invoice:payment:view') && $invoice->refunds->count > 0) {

            ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Status</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Created</th>
                            <th>Modified</th>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($invoice->refunds->data as $oRefund) {

                            ?>
                            <tr>
                                <td class="text-center"><?=$oRefund->id?></td>
                                <td class="text-center">
                                    <?php

                                    echo $oRefund->status->label;

                                    if (!empty($oRefund->fail_msg)) {

                                        echo '<small class="text-danger">';
                                        echo $oRefund->fail_msg . ' (Code: ' . $oRefund->fail_code . ')';
                                        echo '</small>';
                                    }

                                    ?>
                                </td>
                                <td><?=$oRefund->txn_id?></td>
                                <td>
                                    <?=$oRefund->amount->localised_formatted?>
                                </td>
                                <td>
                                    <?=$oRefund->fee->localised_formatted?>
                                </td>
                                <?=adminHelper('loadDateTimeCell', $oRefund->created)?>
                                <?=adminHelper('loadDateTimeCell', $oRefund->modified)?>
                            </tr>
                            <?php

                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <?php

        } else {

            ?>
            <div class="panel-body text-muted">
                No Associated Payments
            </div>
            <?php
        }

        ?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Associated Emails</strong>
        </div>
        <?php

        if ($invoice->emails->count > 0) {

            ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Recipient</th>
                            <th>Sent</th>
                            <th class="text-center">Preview</th>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($invoice->emails->data as $oEmail) {

                            ?>
                            <tr>
                                <td><?=$oEmail->email->type->label?></td>
                                <td><?=$oEmail->recipient?></td>
                                <?=adminHelper('loadDateTimeCell', $oEmail->created)?>
                                <td class="text-center">
                                    <?php

                                    if (!empty($oEmail->email->preview_url)) {

                                        echo anchor(
                                            $oEmail->email->preview_url,
                                            'Preview',
                                            'class="btn btn-xs btn-primary fancybox"'
                                        );

                                    } else {

                                        echo '<span class="text-muted">Not Available</span>';
                                    }

                                    ?>
                                </td>
                            </tr>
                            <?php

                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <?php

        } else {

            ?>
            <div class="panel-body text-muted">
                No Associated Emails
            </div>
            <?php
        }

        ?>
    </div>
</div>
