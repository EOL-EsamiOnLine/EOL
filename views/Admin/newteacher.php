<?php
/**
 * File: newteacher.php
 * User: Masterplan
 * Date: 6/7/13
 * Time: 12:06 PM
 * Desc: Shows form for add new user
 */

global $user, $tt;

?>
<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div class="clearer">
        <?php openBox(ttNewTeacher.'/'.ttAdministrator, 'small', 'register') ?>
        <form class="infoEdit" onsubmit="return false;">

            <label class="b2Space"><?= ttName ?> : </label>
            <input class="writable" type="text" id="userName" size="75%" value="">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttSurname ?> : </label>
            <input class="writable" type="text" id="userSurname" size="75%" value="">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttEmail ?> : </label>
            <input class="writable" type="text" id="userEmail" size="75%" value="">
            <div class="clearer"></div>

            <label class="b2Space"><?= ttConfirmEmail ?> : </label>
            <input class="writable" type="text" id="userEmail2" size="75%" value="">
            <div class="clearer"></div>

            <label><?= ttRole ?> : </label>
            <span class="left">
                <input type="radio" name="userRole" value="a"> <?= ttAdministrator ?> <br/>
                <input type="radio" name="userRole" value="e" checked> <?= tteTeacher ?></br>
                <input type="radio" name="userRole" value="t" > <?= ttTeacher ?>
                <input type="checkbox" value="a" id="administratorRole"> <?= ttAdministrator ?> <br/>
            </span>
            <div class="clearer b2Space"></div>

            <div>
                <a class="normal button" id="create" onclick="createTeacher();"><?= ttCreate ?></a>
            </div>
        </form>
        <?php closeBox() ?>
        <div class="clearer"></div>
    </div>
</div>