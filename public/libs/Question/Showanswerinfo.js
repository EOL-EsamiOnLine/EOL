/**
 * File: Showanswerinfo.js
 * User: Masterplan
 * Date: 19/05/14
 * Time: 11:34
 * Desc: Show answer's info with all translations and CKEditors
 */

$(function(){

    /**
     *  @descr  Function to enable answer tab's dropdownInfo menu effects
     */
    $("#answerOptions .dropdownInfo dt.writable").on("click", function() {
                                                         $(this).children("span").toggleClass("clicked");
                                                         $(this).next().children("ol").slideToggle(200);
                                                     });

    /**
     *  @descr  Function to change infos
     */
    $("#answerOptions .dropdownInfo dd ol li").on("click", function() {
                                                               answerEditing = true;
                                                               updateDropdown($(this));
                                                           });
});

/**
 *  @name   saveAnswerInfo
 *  @descr  Binded function to save answer info
 *  @param  close       Boolean                     Close panel if true
 */
function saveAnswerInfo(close){
    var questionType = questionsTable.row(questionRowEdit).data()[qtci.typeID];
    var score = "";
    switch(questionType){
        case "MR" : score = $("#answerScore").val(); break;
        case "MC" : score = $("#answerScore").find("dt span span").text(); break;
        default : score = "0";
    }
    var answerTranslations = new Array();
    var langAlias = "";
    var langID = "";
    var mainTranslation = "";
    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
        langID = allLangs[indexLang]["idLanguage"];
        langAlias = allLangs[indexLang]["alias"];
        answerTranslations[langID] = CKEDITOR.instances["at"+(langAlias.toUpperCase())].getData();
        if(langID == mainLang)
            mainTranslation = CKEDITOR.instances["at"+(langAlias.toUpperCase())].document.getBody().getText();
    }
    $.ajax({
        url     : "index.php?page=question/updateanswerinfo",
        type    : "post",
        data    : {
            idQuestion      :   questionsTable.row(questionRowEdit).data()[qtci.questionID],
            idAnswer        :   $(answerRowSelected).attr("value"),
            translationsA   :   JSON.stringify(answerTranslations),
            score           :   score,
            mainLang        :   mainLang
        },
        success : function (data) {
//            alert(data);
            data = data.trim().split(ajaxSeparator);
            if(data[0] == "ACK"){
                if(mainTranslation.length > aMaxLength)
                    $(answerRowSelected).html(mainTranslation.substring(0, (aMaxLength - ellipsis.length))+ellipsis);
                else
                    $(answerRowSelected).html(mainTranslation.substring(0, aMaxLength));
                $(answerRowSelected).attr("value", data[2]);
                showSuccessMessage(ttMEdit);
                questionsTable.cell(questionsTable.row(questionRowEdit).index(), qtci.questionID).data(data[1]);
                showQuestionLanguageAndPreview(questionRowSelected);
                resetCKEditorInstances('at', false, false);
                answerEditing = false;
                if(close)
                    closeQuestionInfo(false);
            }else{
//                alert(data);
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}

/**
 *  @name   deleteAnswer
 *  @descr  Binded function to delete answer
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function deleteAnswer(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteAnswer, deleteAnswer, false)))){
        var idAnswer = $(answerRowSelected).attr("value");
        var idQuestion = questionsTable.row(questionRowEdit).data()[qtci.questionID];
        $.ajax({
            url     : "index.php?page=question/deleteanswer",
            type    : "post",
            data    : {
                idQuestion  :   idQuestion,
                idAnswer    :   idAnswer
            },
            success : function (data) {
                data = data.split(ajaxSeparator);
                if(data[0] == "ACK"){
//                    alert(data);
                    $(answerRowSelected).parent().remove();
                    answerRowSelected = null;
                    var langAlias = "";
                    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
                        langAlias = allLangs[indexLang]["alias"].toUpperCase();
                        CKEDITOR.instances["at"+langAlias].setData("");
                        CKEDITOR.instances["at"+langAlias].resetUndo();
                    }
                    $("#answerOptions").html("");
                    $("#saveAnswer, #saveAnswerAndExit, #deleteAnswer").addClass("invisible");
                    showSuccessMessage(ttMAnswerDeleted);
                    questionsTable.cell(questionsTable.row(questionRowEdit).index(), qtci.questionID).data(data[1]);
                    showQuestionLanguageAndPreview(questionRowSelected);
                }else{
//                    alert(data);
                    errorDialog(ttError, data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   cancelNewAnswer
 *  @descr  Binded function to cancel new answer process
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function cancelNewAnswer(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNewAnswer, false)))){
        $("#answersList .boxContent ul li a[value='new']").parent().remove();
        resetCKEditorInstances('at', true, true);
        $("#answerOptions").html("");
        answerEditing = false;
        newAnswer = false;
    }
}

/**
 *  @name   createNewAnswer
 *  @descr  Binded event to create a new answer
 *  @param  close       Boolean     If true close questionInfo panel
 */
function createNewAnswer(close){
    var questionType = questionsTable.row(questionRowEdit).data()[qtci.typeID];
    var score = "";
    switch(questionType){
        case "MR" : score = $("#answerScore").val(); break;
        case "MC" : score = $("#answerScore").find("dt span span").text(); break;
        default : score = "0";
    }
    var answerTranslations = new Array();
    var langAlias = "";
    var langID = "";
    var mainTranslation = "";
    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
        langID = allLangs[indexLang]["idLanguage"];
        langAlias = allLangs[indexLang]["alias"];
        answerTranslations[langID] = CKEDITOR.instances["at"+(langAlias.toUpperCase())].getData();
        if(langID == mainLang)
            mainTranslation = CKEDITOR.instances["at"+(langAlias.toUpperCase())].document.getBody().getText();
    }
    $.ajax({
        url     : "index.php?page=question/newanswer",
        type    : "post",
        data    : {
            idQuestion      :   questionsTable.row(questionRowEdit).data()[qtci.questionID],
            score           :   score,
            translationsA   :   JSON.stringify(answerTranslations),
            mainLang        :   mainLang
        },
        success : function (data) {
//            alert(data);
            data = data.trim().split(ajaxSeparator);
            if(data[0] == "ACK"){
                if(mainTranslation.length > aMaxLength)
                    $(answerRowSelected).html(mainTranslation.substring(0, (aMaxLength - ellipsis.length))+ellipsis);
                else
                    $(answerRowSelected).html(mainTranslation.substring(0, aMaxLength));
                $(answerRowSelected).attr("value", data[2]);
                showSuccessMessage(ttMEdit);
                questionsTable.cell(questionsTable.row(questionRowEdit).index(), qtci.questionID).data(data[1]);
                showQuestionLanguageAndPreview(questionRowSelected);
                resetCKEditorInstances('at', false, false);
                answerEditing = false;
                newAnswer = false;
                if(close)
                    closeQuestionInfo(false);
                else
                    showAnswerInfo(new Array(answerRowSelected, false));
            }else{
//                alert(data);
                showErrorMessage(data);
            }
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });
}