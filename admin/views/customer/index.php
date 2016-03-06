<div class="group-invoice customer browse">
    <p>
        Browse your customer address book.
    </p>
    <?=adminHelper('loadSearch', $search)?>
    <?=adminHelper('loadPagination', $pagination)?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="user">Customer</th>
                    <th class="datetime">Created</th>
                    <th class="datetime">Modified</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($customers) {

                    foreach ($customers as $oCustomer) {

                        ?>
                        <tr>
                            <td class="ref">
                                <?=$oCustomer->organisation?>
                                <?=$oCustomer->first_name . ' ' . $oCustomer->last_name?>
                                <?=$oCustomer->email?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oCustomer->created)?>
                            <?=adminHelper('loadDateTimeCell', $oCustomer->modified)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:invoice:customer:edit')) {

                                    echo anchor(
                                        'admin/invoice/customer/edit/' . $oCustomer->id,
                                        lang('action_edit'),
                                        'class="btn btn-xs"'
                                    );
                                }

                                if (userHasPermission('admin:invoice:customer:delete')) {

                                    echo anchor(
                                        'admin/invoice/customer/delete/' . $oCustomer->id,
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