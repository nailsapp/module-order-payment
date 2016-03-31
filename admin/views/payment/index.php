<div class="group-invoice payment browse">
    <p>
        Browse payments received by the site.
    </p>
    <?=adminHelper('loadSearch', $search)?>
    <?=adminHelper('loadPagination', $pagination)?>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th class="driver">Gateway</th>
                    <th class="txn-ref">Transaction Ref</th>
                    <th class="status">Status</th>
                    <th class="invoice">Invoice</th>
                    <th class="amount">Amount</th>
                    <th class="amount">Fee</th>
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
                            <?php

                            if ($oPayment->status->id == 'PROCESSING') {

                                $sClass = 'warning';
                                $sText  = $oPayment->status->label;

                            } elseif ($oPayment->status->id == 'COMPLETE') {

                                $sClass = 'success';
                                $sText  = $oPayment->status->label;

                            } elseif ($oPayment->status->id == 'FAILED') {

                                $sClass = 'danger';
                                $sText  = $oPayment->status->label;

                            } else {

                                $sClass = '';
                                $sText  = $oPayment->status->label;
                            }

                            echo '<td class="status ' . $sClass . '">';
                            echo $sText;
                            echo '</td>';

                            ?>
                            <td class="invoice">
                                <?php

                                if ($oPayment->invoice_state == 'DRAFT') {

                                    $sUrl = 'admin/invoice/invoice/edit/' . $oPayment->invoice_id;

                                } else {

                                    $sUrl = 'admin/invoice/invoice/view/' . $oPayment->invoice_id;
                                }

                                echo anchor(
                                    $sUrl,
                                    $oPayment->invoice_ref . ' &mdash; ' . $invoiceStates[$oPayment->invoice_state]
                                );

                                ?>
                            </td>
                            <td class="amount">
                                <?php

                                echo $oPayment->amount->localised_formatted;
                                if ($oPayment->amount_refunded->base) {
                                    echo '<small>';
                                    echo 'Refunded: ' . $oPayment->amount_refunded->localised_formatted;
                                    echo '</small>';
                                }

                                ?>
                            </td>
                            <td class="fee">
                                <?php

                                echo $oPayment->fee->localised_formatted;
                                if ($oPayment->fee_refunded->base) {
                                    echo '<small>';
                                    echo 'Refunded: ' . $oPayment->fee_refunded->localised_formatted;
                                    echo '</small>';
                                }

                                ?>
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
    <?=adminHelper('loadPagination', $pagination)?>
</div>