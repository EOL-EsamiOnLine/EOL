/**
 * File: View.js
 * User: Masterplan
 * Date: 5/6/13
 * Time: 5:33 PM
 * Desc: Views archived test
 */

var allOpened = false;

$(function(){

    $("#showHide").on("click", function(){showHide(null)});

});

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