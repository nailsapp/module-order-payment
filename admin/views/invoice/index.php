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
                                $sText .= '<small>Due: ' . toUserDate($oInvoice->due->raw) . '</small>';

                            } elseif ($oInvoice->is_scheduled) {

                                $sClass = 'warning';
                                $sText  = 'Scheduled';
                                $sText .= '<small>Sending: ' . toUserDate($oInvoice->dated->raw) . '</small>';

                            } elseif ($oInvoice->state->id == 'OPEN') {

                                $sClass = 'success';
                                $sText  = $oInvoice->state->label;
                                $sText .= '<small>Due: ' . toUserDate($oInvoice->due->raw) . '</small>';

                            } elseif ($oInvoice->state->id == 'PAID') {

                                $sClass = 'success';
                                $sText  = $oInvoice->state->label;
                                $sText .= '<small>paid: ' . toUserDateTime($oInvoice->paid->raw) . '</small>';

                            } else {

                                $sClass = '';
                                $sText  = $oInvoice->state->label;
                            }

                            echo '<td class="state ' . $sClass . '">';
                            echo $sText;
                            echo '</td>';

                            ?>
                            <td class="customer">
                                <?php

                                if (!empty($oInvoice->customer)) {

                                    echo anchor(
                                        'admin/invoice/customer/edit/' . $oInvoice->customer->id,
                                        $oInvoice->customer->label
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

                                } else {

                                    echo '<span class="text-muted">Unknown</span>';
                                }

                                ?>
                            </td>
                            <td class="amount total">
                                <?=$oInvoice->totals->localised_formatted->sub?>
                            </td>
                            <td class="amount tax">
                                <?=$oInvoice->totals->localised_formatted->tax?>
                            </td>
                            <td class="amount grand">
                                <?=$oInvoice->totals->localised_formatted->grand?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->created)?>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->modified)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:invoice:invoice:edit')) {

                                    if ($oInvoice->state->id == 'DRAFT') {

                                        echo anchor(
                                            'admin/invoice/invoice/edit/' . $oInvoice->id,
                                            lang('action_edit'),
                                            'class="btn btn-xs btn-primary"'
                                        );

                                    } elseif ($oInvoice->state->id == 'DRAFT') {

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

                                        if (empty($oInvoice->payments->count)) {

                                            echo anchor(
                                                'admin/invoice/invoice/make_draft/' . $oInvoice->id,
                                                'Make Draft',
                                                'class="btn btn-xs btn-warning"'
                                            );
                                        }
                                    }
                                }

                                if (userHasPermission('admin:invoice:invoice:delete') && $oInvoice->state->id == 'DRAFT') {

                                    echo anchor(
                                        'admin/invoice/invoice/delete/' . $oInvoice->id,
                                        lang('action_delete'),
                                        'class="btn btn-xs btn-danger confirm" data-body="You cannot undo this action"'
                                    );
                                }

                                ?>
                            </td>
                        <tr>
                        <?php
                    }

                } else {

                    ?>
                    <tr>
                        <td colspan="9" class="no-data">
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