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
                    <th class="customer">Customer</th>
                    <th class="datetime">Created</th>
                    <th class="datetime">Modified</th>
                    <th class="actions" style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($customers) {

                    foreach ($customers as $oCustomer) {

                        ?>
                        <tr>
                            <td class="customer">
                                <?=$oCustomer->label?>
                                <small>
                                    <?php

                                    if (!empty($oCustomer->first_name)) {
                                        echo $oCustomer->first_name . ' ' . $oCustomer->last_name . '<br />';
                                    }

                                    if (!empty($oCustomer->billing_email)) {
                                        echo mailto($oCustomer->billing_email);
                                    } else {
                                        echo mailto($oCustomer->email);
                                    }

                                    ?>
                                </small>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oCustomer->created)?>
                            <?=adminHelper('loadDateTimeCell', $oCustomer->modified)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:invoice:invoice:create')) {
                                    echo anchor(
                                        'admin/invoice/invoice/create?customer_id=' . $oCustomer->id,
                                        'New Invoice',
                                        'class="btn btn-xs btn-success"'
                                    );
                                }

                                if (userHasPermission('admin:invoice:invoice:manage')) {
                                    echo anchor(
                                        'admin/invoice/invoice?customer_id=' . $oCustomer->id,
                                        'View Invoices',
                                        'class="btn btn-xs btn-warning"'
                                    );
                                }

                                if (userHasPermission('admin:invoice:customer:edit')) {
                                    echo anchor(
                                        'admin/invoice/customer/edit/' . $oCustomer->id,
                                        lang('action_edit'),
                                        'class="btn btn-xs btn-primary"'
                                    );
                                }

                                if (userHasPermission('admin:invoice:customer:delete')) {

                                    if ($oCustomer->invoices->count) {

                                        ?>
                                        <div class="tipsy-fix" rel="tipsy" title="Cannot delete customer with invoices.">
                                            <button class="btn btn-xs btn-danger disabled">
                                                Delete
                                            </button>
                                        </div>
                                        <?php

                                    } else {
                                        echo anchor(
                                            'admin/invoice/customer/delete/' . $oCustomer->id,
                                            lang('action_delete'),
                                            'class="btn btn-xs btn-danger confirm" data-body="You cannot undo this action"'
                                        );
                                    }
                                }

                                ?>
                            </td>
                        <tr>
                        <?php
                    }

                } else {

                    ?>
                    <tr>
                        <td colspan="4" class="no-data">
                            No Customers Found
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
