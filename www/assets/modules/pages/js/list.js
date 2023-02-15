/**
 * Created by E_dulentsov on 09.06.2017.
 */
var listing = $("#list_pages");
/**
 * Байндинг селекта, если выделенеи было правой мышкой, то не грузим форму, если левой то грузим.
 */



listing.bind("select_node.jstree", function (event, data) {
    if (typeof data.event != 'undefined') {
        if (data.event.type == 'contextmenu') {
            return;
        }
    }
    if (!data.selected) {
        return false;
    }
    var sel = data.selected[0];
    $.ajax({
        type: "POST",
        url: '/pages/admin/form/' + sel + '/ajax/',
        success: function (msg) {
            $("#contentZone").html(msg);
            //Показываем модально окно
            if (typeof GO === "function") {
                GO();
            }
        }
    });
}).jstree({

    "plugins": ["contextmenu", "dnd", "search", "state"],
    'core': {
        'data': {
            url: '/pages/admin/getlist',
            dataType: 'json',
            data: 'data'
        },
        'callback': false,
        "check_callback" : true,
    },
    "search": {
        "case_insensitive": true,
        "show_only_matches": true
    },
    "contextmenu": {
        "items": function () {
            return {
                "Create": {
                    "label": "Создать дочернюю",
                    "action": function (data) {
                        var ref = $.jstree.reference(data.reference),
                            sel = ref.get_selected();
                        if (!sel.length) {
                            return false;
                        }
                        sel = sel[0];
                        $.ajax({
                            type: "POST",
                            url: '/pages/admin/form/' + 0 + '/ajax/' + sel,
                            success: function (msg) {
                                $("#contentZone").html(msg);
                                //Показываем модально окно
                                if (typeof GO === "function") {
                                    GO();
                                }
                            }
                        });
                    }
                },
                "History": {
                    "label": "Посмотреть историю изменений",
                    "action": function (data) {
                        var ref = $.jstree.reference(data.reference),
                            sel = ref.get_selected();
                        if (!sel.length) {
                            return false;
                        }
                        sel = sel[0];
                        $.ajax({
                            type: "POST",
                            url: '/pages/admin/gethistory/' + sel,
                            success: function (msg) {
                                $("#contentZone").html(msg);
                                //Показываем модально окно
                                if (typeof GO === "function") {
                                    GO();
                                }
                            }
                        });
                    }
                },
                "Delete": {
                    "label": "Удалить ветку",
                    "action": function (data) {
                        var ref = $.jstree.reference(data.reference),
                            sel = ref.get_selected();
                        if (!sel.length) {
                            return false;
                        }
                        var selid = sel[0];
                        $.ajax({
                            type: "POST",
                            url: '/pages/admin/delete/' + selid,
                            success: function (msg) {
                                var rez = JSON.parse(msg);
                                //Показываем модально окно
                                if (rez.error == 0) {
                                    ref.delete_node(sel);
                                    $.toast({
                                        heading: 'Успешно!',
                                        text: 'Изменения сохранены!',
                                        position: 'top-right',
                                        icon: 'success'
                                    });
                                } else {
                                    $.toast({
                                        heading: 'Ошибка!',
                                        text: rez.data,
                                        position: 'top-right',
                                        icon: 'danger'
                                    });
                                }

                            }
                        });

                    }
                },
                "DeleteElement": {
                    "label": "Удалить страницу",
                    "action": function (data) {
                        var ref = $.jstree.reference(data.reference),
                            sel = ref.get_selected();
                        if (!sel.length) {
                            return false;
                        }
                        var selid = sel[0];
                        $.ajax({
                            type: "POST",
                            url: '/pages/admin/deleteelement/' + selid,
                            success: function (msg) {
                                var rez = JSON.parse(msg);
                                //Показываем модально окно
                                if (rez.error == 0) {
                                    location.reload();
                                } else {
                                    $.toast({
                                        heading: 'Ошибка!',
                                        text: rez.data,
                                        position: 'top-right',
                                        icon: 'danger'
                                    });
                                }

                            }
                        });

                    }
                },
                "frontEdit": {
                    "label": "Править на фронте",
                    "action": function (data) {
                        if(data.reference[0].childNodes[2].innerText!="/") {
                            window.location = '/' + data.reference[0].childNodes[2].innerText + '?wysiwyg';
                        } else {
                            window.location = data.reference[0].childNodes[2].innerText + '?wysiwyg';
                        }

                    }
                }

            };
        }
    }
}).bind('move_node.jstree', function (e, data) {
    var params = {
        id: +data.node.id,
        old_parent: +data.old_parent,
        new_parent: +data.parent,
        old_position: +data.old_position,
        new_position: +data.position
    };
    move_page(params);

});


var to = false;
$('#finded').keyup(function () {
    if (to) {
        clearTimeout(to);
    }
    to = setTimeout(function () {
        var v = $('#finded').val();
        $('#list_pages').jstree(true).search(v);
    }, 250);
});

/**
 * Перемещение страницы
 * @param params
 */
function move_page(params) {
    $.ajax({
        type: "POST",
        url: '/pages/admin/move',
        data: params,
        success: function (msg) {
            var rez = JSON.parse(msg);
            if (rez.result != 1) {
                $.toast({
                    heading: 'Ошибка!',
                    text: rez.error,
                    position: 'top-right',
                    icon: 'danger'
                });
            }
        }
    });
}

/**
 * Для всех аякс ссылок делаем навешивание события
 */
$(".ajax").on('click', function (e) {
    e.preventDefault(); //отключаем подъем
    // Получаем данные по ссылке
    var href = $(this).attr('href');
    $.ajax({
        type: "POST",
        url: href,
        success: function (msg) {
            $("#contentZone").html(msg);
            //Показываем модально окно
            if (typeof GO === "function") {
                GO();
            }

        }
    });
});

