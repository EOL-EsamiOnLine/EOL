/**
 * File: Test.js
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test page
 */

var countdownPosX = 20;
var calculatorPosX = 200;
var periodicTablePosX = 200;

function countdownComplete(){
    alert(ttETimeExpired);
}


$(function(){

    /**
     *  @descr  Add draggable attribute to countdown
     */
    $("#countdown").draggable({
        stop : function(event, ui) {
            countdownPosX = $("#countdown").offset().top - $(window).scrollTop();
        }
    });

    /**
     *  @descr  Add draggable attribute to calculator
     */
    $("#calculator").draggable({
        cancel : 'object',
        stop : function(event, ui) {
            calculatorPosX = $("#calculator").offset().top - $(window).scrollTop();
        }
    });

    /**
     *  @descr  Add draggable attribute to periodic table
     */
    $("#periodicTable").draggable({
        cancel : 'img',
        stop : function(event, ui) {
            periodicTablePosX = $("#periodicTable").offset().top - $(window).scrollTop();
        }
    });

    /**
     *  @descr  Add srollable function for countdown and extras
     */
    $(window).scroll(function(event) {
        $('#countdown').css('top', (countdownPosX + $(this).scrollTop()) + "px");
        $('#calculator').css('top', (calculatorPosX + $(this).scrollTop()) + "px");
        $('#periodicTable').css('top', (periodicTablePosX + $(this).scrollTop()) + "px");
    });



    /**
     *  @descr  Bind event for checkboxes
     */
    $("input[type='checkbox'] + span").on("click", function(event){
        if($(this).prev().is(":checked")) {
            $(this).prev().prop("checked", false);
        }else{
            $(this).prev().prop("checked", true);
        }
    });

    /**
     *  @descr  Bind event for radiobuttons
     */
    $("input[type='radio'] + span").on("click", function(event){
        if($(this).prev().is(":checked")) {

        }else{
            $(this).prev().prop("checked", true);
        }
    });

    /**
     *  @descr  Show calculator
     */
    $(".questionText img.calculator").on("click", function(event){
        $("#calculator").show();
    });

    /**
     *  @descr  Show periodic table
     */
    $(".questionText img.periodicTable").on("click", function(event){
        $("#periodicTable").show();
    });

    /**
     *  @descr  Hide extra
     */
    $("span.extraClose").on("click", function(event){
        $(this).closest(".extra").hide();
    });

});

/**
 *  @name   submitTest
 *  @param  askConfirmation         Array       If askConfirmation[0] is true display confirma dialog
 *  @descr  Binded function for Submit button
 */
function submitTest(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCSubmitTest, submitTest, new Array(false)))){
        var questionsTest = new Array();
        var answersTest = new Array();
        $(".questionTest").each(function(index, div){
            questionsTest.push($(div).attr("value"));
            var answers = new Array();
            switch($(div).attr("type")){
                case "MC" : $(div).find("input:checked").first().each(function(index, input){
                                answers.push($(input).attr("value"));
                            }); break;
                case "MR" : $(div).find("input:checked").each(function(index, input){
                                answers.push($(input).attr("value"));
                            }); break;
                case "OP" : $(div).find("textarea").first().each(function(index, input){
                                answers.push($(input).val());
                            }); break;
                default: showErrorMessage(ttEAnswerNotFound); return false;
            }
            answersTest.push(JSON.stringify(answers));
        });
//        alert(answersTest);
//        alert(JSON.stringify(answersTest));
        if(questionsTest.length == answersTest.length){
            $.ajax({
                url     : "index.php?page=student/submittest",
                type    : "post",
                data    : {
                    questions  :  JSON.stringify(questionsTest),
                    answers    :  JSON.stringify(answersTest),
                    submit     :  "true"
                },
                success : function (data) {
                    if(data == "ACK"){
//                        alert(data);
                        showSuccessMessage(ttMTestSubmitted);
                        setTimeout(function(){location.href = "index.php?page=student/index"}, 1500);
                    }else{
//                        alert(data);
                        errorDialog(ttError,  data);
                    }
                },
                error : function (request, status, error) {
                    alert("jQuery AJAX request error:".error);
                }
            });
        }else{
            showErrorMessage(ttEQuestAnswPicker);
        }
    }
}