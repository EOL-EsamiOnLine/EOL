<?php
/**
 * File: QuestionController.php
 * User: Masterplan
 * Date: 4/4/13
 * Time: 6:18 PM
 * Desc: Controller for all questions operations
 */

class QuestionController extends Controller{

    /**
     *  @name   QuestionController
     *  @descr  Create an instance of QuestionController class
     */
    public function QuestionController(){}

    /**
     * @name    executeAction
     * @param   $action     String      Name of requested action
     * @descr   Execute action (if exists and if user is allowed)
     */
    public function executeAction($action){
        global $user;

        // If have necessary privileges execute action
        if ($this->getAccess($user, $action, $this->accessRules())) {
            $action = 'action'.$action;
            $this->$action();
            // Else, if user is not logged bring him the to login page
        }elseif($user->role == '?'){
            header('Location: index.php?page=login');
            // Otherwise: Access denied
        }else{
            Controller::error('AccessDenied');
        }
    }

    /**
     *  @name   actionIndex
     *  @descr  Show index page
     */
    private function actionIndex(){
        global $config, $engine;

        if(isset($_POST['idSubject'])){
            $_SESSION['idSubject'] = $_POST['idSubject'];
            $_SESSION['uploadDir'] = $config['systemUploadDir'].$_POST['idSubject'];
        }
        if(isset($_SESSION['idSubject'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header('Location: index.php?page=subject&r=qstn');
        }
    }

    /********************************************************************
     *                             Question                             *
     ********************************************************************/

    /**
     *  @name   actionShowquestionpreview
     *  @descr  Show preview about requested question
     */
    private function actionShowquestionpreview(){
        global $log, $engine;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idLanguage']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
            die(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowquestionlanguages
     *  @descr  Show languages about requested question
     */
    private function actionShowquestionlanguages(){
        global $log, $engine;

        if(isset($_POST['idQuestion'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
            die(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowquestioninfo
     *  @descr  Show all details about selected question with relative answers with specific language
     */
    private function actionShowquestioninfo(){
        global $log, $engine;

        if((isset($_POST['action'])) && (isset($_POST['idQuestion']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append("Params not set in '".__FUNCTION__.'\' function: $_POST = '.var_export($_POST, true));
            die("Params not set in '".__FUNCTION__." function");
        }
    }

    /**
     *  @name   actionUpdatequestioninfo
     *  @descr  If requested duplicates the question and updates all its infos
     */
    private function actionUpdatequestioninfo(){
        global $log;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idTopic'])) && (isset($_POST['difficulty'])) &&
           (isset($_POST['translationsQ'])) && (isset($_POST['shortText'])) && (isset($_POST['mainLang']))){

            $db = new sqlDB();
            $questionID = $_POST['idQuestion'];
            if($db->qGetEditAndDeleteConstraints('edit', 'question2', array($_POST['idQuestion']))){
                if($db->numResultRows() > 0){
                    if($db->qDuplicateQuestion($questionID, true)){
                        if($questionID = $db->nextRowEnum()){
                            $questionID = $questionID[0];
                        }
                    }else{
                        die($db->getError());
                    }
                }
            }else{
                die($db->getError());
            }
            $translationsQ = json_decode($_POST['translationsQ'], true);
            if($translationsQ[$_POST['mainLang']]){
                if($db->qUpdateQuestionInfo($questionID, $_POST['idTopic'], $_POST['difficulty'], $_POST['shortText'], $translationsQ)){
                    echo $this->updateQuestionRow($questionID, $_POST['mainLang'], $translationsQ);
                }
            }else{
                die(ttEMainLanguageEmpty);
            }
        }else{
            $log->append("Params not set in '".__FUNCTION__.'\' function: $_POST = '.var_export($_POST, true));
            die("Params not set in '".__FUNCTION__." function");
        }

    }

    /**
     *  @name   updateQuestionRow
     *  @param  $questionID         String          Question's ID
     *  @param  $mainLang           String          Question's main lang
     *  @param  $translationsQ      Array           Question's translations
     *  @return null|String
     *  @descr  Update all infos about question
     */
    private function updateQuestionRow($questionID, $mainLang, $translationsQ){
        global $log, $ajaxSeparator, $config;

        $db = new sqlDB();
        if(($db->qQuestionInfo($questionID, $mainLang)) && ($question = $db->nextRowAssoc()) &&
            ($db->qSelect('Languages')) && ($allLangs = $db->getResultAssoc('idLanguage'))){
            $statuses = array('a' => 'Active',
                              'i' => 'Inactive',
                              'e' => 'Error');
            $languages = '';
            foreach($translationsQ as $idLanguage => $translation){
                if((isset($allLangs[$idLanguage])) && trim($translation) != "" )
                    $languages .= '<img title="'.$allLangs[$idLanguage]['description'].'"
                                        class="flag" alt="'.$allLangs[$idLanguage]['alias'].'"
                                        src="'.$config['themeFlagsDir'].$allLangs[$idLanguage]['alias'].'.gif">';
            }
            if(strlen($question['shortText']) > $config['datatablesTextLength'])
                $text = substr($question['shortText'], 0, ($config['datatablesTextLength'] - (strlen($config['ellipsis'])))).$config['ellipsis'];
            else
                $text = $question['shortText'];
            $newQuestion = array(
                '<img title="'.constant('tt'.$statuses[$question['status']]).'"
                      value="'.$question['status'].'" alt="'.$statuses[$question['status']].'"
                      src="'.$config['themeImagesDir'].$statuses[$question['status']].'.png">',
                $text,
                $languages,
                $question['name'],
                constant('ttQT'.$question['type']),
                constant('ttD'.$question['difficulty']),
                $questionID,
                $question['idTopic'],
                $question['type'],
                $_POST['mainLang']
            );
            return 'ACK'.$ajaxSeparator.str_replace('\\/', '/', json_encode($newQuestion));
        }else{
            return $db->getError();
        }
    }

    /**
     *  @name   actionChangestatus
     *  @descr  Action used for change question's status
     */
    private function actionChangestatus(){
        global $log, $engine;
        if((isset($_POST['idQuestion'])) && (isset($_POST['status']))){
            if($TheresNoErrorsInQuestion = true){       // Add function to check if question
                $db = new sqlDB();                      // and it's answers are well formed
                if($db->qChangeQuestionStatus($_POST['idQuestion'], $_POST['status'])){
                    echo 'ACK';
                }else{
                    die($db->getError());
                }
                $db->close();
            }else{
                die("Question/Answers not well formed");
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewquestion
     *  @descr  Action used for create a new question
     */
    private function actionNewquestion(){
        global $log, $config, $ajaxSeparator;

        if((isset($_POST['idTopic'])) && (isset($_POST['idType'])) && (isset($_POST['idDifficulty'])) &&
            (isset($_POST['translationsQ'])) && (isset($_POST['shortText'])) && (isset($_POST['mainLang']))){

            $db = new sqlDB();
            $translationsQ = json_decode($_POST['translationsQ'], true);
            if($translationsQ[$_POST['mainLang']]){
                if($db->qNewQuestion($_POST['idTopic'], $_POST['idType'], $_POST['idDifficulty'], $_POST['shortText'], $translationsQ)){
                    if(($idQuestion = $db->nextRowEnum()) && ($idQuestion = $idQuestion[0])){
                        echo $this->updateQuestionRow($idQuestion, $_POST['mainLang'], $translationsQ);
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionDeletequestion
     *  @descr  Delete questions and its answers (if possible)
     */
    private function actionDeletequestion(){
        global $log;

        if(isset($_POST['idQuestion'])){
            $db = new sqlDB();

            if(($db->qGetEditAndDeleteConstraints('delete', 'question1', array($_POST['idQuestion']))) && ($db->numResultRows() > 0)){
                $error = ttETestSettingDeleteQuestion;
                while($testsetting = $db->nextRowAssoc()){
                    $error .= ' - '.$testsetting['name'].'</br>';
                }
                die($error);
            }elseif($db->qGetEditAndDeleteConstraints('delete', 'question2', array($_POST['idQuestion']))){
                if($db->qDeleteQuestion($_POST['idQuestion'], ($db->numResultRows() == 0))){
                    echo 'ACK';
                }else{
                    die($db->getError());
                }
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                              Answer                              *
     ********************************************************************/

    /**
     *  @name   actionShowanswerinfo
     *  @descr  Show all informations about selected answer
     */
    private function actionShowanswerinfo(){
        global $engine, $log;

        if((isset($_SESSION['idSubject'])) && (isset($_POST['action'])) && (isset($_POST['idQuestion'])) &&
            (isset($_POST['idType'])) && (isset($_POST['idAnswer']))){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdateanswerinfo
     *  @descr  Update all details about a answer
     */
    private function actionUpdateanswerinfo(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idAnswer'])) &&
           (isset($_POST['translationsA'])) && (isset($_POST['score'])) && (isset($_POST['mainLang']))){
            $db = new sqlDB();

            $translationsA = json_decode($_POST['translationsA'], true);
            if($translationsA[$_POST['mainLang']]){
                $updateMandatory = false;
                $questionID = $_POST['idQuestion'];
                $answerID = $_POST['idAnswer'];
                if($db->qGetEditAndDeleteConstraints('edit', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('edit', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory, $answerID)){
                            if($IDs = $db->nextRowEnum()){
                                $questionID = $IDs[0];
                                $answerID = $IDs[1];
                            }
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

            if($db->qUpdateAnswerInfo($answerID, $_POST['score'], $translationsA)){
                echo 'ACK'.$ajaxSeparator.$questionID.$ajaxSeparator.$answerID;
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set - ".var_export($_POST, true));
        }
    }

    /**
     *  @name   actionNewanswer
     *  @descr  Action used for create a new answer
     */
    private function actionNewanswer(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['score'])) &&
           (isset($_POST['translationsA'])) && (isset($_POST['mainLang']))){
            $db = new sqlDB();

            $translationsA = json_decode($_POST['translationsA'], true);
            if($translationsA[$_POST['mainLang']]){

                $updateMandatory = false;
                $questionID = $_POST['idQuestion'];
                if($db->qGetEditAndDeleteConstraints('create', 'answer1', array($questionID))){
                    $updateMandatory = ($db->numResultRows() > 0) ? true : false;
                }else{
                    die($db->getError());
                }

                if($db->qGetEditAndDeleteConstraints('create', 'answer2', array($questionID))){
                    if($db->numResultRows() > 0){
                        if($db->qDuplicateQuestion($questionID, $updateMandatory)){
                            $resultSet = $db->nextRowEnum();
                            $questionID = $resultSet[0];
                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die($db->getError());
                }

                if($db->qNewAnswer($questionID, $_POST['score'], $translationsA)){
                    $resultSet = $db->nextRowEnum();
                    $answerID = $resultSet[0];
                    echo 'ACK'.$ajaxSeparator.$questionID.$ajaxSeparator.$answerID;
                }else{
                    die($db->getError());
                }
            }else{
                die(ttEMainLanguageEmpty);
            }

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionDeleteanswer
     *  @descr  Delete answer and its translation (if possible)
     */
    private function actionDeleteanswer(){
        global $log, $ajaxSeparator;

        if((isset($_POST['idQuestion'])) && (isset($_POST['idAnswer']))){
            $db = new sqlDB();

            $updateMandatory = false;
            $questionID = $_POST['idQuestion'];
            $answerID = $_POST['idAnswer'];
            if($db->qGetEditAndDeleteConstraints('delete', 'answer1', array($questionID))){
                $updateMandatory = ($db->numResultRows() > 0) ? true : false;
            }else{
                die($db->getError());
            }

            if($db->qGetEditAndDeleteConstraints('delete', 'answer2', array($questionID))){
                if($db->numResultRows() > 0){
                    $log->append(var_export($db->nextRowAssoc()), true);
                    if($db->qDuplicateQuestion($questionID, $updateMandatory, $answerID)){
                        if($IDs = $db->nextRowEnum()){
                            $questionID = $IDs[0];
                            $answerID = $IDs[1];
                        }
                    }else{
                        die($db->getError());
                    }
                }
            }else{
                die($db->getError());
            }

            if($db->qDeleteAnswer($answerID)){
                echo 'ACK'.$ajaxSeparator.$questionID;
            }else{
                die($db->getError());
            }

            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   accessRules
     *  @descr  Returns all access rules for Home controller's actions:
     *  array(
     *     array(
     *       (allow | deny),                                     Parameter
     *       'actions' => array('*' | 'act1', ['act2', ....]),   Actions
     *       'roles'   => array('*' | '?' | 'a' | 't' | 's')     User's Role
     *     ),
     *  );
     */
    private function accessRules(){
        return array(
            array(
                'allow',
                'actions' => array('Index', 'Showtopics', 'Showquestionpreview', 'Showquestioninfo',
                                   'Newquestion', 'Showquestionlanguages',
                                   'Updatequestioninfo', 'Deletequestion', 'Changestatus',
                                   'Showanswerinfo', 'Updateanswerdetails',
                                   'Updateanswerinfo', 'Newanswer', 'Deleteanswer'),
                'roles'   => array('t'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }

}
