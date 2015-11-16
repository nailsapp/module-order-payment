<div class="group-payment-payment payment browse">
    <?php

        echo \Nails\Admin\Helper::loadSearch($search);
        echo \Nails\Admin\Helper::loadPagination($pagination);

    ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="ref">Ref</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($payments) {

                    foreach ($payments as $oPayment) {

                        ?>
                        <tr>
                            <td class="quote">
                                <?=$oPayment->ref?>
                            </td>
                            <td class="actions">
                                <?php

                                if (userHasPermission('admin:payment:payment:edit')) {

                                    echo anchor(
                                        'admin/order/payment/edit/' . $oPayment->id,
                                        lang('action_edit'),
                                        'class="awesome small"'
                                    );
                                }

                                if (userHasPermission('admin:payment:payment:delete')) {

                                    echo anchor(
                                        'admin/order/payment/delete/' . $oPayment->id,
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
                            No Payments Found
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