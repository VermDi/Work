/**
 * Created by E_dulentsov on 09.06.2017.
 */
var listing = $("#list_pages");
listing.jstree({
    "core": {
        "check_callback": true
    },
    "plugins": ["contextmenu", "dnd", "search", "state"],
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
                            url: '/menu/admin/form/' + 0 + '/ajax/' + sel,
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
                "Edit": {
                    "label": "Править",
                    "action": function (data) {
                        var inst = $.jstree.reference(data.reference),
                            sel = inst.get_selected();
                        if (!sel.length) {
                            return false;
                        }
                        sel = sel[0];
                        $.ajax({
                            type: "POST",
                            url: '/menu/admin/form/' + sel + '/ajax',
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
                        let ref = $.jstree.reference(data.reference),
                            sel = ref.get_selected();
                        if (!sel.length) {
                            return false;
                        }
                        let selid = sel[0];
                        $.ajax({
                            type: "POST",
                            url: '/menu/admin/delete/' + selid,
                            success: function (msg) {
                                let rez = JSON.parse(msg);
                                //Показываем модально окно
                                if (rez.error == 0) {
                                    /* ИХ ДВА , посдстава из за jstree */
                                    let elem = document.getElementById(selid);
                                    if (typeof elem != "undefined") {
                                        elem.remove();
                                    }
                                    elem = document.getElementById(selid);
                                    if (typeof elem != "undefined") {
                                        elem.remove();
                                    }
                                } else {
                                    alert(rez.data);
                                }

                            }
                        });

                    }
                }
            };
        }
    },
}).on('move_node.jstree', function (e, data) {
    $.ajax({
        'url': '/menu/admin/changeposition',
        'type': 'POST',
        'data': {
            'id': data.node.id,
            'parent_id': data.parent,
            'position': data.position + 1,
            'old_position': data.old_position + 1
        }
    });
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

