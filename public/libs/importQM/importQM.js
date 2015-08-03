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
    $("#importQuestions").on("click",loadPage);
});


/**
 *  @descr  Binded login function
 */
function loadPage() {

        $.ajax({
            url     : "index.php?page=importQM/import",
            type    : "post",
            data    :{
                email       :   $("#email").val(),
                password    :   $("#password").val()
            },
            success : function (data, status) {
                $(".infoEdit").html(data);
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
        });

}
