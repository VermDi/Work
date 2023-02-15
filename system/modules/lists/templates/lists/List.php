<div class="panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-4 pull-left">Списки</div>
        <div class="col-sm-8 pull-right" style="text-align: right;">
            <a href="/lists/lists/form/0" data-title="Добавление" class="btn btn-xs btn-success ajax">Добавить список</a>
        </div>
    </div>
    <div class="panel-body">
            <table class="table table-striped table-hover table-bordered responsive TableLists">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование списка</th>
                    <th>Каноническое имя</th>
                    <th>Кол-во элементов</th>
                    <th data-orderable="false">Управление</th>
                </tr>
                </thead>
            </table>
    </div>
</div>
<div class="modal fade" id="Form">
    <div class="modal-dialog modal-lg modal-full" style="height: 100%;">
        <div class="modal-content" style="height: 90%; overflow-y: scroll;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ContentTitle"></h4>
            </div>
            <div class="modal-body clearfix" id="AjaxContent">

            </div>
        </div>
    </div>
</div>
