/**
 * File: Showquestioninfo.js
 * User: Masterplan
 * Date: 19/05/14
 * Time: 11:34
 * Desc: Show question's info with all translations and CKEditors
 */

// Anchor for answers list
var answerRowSelected = null;

// Anchor for question/answer edit
var questionEditing = false;
var answerEditing = false;
var newAnswer = false;

$(function(){

    /**
     *  @descr  Function to enable QuestionInfo tabs
     */
    $("#questionInfoTabs").tabs().css("border", "none")
                                 .find("ul").css("background", "none")
                                            .css("border", "none");
    $("#questionInfoTabs > div").css("border", "1px solid #686868")
                                .css("border-radius", "5px")
                                .css("margin-top", "7px");

    /**
     *  @descr  Function to enable question's translations tabs
     */
    $("#question-tab").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
    $("#question-tab li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

    /**
     *  @descr  Function to enable answer's translations
     */
    $("#answers-tab").tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
    $("#answers-tab li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );

    /**
    *  @descr  Function to enable question tab's dropdownInfo menu effects
    */
    $("#question-tab .dropdownInfo dt.writable").on("click", function() {
                                                        $(this).children("span").toggleClass("clicked");
                                                        $(this).next().children("ol").slideToggle(200);
                                                    });

    /**
     *  @descr  Function to change infos
     */
    $("#question-tab .dropdownInfo dd ol li").on("click", function() {
                                                              questionEditing = true;
                                                              updateDropdown($(this));
                                                          });
    // Close all dropdowns when click out of it
    // Maybe too heavy for system... IMPROVE
    $(document).on('click', function(e) {
                                var $clicked = $(e.target);
                                if (!($clicked.parents().hasClass("dropdownInfo"))){
                                    $(".dropdownInfo dd ol").slideUp(200);
                                    $(".dropdownInfo dt span").removeClass("clicked");
                                }
                            });

    /**
     *  @descr  Binded event to create new answer
     */
    $("#newAnswer").on("click", function(event){
        newEmptyAnswer(new Array((answerEditing || checkCKEditorEdits("at"))));
    });
});

/**
 *  @name   saveQuestionInfo
 *  @descr  Binded function to save question info
 *  @param  close       Boolean                     Close panel if true
 */
function saveQuestionInfo(close){
    var idTopic = $("#questionTopic").find("dt span span").text();
    var difficulty = $("#questionDifficulty").find("dt span span").text();
    var questionTranslations = new Array();
    var langAlias = "";
    var langID = "";
    var shortText = "";
    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
        langID = allLangs[indexLang]["idLanguage"];
        langAlias = allLangs[indexLang]["alias"];
        questionTranslations[langID] = CKEDITOR.instances["qt"+(langAlias.toUpperCase())].getData();
        if(langID == mainLang)
            shortText = CKEDITOR.instances["qt"+(langAlias.toUpperCase())].document.getBody().getText();
    }
    $.ajax({
        url     : "index.php?page=question/updatequestioninfo",
        type    : "post",
        data    : {
            idQuestion      :   questionsTable.row(questionRowEdit).data()[qtci.questionID],
            idTopic         :   idTopic,
            difficulty      :   difficulty,
            translationsQ   :   JSON.stringify(questionTranslations),
            shortText       :   shortText,
            mainLang        :   mainLang
        },
        success : function (data) {
            data = data.trim().split(ajaxSeparator);
            if(data.length > 1){
                questionsTable.row(questionRowEdit).data(JSON.parse(data[1]));
                questionsTable.draw();
                if($(questionRowEdit)[0] == $(questionRowSelected)[0])
                    showQuestionLanguageAndPreview(questionRowSelected);
                showSuccessMessage(ttMEdit);
                questionEditing = false;
                resetCKEditorInstances('qt', false, false);
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
 *  @name   deleteQuestion
 *  @descr  Binded function to delete question
 *  @param  askConfirmation     Boolean     If true ask confirmation
 */
function deleteQuestion(askConfirmation){
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDeleteQuestion, deleteQuestion, false))){
        var idQuestion = questionsTable.row(questionRowEdit).data()[qtci.questionID];
        $.ajax({
            url     : "index.php?page=question/deletequestion",
            type    : "post",
            data    : {
                idQuestion      :   idQuestion
            },
            success : function (data) {
                if(data == "ACK"){
                    questionsTable.row(questionRowEdit).remove().draw();
                    if($(questionRowEdit)[0] == $(questionRowSelected)[0]){
                        closeQuestionLanguagePanel();
                        closeQuestionPreviewPanel();
                    }
                    questionRowEdit = null;
                    closeQuestionInfo(false);
                    showSuccessMessage(ttMQuestionDeleted);
                }else{
//                    alert(data);
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   showAnswerInfo
 *  @descr  Get and display answer informations and translations form database
 *  @param  selectedAnswerAndConfirm        Array       [Selected answer <a>, Confirmation]
 */
function showAnswerInfo(selectedAnswerAndConfirm){
    var selectedAnswer = selectedAnswerAndConfirm[0];
    var askConfirmation = selectedAnswerAndConfirm[1];
    if((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardEdits, showAnswerInfo, new Array(selectedAnswer, false)))){
        if(newAnswer)
            $("#answersList .boxContent ul li a[value='new']").parent().remove();
        answerRowSelected = $(selectedAnswer);
        $(".showAnswerInfo[value]").removeClass("selected");
        answerRowSelected.addClass("selected");
        answerEditing = false;
        $.ajax({
            url     : "index.php?page=question/showanswerinfo",
            type    : "post",
            data    : {
                action      :   "show",
                idQuestion  :   questionsTable.row(questionRowEdit).data()[qtci.questionID],
                idType      :   questionsTable.row(questionRowEdit).data()[qtci.typeID],
                idAnswer    :   $(answerRowSelected).attr("value")
            },
            success : function (data) {
                if($(data)){
                    $("#answerOptions").html(data);
                    var langAlias;
                    var translation;
                    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
                        langAlias = allLangs[indexLang]["alias"].toUpperCase();
                        if($("#"+langAlias).length > 0){
                            translation = $("#"+langAlias).html();
                            $("#"+langAlias).remove();
                            CKEDITOR.instances["at"+langAlias].setData(translation);
                        }else{
                            CKEDITOR.instances["at"+langAlias].setData("");
                        }
                    }
                    setTimeout(function(){ resetCKEditorInstances("at", false, true); }, 500);
                }else{
//                    alert(data);
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   newEmptyAnswer
 *  @descr  Ajax request for show empty interface for define a new answer
 */
function newEmptyAnswer(askConfirmation) {
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCDiscardEdits, newEmptyAnswer, new Array(false)))){
        if(newAnswer)
            $("#answersList .boxContent ul li a[value='new']").parent().remove();
        var idType = questionsTable.row(questionRowEdit).data()[qtci.typeID];
        $.ajax({
            url     : "index.php?page=question/showanswerinfo",
            type    : "post",
            data    : {
                action      :   "new",
                idQuestion  :   questionsTable.row(questionRowEdit).data()[qtci.questionID],
                idType      :   questionsTable.row(questionRowEdit).data()[qtci.typeID],
                idAnswer    :   "none"
            },
            success : function (data) {
//                alert(data);
                if($(data)){
                    $(".showAnswerInfo[value]").removeClass("selected");
                    $("#answersList .boxContent ul").append($("<li>" +
                                               "    <a class=\"showAnswerInfo selected\" value=\"new\"" +
                                               "    onclick=\"showAnswerInfo(new Array(this, (answerEditing || checkCKEditorEdits('at'))));\">" + ttNewAnswer + "</a>" +
                                               "</li>"));
                    $("#answerOptions").html(data);
                    resetCKEditorInstances('at', true, true);
                    answerRowSelected = $("#answersList .boxContent ul li a[value='new']");
                    answerEditing = true;
                    newAnswer = true;
                }else{
//                    alert(data);
                    showErrorMessage(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });
    }
}

/**
 *  @name   createNewQuestion
 *  @descr  Binded event to create a new question
 */
function createNewQuestion(){
    var questionTopic = $("#questionTopic dt span.value").text();
    var questionDifficulty = $("#questionDifficulty dt span.value").text();
    var questionType = $("#questionType dt span.value").text();
    var questionTranslations = new Array();
    var langAlias = "";
    var langID = "";
    var shortText = "";
    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
        langID = allLangs[indexLang]["idLanguage"];
        langAlias = allLangs[indexLang]["alias"];
        questionTranslations[langID] = CKEDITOR.instances["qt"+(langAlias.toUpperCase())].getData();
        if(langID == mainLang)
            shortText = CKEDITOR.instances["qt"+(langAlias.toUpperCase())].document.getBody().getText();
    }
    $.ajax({
        url     : "index.php?page=question/newquestion",
        type    : "post",
        data    : {
            idTopic         :   questionTopic,
            idDifficulty    :   questionDifficulty,
            idType          :   questionType,
            translationsQ   :   JSON.stringify(questionTranslations),
            shortText       :   shortText,
            mainLang        :   mainLang
        },
        success : function (data) {
            data = data.trim().split(ajaxSeparator);
            if(data.length > 1){
                var questionInfo = JSON.parse(data[1]);
                questionsTable.row.add(questionInfo).draw();
                var newQuestionIndex = questionsTable.rows().eq(0).filter(function(rowIndex){
                    return questionsTable.cell(rowIndex, qtci.questionID).data() == questionInfo[qtci.questionID];
                });
                questionRowEdit = questionsTable.row(newQuestionIndex[0]).node();
                showSuccessMessage(ttMEdit);
                questionEditing = false;
                closeQuestionInfo(false);
                setTimeout(function(){ showQuestionInfo(questionRowEdit) }, 500);
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
 *  @name   cancelNewQuestion
 *  @descr  Closes new question's information after confirm dialog
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function cancelNewQuestion(askConfirmation){
    if(((!askConfirmation) || (confirmDialog(ttWarning, ttCDiscardNew, cancelNewQuestion, false)))){
        questionEditing = false;
        closeQuestionInfo(false);
    }
}

/**
 *  @name   closeQuestionInfo
 *  @descr  Close question's informations after confirm dialog preventing to loose changes
 *  @param  askConfirmation     Boolean     If true display a confirmation dialog
 */
function closeQuestionInfo(askConfirmation){
    if((!askConfirmation) || ((!questionEditing) && (!answerEditing) && (!checkCKEditorEdits('qt')) && (!checkCKEditorEdits('at'))
        || (confirmDialog(ttWarning, ttCDiscardEdits, closeQuestionInfo, false)))){
        closeLightbox($('#questionInfo'));
        questionEditing = false;
        answerEditing = false;
    }
}

/**
 *  @name   resetCKEditorInstances
 *  @descr  Reset text, Undo/Redo and Dirty state of CKEditor instances
 *  @param  instance        String      Instance ID
 */
function createCKEditorInstance(instance){
    var roxyFileman = '/fileman/index.html';
    CKEDITOR.replace(instance,{filebrowserBrowseUrl:roxyFileman,
        filebrowserUploadUrl:roxyFileman,
        filebrowserImageBrowseUrl:roxyFileman+'?type=image',
        filebrowserImageUploadUrl:roxyFileman+'?type=image'});
}

/**
 *  @name   resetCKEditorInstances
 *  @descr  Reset text, Undo/Redo and Dirty state of CKEditor instances
 *  @param  prefix          String          CKEditor instance prefix ('at' | 'qt')
 *  @param  resetText       Boolean         If true reset instance content
 *  @param  resetUndo       Boolean         If true reset instance undo/redo
 */
function resetCKEditorInstances(prefix, resetText, resetUndo){
    var langAlias = "";
    for(var indexLang = 0; indexLang < allLangs.length; indexLang++){
        langAlias = allLangs[indexLang]["alias"].toUpperCase();
        if(resetText)
            CKEDITOR.instances[prefix+langAlias].setData("");
        if(resetUndo)
            CKEDITOR.instances[prefix+langAlias].resetUndo();
        CKEDITOR.instances[prefix+langAlias].resetDirty();
    }
}

/**
 *  @name   checkCKEditorEdits
 *  @descr  Check if CKEditor instances with prefix are edited
 *  @param  prefix          String          CKEditor instance prefix ('at' | 'qt')
 */
function checkCKEditorEdits(prefix){
    var edited = false;
    var langAlias = "";
    var indexLang = 0;
    while((indexLang < allLangs.length) && (!edited)){
        langAlias = allLangs[indexLang]["alias"].toUpperCase();
        if(CKEDITOR.instances[prefix+langAlias] != null)
            edited = CKEDITOR.instances[prefix+langAlias].checkDirty();
        indexLang++
    }
    return edited;
}