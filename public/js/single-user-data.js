$(document).ready(function(){ 
    $('.show-single-data').click(function() {
        var me = $(this);
        var categoryName = me.val();

        $('#categoryName').text(categoryName);

        $.ajax({
            method: "GET",
            url: "/get-single-expenses",
            dataType: 'json',
            data: {categoryName: categoryName},
            success: function(data) {
                var resultString = "";                                             
                $.each(data, function(key, value) {
                    resultString = "<tr><td class='text-left py-2 h6 font-weight-light'>"+value['expenseComment']+"</td><td class = 'text-center py-2 h6 font-weight-light'>"+value['amount']+"</td><td class = 'text-right py-2 h6 font-weight-light'>"+value['expenseDate']+"</td></tr>";
                    $("#expenseData").append(resultString);
                });                
            }          
        });        
    });
    $('.modal').on('hidden.bs.modal', function () {
        $("#expenseData").empty();
        var baseExpenseDataContent = "<tr><th class = 'text-left py-2 h6 font-weight-light'>Komentarz</th><th class = 'text-center py-2 h6 font-weight-light'>Kwota</th><th class ='text-right py-2 h6 font-weight-light'>Data</th><tr>";
        $("#expenseData").append(baseExpenseDataContent);        
    });
});   

