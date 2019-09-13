<table style="width: 100%">
    <td class="container" width="100%">
        <div class="content">
            <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e9e9e9; border-radius: 3px; background: #ffffff">
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
                                                                Invoice {{invoice.ref}}
                                                            </h1>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            {{invoice.customer.label}}
                                                            {{#invoice.customer.billing_address.line_1}}
                                                            <br>{{invoice.customer.billing_address.line_1}}
                                                            {{/invoice.customer.billing_address.line_1}}

                                                            {{#invoice.customer.billing_address.line_2}}
                                                            <br>{{invoice.customer.billing_address.line_2}}
                                                            {{/invoice.customer.billing_address.line_2}}

                                                            {{#invoice.customer.billing_address.town}}
                                                            <br>{{invoice.customer.billing_address.town}}
                                                            {{/invoice.customer.billing_address.town}}

                                                            {{#invoice.customer.billing_address.county}}
                                                            <br>{{invoice.customer.billing_address.county}}
                                                            {{/invoice.customer.billing_address.county}}

                                                            {{#invoice.customer.billing_address.postcode}}
                                                            <br>{{invoice.customer.billing_address.postcode}}
                                                            {{/invoice.customer.billing_address.postcode}}

                                                            {{#invoice.customer.billing_address.country}}
                                                            <br>{{invoice.customer.billing_address.country}}
                                                            {{/invoice.customer.billing_address.country}}

                                                            <br><br>

                                                            <strong style="display: inline-block; width: 40px;">Date:</strong>
                                                            {{invoice.dated}}
                                                            <br>
                                                            <strong style="display: inline-block; width: 40px;">Due:</strong>
                                                            {{invoice.due}}
                                                            <br><br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <table cellpadding="0" cellspacing="0" style="width: 100%;">
                                                                <tbody>
                                                                    {{#invoice.items}}
                                                                    <tr>
                                                                        <td style="border-top: #eee 1px solid; padding:10px;">
                                                                            {{label}}
                                                                            <small style="display: block; margin-top: 0.5em;">{{body}}</small>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: #eee 1px solid; padding:10px; text-align:right;">
                                                                            {{{totals.sub}}}
                                                                        </td>
                                                                    </tr>
                                                                    {{/invoice.items}}

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>Sub-total</strong>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>{{{invoice.totals.sub}}}</strong>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>VAT</strong>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>{{{invoice.totals.tax}}}</strong>
                                                                        </td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td class="alignright" width="80%" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>Total</strong>
                                                                        </td>
                                                                        <td class="alignright" style="border-top: 2px solid #333; border-bottom: 2px solid #333; padding:10px; text-align:right;">
                                                                            <strong>{{{invoice.totals.grand}}}</strong>
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
