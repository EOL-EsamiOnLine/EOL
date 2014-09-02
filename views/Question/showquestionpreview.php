<?php
/**
 * File: showquestionpreview.php
 * User: Masterplan
 * Date: 17/05/14
 * Time: 16:30
 * Desc: Shows a preview of requested question in a specific translation
 */

global $config;

$idQuestion = $_POST['idQuestion'];
$idLanguage = $_POST['idLanguage'];

$db = new sqlDB();
if(($db->qQuestionInfo($idQuestion, $idLanguage)) && ($question = $db->nextRowAssoc())){

    $questionAnswers = '<div class="questionAnswers">';
    switch($question['type']){
        case 'MC' : if(($db->qAnswerSet($idQuestion, $idLanguage, $_SESSION['idSubject'])) && ($answers = $db->getResultAssoc())){
                        foreach($answers as $answer){
                            $class = '';
                            if($answer['fkLanguage'] != $idLanguage)
                                $class = 'mainLang';
                            $questionAnswers .= '<div class="'.$class.'">
                                                         <input class="hidden" type="radio" name="'.$idQuestion.'" value="'.$answer['idAnswer'].'"/>
                                                         <span value="'.$answer['idAnswer'].'"></span>
                                                         <label>'.$answer['translation'].'</label>
                                                     </div>';

                        }
                    } break;
        case 'MR' : if(($db->qAnswerSet($idQuestion, $idLanguage, $_SESSION['idSubject'])) && ($answers = $db->getResultAssoc())){
                        foreach($answers as $answer){
                            $class = '';
                            if($answer['fkLanguage'] != $idLanguage)
                                $class = 'mainLang';
                            $questionAnswers .= '<div class="'.$class.'">
                                                                 <input class="hidden" type="checkbox" name="'.$idQuestion.'" value="'.$answer['idAnswer'].'"/>
                                                                 <span value="'.$answer['idAnswer'].'"></span>
                                                                 <label>'.$answer['translation'].'</label>
                                                             </div>';
                        }
                    } break;
        case 'OP' : $questionAnswers .= '<textarea class="textareaTest"></textarea>'; break;
    }

    $questionAnswers .= '</div>';
    echo '<div class="questionTest" value="'.$idQuestion.'" type="'.$question['type'].'">';
    echo '<div class="questionText">'.$question['translation'];

    if(strpos($question['extra'], 'c') !== false)
        echo '<img class="extraIcon calculator" src="'.$config['themeImagesDir'].'QEc.png'.'">';
    if(strpos($question['extra'], 'p') !== false)
        echo '<img class="extraIcon periodicTable" src="'.$config['themeImagesDir'].'QEp.png'.'">';

    echo '</div>';
    echo $questionAnswers;
    echo '</div>';
}