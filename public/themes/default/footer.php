<?php
/**
 * File: footer.php
 * User: Masterplan
 * Date: 3/16/13
 * Time: 11:39 AM
 * Desc: Footer of all pages
 */
global $config;
?>

<div id="footer">
    <p><?= ttBasedOn ?> <a href="https://github.com/EOL-EsamiOnLine/EOL" class="bold" target="_blank">EOL - Esami On Line</a> (v <?= $config['systemVersion'] ?>)</p>
</div>
</div>
<div id="dialogError"><p></p></div>
<div id="dialogConfirm"><p></p></div>
<div id="modalSuccess" class="hidden"><p></p></div>
<div id="modalError" class="hidden">
    <p></p>
    <span class="lbmClose" title="<?= ttClose ?>"></span>
</div>

</body>
</html>
