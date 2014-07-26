<?php
/**
 * File: showquestionpreview.php
 * User: Masterplan
 * Date: 17/05/14
 * Time: 16:30
 * Desc: Your description HERE
 */

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
    echo '<div class="questionText">'.$question['translation'].'</div>';
    echo $questionAnswers;
    echo '</div>';
}