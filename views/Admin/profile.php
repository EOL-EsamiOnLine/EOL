<?php
/**
 * File: profile.php
 * User: Masterplan
 * Date: 5/30/13
 * Time: 4:13 PM
 * Desc: Shows profile page of user's account
 */

global $user, $log;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div class="clearer"></div>
    <?php openBox(ttProfile, 'small', 'profile') ?>
    <form class="infoEdit" onsubmit="return false;">

        <label class="b2Space"><?= ttName ?> : </label>
        <input class="writable" type="text" id="userName" value="<?= $user->name ?>">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttSurname ?> : </label>
        <input class="writable" type="text" id="userSurname" value="<?= $user->surname ?>">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttEmail ?> : </label>
        <input class="readonly" type="text" id="userEmail" value="<?= $user->email ?>">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttOldPassword ?> : </label>
        <input class="writable" type="password" id="oldPassword" value="">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttNewPassword ?> : </label>
        <input class="writable" type="password" id="newPassword" value="">
        <div class="clearer"></div>

        <label class="b2Space"><?= ttConfirmPassword ?> : </label>
        <input class="writable" type="password" id="newPassword2" value="">
        <div class="clearer"></div>

        <div>
            <a class="normal button" id="saveProfile" onclick="saveProfile();"><?= ttSave ?></a>
        </div>
    </form>
    <?php closeBox() ?>
</div>