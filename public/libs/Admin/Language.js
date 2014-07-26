/**
 * File: Language.js
 * User: Masterplan
 * Date: 7/5/13
 * Time: 12:51 PM
 * Desc: Shows language edit page
 */

$(function(){

    /**
     *  @descr  Function for color translations
     */
    checkTranslation(null);

    /**
     *  @descr  Bind event for textarea lost focus
     */
    $("textarea.language").on("focusout", function(){ checkTranslation(this) });

    /**
     *  @descr  Bind autoresize action
     */
    $("textarea.language").autoresize();

});

/**
 *  @name   saveLanguageXML
 *  @descr  Saves XML file of requested language
 */
function saveLanguageXML(){
    var constants = new Array();
    var translations = new Array();
    $("div.language").each(function(index, div){
        constants.push($(div).attr("id"));
        translations.push($(div).find("textarea.language").val());
    });
    $.ajax({
        url     : "index.php?page=admin/savelanguage",
        type    : "post",
        data    :{
            action       :   "xml",
            alias        :   alias,
            constants    :   JSON.stringify(constants),
            translations :   JSON.stringify(translations)
        },
        success : function (data) {
            if(data == "ACK")
                showSuccessMessage(ttMLanguageSaved);
            else
                showErrorMessage(data);
        },
        error : function (request, status, error) { alert("jQuery AJAX request error:".error); }
    });
}

/**
 *  @name   updateLanguageFiles
 *  @descr  Saves PHP/Javascript file of requested language
 */
function saveLanguageFiles(){
    confirmDialog(ttWarning, ttCUpdateLanguage, function(){
        var empty = false;
        var constants = new Array();
        var translations = new Array();
        $("div.language").each(function(index, div){
            var text = $(div).find("textarea.language").val();
            if(text == "")
                empty = true;
            constants.push($(div).attr("id"));
            translations.push(text);
        });
        if(!empty){
            $.ajax({
                url     : "index.php?page=admin/savelanguage",
                type    : "post",
                data    :{
                    action       :   "files",
                    alias        :   alias,
                    constants    :   JSON.stringify(constants),
                    translations :   JSON.stringify(translations)
                },
                success : function (data) {
                    if(data == "ACKACK")
                        showSuccessMessage(ttMLanguageUpdated);
                    else
                        showErrorMessage(data);
                },
                error : function (request, status, error){ alert("jQuery AJAX request error:".error); }
            });
        }else
            showErrorMessage(ttEEmptyFields);
    })
}

/**
 *  @name   checkTranslation
 *  @descr  Checks if exists the translation in requested textarea,
 *          or (if textarea is null) checks every textareas in page
 *  @param  textarea        DOM Element         Textarea to check
 */
function checkTranslation(textarea){
    if(textarea == null){
        $("textarea.language").each(function(index, textarea){
            if($(textarea).val() != "")
                $(textarea).switchClass("red", "green");
            else
                $(textarea).switchClass("green", "red");
        });
    }else{
        if($(textarea).val() != "")
            $(textarea).switchClass("red", "green");
        else
            $(textarea).switchClass("green", "red");
    }
}