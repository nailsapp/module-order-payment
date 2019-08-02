<p>
    Invoice <strong>{{invoice.ref}}</strong> has been generated for your account and is due {{invoice.due.formatted}}.
</p>
<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'invoice.php';
?>
<p>
    <a href="{{invoice.urls.payment}}" class="btn btn-block btn-primary">
        Pay Online Now
    </a>
</p>
<p>
    <a href="{{invoice.urls.download}}" class="btn btn-block btn-default">
        Download Invoice
    </a>
</p>
