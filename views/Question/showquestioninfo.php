<?php
/**
 * File: showquestioninfo.php
 * User: Masterplan
 * Date: 19/05/14
 * Time: 11:19
 * Desc: Show question and releated answers info panels or show empty infos for new question
 */

global $config, $log, $user;

$allLangs = null;
$subjectInfo = null;
$questionInfo = array();
$questionTranslations = array();

$questionMandatory = '';
$dropdownClass = 'writable';


$db = new sqlDB();
if(($db->qSelect('Languages')) & ($allLangs = $db->getResultAssoc('idLanguage')) &&
   ($db->qSelect('Subjects', 'idSubject', $_SESSION['idSubject'])) && ($subjectInfo = $db->nextRowAssoc())){

    if($_POST['action'] == 'show'){
        $idQuestion = $_POST['idQuestion'];
        if($db->qQuestionInfo($idQuestion)){
            $questionTranslations = $db->getResultAssoc('idLanguage');
            $questionInfo = $questionTranslations[$subjectInfo['fkLanguage']];
        }
        if(($db->qGetEditAndDeleteConstraints('edit', 'question1', array($idQuestion))) && ($row = $db->nextRowAssoc())){
            $questionMandatory = $row['name'];
            $dropdownClass = 'readonly';
        }
    }
}

openBox(ttQuestion." ".ttInfo, 'normal-80%', 'questionInfo');

if($questionMandatory != ''){
    echo '<div class="mandatoryNotice">'.str_replace('_TESTSETTING_', $questionMandatory, ttEMandatoryQuestion).'</div>';
}

?>
<form id="questionInfoForm">
    <div id="questionInfoTabs">
        <ul>
            <li><a href="#question-tab"><?= ttQuestion ?></a></li>
            <?php
            if($_POST['action'] == 'show')
                echo '<li><a href="#answers">'.ttAnswers.'</a></li>';
            ?>
        </ul>
        <div id="question-tab">
            <?php
            $ulElement = '<li>
                              <a href="#qt'.strtoupper($allLangs[$subjectInfo['fkLanguage']]['alias']).'div">
                              <img title="'.$allLangs[$subjectInfo['fkLanguage']]['description'].'" class="flag"
                                   src="'.$config['themeFlagsDir'].$allLangs[$subjectInfo['fkLanguage']]['alias'].'.gif">
                                   '.$allLangs[$subjectInfo['fkLanguage']]['description'].'</a></li>';
            $divsElement = createQuestionDiv($questionTranslations, $subjectInfo['fkLanguage'], $allLangs);
            $CKEDITORScript = 'var mainLang = "'.$allLangs[$subjectInfo['fkLanguage']]['idLanguage'].'";
                               var userLang = "'.$user->lang.'";
                               createCKEditorInstance("qt'.strtoupper($allLangs[$subjectInfo['fkLanguage']]['alias']).'");';

            foreach($allLangs as $idLanguage => $language)
                if($idLanguage != $subjectInfo['fkLanguage']){
                    $ulElement .= '<li>
                                       <a href="#qt'.strtoupper($language['alias']).'div">
                                       <img title="'.$language['description'].'" class="flag"
                                            src="'.$config['themeFlagsDir'].$language['alias'].'.gif">
                                            '.$language['description'].'</a></li>';
                    $divsElement .= createQuestionDiv($questionTranslations, $idLanguage, $allLangs);
                    $CKEDITORScript .= 'createCKEditorInstance("qt'.strtoupper($language['alias']).'");';
                }

            ?>
            <ul> <?= $ulElement ?> </ul>
            <?= $divsElement ?>
            <script>
                <?= $CKEDITORScript ?>
            </script>
            <div class="clearer bSpace"></div>

            <?php
            if($_POST['action'] == 'new'){
                $types = getQuestionTypes();
            ?>
                <div class="right">
                    <label class="left l2Space"><?= ttType ?> : </label>
                    <dl class="dropdownInfo" id="questionType">
                        <dt class="writable"><span><?= constant('ttQT'.$types[0]) ?><span class="value"><?= $types[0] ?></span></span></dt>
                        <dd>
                            <ol>
                                <?php
                                while($type = array_shift($types)){
                                    echo '<li>'.constant('ttQT'.$type).'<span class="value">'.$type.'</span></li>';
                                }
                                ?>
                            </ol>
                        </dd>
                    </dl>
                </div>
            <?php
            }
            ?>

            <div class="right">
                <label class="left"><?= ttDifficulty ?> : </label>
                <dl class="dropdownInfo" id="questionDifficulty">
                    <?php
                    if($_POST['action'] == 'show'){
                        echo '<dt class="'.$dropdownClass.'"><span>'.constant('ttD'.$questionInfo['difficulty']).'<span class="value">'.$questionInfo['difficulty'].'</span></span></dt>';
                    }else{
                        echo '<dt class="'.$dropdownClass.'"><span>'.ttD1.'<span class="value">1</span></span></dt>';
                    }
                    if($questionMandatory == ''){
                    ?>
                    <dd>
                        <ol>
                            <?php
                            $maxdifficulty = getMaxQuestionDifficulty();
                            $index = 1;
                            while($index <= $maxdifficulty){
                                echo '<li>'.constant('ttD'.$index).'<span class="value">'.$index.'</span></li>';
                                $index++;
                            }
                            ?>
                        </ol>
                    </dd>
                    <?php } ?>
                </dl>
            </div>

            <div class="right">
                <label class="left"><?= ttTopic ?> : </label>
                <dl class="dropdownInfo r2Space" id="questionTopic">
                    <?php
                    if(($db->qSelect('Topics', 'fkSubject', $subjectInfo['idSubject'], 'name')) && ($topics = $db->getResultAssoc())){
                        if(count($topics) > 0){
                            if($_POST['action'] == 'show'){
                                $selectedTopic = '';
                                $otherTopics = '';
                                foreach($topics as $topic){
                                    if($topic['idTopic'] == $questionInfo['fkTopic'])
                                        $selectedTopic = '<dt class="'.$dropdownClass.'"><span>'.$topic['name'].'<span class="value">'.$topic['idTopic'].'</span></span></dt>';
                                    $otherTopics .= '<li>'.$topic['name'].'<span class="value">'.$topic['idTopic'].'</span></li>';
                                }
                                echo $selectedTopic;
                                if($questionMandatory == ''){
                                    echo '<dd><ol>';
                                    echo $otherTopics;
                                    echo '</ol></dd>';
                                }
                            }else{
                                echo '<dt class="'.$dropdownClass.'"><span>'.$topics[0]['name'].'<span class="value">'.$topics[0]['idTopic'].'</span></span></dt>';
                                if($questionMandatory == ''){
                                    echo '<dd><ol>';
                                    foreach($topics as $topic){
                                        echo '<li>'.$topic['name'].'<span class="value">'.$topic['idTopic'].'</span></li>';
                                    }
                                    echo '</ol></dd>';
                                }
                            }
                        }else{
                            echo '<dt><span> --- <span class="value">-1</span></span></dt><dd><ol>
                                  <li> --- <span class="value">-1</span></li>
                                  </ol></dd>';
                        }
                    }
                    ?>
                </dl>
            </div>

            <div class="clearer"></div>
            <?php
            if($_POST['action'] == 'show'){ ?>
                <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>
                <a class="button blue right lSpace tSpace" onclick="saveQuestionInfo(true);"><?= ttSaveQuestionInfo ?></a>
                <?php if($questionMandatory == ''){ ?>
                    <a class="button red right tSpace" onclick="deleteQuestion(true);" id="deleteQuestion"><?= ttDelete.' '.ttQuestion ?></a>
                <?php } ?>
            <?php
            }else{
            ?>
                <a class="button normal left rSpace tSpace" onclick="cancelNewQuestion(true);"><?= ttCancel ?></a>
                <a class="button blue right lSpace tSpace" onclick="createNewQuestion();"><?= ttCreate.' '.ttQuestion ?></a>
            <?php
            }
            ?>

            <div class="clearer"></div>
        </div>
        <?php
        if($_POST['action'] == 'show'){
        ?>
            <div id="answers">

                <?php
                switch($questionInfo['type']){
                    case 'MC' :
                    case 'MR' :
                        openBox(ttAnswers, "left-190px", "answersList", array('new-newAnswer'));
                        if($db->qAnswerSet($idQuestion, null, $subjectInfo['idSubject'])){
                            echo '<div class="list">
                        <ul>';
                            while($answer = $db->nextRowAssoc()){
                                echo '<li><a class="showAnswerInfo" value="'.$answer['idAnswer'].'"
                                             onclick="showAnswerInfo(new Array(this, (answerEditing || checkCKEditorEdits(\'at\'))));">'.$answer['translation'].'</a></li>';
                            }
                            echo '</ul></div>';
                        }else{
                            echo ttEDatabase;
                        }
                        closeBox();
                        ?>

                        <div id="answers-tab" class="right">
                            <?php
                            $ulElement = '<li>
                                          <a href="#at'.strtoupper($allLangs[$subjectInfo['fkLanguage']]['alias']).'div">
                                          <img title="'.$allLangs[$subjectInfo['fkLanguage']]['description'].'" class="flag"
                                               src="'.$config['themeFlagsDir'].$allLangs[$subjectInfo['fkLanguage']]['alias'].'.gif">
                                               '.$allLangs[$subjectInfo['fkLanguage']]['description'].'</a></li>';
                            $divsElement = createAnswerDiv($subjectInfo['fkLanguage'], $allLangs);
                            $CKEDITORScript = 'createCKEditorInstance("at'.strtoupper($allLangs[$subjectInfo['fkLanguage']]['alias']).'");';

                            foreach($allLangs as $idLanguage => $language)
                                if($idLanguage != $subjectInfo['fkLanguage']){
                                    $ulElement .= '<li>
                                                   <a href="#at'.strtoupper($language['alias']).'div">
                                                   <img title="'.$language['description'].'" class="flag"
                                                        src="'.$config['themeFlagsDir'].$language['alias'].'.gif">
                                                        '.$language['description'].'</a></li>';
                                    $divsElement .= createAnswerDiv($idLanguage, $allLangs);
                                    $CKEDITORScript .= 'createCKEditorInstance("at'.strtoupper($language['alias']).'");';
                                }
                            ?>
                            <ul> <?= $ulElement ?> </ul>
                            <?= $divsElement ?>
                            <div class="clearer"></div>
                            <div id="answerOptions" class="right"></div>
                            <script>
                                <?= $CKEDITORScript ?>
                            </script>
                        </div>

                <?php
                    break;
                    case 'OP' : echo ttOpenQuestion; break;
                }
                ?>
                <div class="clearer"></div>
                <a class="button normal left rSpace tSpace" onclick="closeQuestionInfo(true);"><?= ttExit ?></a>
                <div class="clearer"></div>
            </div>
        <?php
        }
        ?>
    </div>
</form>
<div class="clearer"></div>

<?php
closeBox();

function createQuestionDiv($questionTranslations, $idLanguage, $allLangs){
    $translation = '';
    if(array_key_exists($idLanguage, $questionTranslations))
        $translation = $questionTranslations[$idLanguage]['translation'];
    $div = '<div class="ui-corner-top" style="height:400px; width:870px; background: white;" id="qt'.strtoupper($allLangs[$idLanguage]['alias']).'div">
                <textarea class="ckeditor" name="qt'.strtoupper($allLangs[$idLanguage]['alias']).'" id="qt'.strtoupper($allLangs[$idLanguage]['alias']).'">'.$translation.'</textarea>
             </div>';

    return $div;
}

function createAnswerDiv($idLanguage, $allLangs){
    $div = '<div class="ui-corner-top" style="height:400px; width:674px; background: white;" id="at'.strtoupper($allLangs[$idLanguage]['alias']).'div">
                <textarea class="ckeditor" name="at'.strtoupper($allLangs[$idLanguage]['alias']).'" id="at'.strtoupper($allLangs[$idLanguage]['alias']).'"></textarea>
             </div>';

    return $div;
}

?>