/**
 * Created by Евгения on 19.06.2017.
 */
var scanButton = $('button[data-action="scan"]');
var scanAddButton = $('button[data-action="scanAdd"]');
var scanPermission = function () {
    scanButton.trigger('scan.permission.start');
    $.get('/user/permission/scan', function (response) {
        scanButton.trigger('scan.permission.success', response);
    })
};
var scanAddPermission = function (permissions) {
    scanAddButton.trigger('scan.add.permission.start');
    $.post('/user/permissionscan/scan', permissions, function (response) {
        scanAddButton.trigger('scan.add.permission.success', response);
    })
};
scanButton
    .on('click', function () {
        scanPermission()
    })
    .on('scan.permission.start', function () {
        $(this).button('loading');
    })
    .on('scan.permission.success', function (event, data) {
        $(this).button('reset');
        var modal = $('#scanPermissionModal');
        var tableHtml = '<thead><tr><th>Модуль</th><th>Имя</th><th>Описание</th></tr></thead><tbody>';
        $.each(data, function (module, permissions) {
            if (permissions !== 1) {
                var firstRow = true;
                $.each(permissions, function (name, description) {
                    if (firstRow) {
                        tableHtml += '<tr><th rowspan="' + Object.keys(permissions).length + '">' + module + '</th><th>' + name + '</th><th>' + description + '</th></tr>'
                        firstRow = false;
                    } else {
                        tableHtml += '<tr><th>' + name + '</th><th>' + description + '</th></tr>'
                    }
                });
            }

            tableHtml += '</tbody>';
        });
        modal.find('#scanPermissionTable').html(tableHtml);
        modal.modal();
    });

scanAddButton.on('click', function () {
    scanAddPermission();
})
    .on('scan.add.permission.start', function () {
        $(this).button('loading');
    })
    .on('scan.add.permission.success', function (event, data) {
        $(this).button('reset');
        $('#scanPermissionModal').modal('hide');
        var resultHtml = '';
        $.each(data.data, function (name, result) {
            resultHtml += '<div class="alert alert-' + result.type + '"><strong>' + name + '</strong> ' + result.message + '</div>';
        });
        var modal = $('#scanPermissionResultModal');
        modal.find('.result-content').html(resultHtml);
        modal.modal();
    });
var modal = $('#scanPermissionResultModal');
modal.on('hidden.bs.modal', function () {
    location.href = '/user/permission'
});

function formatState(state) {
    if (!state.id) {
        return state.text;
    }
    var $state = $(
        '<span><strong>' + state.text + '</strong> ' + state.element.getAttribute('data-subtext') + '</span>'
    );
    return $state;
};
var $permissions = $("#permissions");
$permissions.select2({
    templateResult: formatState
});