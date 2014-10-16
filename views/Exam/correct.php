<?php
/**
 * File: correct.php
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc: Shows test correction page
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
            foreach($questions as $idQuestion => $questionInfo){

                $lastQuestion = (--$numQuestions == 0) ? 'last' : '';
                $answered = json_decode(stripslashes($questionInfo['answer']), true);
                if($answered == '')
                    $answered = array('');

                $question = Question::newQuestion($questionInfo['type'], $questionInfo);
                $question->printQuestionInCorrection($idSubject, $answered, $scale, $lastQuestion);

            }
            ?>

            <div id="lastLine">
                <div id="finalScorePanel">
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
                            <td class="sScore"><label id="scorePost"><?= number_format(round($scoreTest, 0), 0); ?></label></td>
                            <td></td>
                        </tr>
                    </table>
                    <input type="hidden" id="maxScore" value="<?= $scoreType ?>">
                    <input type="hidden" id="idTest" value="<?= $testInfo['idTest'] ?>">

                    <form action="index.php?page=exam/exams" method="post" id="idExamForm">
                        <input type="hidden" id="idExam" name="idExam" value="<?= $testInfo['fkExam'] ?>">
                    </form>
                </div>
                <a class="ok button" onclick="confirmTest(new Array(true));"><?= ttConfirm ?></a>
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