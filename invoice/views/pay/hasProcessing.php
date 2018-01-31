<div class="nailsapp-invoice pending u-center-screen" id="js-invoice">
    <div class="panel">
        <h1 class="panel__header text-center">
            Invoice <?=$oInvoice->ref?>
        </h1>
        <div class="panel__body text-center">
            <p class="alert alert--warning">There are pending payments against this invoice.</p>
            <p>
                The following payment<?=count($aProcessingPayments) > 1 ? 's are' : ' is'?> pending against this
                invoice. To avoid duplicate payments, this system will not let you make further payments.
            </p>
            <ul>
                <?php

                foreach ($aProcessingPayments as $oPayment) {
                    ?>
                    <li>
                        <strong><?=$oPayment->ref?></strong>
                        &ndash; <?=$oPayment->amount->formatted?>
                    </li>
                    <?php
                }

                ?>
            </ul>
        </div>
    </div>
</div>
