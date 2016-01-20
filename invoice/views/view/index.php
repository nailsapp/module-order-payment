<div class="container">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5>
                    Invoice <?=$oInvoice->ref?>
                    <?php

                    if ($oInvoice->isOverdue) {

                        $sText  = 'Overdue';
                        $sClass = 'danger';

                    } else {

                        $sText  = $oInvoice->state->label;
                        $sClass = 'success';
                    }

                    ?>
                    <span class="label label-<?=$sClass?> pull-right">
                        <?=$sText?>
                    </span>
                </h5>
            </div>
            <div class="panel-body">
                <?php dump($oInvoice->totals)?>
                <?php dump($oInvoice->items->data)?>
            </div>
        </div>
    </div>
</div>
