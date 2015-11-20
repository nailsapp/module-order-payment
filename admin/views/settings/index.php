<?php

//  Get any additional libraries we'll need
$oInput = nailsFactory('service', 'Input');

?>
<div class="group-invoice settings">
    <?php

        echo form_open();
        echo '<input type="hidden" value="' . set_value('activeTab', 'tab-misc') . '" id="active-tab" />';

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

        ?>
    </ul>
    <section class="tabs">
        <?php

        if (userHasPermission('admin:invoice:settings:misc')) {

            ?>
            <div class="tab-page tab-misc">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
            </div>
            <?php
        }

        if (userHasPermission('admin:invoice:settings:drivers')) {

            ?>
            <div class="tab-page tab-drivers">
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                    tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                    quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
                    consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                    cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                    proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                </p>
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
