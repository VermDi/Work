/**
 * Created by Евгений on 16.01.2015.
 */
function close_modal()
{ // закрытие модального окна
    $('#modal_window').off('shown');
    delete myCodeMirror;
    $('#modal_window').modal('hide');
    $('#modal_window').html('');
}
function del(t)
{// удаление файла или папки!
    if (confirm("Точно удалить?")) {
        $.get(
            $(t).attr("href"),
            '',
            function (data) {
                if (data = "ok") {
                    $(t).parent('li').css('display','none');
                } else {
                    alert(data);
                }
            }
        );
    }
}
function loading(t)
{ // загрузка окна
    //alert(t);
    $('#modal_content').load($(t).attr("href"));

    $('#modal_window').modal();
    return false;
}
function show_hide(id)
{
    $('#'+id).css({'display':(($('#'+id).css('display') == 'block')?'none':'block')});
}
function sen_file_dt(f)
{

    var frm = $('#modal_content').children("form");
	//alert (frm.serializeArray());
    data = frm.serializeArray();
    //alert(data);
    $.ajax({
        'type': frm.attr('method'),
        'url': frm.attr('action'),
        'data': data,
        'success': function (data) {alert(data);}
    });
    if (f=='true') {
        $('#modal_window').modal('hide');
    }
}
$(window).bind('hashchange', function () {get_list();});

function get_list(way)
{
    if (window.location.hash != '') {
        way = window.location.hash.substr(1);
    }
    $.post(
        '/filemanager/get',
        {
            'path': way,
            'act': 'get'
        },
        function (data) {
            $('#file_list').html(data)
        }
    );
}
//загрузка страницы
$(function () {get_list('');});
