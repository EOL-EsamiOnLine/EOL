<?php
/**
 * File: ExamController.php
 * User: Masterplan
 * Date: 4/19/13
 * Time: 10:04 AM
 * Desc: Your description HERE
 */

class ExamController extends Controller{

    public $defaultAction = 'Exams';

    /**
     *  @name   ExamController
     *  @descr  Create an instance of ExamController class
     */
    public function ExamController(){}

    /**
     * @name    executeAction
     * @param   $action         String      Name of requested action
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

    /********************************************************************
     *                               Exam                               *
     ********************************************************************/

    /**
     *  @name   actionExams
     *  @descr  Show the list of exams
     */
    private function actionExams(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();
    }

    /**
     *  @name   actionShowexaminfo
     *  @descr  Shows all infos about requested exam
     */
    private function actionShowexaminfo(){
        global $log, $engine;

        if(isset($_POST['idExam'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdateexaminfo
     *  @descr  Saves edited informations about an exam
     */
    private function actionUpdateexaminfo(){
        global $ajaxSeparator, $config, $log;

        $db = new sqlDB();
        if((isset($_POST['idExam'])) && (isset($_POST['password']))){
            $newPassword = randomPassword(8);

            if($db->qUpdateExamInfo($_POST['idExam'], null, null, null, null, null, null, $newPassword)){
                echo 'ACK'.$ajaxSeparator.$newPassword;
            }else{
                die($db->getError());
            }

            $db->close();
        }elseif((isset($_POST['idExam'])) && (isset($_POST['name'])) && (isset($_POST['datetime'])) &&
                (isset($_POST['desc'])) && (isset($_POST['regStart'])) && (isset($_POST['regEnd'])) && (isset($_POST['rooms']))){
            $db = new sqlDB();
            if(($db->qUpdateExamInfo($_POST['idExam'], $_POST['name'], $_POST['datetime'], $_POST['desc'],
                                     $_POST['regStart'], $_POST['regEnd'], $_POST['rooms'])) && ($examInfo = $db->nextRowAssoc())){
                $statuses = array('w' => array('Waiting', 'Start'),
                                  's' => array('Started', 'Stop'),
                                  'e' => array('Stopped', 'Start'));
                /*
                $datetime = new DateTime($examInfo['datetime']);
                $day = $datetime->format("d/m/Y");
                $time = $datetime->format("H:i");
                */

                $datetime = strtotime($examInfo['datetime']);
                $day = date('d/m/Y', $datetime);
                $time = date('H:i', $datetime);
                $manage = '<span class="manageButton edit">
                               <img name="edit" src="'.$config['themeImagesDir'].'edit.png"title="'.ttEdit.'" onclick="showExamInfo(this);">
                           </span>
                           <span class="manageButton students">
                               <img name="students" src="'.$config['themeImagesDir'].'users.png" title="'.ttStudents.'" onclick="showStudentsList(this);">
                           </span>
                           <span class="manageButton action">
                               <img name="action" src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][1].'.png" title="'.constant('tt'.$statuses[$examInfo['status']][1]).'" onclick="changeExamStatus(new Array(true, this));">
                           </span>
                           <span class="manageButton archive">
                               <img name="archive" src="'.$config['themeImagesDir'].'Archive.png" title="'.ttArchive.'" onclick="archiveExam();">
                           </span>
                           <span class="manageButton delete">
                               <img name="delete" src="'.$config['themeImagesDir'].'delete.png" title="'.ttDelete.'" onclick="deleteExam(new Array(true, this));">
                           </span>';

                $updatedExam = array(
                    '<img alt="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          title="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][0].'.png">',
                    $day,
                    $time,
                    $examInfo['exam'],
                    $examInfo['subject'],
                    $examInfo['settings'],
                    $examInfo['password'],
                    $manage,
                    $examInfo['idExam'],
                    $examInfo['idSubject'],
                    $examInfo['idTestSetting'],
                    $examInfo['status']
                );
                echo 'ACK'.$ajaxSeparator.str_replace('\\/', '/', json_encode($updatedExam));

            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionShowstudentslist
     *  @descr  Shows the list of registered users for requested exam
     */
    private function actionShowregistrationslist(){
        global $log, $engine;

        if(isset($_POST['idExam'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionNewexam
     *  @descr  Action used to create a new exam
     */
    private function actionNewexam(){
        global $log, $config, $ajaxSeparator;

        if((isset($_POST['name'])) && (isset($_POST['idSubject'])) && (isset($_POST['idTestSettings'])) &&
           (isset($_POST['datetime'])) && (isset($_POST['desc'])) && (isset($_POST['regStart'])) &&
           (isset($_POST['regEnd'])) && (isset($_POST['rooms']))){

            $db = new sqlDB();
            $password = randomPassword(8);
            if(($db->qNewExam($_POST['name'], $_POST['idSubject'], $_POST['idTestSettings'], $_POST['datetime'],
                              $_POST['desc'], $_POST['regStart'], $_POST['regEnd'], $_POST['rooms'], $password)) && ($examInfo = $db->nextRowAssoc())){
                $statuses = array('w' => array('Waiting', 'Start'),
                                  's' => array('Started', 'Stop'),
                                  'e' => array('Stopped', 'Start'));
                /*
                $datetime = new DateTime($examInfo['datetime']);
                $day = $datetime->format("d/m/Y");
                $time = $datetime->format("H:i");
                */





                $datetime = strtotime($examInfo['datetime']);
                $day = date('d/m/Y', $datetime);
                $time = date('H:i', $datetime);

                $log->append($day." ".$time);

                $manage = '<span class="manageButton edit">
                               <img name="edit" src="'.$config['themeImagesDir'].'edit.png"title="'.ttEdit.'" onclick="showExamInfo(this);">
                           </span>
                           <span class="manageButton students">
                               <img name="students" src="'.$config['themeImagesDir'].'users.png" title="'.ttStudents.'" onclick="showStudentsList(this);">
                           </span>
                           <span class="manageButton action">
                               <img name="action" src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][1].'.png" title="'.constant('tt'.$statuses[$examInfo['status']][1]).'" onclick="changeExamStatus(new Array(true, this));">
                           </span>
                           <span class="manageButton archive">
                               <img name="archive" src="'.$config['themeImagesDir'].'Archive.png" title="'.ttArchive.'" onclick="archiveExam(new Array(true, this));">
                           </span>
                           <span class="manageButton delete">
                               <img name="delete" src="'.$config['themeImagesDir'].'delete.png" title="'.ttDelete.'" onclick="deleteExam(new Array(true, this));">
                           </span>';

                $newExam = array(
                    '<img alt="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          title="'.constant('tt'.$statuses[$examInfo['status']][0]).'"
                          src="'.$config['themeImagesDir'].$statuses[$examInfo['status']][0].'.png">',
                    $day,
                    $time,
                    $examInfo['exam'],
                    $examInfo['subject'],
                    $examInfo['settings'],
                    $examInfo['password'],
                    $manage,
                    $examInfo['idExam'],
                    $examInfo['idSubject'],
                    $examInfo['idTestSetting'],
                    $examInfo['status']
                );
                echo 'ACK'.$ajaxSeparator.str_replace('\\/', '/', json_encode($newExam));

            }else{
                die($db->getError());
            }
            $db->close();
        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST = '.var_export($_POST));
        }
    }

    /**
     *  @name   actionDeleteexam
     *  @descr  Deletes selected exam
     */
    private function actionDeleteexam(){
        global $log;

        if(isset($_POST['idExam'])){
            $db = new sqlDB();
            if($db->qDeleteExam($_POST['idExam'])){
                echo 'ACK';
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST = '.var_export($_POST));
        }
    }

    /**
     *  @name   actionChangestatus
     *  @descr  Starts and Stops requested exam
     */
    private function actionChangestatus(){
        global $log;

        if((isset($_POST['idExam'])) && (isset($_POST['action']))){
            $db = new sqlDB();
            if(($db->qSelect('Exams', 'idExam', $_POST['idExam'])) && ($exam = $db->nextRowAssoc())){
                switch($_POST['action']){
                    case 'start' :
                        if($exam['status'] == 'a'){
                            die(ttEExamArchived);
                        }elseif($db->qChangeExamStatus($_POST['idExam'], 's')){
                            echo 'ACK';
                        }else{
                            die($db->getError());
                        } break;
                    case 'stop' :
                        if($exam['status'] == 'w'){
                            die(ttEExamWaiting);
                        }elseif($exam['status'] == 'a'){
                            die(ttEExamArchived);
                        }elseif($db->qChangeExamStatus($_POST['idExam'], 'e')){
                            echo 'ACK';
                        }else{
                            die($db->getError());
                        }break;
                    default :
                        $log->append(__FUNCTION__." : action not set");
                }
            }else{
                die(ttEExamNotFound);
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionArchiveExam
     *  @descr  Archives requested exam
     */
    private function actionArchiveexam(){
        global $log;

        if(isset($_POST['idExam'])){
            $db = new sqlDB();
            if(($db->qSelect('Exams', 'idExam', $_POST['idExam'])) && ($examInfo = $db->nextRowAssoc())){
                if($examInfo['status'] == 'a'){
                    die(ttEExamArchived);
                }elseif(($db->qSelect("Tests", "fkExam", $_POST['idExam'])) && ($tests = $db->getResultAssoc('idTest'))){
                    if(($db->qSelect("TestSettings", "idTestSetting", $examInfo['fkTestSetting'])) && ($examSettings = $db->nextRowAssoc())){
                        $scale = $examSettings['scale'];
                        $allowNegative = ($examSettings['negative'] == 0)? false : true;
                        foreach($tests as $idTest => $testInfo){
                            switch($testInfo['status']){
                                case 'w':
                                case 's':
                                case 'b': if(!$db->qArchiveTest($idTest, $correctScores=array(), $scoreTest=null, $bonus='0', $scoreFinal='0', $scale=0.0, $allowNegative, $status=$testInfo['status']))
                                             die($db->getError()); break;
                                case 'e': if(!$db->qArchiveTest($idTest, $correctScores=array(), $testInfo['scoreTest'], $testInfo['bonus'], $scoreFinal=round($testInfo['scoreTest']+$testInfo['bonus']), $scale, $allowNegative))
                                             die($db->getError()); break;
                            }
                        }
                        if($db->qArchiveExam($_POST['idExam'])){
                            echo 'ACK';
                        }else{
                            die($db->getError());
                        }
                    }
                }
            }else{
                die(ttEExamNotFound);
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                          Test Settings                           *
     ********************************************************************/

    /**
     *  @name   actionSettings
     *  @descr  Show the list of settings for selected subject
     */
    private function actionSettings(){
        global $config, $engine,$user;

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
            if(($user->role=='t') || ($user->role=='at'))
                header('Location: index.php?page=subject&r=set');
            else
                header('Location: index.php?page=subject/index2&r=set');
        }
    }

    /**
     *  @name   actionShowsettingsinfo
     *  @descr  Show info about an exam's settings
     */
    private function actionShowsettingsinfo(){
        global $engine, $log;

        if((isset($_POST['idTestSetting'])) && (isset($_POST['action']))){
            $engine->loadLibs();
            $engine->renderPage();

        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionUpdatesettingsinfo
     *  @descr  Save edited informations about a test settings
     */
    private function actionUpdatesettingsinfo(){
        global $ajaxSeparator, $log;

        if((isset($_POST['idTestSetting'])) && (isset($_POST['name'])) && (isset($_POST['scoreType'])) &&
            (isset($_POST['scoreMin'])) && (isset($_POST['bonus'])) && (isset($_POST['duration'])) &&
            (isset($_POST['negative'])) &&(isset($_POST['editable'])) &&
            (isset($_POST['questions'])) && (isset($_POST['desc'])) && (isset($_POST['questionsT'])) &&
            (isset($_POST['questionsD'])) && (isset($_POST['questionsM'])) && (isset($_POST['completeUpdate']))){

            $db = new sqlDB();
            if($_POST['completeUpdate'] == 'true'){
                if($db->qGetEditAndDeleteConstraints('edit', 'testsetting', array($_POST['idTestSetting']))){
                    if($db->numResultRows() > 0)
                        die('ACK'.$ajaxSeparator.ttEExamsNotArchivedEditTestSettings);
                    else{
                        $totQuestions = $_POST['questions'];
                        $questionsT = json_decode($_POST['questionsT'], true);
                        $questionsD = json_decode($_POST['questionsD'], true);
                        $questionsM = explode('&', $_POST['questionsM']);

                        $distributionMatrix = $this->calcQuestionsDistribution($questionsT, $questionsD, $totQuestions);

                        $db = new sqlDB();
                        if($db->qUpdateTestSettingsInfo($_POST['idTestSetting'], $_POST['completeUpdate'],
                                                        $_POST['name'], $_POST['desc'], $_POST['scoreType'], $_POST['scoreMin'],
                                                        $_POST['bonus'], $_POST['negative'], $_POST['editable'],
                                                        $_POST['duration'], $_POST['questions'],
                                                        $distributionMatrix, $questionsT, $questionsD, $questionsM)){
                            echo 'ACK'.$ajaxSeparator.'ACK';
                        }else{
                            echo $db->getError();
                        }
                    }
                    $db->close();
                }
            }else{
                if($db->qUpdateTestSettingsInfo($_POST['idTestSetting'], $_POST['completeUpdate'],
                                                $_POST['name'], $_POST['desc'])){
                    echo 'ACK'.$ajaxSeparator.'ACK';
                }else{
                    echo $db->getError();
                }
                $db->close();
            }
        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST = '.var_export($_POST, true));
        }
    }

    /**
     *  @name   actionNewsettings
     *  @descr  Show page to create a new test settings
     */
    private function actionNewsettings(){
        global $engine, $log, $ajaxSeparator;

        if((isset($_POST['name'])) && (isset($_POST['scoreType'])) &&
            (isset($_POST['scoreMin'])) && (isset($_POST['bonus'])) &&
            (isset($_POST['negative'])) &&(isset($_POST['editable'])) &&
            (isset($_POST['duration'])) && (isset($_POST['questions'])) &&
            (isset($_POST['desc'])) &&(isset($_POST['questionsT'])) &&
            (isset($_POST['questionsD'])) && (isset($_POST['questionsM'])) &&
            (isset($_SESSION['idSubject']))){

            $totQuestions = $_POST['questions'];
            $questionsT = json_decode($_POST['questionsT'], true);
            $questionsD = json_decode($_POST['questionsD'], true);
            $questionsM = explode('&', $_POST['questionsM']);

            $distributionMatrix = $this->calcQuestionsDistribution($questionsT, $questionsD, $totQuestions);

            $db = new sqlDB();
            if(($db->qNewSettings($_SESSION['idSubject'], $_POST['name'], $_POST['scoreType'], $_POST['scoreMin'],
                                  $_POST['bonus'], $_POST['negative'], $_POST['editable'], $_POST['duration'], $_POST['questions'], $_POST['desc'],
                                  $distributionMatrix, $questionsT, $questionsD, $questionsM)) && ($idNewSetting = $db->nextRowEnum())){
                echo 'ACK'.$ajaxSeparator.$idNewSetting[0];
            }else{
                die($db->getError());
            }
            $db->close();

        }else{
            $log->append(__FUNCTION__.' : Params not set - $_POST: '.var_export($_POST,true));
        }
    }

    /**
     * @name    calcQuestionsDistribution
     * @descr   Returns matrix questions assignament per topics
     * @param   $questionsT         Array       Questions distribution per topic
     * @param   $questionsD         Array       Questions distribution per difficulty
     * @param   $totQuestions       String      Number of total questions per test
     * @return  Array
     */
    private function calcQuestionsDistribution($questionsT, $questionsD, $totQuestions){
        global $log;

        $distributionMatrix = $approxMatrix = array();

//        $log->append('********************************* Store Into distributionMatrix *********************************');

        foreach($questionsT as $idTopic => $arrayQuestionsT)
            if($arrayQuestionsT != null)
                foreach(range(1, getMaxQuestionDifficulty()) as $difficulty)
                    $distributionMatrix[$difficulty][$idTopic] = ($arrayQuestionsT['random'] * $questionsD[$difficulty]['random']) / $totQuestions;

//        $log->append('distributionMatrix : '.var_export($distributionMatrix, true));
//        $log->append('********************************* Approx and Update All Matrix **********************************');

        $assignedForDifficulties = array_fill(1, getMaxQuestionDifficulty(), 0);
        $assignedForTopics = array();

        foreach($distributionMatrix as $difficulty => $arrayQuestionsT){
            foreach($arrayQuestionsT as $idTopic => $questionsNum){
                $decimal = $questionsNum - floor($questionsNum);

                if($decimal== 0){
                    $approxMatrix[$difficulty][$idTopic] = 0;
                }else if($decimal > 0 && $decimal <= 0.16){
                    $distributionMatrix[$difficulty][$idTopic] = floor($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = -1;
                }else if($decimal > 0.16 && $decimal <= 0.33){
                    $distributionMatrix[$difficulty][$idTopic] = floor($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = -2;
                }else if($decimal > 0.33 && $decimal <= 0.49){
                    $distributionMatrix[$difficulty][$idTopic] = floor($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = -3;
                }else if($decimal > 0.49 && $decimal <= 0.66){
                    $distributionMatrix[$difficulty][$idTopic] = ceil($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = 3;
                }else if($decimal > 0.66 && $decimal <= 0.82){
                    $distributionMatrix[$difficulty][$idTopic] = ceil($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = 2;
                }else if($decimal > 0.82 && $decimal <= 0.99){
                    $distributionMatrix[$difficulty][$idTopic] = ceil($questionsNum);
                    $approxMatrix[$difficulty][$idTopic] = 1;
                }

                $assignedForDifficulties[$difficulty] += $distributionMatrix[$difficulty][$idTopic];
                $assignedForTopics[$idTopic] = isset($assignedForTopics[$idTopic])? $assignedForTopics[$idTopic] + $distributionMatrix[$difficulty][$idTopic] : $distributionMatrix[$difficulty][$idTopic];

            }
        }

//        $log->append('distributionMatrix : '.var_export($distributionMatrix, true));
//        $log->append('approxMatrix : '.var_export($approxMatrix, true));
//        $log->append('assignedForDifficulties : '.var_export($assignedForDifficulties, true));
//        $log->append('assignedForTopics : '.var_export($assignedForTopics, true));
//        $log->append('******************************** Adjust Questions Assignaments **********************************');

        foreach(range(1, getMaxQuestionDifficulty()) as $difficulty){
            $gap = $assignedForDifficulties[$difficulty] - $questionsD[$difficulty]['random'];
//            $log->append('$gap'."$difficulty : $gap");
            if($gap > 0){                               // Too many random questions assigned
                                                        // for this difficulty, so remove some
                arsort($approxMatrix[$difficulty]);
                foreach($approxMatrix[$difficulty] as $idTopic => $approximation){
                    if($gap > 0){
                        if($assignedForTopics[$idTopic] > $questionsT[$idTopic]['random']){
                            $distributionMatrix[$difficulty][$idTopic]--;
                            $assignedForTopics[$idTopic]--;
                            $gap--;
                        }
                    }else break;
                }
            }else if($gap < 0){                         // Too few random questions assigned
                                                        // for this difficulty, so add more
                asort($approxMatrix[$difficulty]);
                foreach($approxMatrix[$difficulty] as $idTopic => $approximation){
                    if($gap < 0){
                        if($assignedForTopics[$idTopic] < $questionsT[$idTopic]['random']){
                            $distributionMatrix[$difficulty][$idTopic]++;
                            $assignedForTopics[$idTopic]++;
                            $gap++;
                        }
                    }
                }
            }
        }

//        $log->append('distributionMatrix : '.var_export($distributionMatrix, true));

        return $distributionMatrix;
    }

    /**
     *  @name   actionDeletesettings
     *  @descr  Delete requested settinga info (if possible)
     */
    private function actionDeletesettings(){
        global $log;

        if(isset($_POST['idTestSetting'])){
            $db = new sqlDB();
            if($db->qSelect('Exams', 'fkTestSetting', $_POST['idTestSetting'])){
                $error = false;
                while(($row = $db->nextRowAssoc()) && (!$error)){
                    if($row['status'] != 'a'){                         // At least one exam isn't archived
                        $error = true;                                 // so, do nothing
                    }
                }
                if($error){
                    die(ttEExamsNotArchivedDeleteTestSettings);
                }else{
                    if($db->qDeleteTestSettings($_POST['idTestSetting'])){
                        echo 'ACK';
                    }else{
                        die($db->getError());
                    }
                }
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     * @name    actionTestsettingslist
     * @descr   Shows test settings list for requested subject
     */
    private function actionTestsettingslist() {
        global $log;

        if($_POST['idSubject']){
            $db = new sqlDB();
            if($_POST['idSubject'] == '-1'){
                echo ttSelectSubjectBefore;
            }else{
                if($db->qSelect('TestSettings', 'fkSubject', $_POST['idSubject'], 'name')){
                    if($db->numResultRows() > 0){
                        if($testsetting = $db->nextRowAssoc()){
                            echo '<dt class="writable"><span>'.$testsetting['name'].'<span class="value">'.$testsetting['idTestSetting'].'</span></span></dt>
                                  <dd><ol>
                                  <li>'.$testsetting['name'].'<span class="value">'.$testsetting['idTestSetting'].'</span></li>';
                            while($testsetting = $db->nextRowAssoc()){
                                echo '<li>'.$testsetting['name'].'<span class="value">'.$testsetting['idTestSetting'].'</span></li>';
                            }
                            echo '</ol></dd>';
                        }
                    }else{
                        echo ttNoSettings;
                    }

                }else{
                    die($db->getError());
                }
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /********************************************************************
     *                               Test                               *
     ********************************************************************/

    /**
     *  @name   actionCorrect
     *  @descr  Shows page to correct test or store test details into history table
     */
    private function actionCorrect(){
        global $log, $engine;

        if((isset($_POST['idTest'])) && (isset($_POST['correctScores'])) &&
           (isset($_POST['scoreTest'])) &&
           (isset($_POST['bonus'])) && (isset($_POST['scoreFinal']))){

            $db = new sqlDB();
            if(($db->qTestDetails(null,$_POST['idTest']) && ($testInfo = $db->nextRowAssoc()))){
                $allowNegative = ($testInfo['negative'] == 0)? false : true;
                if($db->qArchiveTest($_POST['idTest'], json_decode(stripslashes($_POST['correctScores']), true),
                                     $_POST['scoreTest'], $_POST['bonus'], $_POST['scoreFinal'], $testInfo['scale'], $allowNegative)){
                    echo 'ACK';
                }else{
                    die($db->getError());
                }
            }else{
                die($db->getError());
            }
        }elseif(isset($_POST['idTest'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header("Location: index.php?page=exam/exams");
        }
    }

    /**
     *  @name   actionToggleblock
     *  @descr  Action to block/unblock student's test
     */
    private function actionToggleblock(){
        global $log, $ajaxSeparator;

        if(isset($_POST['idTest'])){
            $db = new sqlDB();
            if(($db->qSelect('Tests', 'idTest', $_POST['idTest'])) && ($test = $db->nextRowAssoc())){
                switch($test['status']){
                    case 'w' :
                    case 's' : echo ($db->qUpdateTestStatus($_POST['idTest'], 'b'))? 'ACK'.$ajaxSeparator.'b' : $db->getError();
                               break;
                    case 'b' : if($test['timeStart'] != ''){
                                   echo ($db->qUpdateTestStatus($_POST['idTest'], 's'))? 'ACK'.$ajaxSeparator.'s' : $db->getError();
                               }else{
                                   echo ($db->qUpdateTestStatus($_POST['idTest'], 'w'))? 'ACK'.$ajaxSeparator.'w' : $db->getError();
                               }
                               break;
                    case 'e' :
                    case 'a' : echo ttETestAlreadyArchived; break;
                }
            }else{
                die($db->getError());
            }
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionView
     *  @descr  Show page to View test
     */
    private function actionView(){
        global $log, $engine;

        if(isset($_POST['idTest'])){
            $engine->renderDoctype();
            $engine->loadLibs();
            $engine->renderHeader();
            $engine->renderPage();
            $engine->renderFooter();
        }else{
            header("Location: index.php?page=exam/exams");
        }
    }

    /********************************************************************
     *                               User                               *
     ********************************************************************/

    /**
     *  @name   actionShowaddstudentspanel
     *  @descr  Shows panel to add new registrations to the exam
     */
    private function actionShowaddstudentspanel(){
        global $log, $engine;

        if(isset($_POST['idExam'])){
            $engine->loadLibs();
            $engine->renderPage();
        }else{
            $log->append(__FUNCTION__." : Params not set");
        }
    }

    /**
     *  @name   actionRegisterstudents
     *  @descr  Register students to requested exam
     */
    private function actionRegisterstudents(){
        global $log;
        if((isset($_POST['idExam'])) && (isset($_POST['students']))){
            $db = new sqlDB();
            $students = explode('&', $_POST['students']);
            foreach($students as $student){
                if($db->qCheckRegistration($_POST['idExam'], $student)){
                    if($db->numResultRows() == 0){
                        if($db->qMakeQuestionsSet($_POST['idExam'], $student)){

                        }else{
                            die($db->getError());
                        }
                    }
                }else{
                    die(ttEDatabase);
                }
            }
            echo "ACK";
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
                'actions' => array('Settings', 'Showsettingsinfo', 'Updatesettingsinfo', 'Newsettings', 'Deletesettings',
                                   'Exams', 'Showexaminfo', 'Deleteexam', 'Testsettingslist', 'Updateexaminfo', 'Newexam', 'Changestatus',
                                   'Showregistrationslist', 'Showaddstudentspanel', 'Registerstudents', 'Toggleblock', 'Correct', 'View', 'Archiveexam'),
                'roles'   => array('t','e'),
            ),
            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }

}
