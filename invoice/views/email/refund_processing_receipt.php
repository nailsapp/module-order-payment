<p>
    This email confirms that a refund is processing for a previous payment you made.
</p>
<p>
    <strong>Original Payment</strong>
</p>
<table width="100%" style="border: 1px solid #CCC;">
    <tbody>
        <tr>
            <td style="padding: 5px;width: 100px;">Reference:</td>
            <td style="padding: 5px;">{{refund.payment.ref}}</td>
        </tr>
        <tr>
            <td style="padding: 5px;width: 100px;">Invoice:</td>
            <td style="padding: 5px;">{{refund.invoice.ref}}</td>
        </tr>
        <tr>
            <td style="padding: 5px;">Amount:</td>
            <td style="padding: 5px;">{{{refund.payment.amount.formatted}}}</td>
        </tr>
    </tbody>
</table>
<p>
    <strong>Refund Details</strong>
</p>
<table width="100%" style="border: 1px solid #CCC;">
    <tbody>
        <tr>
            <td style="padding: 5px;width: 100px;">Reference:</td>
            <td style="padding: 5px;">{{refund.ref}}</td>
        </tr>
        {{#refund.reason}}
        <tr>
            <td style="padding: 5px;width: 100px;">Reason:</td>
            <td style="padding: 5px;">{{refund.reason}}</td>
        </tr>
        {{/refund.reason}}
        <tr>
            <td style="padding: 5px;">Amount:</td>
            <td style="padding: 5px;">{{{refund.amount.formatted}}}</td>
        </tr>
    </tbody>
</table>
<p>
    If you have any questions regarding this refund, please don't hesitate to contact us.
</p>
