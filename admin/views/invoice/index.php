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
                    <th class="user">Customer</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($invoices) {

                    foreach ($invoices as $oInvoice) {

                        ?>
                        <tr>
                            <td class="quote">
                                <?=$oInvoice->ref?>
                            </td>
                            <?=adminHelper('loadUserCell', $oInvoice->user_id)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:invoice:invoice:edit')) {

                                    echo anchor(
                                        'admin/invoice/invoice/edit/' . $oInvoice->id,
                                        lang('action_edit'),
                                        'class="awesome small"'
                                    );
                                }

                                if (userHasPermission('admin:invoice:invoice:delete')) {

                                    echo anchor(
                                        'admin/invoice/invoice/delete/' . $oInvoice->id,
                                        lang('action_delete'),
                                        'class="awesome red small confirm" data-body="You cannot undo this action"'
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
                        <td colspan="3" class="no-data">
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