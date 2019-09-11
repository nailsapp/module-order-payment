<?php

/**
 * @var \Nails\Invoice\Resource\Payment[] $payments
 */

?>
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
                                <?=$oPayment->driver->getLabel()?>
                                <small>
                                    <?=$oPayment->driver->getSlug()?>
                                </small>
                            </td>
                            <td class="txn-ref">
                                <?=$oPayment->txn_id ?: '<span class="text-muted">&mdash;</span>'?>
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
                            ?>
                            <td class="status <?=$sClass?>">
                                <?=$sText?>
                            </td>
                            <td class="invoice">
                                <?php

                                if (!empty($oPayment->invoice)) {

                                    if ($oPayment->invoice->state->id == 'DRAFT') {
                                        $sUrl = 'admin/invoice/invoice/edit/' . $oPayment->invoice->id;
                                    } else {
                                        $sUrl = 'admin/invoice/invoice/view/' . $oPayment->invoice->id;
                                    }

                                    echo anchor(
                                        $sUrl,
                                        $oPayment->invoice->ref . ' &mdash; ' . $oPayment->invoice->state->label
                                    );
                                } else {
                                    echo '<span class="text-muted">&mdash;</span>';
                                }

                                ?>
                            </td>
                            <td class="amount">
                                <?php
                                echo $oPayment->amount->formatted;
                                if ($oPayment->amount_refunded->raw) {
                                    echo '<small>';
                                    echo 'Refunded: ' . $oPayment->amount_refunded->formatted;
                                    echo '</small>';
                                }
                                ?>
                            </td>
                            <td class="fee">
                                <?php
                                echo $oPayment->fee->formatted;
                                if ($oPayment->fee_refunded->raw) {
                                    echo '<small>';
                                    echo 'Refunded: ' . $oPayment->fee_refunded->formatted;
                                    echo '</small>';
                                }
                                ?>
                            </td>
                            <td class="currency">
                                <?=$oPayment->currency->code?>
                            </td>
                            <?=adminHelper('loadDateTimeCell', $oPayment->created)?>
                            <td class="actions">
                                <?php
                                echo anchor(
                                    'admin/invoice/payment/view/' . $oPayment->id,
                                    lang('action_view'),
                                    'class="btn btn-xs btn-default"'
                                );
                                if (!empty($oPayment->invoice) && userHasPermission('admin:invoice:invoice:manage')) {
                                    echo anchor(
                                        'admin/invoice/invoice/view/' . $oPayment->invoice->id,
                                        'View Invoice',
                                        'class="btn btn-xs btn-default"'
                                    );
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="9" class="no-data">
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
