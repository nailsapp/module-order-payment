<?php

//  Get any additional libraries we'll need
$oInput = \Nails\Factory::service('Input');

use Nails\Invoice\Constants;

?>
<div class="group-invoice settings">
    <?php

    echo form_open();
    $sActiveTab = $oInput->post('active_tab') ?: 'tab-misc';
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
                    <legend>Business Information</legend>
                    <?php

                    $field            = [];
            $field['key']     = 'business_name';
            $field['label']   = 'Name';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field($field);

            $field            = [];
            $field['key']     = 'business_address';
            $field['label']   = 'Address';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field_textarea($field);

            $field            = [];
            $field['key']     = 'business_phone';
            $field['label']   = 'Telephone';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field($field);

            $field            = [];
            $field['key']     = 'business_email';
            $field['label']   = 'Email';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field($field);

            $field            = [];
            $field['key']     = 'business_vat_number';
            $field['label']   = 'VAT Number';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field($field); ?>
                </fieldset>
                <fieldset>
                    <legend>Defaults</legend>
                    <?php

                    $field            = [];
            $field['key']     = 'default_additional_text';
            $field['label']   = 'Invoice Additional Text';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field_textarea($field);

            $field            = [];
            $field['key']     = 'default_payment_terms';
            $field['label']   = 'Invoice Payment Terms';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field_number($field); ?>
                </fieldset>
                <fieldset>
                    <legend>Saved Cards</legend>
                    <?php

                    $field            = [];
            $field['key']     = 'saved_cards_enabled';
            $field['label']   = 'Enabled';
            $field['info']    = 'Allow users to save their cards for future use.';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field_boolean($field); ?>
                </fieldset>
                <fieldset>
                    <legend>Saved Addresses</legend>
                    <?php

                    $field            = [];
            $field['key']     = 'saved_addresses_enabled';
            $field['label']   = 'Enabled';
            $field['info']    = 'Allow users to save their billing addresses for future use.';
            $field['default'] = isset($settings[$field['key']]) ? $settings[$field['key']] : false;

            echo form_field_boolean($field); ?>
                </fieldset>
            </div>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:drivers')) {
            ?>
            <div class="tab-page tab-drivers">
                <?=adminHelper(
                'loadSettingsDriverTable',
                'PaymentDriver',
                Constants::MODULE_SLUG
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
                Constants::MODULE_SLUG
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
