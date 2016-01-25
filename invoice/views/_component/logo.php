<?php

$aPaths = array(
    array(
        FCPATH . 'assets/img/logo.png',
        BASE_URL . 'assets/img/logo.png'
    ),
    array(
        FCPATH . 'assets/img/logo.jpg',
        BASE_URL . 'assets/img/logo.jpg'
    ),
    array(
        FCPATH . 'assets/img/logo.gif',
        BASE_URL . 'assets/img/logo.gif'
    ),
    array(
        FCPATH . 'assets/img/logo/logo.png',
        BASE_URL . 'assets/img/logo/logo.png'
    ),
    array(
        FCPATH . 'assets/img/logo/logo.jpg',
        BASE_URL . 'assets/img/logo/logo.jpg'
    ),
    array(
        FCPATH . 'assets/img/logo/logo.gif',
        BASE_URL . 'assets/img/logo/logo.gif'
    )
);

if (NAILS_BRANDING) {

    $aPaths[] = array(
        NAILS_ASSETS_PATH . 'img/nails/icon/icon@2x.png',
        NAILS_ASSETS_URL . 'img/nails/icon/icon@2x.png'
    );
}


foreach ($aPaths as $aPath) {

    if (is_file($aPath[0])) {

        ?>
        <div class="row">
            <div class="col-xs-2 col-xs-offset-5">
                <h1>
                    <img src="<?=$aPath[1]?>" class="img-responsive"/>
                </h1>
            </div>
        </div>
        <?php
        break;
    }
}
