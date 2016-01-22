<div class="group-invoice payment browse">
    <p>
        Browse payments received by the site.
    </p>
    <?php

        echo adminHelper('loadSearch', $search);
        echo adminHelper('loadPagination', $pagination);

    ?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="driver">Gateway</th>
                    <th class="txn-ref">Transaction Ref</th>
                    <th class="invoice">Invoice</th>
                    <th class="amount">Amount</th>
                    <th class="currency">Currency</th>
                    <th class="datetime">Received</th>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php

                if ($payments) {

                    foreach ($payments as $oPayment) {

                        ?>
                        <tr>
                            <td class="driver">
                                <?=$oPayment->driver->label?>
                            </td>
                            <td class="txn-ref">
                                <?=$oPayment->txn_id?>
                            </td>
                            <td class="invoice">
                                <?php

                                if ($oPayment->invoice_state == 'DRAFT') {

                                    $sUrl = 'admin/invoice/invoice/edit/' . $oPayment->invoice_id;

                                } else {

                                    $sUrl = 'admin/invoice/invoice/view/' . $oPayment->invoice_id;
                                }

                                echo anchor(
                                    $sUrl,
                                    $oPayment->invoice_ref . ' (' . $invoiceStates[$oPayment->invoice_state] . ')'
                                );

                                ?>
                            </td>
                            <td class="amount">
                                <?=$oPayment->amount->localised_formatted?>
                            </td>
                            <td class="currency">
                                <?=$oPayment->currency?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oPayment->created)?>
                            <td class="actions">
                                <?php

                                echo anchor(
                                    'admin/invoice/payment/view/' . $oPayment->id,
                                    lang('action_view'),
                                    'class="btn btn-xs btn-default"'
                                );

                                ?>
                            </td>
                        <tr>
                        <?php
                    }

                } else {

                    ?>
                    <tr>
                        <td colspan="7" class="no-data">
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

        echo adminHelper('loadPagination', $pagination);

    ?>
</div>