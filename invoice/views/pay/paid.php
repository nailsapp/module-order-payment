<div class="nailsapp-invoice paid container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">
                Invoice <?=$oInvoice->ref?>
            </h2>
            <hr>
            <div class="alert alert-success">
                <h3>This invoice has been paid!</h3>
                <p>
                    Payment was received <?=$oInvoice->paid->formatted?>, many thanks for your business.
                </p>
            </div>
            <p class="text-center">
                <a href="<?=$oInvoice->urls->download?>" class="btn btn-primary btn-sm">
                    Download Invoice
                </a>
            </p>
        </div>
    </div>
</div>