<div class="group-invoice invoice edit">
    <?php if (!empty($invoice)) { dump($invoice); } ?>
    <?=form_open()?>
    <fieldset>
        <legend>Details</legend>
        <p class="alert alert-warning">
            <strong>@todo:</strong> e.g. customer, payment terms, customer details, additional text, etc
        </p>
    </fieldset>
    <fieldset>
        <legend>Line Items</legend>
        <table>
            <thead>
                <tr>
                    <th>Quantity</th>
                    <th>Details</th>
                    <th>Unit Price</th>
                    <th>Tax</th>
                    <th></th>
                </tr>
            </thead>
        </table>
        <p>
            <button class="btn btn-block btn-sm btn-success">
                <b class="fa fa-plus"></b>
                Add Line Item
            </button>
        </p>
    </fieldset>
    <?=form_close()?>
</div>