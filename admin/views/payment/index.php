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
                    <th class="processor">Processor</th>
                    <th class="trans-ref">Transaction Ref</th>
                    <th class="invoice">Invoice</th>
                    <th class="amount">Amount</th>
                    <th class="amount fee">Fee</th>
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
                            <td class="processor">
                                <?php

                                if (empty($drivers[$oPayment->processor])) {
                                    echo $oPayment->processor;
                                    ?>
                                    <small class="text-danger">
                                        <b class="fa fa-exclamation-triangle"></b>
                                        Payment Processor not installed
                                    </small>
                                    <?php

                                } else {

                                    echo $drivers[$oPayment->processor]->getLabel();
                                }

                                ?>
                            </td>
                            <td class="trans-ref">
                                <?=$oPayment->transaction_ref?>
                            </td>
                            <td class="invoice">
                                <?php

                                if ($oPayment->invoice_state == 'EDIT') {

                                    echo anchor(
                                        'admin/invoice/invoice/edit/' . $oPayment->invoice_id,
                                        $oPayment->invoice_ref . ' (' . $invoiceStates[$oPayment->invoice_state] . ')'
                                    );

                                } else {

                                    echo anchor(
                                        'admin/invoice/invoice/view/' . $oPayment->invoice_id,
                                        $oPayment->invoice_ref . ' (' . $invoiceStates[$oPayment->invoice_state] . ')'
                                    );
                                }

                                ?>
                            </td>
                            <td class="amount">
                                <?=$oPayment->amount?>
                            </td>
                            <td class="amount fee">
                                <?=$oPayment->fee?>
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
                        <td colspan="8" class="no-data">
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