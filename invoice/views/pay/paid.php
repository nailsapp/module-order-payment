<div class="nails-invoice paid u-center-screen" id="js-invoice">
    <div class="panel">
        <h1 class="panel__header text-center">
            Invoice <?=$oInvoice->ref?>
        </h1>
        <div class="panel__body text-center">
            <p class="alert alert--success">This invoice has been paid.</p>
            <p>Payment was received <?=$oInvoice->paid->formatted?>, many thanks for your business.</p>
            <p>
                <a href="<?=$oInvoice->urls->download?>" class="btn btn--block">
                    Download Invoice
                </a>
            </p>
        </div>
    </div>
</div>
