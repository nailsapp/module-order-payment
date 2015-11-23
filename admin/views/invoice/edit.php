<div class="group-invoice invoice edit">
    <?php if (!empty($invoice)) { dump($invoice); } ?>
    <?=form_open()?>
    <fieldset>
        <legend>Details</legend>
        <?php

        $aField = array(
            'key'     => 'ref',
            'label'   => 'Reference',
            'default' => !empty($invoice->ref) ? $invoice->ref : '',
            'info'    => '<a href="#" class="btn btn-default btn-xs generate-ref" data-bind="click: generateRef">' .
                         'Generate <b class="fa fa-spin fa-spinner"></b>' .
                         '</a>',
            'id'      => 'invoice-ref'
        );
        echo form_field($aField);

        // --------------------------------------------------------------------------

        $aField = array(
            'key'     => 'state',
            'label'   => 'State',
            'default' => !empty($invoice->state) ? $invoice->state : '',
            'class'   => 'select2'
        );
        echo form_field_dropdown($aField, $invoiceStates);

        // --------------------------------------------------------------------------

        $aField = array(
            'key'     => 'dated',
            'label'   => 'Dated',
            'default' => !empty($invoice->user_id) ? $invoice->user_id : date('Y-m-d')
        );
        echo form_field_date($aField);

        // --------------------------------------------------------------------------

        $aField = array(
            'key'     => 'terms',
            'label'   => 'Payment Terms',
            'default' => !empty($invoice->terms) ? $invoice->terms : 0,
            'info'    => 'Set to zero to display "Due on Receipt" on the invoice'
        );
        echo form_field_number($aField);

        // --------------------------------------------------------------------------

        $aField = array(
            'key'     => 'user_id',
            'label'   => 'User',
            'default' => !empty($invoice->ref) ? $invoice->ref : '',
            'class'   => 'user-search'
        );
        echo form_field($aField);

        // --------------------------------------------------------------------------

        $aField = array(
            'key'     => 'user_email',
            'label'   => 'User Email',
            'default' => !empty($invoice->user_email) ? $invoice->user_email : '',
            'info'    => '<span class="alert alert-info">If a user is selected above, setting this field will ' .
                         'override the email address to which this invoice is sent.</span>'
        );
        echo form_field_email($aField);

        // --------------------------------------------------------------------------

        $aField = array(
            'key'     => 'additional_text',
            'label'   => 'Additional Text',
            'default' => !empty($invoice->additional_text) ? $invoice->additional_text : ''
        );
        echo form_field_textarea($aField);

        ?>
    </fieldset>
    <fieldset>
        <legend>Line Items</legend>
        <div clas="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="quantity text-center">Quantity</th>
                        <th class="unit text-center">Unit</th>
                        <th>Details</th>
                        <th class="price text-center">Unit Price</th>
                        <th class="tax text-center">Tax</th>
                        <th class="actions"></th>
                    </tr>
                </thead>
                <tbody data-bind="foreach: items">
                    <tr>
                        <td class="quantity text-center">
                            <input type="hidden" data-bind="attr: {name: 'items[' + $index() + '][id]', value: id}" />
                            <input type="text" data-bind="attr: {name: 'items[' + $index() + '][quantity]', value: quantity}" />
                        </td>
                        <td class="unit">
                            <select data-bind="
                                attr: {name: 'items[' + $index() + '][unit]'},
                                options: $root.units,
                                optionsText: 'label',
                                optionsValue: 'slug',
                                value: unit"></select>
                        </td>
                        <td>
                            <input type="text" data-bind="attr: {name: 'items[' + $index() + '][label]', value: label}" />
                            <textarea data-bind="attr: {name: 'items[' + $index() + '][body]', value: body}"></textarea>
                        </td>
                        <td class="price text-center">
                            <input type="text" data-bind="attr: {name: 'items[' + $index() + '][price]', value: price}" />
                        </td>
                        <td class="tax">
                            <select data-bind="
                                attr: {name: 'items[' + $index() + '][tax]'},
                                options: $root.taxes,
                                optionsText: 'label',
                                optionsValue: 'id',
                                value: tax"></select>
                        </td>
                        <td class="actions text-center">
                            <!-- ko if: $index() != 0 -->
                            <a href="#" data-bind="click: $root.moveUp">
                                <i class="fa fa-caret-up"></i>
                            </a>
                            <!-- /ko -->
                            <a href="#" data-bind="click: $root.removeItem">
                                <b class="fa fa-times-circle text-danger"></b>
                            </a>
                            <!-- ko if: ($index() + 1) != $root.items().length -->
                            <a href="#" data-bind="click: $root.moveDown">
                                <i class="fa fa-caret-down"></i>
                            </a>
                            <!-- /ko -->
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p>
            <a class="btn btn-block btn-sm btn-success" data-bind="click: addItem">
                <b class="fa fa-plus"></b>
                Add Line Item
            </a>
        </p>
    </fieldset>
    <p>
        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    </p>
    <?=form_close()?>
</div>