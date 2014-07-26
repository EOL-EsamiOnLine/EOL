<?php
/**
 * File: newstudent.php
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
        <?php openBox(ttNewStudent, 'small', 'register') ?>
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

            <?php if($user->role == '?'){ ?>
                <label class="b2Space"><?= ttPassword ?> : </label>
                <input class="writable" type="password" id="userPassword" size="75%" value="">
                <div class="clearer"></div>

                <label class="b2Space"><?= ttConfirmPassword ?> : </label>
                <input class="writable" type="password" id="userPassword2" size="75%" value="">
                <div class="clearer b2Space"></div>
            <?php } ?>

            <div>
                <a class="normal button" id="create" onclick="createStudent();"><?= ttCreate ?></a>
            </div>
        </form>
        <?php closeBox() ?>
        <div class="clearer"></div>
    </div>
</div>