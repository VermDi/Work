$(document).ready(function (){

    $(".control-links .btn").on('click', function (){
        if ($(this).hasClass('active')){
            return false;
        } else {
            $(this).toggleClass( "active");
        }
    });

});