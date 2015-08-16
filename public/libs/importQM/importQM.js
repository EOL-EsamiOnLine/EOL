/**
 * File: Login.js
 * User: Masterplan
 * Date: 3/15/13
 * Time: 7:32 PM
 * Desc: Javascript library for login module
 */

$(document).ready(function () {

    /**
     *  @descr  Binded event for ENTER key on fields
     */
    initImport();
    $("#prepareImport").on("click",prepareImport);
    $("#importQuestions").on("click",startImport);
});






/**
 *  @descr  init Import function
 */
function initImport() {

    $.ajax({
        url     : "index.php?page=importQM/initimport",
        type    : "post",
        data    :{
        },
        success : function (data, status) {
            $("#ImportMsg").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

}




/**
 *  @descr prepare Import function
 */
function prepareImport() {

    $.ajax({
        url     : "index.php?page=importQM/prepareimport",
        type    : "post",
        data    :{
        },
        success : function (data, status) {
            $("#ImportMsg").html(data);
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

}

/**
 *  @descr  stat Import Procedure function
 */
function startImport() {

        $.ajax({
            url     : "index.php?page=importQM/import",
            type    : "post",
            data    :{
            },
            success : function (data, status) {
                //$(".infoEdit").html(data);

                if(data=="ACK"){
                    showSuccessMessage(ttImportComplete);
                    setTimeout(function(){ location.replace("index.php?page=admin/index") }, 3000);
                }
                else{
                    errorDialog(ttError , data);
                }


            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });

}
