/**
 * File: Test.js
 * User: Masterplan
 * Date: 5/3/13
 * Time: 12:30 PM
 * Desc: Shows test page
 */

var posX = 20;

function countdownComplete(){
    alert("looool");
}


$(function(){

    /**
     *  @descr  Add draggable attribute to countdown
     */
    $("#countdown").draggable({
        stop : function(event, ui) {
            posX = $("#countdown").offset().top - $(window).scrollTop();
        }
    });

    /**
     *  @descr  Add srollable function for countdown
     */
    $(window).scroll(function(event) {
        $('#countdown').css('top', (posX + $(this).scrollTop()) + "px");
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








