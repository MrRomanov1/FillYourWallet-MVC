var category = '';
$(document).ready(function () {

    $('.selectpicker').change(function () {
        category = $('#expenseClass').val();

        var expenseTotal = 0;
        var expenseAmount = +$('input[name=amount]').val();
        
        getExpenseData(expenseTotal, expenseAmount);
        
    });

    $('button[type=reset').click(function (){
        $('.alert').css('display', 'none');
    });

    $('input[name=amount]').keyup(function () {
        var expenseTotal = 0;
        var expenseAmount = +$(this).val();

        if (category != '') {
            getExpenseData(expenseTotal, expenseAmount);
        }
    })
});

function getExpenseData(expenseTotal, expenseAmount) {
    $.ajax({
        method: "GET",
        url: "/ExpenseManager/getUserExpensesAndLimits",
        dataType: 'json',
        data: { category: category },
        success: function (data) {
            expenseLimit = +data['limit'];
            expenseTotal = data['amount'] + expenseAmount;

            if (expenseLimit > 0) {
                $('#alert-category-limit').text(expenseLimit+" PLN");
                $('#alert-category-expenses').text(data['amount']+" PLN");
                $('#alert-category-total').text(expenseTotal+" PLN");
                if (expenseTotal >= expenseLimit) {
                    $('.alert').css('display', 'block');
                    $('.alert').removeClass('alert-warning alert-success').addClass('alert-danger');
                }
                else if (expenseTotal > (0.75 * expenseLimit) && expenseTotal < expenseLimit) {
                    $('.alert').css('display', 'block');
                    $('.alert').removeClass('alert-danger alert-success').addClass('alert-warning');
                }
                else {
                    $('.alert').css('display', 'block');
                    $('.alert').removeClass('alert-danger alert-warning').addClass('alert-success');
                }
            }
            else {
                $('.alert').css('display', 'none');
                $('.alert').removeClass('alert-warning alert-success alert-danger');
            }
        }
    });
}