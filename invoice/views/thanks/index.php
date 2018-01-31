<div class="nailsapp-invoice paid u-center-screen" id="js-invoice">
    <div class="panel">
        <h1 class="panel__header text-center">
            Invoice <?=$oPayment->invoice->ref?>
        </h1>
        <div class="panel__body text-center">
            <p>Thank you for your payment of <?=$oPayment->amount->formatted?>.</p>
            <p>Your payment reference is <strong><?=$oPayment->ref?></strong>.</p>
            <p>
                <a href="<?=$oPayment->invoice->urls->download?>" class="btn btn--block">
                    Download Invoice
                </a>
            </p>
        </div>
    </div>
</div>
