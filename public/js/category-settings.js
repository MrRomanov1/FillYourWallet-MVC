$(document).ready(function(){
    $(".btnEdit, .btnDelete").click(function()
        {
            var me = $(this);
            setInputValues(me);
        });
    $('.modal').on('hidden.bs.modal', function () {
        $('form').find("input[type=text]").each(function(ev)
            {        
                $(this).removeAttr("value");
            }); 
    });
}); 

function setInputValues(me){
    
    $('form').find("input[type=text], input[type=hidden]").each(function(ev)
    {        
        $(this).attr("value", me.val());
    });
    $('.deleteCategory').text(me.val());
}