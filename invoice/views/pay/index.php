<div class="nailsapp-invoice pay">
    <?=form_open(null, 'id="js-main-form"')?>
    <div class="row">
        <div class="col-md-6">
            <div class="panel-group">
                <?php

                if (count($aDrivers) > 1) {

                    ?>
                    <div class="panel panel-default js-section" id="js-panel-payment-method">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                1. Choose Payment Method
                            </h4>
                        </div>
                        <div class="panel-collapse">
                            <div class="panel-body">
                                <ul class="list-group">
                                    <?php

                                    foreach ($aDrivers as $oDriver) {

                                        ?>
                                        <li class="list-group-item">
                                            <label class="driver-select">
                                                <?=form_radio('driver', $oDriver->slug)?>
                                                <?=$oDriver->name?>
                                            </label>
                                        </li>
                                        <?php
                                    }

                                    ?>
                                </ul>
                                <hr />
                                <a href="<?=$sUrlCancel?>" class="btn btn-danger">
                                    Cancel
                                </a>
                                <button type="button" class="btn btn-success pull-right js-goto-section" data-section="payment-details">
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php

                } else {

                    echo form_hidden('driver', $aDrivers[0]->slug);
                }

                ?>
                <div class="panel panel-default js-section" id="js-panel-payment-details">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <?=count($aDrivers) > 1 ? '2.' : ''?>
                            Card details
                        </h4>
                    </div>
                    <div class="panel-collapse <?=count($aDrivers) > 1 ? 'collapse' : ''?>">
                        <div class="panel-body">
                            <?php

                            if (!empty($aCards)) {

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
                                                <li class="list-group-item">
                                                    <label class="card-select js-card-select">
                                                        <?=form_radio('cc_saved', 'NEW', set_radio('cc_saved', 'NEW'))?>
                                                        Add New Card
                                                    </label>
                                                </li>
                                            </ul>
                                            <?=form_error('cc_saved', '<p class="alert alert-danger">', '</p>')?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }

                            ?>
                            <div id="js-add-card" class="<?=!empty($aCards) ? 'hidden' : ''?>">
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
                                        <div class="row">
                                            <div class="col-xs-12">
                                                <label>
                                                    <?=form_checkbox('cc_save', true, set_radio('cc_save', true, true))?>
                                                    Remember Card Details
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <?php

                            if (count($aDrivers) > 1) {

                                ?>
                                <button type="button" class="btn btn-danger js-goto-section" data-section="payment-method">
                                    Back
                                </button>
                                <?php

                            } else {

                                ?>
                                <a href="<?=$sUrlCancel?>" class="btn btn-danger">
                                    Cancel
                                </a>
                                <?php

                            }

                            ?>
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
        <div class="col-md-6 hidden-xs hidden-sm">
            <iframe src="<?=$oInvoice->urls->view?>?autosize=1" id="js-view-invoice" frameborder="0" width="100%" scrolling="no"></iframe>
        </div>
    </div>
    <?=form_close()?>
</div>