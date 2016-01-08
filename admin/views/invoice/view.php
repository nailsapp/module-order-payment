<div class="group-invoice invoice view">
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Invoice Details</strong>
        </div>
        <div class="panel-body">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
            tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
            quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
            consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
            cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
            proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Line Items &amp; Totals</strong>
        </div>
        <?php

        if ($invoice->items->count > 0) {

            ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>cat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        foreach ($invoice->items->data as $oItem) {

                            ?>
                            <tr>
                                <td>
                                    <?=$oItem->id?>
                                </td>
                            </tr>
                            <?php
                        }

                        ?>
                    </tbody>
                    <tfoot class="invoice-total" data-bind="visible: items().length">
                        <tr class="total-row">
                            <td colspan="6" class="text-right">
                                <strong>Sub Total:</strong>
                                <?=$invoice->totals->localised->sub?>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="6" class="text-right">
                                <strong>Tax:</strong>
                                <?=$invoice->totals->localised->tax?>
                            </td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="6" class="text-right">
                                <strong>Grand Total:</strong>
                                <?=$invoice->totals->localised->grand?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <?php

        } else {

            ?>
            <div class="panel-body">
                No line items recorded on this invoice.
            </div>
            <?php
        }

        ?>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Total</strong>
        </div>
        <div class="panel-body">
            <?php dump($invoice->totals)?>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Associated Payments</strong>
        </div>
        <div class="panel-body">
            <?php dump($invoice->payments)?>
        </div>
    </div>
</div>
<?php

dump($invoice);