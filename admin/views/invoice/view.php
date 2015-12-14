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
            <strong>Line Items</strong>
        </div>
        <div class="panel-body">
            <?php dump($invoice->items)?>
        </div>
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