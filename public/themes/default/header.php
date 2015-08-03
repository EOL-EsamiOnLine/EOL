<?php
/**
 * File: header.php
 * User: Masterplan
 * Date: 3/16/13
 * Time: 11:38 AM
 * Desc: Header of all pages
 */

global $user, $config;
?>

<div id="header">
    <div id="eolLogo"></div>

    <div id="logoContainer">
        <!--<img src="<?= $config['systemLogo'] ?>">-->
        <div id="systemTitle">EOL - Esami On Line</div>
    </div>

    <div id="welcome">
        <div>
            <a class="w"><?= ttWelcome ?></a><br/>
            <a class="u"><?= "$user->name $user->surname"; ?></a><br/>
            IP:<a class="i"><?= ($_SERVER['REMOTE_ADDR'] == "::1") ? "127.0.0.1" : $_SERVER['REMOTE_ADDR']; ?></a><br/><br/>
            <a class="l" href="index.php?page=login/logout"><?= ttLogout ?></a>
        </div>
    </div>
    <div class="clearer"></div>
</div>