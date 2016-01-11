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
                            <td><?=toUserDate($invoice->dated)?></td>
                        </tr>
                        <tr>
                            <td class="header">Terms</td>
                            <td><?=$invoice->terms?> Days</td>
                        </tr>
                        <tr>
                            <td class="header">Due</td>
                            <td><?=toUserDate($invoice->due)?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Recipient</strong>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="header">User</td>
                            <?=adminHelper('loadUserCell', $invoice->user->id)?>
                        </tr>
                        <tr>
                            <td class="header">Sent To</td>
                            <td>
                                <?php

                                if (!empty($invoice->user_email)) {
                                    echo mailto($invoice->user_email);
                                } else {
                                    echo mailto($invoice->user->email);
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

        if ($invoice->payments->count > 0) {

        } else {

            ?>
            <div class="panel-body text-muted">
                No Associated Payments
            </div>
            <?php
        }

        ?>
    </div>
</div>
