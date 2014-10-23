<?php
/**
 * File: AT_TF.php
 * User: Masterplan
 * Date: 18/09/14
 * Time: 12:41
 * Desc: * Desc: Class for answer in True/False questions
 */

class AT_TF extends Answer {

    public function printAnswerEditForm($action){
        global $config, $log;

        $translation = array('T' => ttTrue, 'F' => ttFalse);

        $score = explode('*', $this->get('score'));             // e.g. 'T*0'

        ?>

        <div class="tSpace left" style="margin-left: 80px;">
            <input type="hidden" id="scoreTranslation" value="<?= $score[0] ?>">
            <input disabled="disabled" value="<?= $translation[$score[0]] ?>">
        </div>

        <!-- Print all other answer's info -->
        <?php $this->printAnswerInfoEditForm($action) ?>

        <!-- Print buttons for answer panel -->
        <?php $this->printAnswerEditButtons($action); ?>

        <div class="clearer"></div>

        <?php $this->printAnswerTypeLibrary(); ?>

    <?php
    }

    public function printAnswerInfoEditForm($action){
        global $log;

        $score = explode('*', $this->get('score'));             // e.g. 'T*0'
        ?>

        <dl class="dropdownInfo scoreTF tSpace right" style="margin-right: 80px;" id="answerScore">
            <dt class="tSpace writable">
                <span><?= $score[1] ?><span class="value"><?= $score[1] ?></span></span>
            </dt>
            <dd>
                <ol>
                    <li>1<span class="value">1</span></li>
                    <li>0<span class="value">0</span></li>
                </ol>
            </dd>
        </dl>

        <label class="right tSpace"><?= ttScore ?> :</label><div class="clearer"></div>
        <div class="clearer bSpace"></div>

    <?php
    }

    public function printAnswerEditButtons($action){ ?>
        <a class="button normal left rSpace tSpace" onclick="closeAnswerInfo(answerEditing);"><?= ttExit ?></a>
        <a class="button blue right lSpace tSpace" onclick="saveAnswerInfo_TF(closePanel = true);"><?= ttSave ?></a>
    <?php
    }

    public function getAnswerRowInTable(){
        $translation = array('T' => ttTrue, 'F' => ttFalse);
        $score = explode('*', $this->get('score'));             // e.g. 'T*0'

        return array($score[1], $translation[$score[0]], $this->get('idAnswer'));
    }

    public function getAnswerScore(){
        $score = explode('*', $this->get('score'));             // e.g. 'T*0'
        return $score[1];
    }

    public function getScoreFromGivenAnswer(){
        $score = 0;

        $answer = json_decode(stripslashes($this->get('answer')), true);
        if(count($answer) > 0){
            $db = new sqlDB();
            if(($db->qSelect('Answers', 'idAnswer', $answer[0])) && ($result = $db->nextRowAssoc())){
                $scores = explode('*', $result['score']);       // e.g. 'T*0'
//                $score = round(($scores[1] * $this->get('scale')), 1);;
                $score = $scores[1];
            }else die($db->getError());
        }

        return $score;
    }
}