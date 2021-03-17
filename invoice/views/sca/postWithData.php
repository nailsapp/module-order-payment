<?php
/**
 * @var string $sFormUrl
 * @var array  $aFormData
 */
?>
<form id="form" method="POST" action="<?=$sFormUrl?>">
    <?php
    foreach ($aFormData as $sKey => $sValue) {
        ?>
        <input type="hidden" name="<?=$sKey?>" value="<?=$sValue?>" />
        <?php
    }
    ?>
</form>
<script>
window.onload = function() {
    document.getElementById('form').submit();
}
</script>
