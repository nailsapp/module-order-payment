<div class="nailsapp-invoice post u-center-screen">
    <div class="panel">
        <div class="panel__body text-center">
            <p>Please wait while we redirect you to our payment provider...</p>
        </div>
    </div>
</div>
<form action="<?=$redirectUrl?>" method="POST" id="form">
    <?php

    foreach ($postFields as $sKey => $sValue) {
        echo '<input type="hidden" name="' . $sKey . '" value="' . $sValue . '" />';
    }

    ?>
</form>
<script type="text/javascript">
    document.getElementById('form').submit();
</script>
