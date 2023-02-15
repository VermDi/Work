<div class="panel">
    <div class="panel-heading">Записи в историке</div>
    <div class="panel-body">
        <table class="Listable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Ключ Модуля</th>
                <th>Ключ Записи</th>
                <th>USER ID</th>
                <th>DATE</th>
                <th>CONTROL</th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<script>


    document.addEventListener("DOMContentLoaded", function () {

        if ($('.Listable').length > 0) {
            window.TableLists = $('.Listable').DataTable({
                "ajax": "/historian/admin/getlist",
                "processing": true,
                "serverSide": false,
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                "ordering": false,
                "iDisplayLength": 50,
                rowId: 'id',
                "columns": [
                    {
                        "data": "id"
                    },
                    {
                        "data": "mod_key"
                    },
                    {
                        "data": "row_key"
                    },
                    {
                        "data": "user_id"
                    },
                    {
                        "data": "create_at"
                    },
                    {
                        "data": "control"
                    }
                ],
                "language": {
                    "url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
                }
            });
        }
    });
</script>