<div class="nailsapp-invoice paid container">
    <?=$this->load->view('invoice/_component/logo', array(), true)?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">
                Invoice <?=$oPayment->invoice->ref?>
            </h2>
            <hr>
            <div class="panel panel-success text-center">
                <div class="panel-heading">
                    <h3 class="panel-title">Thank you for your payment of <?=$oPayment->amount->formatted?></h3>
                </div>
                <div class="panel-body">
                    Your payment reference is <strong><?=$oPayment->ref?></strong>
                </div>
            </div>
            <hr />
            <div class="panel panel-warning">
                <div class="panel-heading text-center">
                    <h3 class="panel-title">Payment is Processing</h3>
                </div>
                <div class="panel-body">
                    <strong>Please note:</strong> Your payment has not completed processing yet, you will be informed by email once the payment
                    is complete at which point your purchases will be actioned.
                </div>
            </div>
            <p class="text-center">
                <a href="<?=$oPayment->invoice->urls->download?>" class="btn btn-primary btn-sm">
                    Download Invoice
                </a>
            </p>
        </div>
    </div>
</div>
