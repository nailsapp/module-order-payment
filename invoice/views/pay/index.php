<div class="nailsapp-invoice pay container" id="js-invoice">
    <?=form_open(null, 'id="js-invoice-main-form"')?>
    <div class="mask" id="js-invoice-mask">
        <b class="glyphicon glyphicon-refresh"></b>
    </div>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h2 class="text-center">
                Invoice <?=$oInvoice->ref?>
            </h2>
            <hr>
            <?php

            if (!empty($error)) {

                ?>
                <p class="alert alert-danger">
                    <?=$error?>
                </p>
                <?php
            }

            ?>
        </div>
    </div>
    <div class="row shakeable">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        Choose Payment Method
                    </h4>
                </div>
                <div class="panel-body">
                    <ul class="list-group" id="js-invoice-driver-select">
                        <?php

                        foreach ($aDrivers as $oDriver) {

                            $sHasFields  = json_encode(!empty($oDriver->getPaymentFields()));
                            $sIsCard     = json_encode($oDriver->getPaymentFields() === 'CARD');
                            $sIsRedirect = json_encode($oDriver->isRedirect());

                            $aData = array(
                                'data-driver="' . $oDriver->getSlug() . '"',
                                'data-has-fields="' . $sHasFields . '"',
                                'data-is-card="' . $sIsCard . '"',
                                'data-is-redirect="' . $sIsRedirect . '"'
                            );

                            ?>
                            <li class="list-group-item">
                                <label class="js-invoice-driver-select">
                                    <?php

                                    echo form_radio(
                                        'driver',
                                        $oDriver->getSlug(),
                                        set_radio('driver', $oDriver->getSlug()),
                                        implode(' ', $aData)
                                    );

                                    echo $oDriver->getLabel();

                                    $sLogoUrl = $oDriver->getLogoUrl(400, 20);
                                    if (!empty($sLogoUrl)) {
                                        echo img(
                                            array(
                                                'src'   => $sLogoUrl,
                                                'class' => 'pull-right'
                                            )
                                        );
                                    }

                                    ?>
                                </label>
                            </li>
                            <?php
                        }

                        ?>
                    </ul>
                    <?=form_error('driver') ? '<p class="alert alert-danger">Please select an option.</p>' : ''?>
                </div>
            </div>
            <?php

            $bShowCardFields = false;
            foreach ($aDrivers as $oDriver) {

                $mFields    = $oDriver->getPaymentFields();
                $sDriverKey = $oDriver->getSlug();

                if (!empty($mFields) && $mFields === 'CARD') {

                    $bShowCardFields = true;

                } elseif (!empty($mFields)) {

                    ?>
                    <div class="panel panel-default hidden js-invoice-panel-payment-details" data-driver="<?=$oDriver->getSlug()?>">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                Payment Details
                            </h4>
                        </div>
                        <div class="panel-body">
                            <?php

                            foreach ($mFields as $aField) {

                                ?>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="form-group">
                                            <?php

                                            $sKey         = $sDriverKey . '[' . $aField['key'] . ']';
                                            $sDefault     = !empty($aField['default']) ? $aField['default'] : '';
                                            $sLabel       = !empty($aField['label']) ? $aField['label'] : '';
                                            $sType        = !empty($aField['type']) ? $aField['type'] : 'text';
                                            $sPlaceholder = !empty($aField['placeholder']) ? $aField['placeholder'] : '';
                                            $sRequired    = !empty($aField['required']) ? 'true' : 'false';
                                            $sErrorClass  = form_error($sKey) ? 'has-error' : '';

                                            echo '<label>';
                                            echo $sLabel ;

                                            switch ($sType) {

                                                case 'password':
                                                    echo form_password(
                                                        $sKey,
                                                        null,
                                                        'class="form-control" ' .
                                                        'placeholder="' . $sPlaceholder . '" ' .
                                                        'data-is-required="' . $sRequired . '"'
                                                    );
                                                    break;

                                                case 'text':
                                                default:
                                                    echo form_input(
                                                        $sKey,
                                                        set_value($sKey),
                                                        'class="form-control ' . $sErrorClass . '" ' .
                                                        'placeholder="' . $sPlaceholder . '" ' .
                                                        'data-is-required="' . $sRequired . '"'
                                                    );
                                                    break;
                                            }

                                            echo form_error($sKey, '<p class="alert alert-danger">', '</p>');

                                            echo '</label>';

                                            ?>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                                <?php
                            }

                            ?>
                        </div>
                    </div>
                    <?php
                }
            }

            if ($bShowCardFields) {

                ?>
                <div class="panel panel-default hidden js-invoice-panel-payment-details" id="js-invoice-panel-payment-details-card">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Payment Details
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <label>
                                        <?php

                                        $sKey         = 'cc[name]';
                                        $sDefault     = activeUser('first_name, last_name');
                                        $sLabel       = 'Cardholder Name';
                                        $sPlaceholder = '';
                                        $sErrorClass  = form_error($sKey) ? 'has-error' : '';

                                        echo $sLabel;
                                        echo form_input(
                                            $sKey,
                                            set_value($sKey, $sDefault),
                                            'class="form-control js-invoice-cc-name ' . $sErrorClass . '" ' .
                                            'placeholder="' . $sPlaceholder . ' "' .
                                            'data-is-required="true" ' .
                                            'autocomplete="on"'
                                        );

                                        echo form_error($sKey, '<p class="alert alert-danger">', '</p>');

                                        ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="form-group">
                                    <label>
                                        <?php

                                        $sKey         = 'cc[num]';
                                        $sDefault     = '';
                                        $sLabel       = 'Card Number';
                                        $sPlaceholder = '•••• •••• •••• ••••';
                                        $sErrorClass  = form_error($sKey) ? 'has-error' : '';

                                        echo $sLabel;
                                        echo form_tel(
                                            $sKey,
                                            set_value($sKey, $sDefault),
                                            'class="form-control js-invoice-cc-num ' . $sErrorClass . '" ' .
                                            'placeholder="' . $sPlaceholder . '" ' .
                                            'data-cc-num="true" ' .
                                            'autocomplete="on"'
                                        );

                                        echo form_error($sKey, '<p class="alert alert-danger">', '</p>');

                                        ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-3">
                                <div class="form-group">
                                    <label>
                                        <?php

                                        $sKey         = 'cc[exp]';
                                        $sDefault     = '';
                                        $sLabel       = 'Expiry';
                                        $sPlaceholder = '•• / ••';

                                        echo $sLabel;
                                        echo form_tel(
                                            $sKey,
                                            set_value($sKey, $sDefault),
                                            'class="form-control js-invoice-cc-exp ' . $sErrorClass . '" ' .
                                            'placeholder="' . $sPlaceholder . '" ' .
                                            'data-cc-exp="true" ' .
                                            'autocomplete="on"'
                                        );

                                        echo form_error($sKey, '<p class="alert alert-danger">', '</p>');

                                        ?>
                                    </label>
                                </div>
                            </div>
                            <div class="col-xs-6 col-sm-3">
                                <div class="form-group">
                                    <label>
                                        <?php

                                        $sKey         = 'cc[cvc]';
                                        $sDefault     = '';
                                        $sLabel       = 'CVC';
                                        $sPlaceholder = '•••';

                                        echo $sLabel;
                                        echo form_tel(
                                            $sKey,
                                            set_value($sKey, $sDefault),
                                            'class="form-control js-invoice-cc-cvc ' . $sErrorClass . '" ' .
                                            'placeholder="' . $sPlaceholder . '" ' .
                                            'data-cc-cvc="true" ' .
                                            'autocomplete="off"'
                                        );

                                        echo form_error($sKey, '<p class="alert alert-danger">', '</p>');

                                        ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }

            ?>
            <p>
                <button type="submit" class="btn btn-warning btn-lg btn-block disabled" id="js-invoice-pay-now">
                    Choose a Payment Method
                </button>
            </p>
            <p class="text-center small cancel-payment">
                <?=anchor($sUrlCancel, 'Cancel Payment')?>
            </p>
        </div>
    </div>
    <?=form_close()?>
</div>