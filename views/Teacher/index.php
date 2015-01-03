<?php
/**
 * File: index.php
 * User: Masterplan
 * Date: 3/21/13
 * Time: 8:44 PM
 * Desc: Teacher Homepage
 */

global $config, $user;

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div id="examsTableMinContainer">
        <?php
        $db = new sqlDB();
        if($db->qExamsInProgress($user->id)){
            echo '<table id="homeExamsTable" class="hover stripe order-column">
                      <thead>
                          <tr>
                              <th class="eStatus"></th>
                              <th class="eName">'.ttName.'</th>
                              <th class="eSubject">'.ttSubject.'</th>
                              <th class="eDay">'.ttDay.'</th>
                              <th class="eTime">'.ttTime.'</th>
                              <th class="eExamID"></th>
                          </tr>
                      </thead>
                      <tbody>';
            $statuses = array('w' => 'Waiting',
                              's' => 'Started',
                              'e' => 'Stopped');
            while($examInfo = $db->nextRowAssoc()){
                $status = '<img alt="'.$examInfo['status'].'" src="'.$config['themeImagesDir'].$statuses[$examInfo['status']].'.png"
                                title="'.constant('tt'.$statuses[$examInfo['status']]).'"/>';
                $exam = $examInfo['examName'];
                $subject = $examInfo['subjectName'];
                $datetime = new DateTime($examInfo['datetime']);
                $day = $datetime->format("d/m/Y");
                $time = $datetime->format("H:i");
                $idExam = $examInfo['idExam'];

                echo '<tr>
                          <td>'.$status.'</td>
                          <td>'.$exam.'</td>
                          <td>'.$subject.'</td>
                          <td>'.$day.'</td>
                          <td>'.$time.'</td>
                          <td>'.$idExam.'</td>
                      </tr>';
            }
            echo '</tbody>
              </table>';
        }else{
            echo $db->getError();
        }
        ?>
    </div>
    <div id="testsTableContainer">
        <?php
        $db = new sqlDB();
        $db2 = new sqlDB();
        if($db->qTestsList($user->id)){
            echo '<table id="homeTestsTable" class="hover stripe order-column">
                      <thead>
                          <tr>
                              <th class="tName">'.ttName.'</th>
                              <th class="tSubject">'.ttSubject.'</th>
                              <th class="tTime">'.ttTimeUsed.'</th>
                              <th class="tTestID"></th>
                          </tr>
                      </thead>
                      <tbody>';
            while($test = $db->nextRowAssoc()){
                if($test['status'] == 'e'){

                    $subject = $test['subName'];
                    $idTest = $test['idTest'];
                    $start = new DateTime($test['timeStart']);
                    $end = new DateTime($test['timeEnd']);
                    $diff = $start->diff($end);
                    $time = $diff->format("%H:%I:%S");

                    echo '<tr>
                              <td>'.$test['surname'].' '.$test['name'].'</td>
                              <td>'.$subject.'</td>
                              <td>'.$time.'</td>
                              <td>'.$idTest.'</td>
                          </tr>';
                }
            }
            echo '</tbody>
             </table>';
        }
        ?>
    </div>
    <div class="clearer"></div>
</div>

<form action="" method="post" id="form" target="_blank">
    <input type="hidden" name="idExam" value="">
    <input type="hidden" name="idTest" value="">
</form>