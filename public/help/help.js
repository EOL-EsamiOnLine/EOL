/**
 * File:
 * User: Masterplan
 * Date: 23/10/14
 * Time: 10:56
 * Desc:
 */

$(function(){

    $.ajax({
        url     : "contents.html",
        success : function (data) {
            $("#contentsList").html(data);
            $("#contentsList a.showHelp").on("click", function(){showHelp($(this))});
            $("#contentsList li span").html("&nbsp;&nbsp;&nbsp;&nbsp;")
                                      .on("click", function(){showHideContents($(this))});
        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error:".error);
        }
    });

});

function showHelp(content){
    var helpPage = content.attr("value")+".html";
    $.ajax({
        url     : helpPage,
        success : function (data) {
            $("#help").html($(data)).find("link").remove();

        },
        error : function (request, status, error) {
            alert("jQuery AJAX request error");
        }
    });
}

function showHideContents(content){
    var li = content.parent();
    if(li.hasClass("closed")){
        li.removeClass("closed");
        li.next().removeClass("closed");
    }else{
        li.addClass("closed");
        li.next().addClass("closed");
    }
}

function contentsToggle(tool){
    panel = $("#contents");
    if(panel.hasClass("closed")){
        panel.removeClass("closed");
        panel.css("width", "20%");
        tool.removeClass("right").addClass("left");
        $("#main").width("78%");
    }else{
        panel.addClass("closed");
        panel.css("width", "22px");
        tool.removeClass("left").addClass("right");
        $("#main").width("98%");
    }
}