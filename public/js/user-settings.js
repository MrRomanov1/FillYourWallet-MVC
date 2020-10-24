$(document).ready(function() {

    $('#ajaxForm').validate({
        rules: {            
            password: {                
                remote: '/profilemanager/check-user-password'
            }            
        },
        messages: {
            password: {
                remote: 'email already taken'
            }
        }
    });
});