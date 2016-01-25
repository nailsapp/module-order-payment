<?php

$paths   = array();

$paths[] = array(
    FCPATH . 'assets/img/logo.png',
    BASE_URL . 'assets/img/logo.png'
);

$paths[] = array(
    FCPATH . 'assets/img/logo.jpg',
    BASE_URL . 'assets/img/logo.jpg'
);

$paths[] = array(
    FCPATH . 'assets/img/logo.gif',
    BASE_URL . 'assets/img/logo.gif'
);

$paths[] = array(
    FCPATH . 'assets/img/logo/logo.png',
    BASE_URL . 'assets/img/logo/logo.png'
);

$paths[] = array(
    FCPATH . 'assets/img/logo/logo.jpg',
    BASE_URL . 'assets/img/logo/logo.jpg'
);

$paths[] = array(
    FCPATH . 'assets/img/logo/logo.gif',
    BASE_URL . 'assets/img/logo/logo.gif'
);

if (NAILS_BRANDING) {

    $paths[] = array(
        NAILS_ASSETS_PATH . 'img/nails/icon/icon@2x.png',
        NAILS_ASSETS_URL . 'img/nails/icon/icon@2x.png'
    );
}


foreach ($paths as $path) {

    if (is_file($path[0])) {

        ?>
        <div class="row">
            <div class="col-xs-2 col-xs-offset-5">
                <h1>
                    <img src="<?=$path[1]?>" class="img-responsive"/>
                </h1>
            </div>
        </div>
        <?php
        break;
    }
}
