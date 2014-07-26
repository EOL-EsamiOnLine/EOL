<?php
/**
 * File: view.php
 * User: Masterplan
 * Date: 5/6/13
 * Time: 3:42 PM
 * Desc: View archived test
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
        $idSubject = $testInfo['fkSubject'];
        $numQuestions = $testInfo['questions'];
        $scoreTest = $testInfo['scoreTest'];
        $bonus = $testInfo['testBonus'];
        $scoreFinal = $testInfo['scoreFinal'];
        $scale = $testInfo['scale'];

        if(($db->qViewArchivedTest($_POST['idTest'], null, $idSubject)) && ($questions = $db->getResultAssoc('idQuestion'))){
            if(count($questions) != $numQuestions){
                die(ttEQuestionNotFound);
            }
            openBox(ttTest.': '.$studentName.' ('.$scoreFinal.')', 'normal', 'correct', array('showHide'));
            $lastQuestion = '';
            foreach($questions as $idQuestion => $question){
                $questionAnswers = '';
                $questionScore = 0;

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
                                    $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion';
                                }else{ die(ttEAnswers); } break;
                    case 'OP' : $questionAnswers .= '<div class="responseOP" value="'.$idQuestion.'">'.$answered[0].'</div>
                                                     <dl class="dropdownScore">
                                                         <dt class="readonly"><span>'.$question['score'].'<span class="value">'.$question['score'].'</span></span></dt>
                                                     </dl>
                                                     <label class="score">'.ttScore.' : </label>
                                                     <div class="clearer"></div>';
                                $questionScore = $question['score'];
                                $questionClass = ($questionScore > 0) ? 'rightQuestion' : 'wrongQuestion'; break;
                    default: die(ttEQuestionType);
                }

                ?>
                <div class="questionTest <?= $questionClass ?> <?= $lastQuestion ?>" value="<?= $idQuestion ?>" type="<?= $question['type'] ?>">
                    <div class="questionText" onclick="showHide(this);">
                        <span class="responseQuestion"></span>
                        <?= $question['translation'] ?>
                        <span class="responseScore"><?= $questionScore ?></span>
                    </div>
                    <div class="questionAnswers" style="display:none;"><?= $questionAnswers ?></div>
                </div>
            <?php
            }
            ?>

            <div id="finalScorePanel">
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
                                    <dt class="readonly"><span><?= $bonus ?><span class="value"><?= $bonus ?></span></span></dt>
                                </dl>
                            </td>
                            <td>=</td>
                        </tr>
                        <tr>
                            <td colspan="3"><hr></td>
                        </tr>
                        <tr>
                            <td class="sLabel"><?= ttFinalScore ?></td>
                            <td class="sScore"><label id="scorePost"><?= $scoreFinal ?></label></td>
                            <td></td>
                        </tr>
                    </table>
                    <input type="hidden" id="idTest" value="<?= $testInfo['idTest'] ?>">

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