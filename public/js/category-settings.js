$(document).ready(function(){
    $(".btnEdit").click(function()
        {
            var me = $(this);

            $('form').find("input[type=text]").each(function(ev)
            {        
                $(this).attr("placeholder", me.val());
            });              
        });
    $('.modal').on('hidden.bs.modal', function () {
        $('form').find("input[type=text]").each(function(ev)
            {        
                $(this).val("");
            }); 
    });
}); 
