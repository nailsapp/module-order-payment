<div class="nailsapp-invoice pending container">
    <?=$this->load->view('invoice/_component/logo', array(), true)?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">
                Invoice <?=$oInvoice->ref?>
            </h2>
            <hr>
            <div class="panel panel-danger">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">There are pending payments against this invoice</h3>
                </div>
                <div class="panel-body">
                    <p>
                        The following payment<?=count($aProcessingPayments) > 1 ? 's are' : ' is'?> pending against this
                        invoice. To avoid duplicate payments, this system will not let you make further payments.
                    </p>
                    <ul class="list-group">
                    <?php

                    foreach ($aProcessingPayments as $oPayment) {

                        ?>
                        <li class="list-group-item">
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
    </div>
</div>
