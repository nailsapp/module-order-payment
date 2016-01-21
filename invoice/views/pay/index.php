<div class="nailsapp-invoice pay">
    <?=form_open(null, 'id="js-main-form"')?>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2 class="text-center">
                Invoice <?=$oInvoice->ref?>
            </h2>
            <hr>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel-group">
                <div class="panel panel-default js-section" id="js-panel-payment-method">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Choose Payment Method
                        </h4>
                    </div>
                    <div class="panel-collapse">
                        <div class="panel-body">
                            <ul class="list-group">
                                <?php

                                foreach ($aDrivers as $oDriver) {

                                    $sHasFields  = json_encode(!empty($oDriver->paymentFields()));
                                    $sIsRedirect = json_encode($oDriver->isRedirect());

                                    $aData = array(
                                        'data-has-fields="' . $sHasFields . '"',
                                        'data-is-redirect="' . $sIsRedirect . '"'
                                    );

                                    ?>
                                    <li class="list-group-item">
                                        <label class="driver-select">
                                            <?php

                                            echo form_radio('driver', $oDriver->getSlug(), null, implode(' ', $aData));
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
                        </div>
                    </div>
                </div>
                <?php

                foreach ($aDrivers as $oDriver) {

                }

                ?>
                <div class="panel panel-default js-section" id="js-panel-payment-details">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            Payment Details
                        </h4>
                    </div>
                    <div class="panel-collapse">
                        <div class="panel-body">
                            <?php

                            if ($bSavedCardsEnabled && !empty($aCards)) {

                                ?>
                                <div class="row">
                                    <div class="col-xs-12" id="js-saved-cards">
                                        <div class="form-group">
                                            <label>Saved Cards</label>
                                            <ul class="list-group">
                                                <?php

                                                foreach ($aCards as $oCard) {

                                                    ?>
                                                    <li class="list-group-item">
                                                        <label class="card-select js-card-select">
                                                            <?php

                                                            $sDisable = $oCard->isExpired ? 'disabled="disabled"' : '';

                                                            echo form_radio(
                                                                'cc_saved',
                                                                $oCard->id,
                                                                set_radio('cc_saved', $oCard->id),
                                                                $sDisable
                                                            );
                                                            echo $oCard->label_formatted;
                                                            echo $sDisable ? '<span class="text-muted">Expired</span>' : '';

                                                            ?>
                                                            <a href="<?=site_url('invoice/card/delete/' . $oCard->id . '?return=' . urlencode(current_url()))?>" class="text-danger pull-right">
                                                                <b class="glyphicon glyphicon-remove-sign"></b>
                                                            </a>
                                                        </label>
                                                    </li>
                                                    <?php
                                                }

                                                ?>
                                            </ul>
                                            <?=form_error('cc_saved', '<p class="alert alert-danger">', '</p>')?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }

                            ?>
                            <div id="js-add-card">
                                <div class="panel panel-default ">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <div class="form-group">
                                                    <label for="js-cc-name">Cardholder Name</label>
                                                    <input type="text" name="cc_name" class="form-control" id="js-cc-name" placeholder="Name" value="<?=set_value('cc_name', activeUser('first_name, last_name'))?>">
                                                    <?=form_error('cc_name', '<p class="alert alert-danger">', '</p>')?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                                                    <label for="js-cc-num">Card Number</label>
                                                    <input type="tel" name="cc_num" autocomplete="cc-number" class="form-control cc-num" id="js-cc-num" placeholder="•••• •••• •••• ••••" value="<?=set_value('cc_num')?>">
                                                    <?=form_error('cc_num', '<p class="alert alert-danger">', '</p>')?>
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="js-cc-exp">Expiry</label>
                                                    <input type="tel" name="cc_exp" autocomplete="cc-exp" class="form-control" id="js-cc-exp" placeholder="•• / ••" value="<?=set_value('cc_exp')?>">
                                                    <?=form_error('cc_exp', '<p class="alert alert-danger">', '</p>')?>
                                                </div>
                                            </div>
                                            <div class="col-xs-6 col-md-3">
                                                <div class="form-group">
                                                    <label for="js-cc-cvc">CVC</label>
                                                    <input type="tel" name="cc_cvc" autocomplete="off" class="form-control" id="js-cc-cvc" placeholder="•••" value="<?=set_value('cc_cvc')?>">
                                                    <?=form_error('cc_cvc', '<p class="alert alert-danger">', '</p>')?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php

                                        if ($bSavedCardsEnabled) {

                                            ?>
                                            <div class="row">
                                                <div class="col-xs-12">
                                                    <label>
                                                        <?=form_checkbox('cc_save', true, set_radio('cc_save', true, true))?>
                                                        Remember Card Details
                                                    </label>
                                                </div>
                                            </div>
                                            <?php
                                        }

                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <p>
                <button type="submit" class="btn btn-primary btn-lg btn-block" id="js-pay-now">
                    Pay Now
                </button>
            </p>
        </div>
    </div>
    <?=form_close()?>
</div>