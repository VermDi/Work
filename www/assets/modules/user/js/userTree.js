/**
 * Created by Евгения on 16.06.2017.
 */
var $userTree = $('#tree');
$userTree.on('select_node.jstree', function (e, data) {
    user.data = {'id': data.node.id};
    user.getUserInfo(function (data) {
        moment.locale();
        var date = "Активность отсутствует";
        var blocked = "Активен";
        var roles = 'Отсутствуют';
        var permissions = 'Отсутствуют';
        var label = 'success';
        if (data.login_at !== '0000-00-00 00:00:00') {
            date = moment(new Date(data.login_at.substr(0, 4), data.login_at.substr(5, 2) - 1, data.login_at.substr(8, 2), data.login_at.substr(11, 2), data.login_at.substr(14, 2), data.login_at.substr(17, 2))).format('DD.MM.YYYY hh:mm:ss');
        }
        if (data.blocked === "2") {
            blocked = "Заблокирован";
            label = "danger";
        }
        if (data.roles.length > 0) {
            roles = data.roles.join(', ');
        }
        if (data.permissions.length > 0) {
            permissions = data.permissions.join(', ');
        }
        $('.tree-right').html('<div class="user-info">' +
            '<div><span>Логин/E-mail:</span> ' + data.email + "</div>" +
            "<div class=\"blocked\"><span class=\"label label-" + label + "\">" + blocked + "</span></div>" +
            "<div><span>Уровень:</span> " + data.level + "</div>" +
            "<div><span>Дата последней активности:</span> " + date + "</div>" +
            "<div><span>Группы:</span> " + roles + "</div>" +
            "<div><span>Права:</span> " + permissions + "</div>" +
            "</div>");
    });
}).jstree({
    "core": {
        // so that create works
        "check_callback": true
    },
    "plugins": ["contextmenu"],
    "contextmenu": {
        "items": function ($node) {
            var tree = $("#tree").jstree(true);
            return {
                "Create": {
                    "label": "Редактировать",
                    "action": function (obj) {
                        $('#FormModal').modal({'remote': '/user/modal/' + $('#tree').data('id') + "/" + $node.data.id});
                    },
                    "icon": "glyphicon glyphicon-pencil"
                },
                "Rename": {
                    "label": "Добавить пользователя",
                    "action": function (obj) {
                        $("#FormModal").modal({'remote': '/user/modal/' + $node.data.id});
                    },
                    "icon": "fa fa-user-plus"
                },
                "Delete": {
                    "label": "Удалить",
                    "action": function (obj) {
                        if (confirm('Вы уверены что хотите удалить пользователя и всех его дочерних пользователей?')) {
                            user.data = {'id': $node.data.id};
                            user.deleteUser();
                            tree.delete_node($node);

                        }
                    },
                    "icon": "glyphicon glyphicon-trash"
                }
            };
        }
    }
});

var $formModal = $("#FormModal");
// $formModal.on('show.bs.modal', function (e) {
//     var target = $(e.relatedTarget);
//     $(this).find('.modal-title').html(target.data('title'));
//     $(this).find('.modal-body').load(target.data('action'));
// });

$formModal.on('shown.bs.modal', function () {
    var $role = $("#role");
    var $permission = $("#permission");
    $role.select2({}).on('select2:select', function (e) {
        user.data = {'role_id': e.params.data.id};
        user.getPermission(function (data) {
            if (data) {
                $permission.val(data).trigger('change')
            }

        });

    });
    $permission.select2();
    if ($("#email").val() !== '') {
        $document.find('form').find('.add-user-button').removeClass('disabled');
    }
});

$formModal.on('hidden.bs.modal', function (e) {
    $(this).removeData('bs.modal');
});

