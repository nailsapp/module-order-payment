<div class="group-order-payment order browse">
    <p>
        Browse orders which have been generated.
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

                if ($orders) {

                    foreach ($orders as $oOrder) {

                        ?>
                        <tr>
                            <td class="quote">
                                <?=$oOrder->ref?>
                            </td>
                            <?=adminHelper('loadUserCell', $oOrder->user_id)?>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:order:order:edit')) {

                                    echo anchor(
                                        'admin/order/order/edit/' . $oOrder->id,
                                        lang('action_edit'),
                                        'class="awesome small"'
                                    );
                                }

                                if (userHasPermission('admin:order:order:delete')) {

                                    echo anchor(
                                        'admin/order/order/delete/' . $oOrder->id,
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
                            No Orders Found
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