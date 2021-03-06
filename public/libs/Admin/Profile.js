/**
 * File: Profile.js
 * User: Masterplan
 * Date: 5/30/13
 * Time: 4:49 PM
 * Desc: Shows profile page of user's account and updates user's information
 */

/**
 *  @name   saveProfile
 *  @descr  Save user's informations
 */
function saveProfile(){
    var name = $("#userName").val().trim();
    var surname = $("#userSurname").val().trim();
    var oldPassword = $("#oldPassword").val().trim();
    var newPassword = $("#newPassword").val().trim();
    var newPassword2 = $("#newPassword2").val().trim();

    if((name != "") && (surname != "")){
        if((oldPassword == "") || (newPassword == newPassword2)){
            if((oldPassword == "") || (newPassword.length >= 8)){
                $.ajax({
                    url     : "index.php?page=admin/updateprofile",
                    type    : "post",
                    data    : {
                        name         :   name,
                        surname      :   surname,
                        oldPassword  :   $("#oldPassword").val(),
                        newPassword  :   $("#newPassword").val()
                    },
                    success : function (data, status) {
                        if(data == "ACK"){
                            //alert(data);
                            showSuccessMessage(ttMEdit);
                            setTimeout(function(){location.href = "index.php"}, 2000);
                        }else{
                            //alert(data);
                            showErrorMessage(data);
                        }
                    },
                    error : function (request, status, error) {
                        alert("jQuery AJAX request error:".error);
                    }
                });
            }else showErrorMessage(ttEPasswordShort);
        }else showErrorMessage(ttEPasswordsNotMatch);
    }else showErrorMessage(ttEEmptyFields);
}