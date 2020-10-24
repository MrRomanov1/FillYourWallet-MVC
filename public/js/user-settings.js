$(document).ready(function() {

    $('#ajaxForm').validate({
        rules: {  
            username: 'required',
            email: 'required',          
            password: { 
                             
                remote: '/validate-password'
            }            
        },
        messages: {
            username: 'Podaj imię' ,               
            email : 'Podaj adres email', 
            password: {
                required: 'Wpisz hasło aby zatwierdzić zmiany',
                remote: 'Podaj poprawne hasło'
            }
        }
    });
});