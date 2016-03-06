<div class="group-invoice customer edit">
    <?=form_open()?>
    <fieldset>
        <legend>
            Customer Details
        </legend>
        <?php

        echo form_field(
            array(
                'key'     => 'first_name',
                'label'   => 'First Name',
                'default' => !empty($item->first_name) ? $item->first_name : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'last_name',
                'label'   => 'Surname',
                'default' => !empty($item->last_name) ? $item->last_name : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'organisation',
                'label'   => 'Organisation',
                'default' => !empty($item->organisation) ? $item->organisation : ''
            )
        );

        echo form_field_email(
            array(
                'key'     => 'email',
                'label'   => 'Email',
                'default' => !empty($item->email) ? $item->email : ''
            )
        );

        echo form_field_email(
            array(
                'key'     => 'billing_email',
                'label'   => 'Billing Email',
                'default' => !empty($item->billing_email) ? $item->billing_email : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'telephone',
                'label'   => 'Telephone',
                'default' => !empty($item->telephone) ? $item->telephone : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'vat_number',
                'label'   => 'VAT Number',
                'default' => !empty($item->vat_number) ? $item->vat_number : ''
            )
        );

        ?>
    </fieldset>
    <fieldset>
        <legend>
            Billing Address
        </legend>
        <?php

        echo form_field(
            array(
                'key'     => 'billing_address_line_1',
                'label'   => 'Line 1',
                'default' => !empty($item->billing_address->line_1) ? $item->billing_address->line_1 : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'billing_address_line_2',
                'label'   => 'Line 2',
                'default' => !empty($item->billing_address->line_2) ? $item->billing_address->line_2 : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'billing_address_town',
                'label'   => 'Town',
                'default' => !empty($item->billing_address->town) ? $item->billing_address->town : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'billing_address_county',
                'label'   => 'County',
                'default' => !empty($item->billing_address->county) ? $item->billing_address->county : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'billing_address_postcode',
                'label'   => 'Postcode',
                'default' => !empty($item->billing_address->postcode) ? $item->billing_address->postcode : ''
            )
        );

        echo form_field(
            array(
                'key'     => 'billing_address_country',
                'label'   => 'Country',
                'default' => !empty($item->billing_address->country) ? $item->billing_address->country : ''
            )
        );

        ?>
    </fieldset>
    <p>
        <button type="submit" class="btn btn-primary">
            <?=lang('action_save')?>
        </button>
    </p>
    <?=form_close()?>
</div>