<p>
    This email confirms that a refund has been processed for a previous payment you made.
</p>
<p>
    <strong>Original Payment</strong>
</p>
<table width="100%" style="border: 1px solid #CCCCCC;background: #FFFFFF;">
    <tbody>
        <tr>
            <td style="padding: 5px;width: 100px;">Reference:</td>
            <td style="padding: 5px;">{{payment.ref}}</td>
        </tr>
        <tr>
            <td style="padding: 5px;width: 100px;">Invoice:</td>
            <td style="padding: 5px;">{{invoice.ref}}</td>
        </tr>
        <tr>
            <td style="padding: 5px;">Amount:</td>
            <td style="padding: 5px;">{{{payment.amount}}}</td>
        </tr>
    </tbody>
</table>
<p>
    <strong>Refund Details</strong>
</p>
<table width="100%" style="border: 1px solid #CCCCCC;background: #FFFFFF;">
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
            <td style="padding: 5px;">{{{refund.amount}}}</td>
        </tr>
    </tbody>
</table>
<p>
    If you have any questions regarding this refund, please don't hesitate to contact us.
</p>
