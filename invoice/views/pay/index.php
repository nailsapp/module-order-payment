<div class="nails-invoice pay u-center-screen" id="js-invoice">
    <div class="panel shakeable">
        <?=form_open($sFormUrl, 'id="js-invoice-main-form"')?>
        <div class="mask" id="js-invoice-mask">
            Loading...
        </div>
        <h1 class="panel__header text-center">
            Invoice <?=$oInvoice->ref?>
        </h1>
        <div class="panel__body">
            <p class="alert alert--danger <?=empty($error) ? 'hidden' : ''?>" id="js-error">
                <?=$error?>
            </p>
            <p class="alert alert--success <?=empty($success) ? 'hidden' : ''?>">
                <?=$success?>
            </p>
            <p class="alert alert--warning <?=empty($message) ? 'hidden' : ''?>">
                <?=$message?>
            </p>
            <p class="alert alert--info <?=empty($info) ? 'hidden' : ''?>">
                <?=$info?>
            </p>
            <table class="table">
                <tbody>
                    <?php
                    foreach ($oInvoice->items->data as $oItem) {
                        ?>
                        <tr>
                            <td>
                                <?=$oItem->label?>
                            </td>
                            <td align="right">
                                <?=$oItem->totals->formatted->grand?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td>
                            Total
                        </td>
                        <td align="right">
                            <?=$oInvoice->totals->formatted->grand?>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <hr>
            <h5>Choose Payment Method</h5>
            <ul class="list list--unstyled list--bordered" id="js-invoice-driver-select">
                <?php

                foreach ($aDrivers as $oDriver) {

                    $sHasFields  = json_encode(!empty($oDriver->getPaymentFields()));
                    $sIsCard     = json_encode($oDriver->getPaymentFields() === 'CARD');
                    $sIsRedirect = json_encode($oDriver->isRedirect());

                    $aData = [
                        'data-driver="' . $oDriver->getSlug() . '"',
                        'data-has-fields="' . $sHasFields . '"',
                        'data-is-card="' . $sIsCard . '"',
                        'data-is-redirect="' . $sIsRedirect . '"',
                    ];

                    ?>
                    <li>
                        <label class="form__group js-invoice-driver-select">
                            <?php

                            echo form_radio(
                                'driver',
                                $oDriver->getSlug(),
                                set_radio('driver', $oDriver->getSlug(), count($aDrivers) === 1),
                                implode(' ', $aData)
                            );

                            echo $oDriver->getLabel();

                            $sLogoUrl = $oDriver->getLogoUrl(400, 20);
                            if (!empty($sLogoUrl)) {
                                echo img(
                                    [
                                        'src'   => $sLogoUrl,
                                        'class' => 'pull-right',
                                    ]
                                );
                            }

                            ?>
                        </label>
                    </li>
                    <?php
                }

                ?>
            </ul>
            <?php

            $bShowCardFields = false;
            foreach ($aDrivers as $oDriver) {

                $mFields    = $oDriver->getPaymentFields();
                $sDriverKey = md5($oDriver->getSlug());

                if (!empty($mFields) && $mFields === 'CARD') {

                    $bShowCardFields = true;

                } elseif (!empty($mFields)) {

                    ?>
                    <div class="hidden js-invoice-panel-payment-details" data-driver="<?=$oDriver->getSlug()?>">
                        <h5>Payment Details</h5>
                        <?php

                        foreach ($mFields as $aField) {

                            $sKey         = $sDriverKey . '[' . $aField['key'] . ']';
                            $sDefault     = !empty($aField['default']) ? $aField['default'] : '';
                            $sLabel       = !empty($aField['label']) ? $aField['label'] : '';
                            $sType        = !empty($aField['type']) ? $aField['type'] : 'text';
                            $sPlaceholder = !empty($aField['placeholder']) ? $aField['placeholder'] : '';
                            $sRequired    = !empty($aField['required']) ? 'true' : 'false';
                            $aOptions     = !empty($aField['options']) ? $aField['options'] : [];
                            $sErrorClass  = form_error($sKey) ? 'has-error' : '';

                            $sId   = empty($aField['id']) ? 'input-' . $sKey : $aField['id'];
                            $aAttr = [
                                'id="' . $sId . '"',
                                'placeholder="' . $sPlaceholder . '" ',
                                'data-is-required="' . $sRequired . '"',
                            ];

                            ?>
                            <div class="form__group <?=form_error($sKey) ? 'has-error' : ''?>">
                                <label for="<?=$sId?>"><?=$sLabel?></label>
                                <?php

                                switch ($sType) {

                                    case 'dropdown':
                                    case 'select':

                                        echo form_dropdown(
                                            $sKey,
                                            $aOptions,
                                            set_value($sKey),
                                            implode(' ', $aAttr)
                                        );
                                        break;

                                    case 'password':
                                        echo form_password(
                                            $sKey,
                                            null,
                                            implode(' ', $aAttr)
                                        );
                                        break;

                                    case 'text':
                                    default:
                                        echo form_input(
                                            $sKey,
                                            set_value($sKey),
                                            implode(' ', $aAttr)
                                        );
                                        break;
                                }
                                ?>
                                <?=form_error($sKey, '<p class="form__error">', '</p>')?>
                            </div>
                            <?php
                        }

                        ?>
                    </div>
                    <?php
                }
            }

            if ($bShowCardFields) {
                ?>
                <div class="hidden js-invoice-panel-payment-details" id="js-invoice-panel-payment-details-card">
                    <h5>Payment Details</h5>
                    <?php

                    $sFieldKey         = 'card_name';
                    $sFieldLabel       = 'Cardholder Name';
                    $sFieldPlaceholder = '';
                    $sFieldDefault     = activeUser('first_name, last_name');
                    $sFieldAttr        = implode(' ', [
                        'class="js-invoice-cc-name"',
                        'id="input-' . $sFieldKey . '"',
                        'placeholder="' . $sFieldPlaceholder . '"',
                        'autocomplete="on"',
                        'data-is-required="true"',
                    ]);

                    ?>
                    <div class="form__group <?=form_error($sFieldKey) ? 'has-error' : ''?>">
                        <label for="input-<?=$sFieldKey?>"><?=$sFieldLabel?></label>
                        <?=form_text($sFieldKey, set_value($sFieldKey, $sFieldDefault), $sFieldAttr)?>
                        <?=form_error($sFieldKey, '<p class="form__error">', '</p>')?>
                    </div>
                    <?php

                    $sFieldKey         = 'card_number';
                    $sFieldLabel       = 'Card Number';
                    $sFieldPlaceholder = '•••• •••• •••• ••••';
                    $sFieldDefault     = '';
                    $sFieldAttr        = implode(' ', [
                        'class="js-invoice-cc-num"',
                        'id="input-' . $sFieldKey . '"',
                        'placeholder="' . $sFieldPlaceholder . '"',
                        'autocomplete="on"',
                        'data-is-required="true"',
                        'data-cc-num="true"',
                    ]);

                    ?>
                    <div class="form__group <?=form_error($sFieldKey) ? 'has-error' : ''?>">
                        <label for="input-<?=$sFieldKey?>"><?=$sFieldLabel?></label>
                        <?=form_tel($sFieldKey, set_value($sFieldKey, $sFieldDefault), $sFieldAttr)?>
                        <?=form_error($sFieldKey, '<p class="form__error">', '</p>')?>
                    </div>
                    <div class="form__row">
                        <?php

                        $sFieldKey         = 'card_expire';
                        $sFieldLabel       = 'Expiry';
                        $sFieldPlaceholder = '•• / ••';
                        $sFieldDefault     = '';
                        $sFieldAttr        = implode(' ', [
                            'class="js-invoice-cc-exp"',
                            'id="input-' . $sFieldKey . '"',
                            'placeholder="' . $sFieldPlaceholder . '"',
                            'autocomplete="on"',
                            'data-is-required="true"',
                            'data-cc-exp="true"',
                        ]);

                        ?>
                        <div class="form__group form__group--half <?=form_error($sFieldKey) ? 'has-error' : ''?>">
                            <label for="input-<?=$sFieldKey?>"><?=$sFieldLabel?></label>
                            <?=form_tel($sFieldKey, set_value($sFieldKey, $sFieldDefault), $sFieldAttr)?>
                            <?=form_error($sFieldKey, '<p class="form__error">', '</p>')?>
                        </div>
                        <?php

                        $sFieldKey         = 'card_cvc';
                        $sFieldLabel       = 'CVC';
                        $sFieldPlaceholder = '•••';
                        $sFieldDefault     = '';
                        $sFieldAttr        = implode(' ', [
                            'class="js-invoice-cc-cvc"',
                            'id="input-' . $sFieldKey . '"',
                            'placeholder="' . $sFieldPlaceholder . '"',
                            'autocomplete="on"',
                            'data-is-required="true"',
                            'data-cc-cvc="true"',
                        ]);

                        ?>
                        <div class="form__group form__group--half <?=form_error($sFieldKey) ? 'has-error' : ''?>">
                            <label for="input-<?=$sFieldKey?>"><?=$sFieldLabel?></label>
                            <?=form_tel($sFieldKey, set_value($sFieldKey, $sFieldDefault), $sFieldAttr)?>
                            <?=form_error($sFieldKey, '<p class="form__error">', '</p>')?>
                        </div>
                    </div>
                </div>
                <?php
            }

            ?>
            <hr>
            <p>
                <button type="submit" class="btn btn--block btn--primary btn--disabled" id="js-invoice-pay-now">
                    Choose a Payment Method
                </button>
            </p>
            <p class="text-center">
                <a href="<?=$sUrlCancel?>" class="btn btn--link">
                    Cancel payment
                </a>
            </p>

        </div>
        <?=form_close()?>
    </div>
</div>
