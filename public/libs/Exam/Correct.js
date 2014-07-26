/**
 * File:
 * User: Masterplan
 * Date: 5/5/13
 * Time: 3:51 PM
 * Desc:
 */

var allOpened = false;

$(function(){

    $("#showHide").on("click", function(){showHide(null)});

    /**
     *  @descr  Function for dropdown menu effects
     */
    $(".dropdownScore dt, .dropdownBonus dt").on("click", function(event){
        $(this).children("span").toggleClass("clicked");
        $(this).next().children("ol").slideToggle(200);
    });

    /**
     *  @descr  Function to change score for open questions
     */
    $(".dropdownScore dd ol li, .dropdownBonus dd ol li").on("click", function(event){ updateTestScore($(this)); });

    // Close all dropdowns when click out of it
    // Maybe too heavy for system... IMPROVE
    $(document).on('click', function(e) {
        var $clicked = $(e.target);
        if (!(($clicked.parents().hasClass("dropdownScore")) ||
              ($clicked.parents().hasClass("dropdownBonus"))) ){
            $(".dropdownScore dd ol, .dropdownBonus dd ol").slideUp(200);
            $(".dropdownScore dt span, .dropdownBonus dt span").removeClass("clicked");
        }
    });

});

/**
 *  @name   confirmTest
 *  @param  askConfirmation         Array       If askConfirmation[0] is true display confirm dialog
 *  @descr  Confirm final score and archive test
 */
function confirmTest(askConfirmation){
    if((!askConfirmation[0]) || (confirmDialog(ttWarning, ttCConfirmTest, confirmTest, new Array(false)))){
        var correctScores = new Array();
        $("div.responseOP").each(function(index, div){
            correctScores[$(div).attr("value")] = $(div).next().children("dt").find("span.value").text();
        });
        $.ajax({
            url     : "index.php?page=exam/correct",
            type    : "post",
            data    : {
                idTest      :   $("#idTest").val(),
                correctScores :   JSON.stringify(correctScores),
                scoreTest     :   $("#scorePre").text(),
                bonus         :   $(".dropdownBonus dt span.value").text(),
                scoreFinal    :   $("#scorePost").text()
            },
            success : function (data) {
                if(data == "ACK"){
//                    alert(data);
                    showSuccessMessage(ttMConfirm);
                    setTimeout(function(){ $("#idExamForm").submit(); }, 1500);
                }else{
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
 *  @name   updateTestScore
 *  @param  selected            DOM Element         Selected dropdown
 *  @descr  Updates question and test's score
 */
function updateTestScore(selected){
    var dropdown = selected.closest("dl");
    dropdown.children("dt").children("span").toggleClass("clicked");
    var text = selected.html();
    var bonus = 0;
    if(dropdown.hasClass("dropdownScore")){
        var oldScore = parseFloat(dropdown.children("dt").find("span.value").text());
        var newScore = parseFloat(selected.children("span.value").text());
        var maxScore = parseFloat($("#maxScore").val());
        bonus = parseFloat($(".dropdownBonus").children("dt").find("span.value").text());

        var scorePre = parseFloat($("#scorePre").text()) - oldScore + newScore;
        var scorePost = scorePre + bonus;
        if(scorePre > maxScore){
            scorePre = maxScore;
        }
        if(scorePost > maxScore){
            scorePost = maxScore;
        }
        $("#scorePre").text(scorePre.toFixed(1));
        $("#scorePost").text(scorePost.toFixed(0));

        $(selected).closest(".questionTest").removeClass('correctQuestion wrongQuestion rightQuestion');
        if(newScore > 0)
            $(selected).closest(".questionTest").addClass('rightQuestion');
        else
            $(selected).closest(".questionTest").addClass('wrongQuestion');

        $(selected).closest(".questionTest").find(".questionText span.responseScore").html(newScore);

    }
    selected.parent().parent().prev().children("span").html(text);
    selected.parent().hide();
    if(dropdown.hasClass("dropdownBonus")){
        bonus = parseFloat(selected.children("span.value").text());
        scorePost = parseFloat($("#scorePre").text()) + bonus;
        if(scorePost > maxScore){
            scorePost = maxScore;
        }
        $("#scorePost").text(scorePost.toFixed(0));
    }
}

/**
 *  @name   showHide
 *  @param  selected            DOM Element             <div> of selected question
 *  @descr  Shows or Hide answers sections
 */
function showHide(selected){
    if(selected == null){
        if(allOpened){
            $(".questionAnswers").slideUp();
            allOpened = false;
        }else{
            $(".questionAnswers").slideDown();
            allOpened = true;
        }
    }else{
        $(selected).parent().find(".questionAnswers").slideToggle();
    }
}