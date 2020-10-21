$(document).ready(function(){
    $(".btnEdit, .btnDelete").click(function()
        {
            var me = $(this);
            var result = me.val().split('|');
            setInputValues(result);
            checkLimitBox(result);
        });
    $('.modal').on('hidden.bs.modal', function () {
        $('form').find("input[type=text]").each(function(ev)
            {        
                $(this).removeAttr("value");
            }); 
    });
});

function setInputValues(result){
    
    $('form').find("input[type=text], input[type=hidden]").each(function(ev)
    {        
        $(this).attr("value", result[0]);
    });
    $('.deleteCategory').text(result[0]);
};

function checkLimitBox(result){    

    if (result[1] > 0) {
        $('#limitsCheckbox').prop("checked", true);
        $('#limitAmount').css("display", "inline");
        $('#limitAmount').prop("value", result[1]);
    }
    else {
        $('#limitsCheckbox').prop("checked", false);
        $('#limitAmount').css("display", "none");
        $('#limitAmount').removeAttr("value");
    }    
}