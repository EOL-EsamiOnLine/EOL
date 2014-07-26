<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 5/2/13
 * Time: 12:06 PM
 * Desc: Student's Homepage
 */

global $user;
?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">
    <div>
        <?php openBox(ttSubjects, 'left', 'subjectList');

        $db = new sqlDB();

        if($db->qSubjects(null, $user->role)){
            echo '<div class="list"><ul>';
            while($subject = $db->nextRowAssoc()){
                echo '<li><a class="showSubjectInfoAndExams" value="'.$subject['idSubject'].'" onclick="showSubjectInfoAndExams(this);">'.$subject['name'].'</a></li>';
            }
            echo '</ul></div>';
        }else{
            die($db->getError());
        }
        closeBox();

        openBox(ttInfo, 'right', 'subjectInfoAndExams'); ?>
        <form class="infoEdit" onsubmit="return false;"></form>

        <form method="post" id="idExamForm">
            <input type="hidden" id="idExam" name="idExam" value="">
        </form>

        <?php closeBox(); ?>

        <div class="clearer"></div>
    </div>
</div>