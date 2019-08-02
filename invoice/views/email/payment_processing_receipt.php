<p>
    This email confirms that we are processing a payment of <strong>{{{payment.amount.formatted}}}</strong>
    against invoice <strong>{{payment.invoice.ref}}</strong>. The payment has been given reference <strong>{{payment.ref}}</strong>.
</p>
<p>
    You will receive an email when payment has been processed fully and debited from your account.
</p>
<?php
include __DIR__ . DIRECTORY_SEPARATOR . 'invoice.php';
