<?php
/**
 * File: ImportQMController.php
 * User: Elia
 * Date: 5/21/15
 * Time: 10:45 AM
 * Desc: Controller for QM import operations
 */

class ImportQMController extends Controller{

    /**
     *  @name   ImportQMController
     *  @descr  Create an instance of ImportQMController class
     */
    public function ImportQMController (){}

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
     *  @descr  Show import index page
     */
    private function actionImportpage(){
        global $engine;

        $engine->renderDoctype();
        $engine->loadLibs();
        $engine->renderHeader();
        $engine->renderPage();
        $engine->renderFooter();

    }

    /**
     *  @name   actionInit
     *  @descr  Perform a  init importQM procedure
     */

    private function actionInitimport(){
        global $config, $log;
        if(file_exists($config['importQMDir'])){
            echo 'Questions Folder: <span style=\'color:green; font-weight:bold\'>Found</span></br></br>';
        }
        else {
            echo 'Questions Folder: <span style=\'color:red; font-weight:bold\'>Not Found</span></br></br>';

        }
    }


    /**
     *  @name   actionPrepareimport
     *  @descr  Prepare importQM procedure
     */

    private function actionPrepareimport()
    {

        global $config, $log;
        $dir_iterator = new RecursiveDirectoryIterator($config['importQMDir']);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);
        $i = 0;
        $fileCounter = 0;
        $questionCounter=0;
        //OTTENGO LE INFO NECESSARIE SULLE DOMANDE IN PREPARAZIONE ALL'IMPORT
        foreach ($iterator as $file) {
            if ($file->isfile()) {
                $path = pathinfo($file);
                if ($path['extension'] == 'xml' && filesize($file) > 0) {
                    $xml = file_get_contents($file) or die("Error: Cannot create object");

                    $xml = ImportQMController::fixImportErrors($xml);
                    $root = new SimpleXMLElement($xml);
                    $fileCounter++;

                    foreach ($root->children() as $item) {
                        $questionCounter++;
                        $itemtype = $item->itemmetadata->qmd_itemtype;
                        $questionsTypeArray[$i] = $itemtype;
                        $i++;

                    }

                }
            }
        }

        $questionsTypeArray = array_unique($questionsTypeArray);
        $i = 0;
        foreach ($questionsTypeArray as $key => $value) {
            $res[$i] = $value;
            //echo $res[$i]."<br>";
            $i++;
        }

        echo "<strong>".$fileCounter."</strong> XML Files Found<br/>";
        echo "<strong>".$questionCounter."</strong> Questions Found<br/><br/>";


    }

    /**
     *  @name   actionImport
     *  @descr  Perform a importQM procedure
     */
    private function actionImport()
    {
        global $config, $log;
        $dir_iterator = new RecursiveDirectoryIterator($config['importQMDir']);
        $iterator = new RecursiveIteratorIterator($dir_iterator, RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $file) {
            if ($file->isfile()) {
                $path = pathinfo($file);
                if ($path['extension'] == 'xml' && filesize($file) > 0) {
                    $xml = file_get_contents($file) or die("Error: Cannot create object");


                    $xml = ImportQMController::fixImportErrors($xml);

                    //PATH DELLE IMMAGINI
                    $xml = str_replace("%SERVER.GRAPHICS%", "../../", $xml);


                    $root = new SimpleXMLElement($xml);


                    foreach ($root->children() as $item) {

                        $itemtype = $item->itemmetadata->qmd_itemtype;


                        switch ($itemtype) {
                            case 'Multiple Response':
                                //parser($item);
                                break;

                        }

                    }

                }
            }
        }
    }




    /**
     * @name fixImportErrors
     * @param String $xml root $xml
     * @return Array $xml root $xml
     */

    private static function fixImportErrors($xml){
        //FIX ERROR GENERATED BY EXPORT
        $xml=str_replace("&apos","&apos;",$xml);
        $xml=str_replace("^","&and;",$xml);
        $xml=str_replace("&lt","&lt;",$xml);
        $xml=str_replace("&gt","&gt;",$xml);
        $xml=str_replace("&auml;","&#228;",$xml);
        $xml=str_replace("><</", ">&lt;</", $xml);
        if(strpos($xml,"</questestinterop>")==false){
            $xml=$xml."</questestinterop>";
        }
        return $xml;


    }

    /**
     * @name parsingQPath
     * @param String $qpath question path
     * @return Array $res quesion info
     */
    private static function parsingQPath($qPath){

        //ARRAY COD -> NAME SUBJECTS
        $subjectsList['ac3']='Analyical Chemistry 3';
        $subjectsList['bc3']='Biological Chemistry 3';
        $subjectsList['cc4']='Computational Chemistry 4';
        $subjectsList['ce3']='Chemical Engineering 3';
        $subjectsList['ch4']='Cultural Heritage 4';
        $subjectsList['gc1']='General Chemistry 1';
        $subjectsList['gc2']='General Chemistry 2';
        $subjectsList['gc']='General Chemistry';
        $subjectsList['ic3']='Inorganic Chemistry 3';
        $subjectsList['oc3']='Organic Chemistry 3';
        $subjectsList['pc3']='Physical Chemistry 3';
        $subjectsList['mc']=' mc ';
        $subjectsList['xxx']='END';

        //CREATE ASSOCIATIVE ARRAY FROM PATH STRING
        if(substr_count($qPath,"/")>0)
            $parts=explode("/", $qPath);
        else
            $parts=explode("\\", $qPath);

        //GET ARRAY $parts LENGTH
        $lenParts=count($parts);

        //MANAGE ALL CASES
        switch($lenParts){
            case 2:
                $subjectName=strtolower($parts[0]);
                $difficulty=1;
                $TopicName=$parts[1];

                //GESTISCO IL CASO UNICO cdb_mc_v400en
                if($subjectName=='cdb_mc_v400en'){

                    $sbjName='mc';
                    $sbjLang='english';
                    $version='4.00';
                    $difficulty=1;

                }
                break;
            case 3:
                $subjectName=strtolower($parts[0]);
                $difficulty=substr($parts[1],strlen($parts[1])-1,1);
                $TopicName=$parts[2];
                break;
            case 4:
                $subjectName=strtolower($parts[0]);
                $difficulty=substr($parts[2],strlen($parts[2])-1,1);
                $TopicName=$parts[3];
                break;

        }

        if(substr_count($subjectName,"version")>0){
            $SubjectParts=explode("version",$subjectName);
            $SubjectNameAndLang=explode(" ",$SubjectParts[0]);
            $sbjName=$SubjectNameAndLang[1];
            $sbjLang=$SubjectNameAndLang[2];
            $version=$SubjectParts[1];
            if(substr($version,2,1)!='.'){
                $version=substr($version,1,1).'.'.substr($version,2,2);
            }
            $version=floatval($version);

        }

        //PREPARE RESULTS ASSOCIATIVE ARRAY
        $TopicNames=explode(' - ',$TopicName);
        if(count($TopicNames)>1)
            $res['topicName']=$TopicNames[1];
        else
            $res['topicName']=$TopicNames[0];

        $res['sbjName']=$subjectsList[$sbjName];
        $res['sbjLang']=$sbjLang;
        $res['sbjVers']=$version;
        $res['topicDifficulty']=$difficulty;

        return $res;


    }




    /**
     *  @name   accessRules
     *  @descr  Returns all access rules for Login controller's actions:
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
                'actions' => array('Initimport','Prepareimport','Import','Importpage'
                ),
                'roles'   => array('a'),
            ),

            array(
                'deny',
                'actions' => array('*'),
                'roles'   => array('*'),
            ),
        );
    }

}