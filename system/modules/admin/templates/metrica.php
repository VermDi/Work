<div class="panel">
    <div class="panel-heading">Внести код метрик(и) в форму ниже, и нажмите "сохранить изменения"</div>
    <div class="panel-body">
        <form action="#" method="post" enctype="multipart/form-data">
            <textarea name="metrica" class="form-control"><?=$data;?></textarea>
            <button type="submit" class="btn btn-send" style="margin-top: 25px;">Сохранить изменения</button>
        </form>
        <blockquote style="margin-top: 15px;">Для вывода метрики используйте код &lt;?=\core\Html::Metrics();?>           </blockquote>
    </div>
</div>