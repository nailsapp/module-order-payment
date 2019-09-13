<?php
/**
 * @var \Nails\Invoice\Resource\Payment $payment
 */
?>
<div class="group-invoice payment view">
    <div class="row">
        <div class="col-md-3">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Details</strong>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="header">Gateway</td>
                            <td>
                                <?=$payment->driver->getLabel()?>
                                <small>
                                    <?=$payment->driver->getSlug()?>
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td class="header">Transaction ID</td>
                            <td><?=$payment->transaction_id?></td>
                        </tr>
                        <tr>
                            <td class="header">Description</td>
                            <td><?=$payment->description?></td>
                        </tr>
                        <tr>
                            <td class="header">Status</td>
                            <td>
                                <?php

                                echo $payment->status->label;

                                if (!empty($payment->fail_msg)) {
                                    ?>
                                    <small class="text-danger">
                                        <?=$payment->fail_msg?> (Code: <?=$payment->fail_code?>)
                                    </small>
                                    <?php
                                }

                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Values</strong>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="header">Currency</td>
                            <td><?=$payment->currency->code?></td>
                        </tr>
                        <tr>
                            <td class="header">Amount</td>
                            <td><?=$payment->amount->formatted?></td>
                        </tr>
                        <tr>
                            <td class="header">Amount (refunded)</td>
                            <td><?=$payment->amount_refunded->formatted?></td>
                        </tr>
                        <tr>
                            <td class="header">Fee</td>
                            <td><?=$payment->fee->formatted?></td>
                        </tr>
                        <tr>
                            <td class="header">Fee (refunded)</td>
                            <td><?=$payment->fee_refunded->formatted?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Dates</strong>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td class="header">Created</td>
                            <td><?=toUserDateTime($payment->created)?></td>
                        </tr>
                        <tr>
                            <td class="header">Modified</td>
                            <td><?=toUserDateTime($payment->modified)?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="panel panel-default match-height">
                <div class="panel-heading">
                    <strong>Custom Data</strong>
                </div>
                <?php

                if (!empty((array) $payment->custom_data)) {
                    ?>
                    <pre style="padding: 1em;"><?=json_encode($payment->custom_data, JSON_PRETTY_PRINT)?></pre>
                    <?php
                } else {
                    ?>
                    <div class="panel-body text-muted">
                        No Custom Data
                    </div>
                    <?php
                }

                ?>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Associated Refunds</strong>
        </div>
        <?php

        if ($payment->refunds->count > 0) {
            ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">Status</th>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Fee</th>
                            <th>Created</th>
                            <th>Modified</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($payment->refunds->data as $oRefund) {
                            ?>
                            <tr>
                                <td class="text-center"><?=$oRefund->id?></td>
                                <td class="text-center">
                                    <?php

                                    echo $oRefund->status->label;

                            if (!empty($oRefund->fail_msg)) {
                                echo '<small class="text-danger">';
                                echo $oRefund->fail_msg . ' (Code: ' . $oRefund->fail_code . ')';
                                echo '</small>';
                            } ?>
                                </td>
                                <td><?=$oRefund->transaction_id?></td>
                                <td>
                                    <?=$oRefund->amount->formatted?>
                                </td>
                                <td>
                                    <?=$oRefund->fee->formatted?>
                                </td>
                                <?=adminHelper('loadDateTimeCell', $oRefund->created)?>
                                <?=adminHelper('loadDateTimeCell', $oRefund->modified)?>
                            </tr>
                            <?php
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php
        } else {
            ?>
            <div class="panel-body text-muted">
                No Associated Refunds
            </div>
            <?php
        }

        ?>
    </div>
</div>
