<?

use core\Parameters;

/*
  * @property string id -
 * @property string title  -
 * @property string meta_keywords -
 * @property string meta_description -
 * @property string meta_additional -
 * @property string content -
 * @property string design -
 * @property string menu_name -
 * @property string visible -
 * @property string url -
 * @property string create_at -
 * @property string user_id -
 * @property string level -
 * @property string left -
 * @property string right -
 * @property string domain
  *
  *
  * Visible - 0 нет, 1 - да, 2 - черновик (видит тока админ)
  *
  * */
/**
 * @var $data array
 */
$pid = $data[1]; //если мы создаем дочернююю страницу!
$data = $data[0];
$params = Parameters::getDefault('menu');
?>
<div class="panel row">
    <div class="panel-heading">Форма редактирования меню</div>
    <div class="panel-body pages-form">
        <form action="/menu/admin/save" method="post" class="form-horizontal form" enctype="multipart/form-data"
              id="PageForm">
            <?php if ($data->id > 0) { ?><input type="hidden" name="id" value="<?= $data->id; ?>"> <?php } ?>
            <?php if ($pid != false) { ?><input type="hidden" name="pid" value="<?= $pid; ?>"> <?php } ?>
            <div class="row clearfix">
                <div class="col-sm-6">
                    <div class="input-group-sm">
                        <label for="url">URL пункта меню</label>
                        <input type="text" name="url" id="url" class="form-control" value="<?= $data->url; ?>">
                        <span class="help-inline" id="numtypes">0</span>
                    </div>
                    <div class="col-sm-12">
                        <div class="col-sm-6" style="padding: 0 0 0 7px;">
                            <label>
                                <input type="checkbox" name="is_nofollow"
                                       value="1" <?= ($data->is_nofollow == 1) ? "checked" : ""; ?>> - nofollow
                            </label>
                        </div>
                        <div class="col-sm-6" style="padding: 0 0 0 7px;">
                            <label>
                                <input type="checkbox" name="is_noindex"
                                       value="1" <?= ($data->is_nofollow == 1) ? "checked" : ""; ?>> - noindex</label>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="name">Название для меню</label>
                    <input type="text" name="name" class="form-control" value="<?= $data->name; ?>">


                    <div class="col-sm-6" style="padding: 0 0 0 7px;">
                        <label for="visible">Видимость</label>
                        <select name="visible" class="form-control">
                            <option value="1" <?= ($data->visible == 1 or empty($data->visible)) ? "selected" : ""; ?>>
                                Видима
                            </option>
                            <option value="0" <?= ($data->visible == 0) ? "selected" : ""; ?>>Скрыта</option>
                            <option value="2" <?= ($data->visible == 2) ? "selected" : ""; ?>>Черновик</option>
                        </select>
                    </div>

				</div>
				<div class="col-sm-12">
					<? \modules\menu\services\extParametes::print_form($data->extData); ?>
				</div>
				<?php if (!empty($params['params']['menu_icon']) and class_exists($params['params']['menu_icon']['class'])) { ?>
					<div class="col-sm-12">
						<div class="col-sm-12" style="padding: 0 0 0 7px;">
							<label for="menu_icon">Картинка</label>
							<input type="checkbox" name="image" id="menu_icon"
								   value="1" <?= (in_array($data->id, $params['params']['menu_icon']['record_id'])) ? "checked" : ""; ?>>
							картинка

                        </div>
                        <div class="form-menu-icon col-sm-12">
                            <label for="sizes">Размер (ШхВ)</label>
                            <input type="text" name="sizes" class="form-control" id="sizes"
                                   value="<?= $params['params']['menu_icon']['size']; ?>">
                            <?php if (!empty($params['params']['menu_icon']['class'])) {

                                    $image = new $params['params']['menu_icon']['class']('menu');
                                    echo $image->show($data->id);

                            } ?>
                        </div>
                    </div>
                <?php } ?>

            </div>


            <div class="col-sm-12 row" style="margin-top: 15px;">
                <button class='btn btn-success col-sm-5' id='sendAndOut' type='submit'>Сохранить</button>
                <button class='btn btn-warning col-sm-5 col-sm-offset-2' id='sendAndStop' type='submit'>Сохранить и
                    остаться
                </button>

            </div>
        </form>
    </div>
</div>
<style>
    .pages-form label {
        font-size: 13px;
        font-weight: normal;
    }

    .help-inline {
        font-size: 10px;
        float: right;
    }

    .form-menu-icon {
        display: none;
    }
</style>
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", GO);


    function GO() {
        /*
         * ссылка
         */
        var url = document.getElementById('url');
        var counter = document.getElementById('numtypes');

        function showCount() {
            counter.innerHTML = url.value.length;
        }

        var $document = $(document);
        $document.on('click', '#menu_icon', function () {
            $('.form-menu-icon').toggle();
        });
        url.onChange = url.onkeydown = url.onblur = url.onfocus = showCount;

        /*
         * Отправка формы на сервер ajax! Она уже пашет не как ajax а это добавляет аякс
         */
        $("#PageForm").submit(function (e) {
            var btn = $(document.activeElement, this).attr('id');
            e.preventDefault();
            var href = $(this).attr('action');
            var d = $(this).serialize();
            $.ajax({
                type: "POST",
                url: href,
                data: d,
                success: function (msg) {
                    var result = JSON.parse(msg);
                    if (result.error === 0) {
						if (!btn){
							$("#Modalkabody").html('');
							$("#Modalka").modal('hide');
							location.reload();
						}
                        if (btn === 'sendAndOut') {
                            $("#Modalkabody").html('');
                            $("#Modalka").modal('hide');
                            location.reload();
                        }
                        if (btn === 'sendAndStop') {
                            $("#PageForm").append('<input type="hidden" name="id" value="' + result.data.id + '">');
                            $('#url').val(result.data.url);
                        }
                    } else {
                        alert('ОШИБКА!');
                    }
                }
            });
            return false;
        });
    }
</script>

