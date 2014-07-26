<?php
/**
 * File: language.php
 * User: Masterplan
 * Date: 7/5/13
 * Time: 12:51 PM
 * Desc: Shows language edit page
 */

global $config, $user;

$xmlFrom = new DOMDocument();
$xmlFrom->load($config['systemLangsXml'].$user->lang.'.xml');
$langFrom = $xmlFrom->getElementById('name')->nodeValue.' ('.$xmlFrom->getElementById('alias')->nodeValue.')';

$xmlTo = new DOMDocument();
$xmlTo->load($config['systemLangsXml'].$_POST['alias'].'.xml');
$langTo = $xmlTo->getElementById('name')->nodeValue.' ('.$xmlTo->getElementById('alias')->nodeValue.')';

?>

<div id="navbar">
    <?php printMenu(); ?>
</div>

<div id="main">
    <div>

        <?php
        openBox(ttLanguage.": $langFrom --> $langTo", 'normal', 'language');

        $textsFrom = $xmlFrom->getElementsByTagName('text');

        $class = 'odd';
        for($index = 0; $index < $textsFrom->length; $index++){
            $class = ($class == 'odd') ? 'even' : 'odd';

            $elementID = $textsFrom->item($index)->getAttribute('id');
            echo '<div class="'.$class.' language bPad" id="'.$elementID.'">
                    <div class="left">'.
                        str_replace('\n', '¶<br/>', $textsFrom->item($index)->nodeValue).
                    '</div>
                    <textarea class="language left">';
            $xmlToElement = $xmlTo->getElementById($elementID);
            if($xmlToElement != null)
                echo str_replace('\n', "\n", $xmlToElement->nodeValue);
            echo '</textarea>
                    <div class="clearer"></div>
                 </div>';

        }
        ?>

        <a class="ok button right lSpace tSpace" id="update" onclick="saveLanguageFiles();"><?= ttSaveLanguageFiles ?></a>
        <a class="normal button right lSpace tSpace" id="save" onclick="saveLanguageXML();"><?= ttSaveLanguageXML ?></a>
        <a class="normal button left tSpace" id="cancel" onclick="window.location = 'index.php';"><?= ttCancel ?></a>

        <div class="clearer"></div>

        <?php
        closeBox();
        ?>

        <div class="clearer"></div>

    </div>
</div>
<script type="text/javascript">
    alias = "<?= $xmlTo->getElementById('alias')->nodeValue ?>";
</script>