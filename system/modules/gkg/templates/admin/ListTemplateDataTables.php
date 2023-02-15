<?php
$rezult = \core\Db::instance()
    ->query("SHOW FULL COLUMNS from " . htmlspecialchars($_POST['GKGtable']))
    ->fetchAll();

$end = "<div class=\"panel\">
    <div class=\"panel-heading clearfix\">
        <div class=\"pull-left\"> Список объектов</div>
        <div class=\"pull-right btn-group\">
            <a class=\"btn btn-xs btn-success\" onclick=\"" . $data['GKGName'] . "List.showForm(<?=(isset(\$data->id) and \$data->id>0)?\$data->id:0;?>);\">Добавить
                Объект</a><a href=\"/" . $data['GKGName'] . "/admin/tocsv\" class=\"btn btn-xs btn-primary\">Скачать CSV</a><a
                href=\"/" . $data['GKGName'] . "/admin/fromcsv\" class=\"btn btn-xs btn-default\">Загрузить CSV</a></div>
    </div>
    <div class=\"panel-body\">
        <table id=\"" . $data['GKGName'] . "List-list\" class=\"table table-hover\">
            <thead>
            <tr>
            ";
foreach ($rezult as $k => $v) {
    $end .= "<th>" . $v[0] . "-" . $v['Comment'] . "</th>";
}
$end .= "
              
                <th>control</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<!-- ФОРМА ОБЪЕКТА -->
<div class=\"modal fade\" id=\"" . $data['GKGName'] . "List-modal\" tabindex=\"-1\" role=\"dialog\">
    <div class=\"modal-dialog modal-lg\" role=\"document\" style=\"width: 90%;\">
        <div class=\"modal-content\">
            <div class=\"modal-header\">
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span
                        aria-hidden=\"true\">&times;</span></button>
                <h4 class=\"modal-title\">Добавить объект</h4>
            </div>
            <div class=\"modal-body\" id=\"" . $data['GKGName'] . "List-body\">

            </div>
            <div class=\"modal-footer\">
                <button type=\"button\" class=\"btn btn-primary\" onclick=\"" . $data['GKGName'] . "List.saveForm();\">Отправить
                </button>
                <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Закрыть</button>
            </div>
        </div>
    </div>
</div>
<!-- /ФОРМА ОБЪЕКТА -->
<script type=\"text/javascript\">
    //создадим объект
    var " . $data['GKGName'] . "List =
        {
            table: \"\",
            showForm: function (id) {
                document.getElementById('" . $data['GKGName'] . "List-body').innerHTML = \"\";
                let url = \"/" . $data['GKGName'] . "/admin/form/\" + id +'/1'; //ajax
                $('#" . $data['GKGName'] . "List-modal').modal('show');
               // \$EM.ajax.load('#" . $data['GKGName'] . "List-body', url);
               // \$EM.helpers.init();
            },
            loadTable: function () {
                this.table = $('#" . $data['GKGName'] . "List-list').DataTable({
                    ajax: {
                        url: '/" . $data['GKGName'] . "/admin/getlist/true',
                        dataSrc: ''
                    },
                    rowId: 'id',
                    columns: [";
foreach ($rezult as $k => $v) {
    $end .= "{data: '" . $v[0] . "'},".PHP_EOL;
}
$end .= "
                     
                        {
                            data: null,
                            \"defaultContent\": '<span class=\"btn btn-xs btn-warning\" onclick=\"" . $data['GKGName'] . "List.edit(this);\"><i class=\"fa fa-edit\"></i></span>' +
                                ' <span class=\"btn btn-xs btn-danger\" onclick=\"" . $data['GKGName'] . "List.del(this);\"><i class=\"fa fa-trash\"></i></span>'

                        },

                    ],
                    \"language\": {
                        \"url\": \"/assets/vendors/datatables/datatables.ru/datatables_ru.json\"
                    }
                });

            },
            saveForm: function () {
                let dt = new FormData(document.forms['" . $data['GKGName'] . "List-form']);
                dt.append('content', ContentFormBuilder.collectToFormData());
                dt.append('point', Coordintas" . $data['GKGName'] . ");
                \$EM.ajax.post('/" . $data['GKGName'] . "/admin/save/1', dt).then(function (response) {
                    if (typeof response == \"undefined\") {
                        \$EM.html.toast.show({position: 'top-right', text: \"ЧТО ТО ПОШЛО НЕ ТАК!!!\", bgColor: 'red'});
                        return;
                    }
                    let resp = JSON.parse(response);

                    if (typeof resp.error == \"undefined\") {
                        $('#" . $data['GKGName'] . "List-modal').modal('hide');
                        " . $data['GKGName'] . "List.reloadTable();
                        \$EM.html.toast.show({position: 'top-right', text: 'Успешно!'});
                        return;
                    }

                    let msg = \"Ошибка: \"; //начало ошибки

                    if (typeof resp.data === 'object') { //объект, а если ошибки не будет то здесь будет undefined

                        let isFirst = true; //чтобы фокус был на первом ошибочном

                        for (let key in resp.data) {

                            let target = document.querySelector('input[name=\"' + key + '\"]'); //ипут поле с ошибкой

                            /* Блок для первой ошибки! */
                            if (isFirst) {
                                target.focus(); // переместили сюда курсор
                                isFirst = false;
                            }

                            target.classList.add('inputError');

                            target.addEventListener('keydown', removeErrorClass); // мы добавили по клику уаление ошибки

                            function removeErrorClass() {
                                target.classList.remove('inputError'); //удаляем ошибку
                                target.removeEventListener('keydown', removeErrorClass); // УБРАЛИ событие клика по уалению ошибки
                            }

                            msg = msg + ' Поле ' + key + ': ' + resp.data[key][0];


                        }

                    } else {
                        msg = resp.data;
                    }


                    if (msg !== undefined) {
                        \$EM.html.toast.show({position: 'top-right', text: msg, bgColor: '#c5141c'});
                    }


                });


            },
            reloadTable: function () {
                this.table.ajax.reload();
                //window.TableNews.ajax.reload();
            },
            del: function (obj) {
                let rowId = obj.parentNode.parentNode.getAttribute('id');
                \$EM.ajax.get('/" . $data['GKGName'] . "/admin/delete/' + rowId + '/ajax').then(function (response) {
                    console.log(response);
                    let resp = JSON.parse(response);
                    if (typeof resp.success != \"undefined\") {
                        " . $data['GKGName'] . "List.table.row(\"#\" + rowId).remove().draw();
                    } else {
                        \$EM.html.toast.show({position: 'top-right', text: 'ОШИБКА!!!'});
                    }
                });


            },
            edit: function (obj) {
                " . $data['GKGName'] . "List.showForm(obj.parentNode.parentNode.getAttribute('id'));
            }
        };

    document.addEventListener(\"DOMContentLoaded\", function (event) {
        " . $data['GKGName'] . "List.loadTable();

    });

    function show_wyswig() {
        if (CKEDITOR) {
            $('.ckeditor').ckeditor({
                filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr=',
            });
        } else {
            console.log('noEditor');
        }
    }

</script>";


echo $end;