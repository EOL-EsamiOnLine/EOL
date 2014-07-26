/**
 * File: Showquestionpreview.js
 * User: Masterplan
 * Date: 17/05/14
 * Time: 17:31
 * Desc: Shows question's preview with answers
 */

$(function(){

    /**
     *  @descr  Bind event for checkboxes
     */
    $("#questionPreview input[type='checkbox'] + span").on("click", function(event){
        if($(this).prev().is(":checked")) {
            $(this).prev().prop("checked", false);
        }else{
            $(this).prev().prop("checked", true);
        }
    });

    /**
     *  @descr  Bind event for radiobuttons
     */
    $("#questionPreview input[type='radio'] + span").on("click", function(event){
        if($(this).prev().is(":checked")) {

        }else{
            $(this).prev().prop("checked", true);
        }
    });

});