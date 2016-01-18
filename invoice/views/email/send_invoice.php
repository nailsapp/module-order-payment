<p>
    Invoice <strong>{{invoice.ref}}</strong> has been generated for your account and is due {{invoice.due.formatted}}.
</p>
<p>
    <a href="{{invoice.urls.payment}}" class="btn btn-primary">
        Pay Online Now
    </a>
    <a href="{{invoice.urls.download}}" class="btn btn-default">
        Download Invoice
    </a>
</p>