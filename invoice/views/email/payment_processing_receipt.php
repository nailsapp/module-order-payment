<p>
    This email confirms that we are processing a payment of <strong>{{{payment.amount.localised_formatted}}}</strong>
    against invoice <strong>{{payment.invoice.ref}}</strong>. The payment has been given reference <strong>{{payment.ref}}</strong>.
</p>
<p>
    You will receive an email when payment has been processed fully and debited from your account.
</p>
<table style="margin-top: 2em;">
    <td class="container" width="600">
        <div class="content">
            <table width="100%" cellpadding="0" cellspacing="0" style="background: #fff;border: 1px solid #e9e9e9;border-radius: 3px;">
                <tbody>
                    <tr>
                        <td class="content-wrap" style="text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table style="margin: 40px auto;text-align: left;width: 80%;">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <h1 style="text-align:left;font-size:25px;">
                                                                Invoice {{payment.ref}}
                                                            </h1>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{payment.invoice.customer.label}}
                                                            {{#payment.invoice.customer.address.line_1}}
                                                                <br>{{payment.invoice.customer.address.line_1}}
                                                            {{/payment.invoice.customer.address.line_1}}

                                                            {{#payment.invoice.customer.address.line_2}}
                                                                <br>{{payment.invoice.customer.address.line_2}}
                                                            {{/payment.invoice.customer.address.line_2}}

                                                            {{#payment.invoice.customer.address.line_town}}
                                                                <br>{{payment.invoice.customer.address.line_town}}
                                                            {{/payment.invoice.customer.address.line_town}}

                                                            {{#payment.invoice.customer.address.line_county}}
                                                                <br>{{payment.invoice.customer.address.line_county}}
                                                            {{/payment.invoice.customer.address.line_county}}

                                                            {{#payment.invoice.customer.address.line_postcode}}
                                                                <br>{{payment.invoice.customer.address.line_postcode}}
                                                            {{/payment.invoice.customer.address.line_postcode}}

                                                            {{#payment.invoice.customer.address.line_country}}
                                                                <br>{{payment.invoice.customer.address.line_country}}
                                                            {{/payment.invoice.customer.address.line_country}}

                                                            <br><br>

                                                            <strong style="display: inline-block; width: 40px;">Date:</strong> {{payment.invoice.dated.formatted}}
                                                            <br>
                                                            <strong style="display: inline-block; width: 40px;">Due:</strong> {{payment.invoice.due.formatted}}
                                                            <br><br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                                                <tbody>
                                                                    {{#payment.invoice.items.data}}
                                                                        <tr>
                                                                            <td style="border-top: #eee 1px solid;padding:10px;">
                                                                                {{label}}
                                                                                <small style="display: block;margin-top: 0.5em;">{{body}}</small>
                                                                            </td>
                                                                            <td class="alignright" style="border-top: #eee 1px solid;padding:10px;text-align:right;">
                                                                                {{{totals.localised_formatted.sub}}}
                                                                            </td>
                                                                        </tr>
                                                                    {{/payment.invoice.items.data}}

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333;border-bottom: 2px solid #333;font-weight: 700;padding:10px;text-align:right;">VAT</td>
                                                                        <td class="alignright" style="border-top: 2px solid #333;border-bottom: 2px solid #333;font-weight: 700;padding:10px;text-align:right;">
                                                                            {{{payment.invoice.totals.localised_formatted.tax}}}
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333;border-bottom: 2px solid #333;font-weight: 700;padding:10px;text-align:right;">Total</td>
                                                                        <td class="alignright" style="border-top: 2px solid #333;border-bottom: 2px solid #333;font-weight: 700;padding:10px;text-align:right;">
                                                                            {{{payment.invoice.totals.localised_formatted.grand}}}
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table style="margin: 40px auto;text-align: left;width: 80%;">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <table border="0" cellspacing="0" cellpadding="0">
                                                                <tr>
                                                                    <td align="center">
                                                                        <a href="{{payment.invoice.urls.download}}" class="btn">
                                                                            Download
                                                                        </a>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php

                                            $aDetails = array(
                                                appSetting('business_name', 'nailsapp/module-invoice') ? '<strong>' . appSetting('business_name', 'nailsapp/module-invoice') . '</strong>' : '',
                                                appSetting('business_address', 'nailsapp/module-invoice') ? nl2br(appSetting('business_address', 'nailsapp/module-invoice')) : '',
                                                appSetting('business_phone', 'nailsapp/module-invoice'),
                                                appSetting('business_email', 'nailsapp/module-invoice'),
                                                appSetting('business_vat_number', 'nailsapp/module-invoice') ? '<br>VAT Registration No. ' . appSetting('business_vat_number', 'nailsapp/module-invoice') : ''
                                            );

                                            $aDetails = array_filter($aDetails);

                                            if (!empty($aDetails)) {

                                                ?>
                                                <table style="margin: 0;text-align: left;width: 100%;border-top: 1px solid #EFEFEF; background: #FBFBFB">
                                                    <tbody>
                                                        <tr>
                                                            <td style="font-size:0.7em; line-height: 160%; padding: 25px 50px">
                                                                <?=implode('<br>', $aDetails)?>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <?php
                                            }

                                            ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </td>
</table>