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
                    <th class="amount total">Total</th>
                    <th class="amount tax">Tax</th>
                    <th class="amount fee">Fee</th>
                    <th class="datetime">Created</th>
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
                            <?=adminHelper('loadUserCell', $oInvoice->user_id)?>
                            <td class="amount total">
                                <?=$oInvoice->total?>
                            </td>
                            <td class="amount tax">
                                <?=$oInvoice->tax?>
                            </td>
                            <td class="amount fee">
                                <?=$oInvoice->fee?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oInvoice->created)?>
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