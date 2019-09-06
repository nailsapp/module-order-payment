<table style="width: 100%">
    <td class="container" width="100%">
        <div class="content">
            <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e9e9e9; border-radius: 3px;">
                <tbody>
                    <tr>
                        <td>
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <table style="margin: 40px auto; width: 80%;">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <h1 style="font-size:25px;">
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

                                                            <strong style="display: inline-block; width: 40px;">Date:</strong>
                                                            {{payment.invoice.dated.formatted}}
                                                            <br>
                                                            <strong style="display: inline-block; width: 40px;">Due:</strong>
                                                            {{payment.invoice.due.formatted}}
                                                            <br><br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                                                <tbody>
                                                                    {{#payment.invoice.items.data}}
                                                                    <tr>
                                                                        <td style="border-top: #eee 1px solid; padding:10px;">
                                                                            {{label}}
                                                                            <small style="display: block; margin-top: 0.5em;">{{body}}</small>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: #eee 1px solid; padding:10px; text-align:right;">
                                                                            {{{totals.formatted.sub}}}
                                                                        </td>
                                                                    </tr>
                                                                    {{/payment.invoice.items.data}}

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>VAT</strong>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>{{{payment.invoice.totals.formatted.tax}}}</strong>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>Total</strong>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>{{{payment.invoice.totals.formatted.grand}}}</strong>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php

                                            use Nails\Invoice\Constants;

                                            $aDetails = [
                                                appSetting('business_name', Constants::MODULE_SLUG) ? '<strong>' . appSetting('business_name', Constants::MODULE_SLUG) . '</strong>' : '',
                                                appSetting('business_address', Constants::MODULE_SLUG) ? nl2br(appSetting('business_address', Constants::MODULE_SLUG)) : '',
                                                appSetting('business_phone', Constants::MODULE_SLUG),
                                                appSetting('business_email', Constants::MODULE_SLUG),
                                                appSetting('business_vat_number', Constants::MODULE_SLUG) ? '<br>VAT Registration No. ' . appSetting('business_vat_number', Constants::MODULE_SLUG) : '',
                                            ];

                                            $aDetails = array_filter($aDetails);

                                            if (!empty($aDetails)) {
                                                ?>
                                                <table style="margin: 0; width: 100%; border-top: 1px solid #EFEFEF;">
                                                    <tbody>
                                                        <tr>
                                                            <td style="padding: 25px 50px;">
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
