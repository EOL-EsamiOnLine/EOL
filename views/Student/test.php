<?php
/**
 * File: test.php
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test page
 */

global $user, $log;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="countdown"></div>
<div id="main">

    <?php
    $idSet = $_SESSION['idSet'];
    $db = new sqlDB();
    if(($db->qTestDetails($idSet)) && ($testInfo = $db->nextRowAssoc())){
        $lang = $testInfo['fkLanguage'];
        $subject = $testInfo['fkSubject'];
        $questionsNum = $testInfo['questions'];
        $timeStart = $testInfo['timeStart'];
        $timeEnd = $testInfo['timeEnd'];
        $now = date("Y-m-d H:i:s");
        $remaining = $duration = $testInfo['duration'] * 60;  // Minutes to seconds conversion

        switch($testInfo['status']){
            case 'e' :
            case 'a' : // This test has been already submitted, so don't load questions and exit
                       die(ttETestAlreadySubmitted);
            case 'b' : // This test has been blocked, so don't load questions and exit
                       die(ttBlocked); break;
            case 'w' : // Opening test for the first time, so set timeStart, status and load questions
                       if(!($db->qStartTest($testInfo['idTest'], $now))){ die(ttEDatabase); } break;
            case 's' : // This test was already opened (status = s), check remaining time
                       $timeStart = strtotime($testInfo['timeStart']);
                       $now = strtotime($now);
                       $used = $now - $timeStart;
                       if($used > $duration)      // Time exipred, exit
                           die(ttETimeExpired);
                       else                       // There is a remaining time, load questions
                           $remaining = $duration - $used;
        }

        if(($db->qQuestionSet($idSet, $lang, $subject)) && ($questions = $db->getResultAssoc())){
            if(count($questions) != $questionsNum){
                die(ttEQuestionNotFound);
            }
            shuffle($questions);
            openBox(ttTest, 'normal', 'test');
            foreach($questions as $question){
                $idQuestion = $question['idQuestion'];
                $answered = json_decode(stripslashes($question['answer']), true);
                if($answered == '')
                    $answered = array('');

                $questionAnswers = '';
                switch($question['type']){

                    case 'MC' : if(($db->qAnswerSet($idQuestion, $lang, $subject)) && ($answers = $db->getResultAssoc())){
                                    shuffle($answers);
                                    foreach($answers as $answer){
                                        $checked = (in_array($answer['idAnswer'], $answered)) ? 'checked' : '';
                                        $questionAnswers .= '
                                                <div>
                                                    <input class="hidden" type="radio" name="'.$idQuestion.'" value="'.$answer['idAnswer'].'" '.$checked.'/>
                                                    <span value="'.$answer['idAnswer'].'"></span>
                                                    <label>'.$answer['translation'].'</label>
                                                </div>';
                                    }
                                } break;
                    case 'MR' : if(($db->qAnswerSet($idQuestion, $lang, $subject)) && ($answers = $db->getResultAssoc())){
                                    shuffle($answers);
                                    foreach($answers as $answer){
                                        $checked = (in_array($answer['idAnswer'], $answered)) ? 'checked' : '';
                                        $questionAnswers .= '
                                                <div>
                                                    <input class="hidden" type="checkbox" name="'.$idQuestion.'" value="'.$answer['idAnswer'].'" '.$checked.'/>
                                                    <span value="'.$answer['idAnswer'].'"></span>
                                                    <label>'.$answer['translation'].'</label>
                                                </div>';
                                    }
                                } break;
                    case 'OP' : $questionAnswers .= '<textarea class="textareaTest">'.$answered[0].'</textarea>'; break;
                    default: die(ttEQuestionType);

                }

                ?>
                <div class="questionTest" value="<?= $idQuestion ?>" type="<?= $question['type'] ?>">
                    <div class="questionText"><?= $question['translation'] ?></div>
                    <div class="questionAnswers"><?= $questionAnswers ?></div>
                </div>
            <?php
            }
            ?>
                <a class="ok button right" id="submitTest" onclick="submitTest(new Array(true));"><?= ttSubmit ?></a>
                <div class="clearer"></div>
            <?php closeBox(); ?>
                <div class="clearer"></div>
        <?php
        }else{
            die(ttEDatabase);
        }
    }else{
        die(ttEDatabase.' / '.ttETestNotFound);
    }
    ?>

</div>

<script type="application/javascript">
    var countdown = new Countdown({
        time	    : <?= $remaining ?>,
        width		: 210,
        height		: 50,
        inline		: true,
        target		: "countdown",
        style 		: "flip",
        rangeHi		: "hour",
        rangeLo		: "second",
        padding 	: 0.4,
        onComplete	: countdownComplete,
        labels		: 	{
            font 	: "Arial",
            color	: "#ffffff",
            weight	: "normal"
        }
    });
</script>