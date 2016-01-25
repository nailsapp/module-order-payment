<div class="nailsapp-invoice post container">
    <?=$this->load->view('invoice/_component/logo', array(), true)?>
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h3 class="text-center">
                Please wait while we redirect you to our payment provider...
            </h3>
            <form action="<?=$redirectUrl?>" method="POST" id="form">
                <?php

                foreach ($postFields as $sKey => $sValue) {
                    echo '<input type="hidden" name="' . $sKey . '" value="' . $sValue . '" />';
                }

                ?>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    document.getElementById('form').submit();
</script>