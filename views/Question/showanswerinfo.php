<?php
/**
 * File: showanswerinfo.php
 * User: Masterplan
 * Date: 4/10/13
 * Time: 8:59 PM
 * Desc: Your description HERE
 */

global $user, $config;
$answerInfo = null;

$db = new sqlDB();
if(($_POST['action'] == 'new') || (($db->qAnswerInfo($_POST['idAnswer'])) && ($answerTranslations = $db->getResultAssoc()))){

    if($_POST['action'] == 'new'){
        $answerInfo = array('score' => 0);
    }else{
        switch($_POST['idType']){
            case 'MC' :
            case 'MR' : foreach($answerTranslations as $answerInfo){
                            echo '<div class="hidden" id="'.strtoupper($answerInfo['alias']).'">'.$answerInfo['translation'].'</div>';
                        }; break;
    //        case 'OP' : break;
            default : die(ttError); break;
        }
        $answerInfo = $answerTranslations[0];
    }

    switch($_POST['idType']){
        case 'MC' : echo '<dl class="dropdownInfo scoreMC lSpace tSpace right" id="answerScore">';
                    if($answerInfo['score'] == 0)
                        echo '<dt class="tSpace writable"><span>'.ttFalse.'<span class="value">0</span></span></dt>
                              <dd>
                                  <ol>';
                    else
                        echo '<dt><span>'.ttTrue.'<span class="value">1</span></span></dt>
                              <dd>
                                  <ol>';
                    echo '<li>'.ttTrue.'<span class="value">1</span></li>
                          <li>'.ttFalse.'<span class="value">0</span></li>
                            </ol>
                        </dd>
                    </dl>'; break;
        case 'MR' : echo '<input id="answerScore" class="scoreMR lSpace tSpace right writable" type="text" value="'.$answerInfo['score'].'">';
                    break;
//        case 'OP' : break;
        default : die(ttError); break;
    }
    echo '<label class="right tSpace">'.ttScore.' :</label><div class="clearer"></div>';

    if($_POST['action'] == 'new'){ ?>
        <a class="button blue right lSpace tSpace" onclick="createNewAnswer(false);" id="createNewAnswer"><?= ttCreate.' '.ttAnswer?></a>
        <a class="button red right left tSpace" onclick="cancelNewAnswer(true);" id="cancelNewAnswer"><?= ttCancel ?></a>
    <?php
    }else{ ?>
        <a class="button blue right lSpace tSpace" onclick="saveAnswerInfo(false);" id="saveAnswer"><?= ttSaveAnswerInfo ?></a>
        <a class="button red right left tSpace" onclick="deleteAnswer(true);" id="deleteAnswer"><?= ttDelete.' '.ttAnswer ?></a>
    <?php
    }

}else{
    die($db->getError());
}
?>