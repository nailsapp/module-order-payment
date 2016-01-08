<?php

//  Get any additional libraries we'll need
$oInput = nailsFactory('service', 'Input');

?>
<div class="group-invoice settings">
    <?php

        echo form_open();
        $sActiveTab = $this->input->post('active_tab') ?: 'tab-misc';
        echo '<input type="hidden" name="active_tab" value="' . $sActiveTab . '" id="active-tab">';

    ?>
    <ul class="tabs" data-active-tab-input="#active-tab">
        <?php

        if (userHasPermission('admin:invoice:settings:misc')) {

            ?>
            <li class="tab">
                <a href="#" data-tab="tab-misc">Miscellaneous</a>
            </li>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:currency')) {

            ?>
            <li class="tab">
                <a href="#" data-tab="tab-currency">Currency</a>
            </li>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:drivers')) {

            ?>
            <li class="tab">
                <a href="#" data-tab="tab-drivers">Drivers</a>
            </li>
            <?php
        }

        ?>
    </ul>
    <section class="tabs">
        <?php

        if (userHasPermission('admin:invoice:settings:misc')) {

            ?>
            <div class="tab-page tab-misc">
                <p class="alert alert-warning">
                    <strong>@todo:</strong> Any misc settings.
                </p>
            </div>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:currency')) {

            ?>
            <div class="tab-page tab-currency">
                <p class="alert alert-warning">
                    <strong>@todo:</strong> Currency settings
                </p>
            </div>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:drivers')) {

            ?>
            <div class="tab-page tab-drivers">
                <?=adminHelper(
                    'loadSettingsDriverTable',
                    'enabled_payment_drivers',
                    $payment_drivers,
                    $payment_drivers_enabled
                )?>
            </div>
            <?php
        }

    ?>
    </section>
    <p>
        <?=form_submit('submit', lang('action_save_changes'), 'class="btn btn-primary"')?>
    </p>
    <?=form_close()?>
</div>
