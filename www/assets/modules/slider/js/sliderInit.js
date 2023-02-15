/**
 * Created by Евгения on 08.09.2017.
 */
$(document).ready(function () {
    $.ajax({
        url:'/slider/settings/getsettings',
        'type':'POST',
        'dataType':'JSON',
        'data':{
            id:$('#id').val()
        }
    }).done(function (response) {
        response.appendDots = $('.appendDots');
        response.dots = true;
        //console.log(response);
        $('.slider-content').slick(response);
       // console.log(response);
    });

});