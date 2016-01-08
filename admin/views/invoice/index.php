<div class="group-invoice invoice browse">
    <p>
        Browse invoices which have been raised.
    </p>
    <?php

        echo adminHelper('loadSearch', $search);
        echo adminHelper('loadPagination', $pagination);

    ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="ref">Ref</th>
                    <th class="state">State</th>
                    <th class="user">Customer</th>
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
                            <td class="state">
                                <?=$invoiceStates[$oInvoice->state]?>
                            </td>
                            <?=adminHelper('loadUserCell', $oInvoice->user->id)?>
                            <td class="amount total">
                                <?=$oInvoice->totals->localised->sub?>
                            </td>
                            <td class="amount tax">
                                <?=$oInvoice->totals->localised->tax?>
                            </td>
                            <td class="amount grand">
                                <?=$oInvoice->totals->localised->grand?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->created)?>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->modified)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:invoice:invoice:edit')) {

                                    if ($oInvoice->state == 'DRAFT') {

                                        echo anchor(
                                            'admin/invoice/invoice/edit/' . $oInvoice->id,
                                            lang('action_edit'),
                                            'class="btn btn-xs btn-primary"'
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

                                        if (empty($oInvoice->payments)) {

                                            echo anchor(
                                                'admin/invoice/invoice/make_draft/' . $oInvoice->id,
                                                'Make Draft',
                                                'class="btn btn-xs btn-warning"'
                                            );

                                        } else {

                                            echo '<a href="#" class="btn btn-xs btn-warning" disabled rel="tipsy" title="An invoice with associated payments cannot be edited">';
                                                echo 'Make Draft';
                                            echo '</a>';
                                        }
                                    }
                                }

                                if (userHasPermission('admin:invoice:invoice:delete') && $oInvoice->state == 'DRAFT') {

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
                        <td colspan="8" class="no-data">
                            No Invoices Found
                        </td>
                    </tr>
                    <?php
                }

                ?>
            </tbody>
        </table>
    </div>
    <?php

        echo adminHelper('loadPagination', $pagination);
        echo adminHelper('loadPagination', $pagination);

    ?>
</div>