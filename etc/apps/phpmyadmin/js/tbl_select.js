$(document).ready(function(){
    $.ajaxSetup({
        cache:"false"
    });
    $('<div id="togglesearchformdiv"><a id="togglesearchformlink"></a></div>').insertAfter("#tbl_search_form").hide();
    $("#togglesearchformlink").html(PMA_messages.strShowSearchCriteria).bind("click",function(){
        var a=$(this);
        $("#tbl_search_form").slideToggle();
        a.text()==PMA_messages.strHideSearchCriteria?a.text(PMA_messages.strShowSearchCriteria):a.text(PMA_messages.strHideSearchCriteria);
        return false
        });
    $("#tbl_search_form.ajax").live("submit",
        function(a){
            $search_form=$(this);
            a.preventDefault();
            $("#sqlqueryresults").empty();
            var c=PMA_ajaxShowMessage(PMA_messages.strSearching);
            PMA_prepareForAjaxRequest($search_form);
            $.post($search_form.attr("action"),$search_form.serialize(),function(b){
                if(typeof b=="string"){
                    $("#sqlqueryresults").html(b);
                    $("#sqlqueryresults").trigger("appendAnchor");
                    $("#tbl_search_form").slideToggle().hide();
                    $("#togglesearchformlink").text(PMA_messages.strShowSearchCriteria);
                    $("#togglesearchformdiv").show();
                    PMA_init_slider()
                    }else $("#sqlqueryresults").html(b.message);
                c.clearQueue().fadeOut("medium",function(){
                    $(this).hide()
                    })
                })
            })
    },"top.frame_content");
