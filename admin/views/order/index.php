<div class="group-order-payment order browse">
    <?php

        echo \Nails\Admin\Helper::loadSearch($search);
        echo \Nails\Admin\Helper::loadPagination($pagination);

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
                            <?=\Nails\Admin\Helper::loadUserCell($oOrder->user_id)?>
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
                        <td colspan="2" class="no-data">
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

        echo \Nails\Admin\Helper::loadPagination($pagination);

    ?>
</div>