$(document).ready(function(){ 
    $('.show-single-data').click(function() {
        var me = $(this);
        var categoryName = me.val();
        var balanceBeginDate = $("#balanceBeginDate").val();
        var balanceEndDate = $("#balanceEndDate").val();

        $('#categoryName').text(categoryName);

        $.ajax({
            method: "GET",
            url: "/get-single-expenses",
            dataType: 'json',
            data: {categoryName: categoryName,
                balanceBeginDate: balanceBeginDate,
                balanceEndDate: balanceEndDate},
            success: function(data) {
                var resultString = "";                                                           
                $.each(data, function(key, value) {                    
                    resultString = "<tr><td class='text-left py-2 h6 font-weight-light'>"+value['expenseComment']+"</td><td class = 'text-center py-2 h6 font-weight-light'>"+value['amount']+"</td><td class = 'text-center py-2 h6 font-weight-light'>"+value['expenseDate']+"</td><td class = 'text-center py-2'><button type = 'button' class = 'btn btn-dark-green float-right btnEdit btn-sm' id='editSingleExpense' value='"+value['id']+"'data-toggle='modal' data-target='#editExpense'><svg width='1em' height='1em' viewBox='0 0 16 16' class='bi bi-pencil' fill='currentColor' xmlns='http://www.w3.org/2000/svg'><path fill-rule='evenodd' d='M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z'/></svg></button></td></tr>";
                    $("#expenseData").append(resultString);                    
                });                
            }          
        });        
    });
    $('#showSingleExpenseOptions').on('hidden.bs.modal', function () {
        $("#expenseData").empty();
        var baseExpenseDataContent = "<tr><th class = 'text-left py-2 h6 font-weight-light'>Komentarz</th><th class = 'text-center py-2 h6 font-weight-light'>Kwota</th><th class ='text-center py-2 h6 font-weight-light'>Data</th></th><th class ='text-right py-2 h6 font-weight-light pr-4'>Edytuj</th><tr>";
        $("#expenseData").append(baseExpenseDataContent);        
    });
    $('.editExpense').click(function(){
        $('#expenseCommentLabel, #expenseComment, #amountLabel, #amount, #dateLabel, #expenseDate').css('display', 'inline');
        $('.selectCategories').css('display','none');
    });
    $('.moveExpense').click(function(){
        $('#expenseCommentLabel, #expenseComment, #amountLabel, #amount, #dateLabel, #expenseDate').css('display', 'none');
        $('.selectCategories').css('display','inline');
    });
    $('.deleteExpense').click(function(){
        $('#expenseCommentLabel, #expenseComment, #amountLabel, #amount, #dateLabel, #expenseDate').css('display', 'none');
        $('.selectCategories').css('display','none');
    });
    $(document).on('click','#editSingleExpense',function(){
        var def = $(this);
        var expenseId = def.val();        
        $.ajax({
            method: "GET",
            url: "/get-single-expense-data",
            dataType: 'json',
            data: {expenseId: expenseId},

            success: function(data) {
                $.each(data, function(key, value) { 
                    $('input[name=expenseComment]').val(value['expenseComment']);
                    $('input[name=amount]').val(value['amount']);
                    $('input[name=expenseDate]').val(value['expenseDate']);
                    $('input[name=hiddenExpenseId]').val(value['id']);
                });
            }
        });
    });
    $('#editExpense').on('hidden.bs.modal', function () {
        $('input[name=expenseComment]').removeAttr("value");
        $('input[name=amount]').removeAttr("value");
        $('input[name=expenseDate]').removeAttr("value");
              
    });
});   

