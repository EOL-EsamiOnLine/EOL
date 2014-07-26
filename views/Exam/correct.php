<?php
/**
 * File: correct.php
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc: Shows test correct page
 */

global $config;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>
<div id="main">

    <?php
    $db = new sqlDB();
    if(($db->qTestDetails(null, $_POST['idTest'])) && ($testInfo = $db->nextRowAssoc())){
        $studentName = $testInfo['name'].' '.$testInfo['surname'];
        $idSet = $testInfo['fkSet'];
        $idSubject = $testInfo['fkSubject'];
        $numQuestions = $testInfo['questions'];
        $scoreTest = $testInfo['scoreTest'];
        $scoreType = $testInfo['scoreType'];
        $scale = $testInfo['scale'];

        if(($db->qQuestionSet($idSet, null, $idSubject)) && ($questions = $db->getResultAssoc('idQuestion'))){
            if(count($questions) != $numQuestions){
                die(ttEQuestionNotFound);
            }
            openBox(ttTest.': '.$studentName.' ('.$scoreTest.')', 'normal', 'correct', array('showHide'));
            $lastQuestion = '';
            foreach($questions as $idQuestion => $question){
                $questionAnswers = '';
                $questionScore = 0;
                $questionClass = 'emptyQuestion';
                $answerStyle = '';

                $lastQuestion = (--$numQuestions == 0) ? 'last' : '';
                $answered = json_decode(stripslashes($question['answer']), true);
                if($answered == '')
                    $answered = array('');

                switch($question['type']){
                    case 'MC' :
                    case 'MR' : if(($db->qAnswerSet($idQuestion, null, $idSubject)) && ($answers = $db->getResultAssoc('idAnswer'))){
                                    foreach($answers as $idAnswer => $answer){
                                        $answerdClass = "";
                                        $right_wrongClass = ($answer['score'] > 0) ? 'response'.$question['type'].' rightAnswer' : 'response'.$question['type'].' wrongAnswer';
                                        if(in_array($idAnswer, $answered)){
                                            $questionScore += round(($answer['score'] * $scale), 1);
                                            $answerdClass = 'answered';
                                        }
                                        $questionAnswers .= '<div class="'.$answerdClass.'">
                                                                 <span value="'.$idAnswer.'" class="'.$right_wrongClass.'"></span>
                                                                 <label>'.$answer['translation'].'</label>
                                                                 <label class="score">'.round($answer['score'] * $scale, 1).'</label>
                                                             </div>';
                                    }
                                    $questionAnswers .= '<label class="questionScore">'.$questionScore.'</label>
                                                         <div class="clearer"></div>';
                                    if(count($answered) != 0)
                                        $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';
                                    $answerStyle = 'style="display:none;"';
                                }else{ die(ttEAnswers); } break;
                    case 'OP' : $questionAnswers .= '<div class="responseOP" value="'.$idQuestion.'">'.$answered[0].'</div>
                                                     <dl class="dropdownScore">
                                                         <dt><span>0<span class="value">0</span></span></dt>
                                                         <dd>
                                                             <ol>';
                                $index = (-1 * $scale);
                                while($index < -0.1){
                                    $questionAnswers .= '<li>'.$index.'<span class="value">'.$index.'</span></li>';
                                    $index += (0.1 * $scale);
                                }
                                $questionAnswers .= '<li>0<span class="value">0</span></li>';
                                $index = (0.1 * $scale);
                                while($index <= (1 * $scale)){
                                    $questionAnswers .= '<li>'.$index.'<span class="value">'.$index.'</span></li>';
                                    $index += (0.1 * $scale);
                                }
                                $questionAnswers .= '</ol>
                                                 </dd>
                                             </dl>
                                             <label class="score">'.ttScore.' : </label>
                                             <div class="clearer"></div>';
                                if(count($answered) != 0)
                                    $questionClass = 'correctQuestion'; break;
                    default: die(ttEQuestionType);
                }
                ?>
                <div class="questionTest <?= $questionClass ?> <?= $lastQuestion ?>" value="<?= $idQuestion ?>" type="<?= $question['type'] ?>">
                    <div class="questionText" onclick="showHide(this);">
                        <span class="responseQuestion"></span>
                        <?= $question['translation'] ?>
                        <span class="responseScore"><?= $questionScore ?></span>
                    </div>
                    <div class="questionAnswers" <?= $answerStyle ?>><?= $questionAnswers ?></div>
                </div>
            <?php
            }
            ?>

            <div id="finalScorePanel">
                <a class="ok button" onclick="confirmTest(new Array(true));"><?= ttConfirm ?></a>
                <div class="right">
                    <table id="finalScore">
                        <tr>
                            <td class="sLabel"><?= ttScoreTest ?></td>
                            <td class="sScore"><label id="scorePre"><?= $scoreTest ?></label></td>
                            <td>+</td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttBonus ?></td>
                            <td>
                                <dl class="dropdownBonus">
                                    <dt><span>0<span class="value">0</span></span></dt>
                                    <dd>
                                        <ol>
                                            <?php
                                            $index = 0;
                                            while($index <= $testInfo['bonus']){
                                                echo '<li>'.$index.'<span class="value">'.$index.'</span></li>';
                                                $index += 0.5;
                                            }
                                            ?>
                                        </ol>
                                    </dd>
                                </dl>
                            </td>
                            <td>=</td>
                        </tr>
                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttFinalScore ?></td>
                            <td class="sScore"><label id="scorePost"><?= round($scoreTest) ?></label></td>
                            <td></td>
                        </tr>
                    </table>
                    <input type="hidden" id="maxScore" value="<?= $scoreType ?>">
                    <input type="hidden" id="idTest" value="<?= $testInfo['idTest'] ?>">

                    <form action="index.php?page=exam/exams" method="post" id="idExamForm">
                        <input type="hidden" id="idExam" name="idExam" value="<?= $testInfo['fkExam'] ?>">
                    </form>

                </div>
            </div>

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