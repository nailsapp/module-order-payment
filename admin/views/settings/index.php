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

        if (userHasPermission('admin:invoice:settings:invoiceskin')) {

            ?>
            <li class="tab">
                <a href="#" data-tab="tab-invoice-skin">Invoice Skin</a>
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
                <fieldset>
                    <legend>Saved Cards</legend>
                    <?php

                        $field            = array();
                        $field['key']     = 'saved_cards_enabled';
                        $field['label']   = 'Enabled';
                        $field['info']    = 'Allow users to save their cards for future use.';
                        $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

                        echo form_field_boolean($field);

                    ?>
                </fieldset>
                <fieldset>
                    <legend>Saved Addresses</legend>
                    <?php

                        $field            = array();
                        $field['key']     = 'saved_addresses_enabled';
                        $field['label']   = 'Enabled';
                        $field['info']    = 'Allow users to save their billing addresses for future use.';
                        $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

                        echo form_field_boolean($field);

                    ?>
                </fieldset>
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
                    'PaymentDriver',
                    'nailsapp/module-invoice'
                )?>
            </div>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:invoiceskin')) {

            ?>
            <div class="tab-page tab-invoice-skin">
                <?=adminHelper(
                    'loadSettingsDriverTable',
                    'InvoiceSkin',
                    'nailsapp/module-invoice'
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
