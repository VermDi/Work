<?php
include_once 'menu.php';
$viewSel = \modules\tinkoff\helpers\TinkoffHelper::$viewSel;
$buttonStyle = \modules\tinkoff\helpers\TinkoffHelper::$buttonStyle;
?>
<section>
    <div class="panel-heading clearfix">
        <h5 class="pull-left text-uppercase">Настройка кнопки Tinkoff Кредитования</h5>
    </div>
    <div class="tinkoff_img_box">
        <div class="image">
            <img width="30%" src="/assets/modules/tinkoff/img/tinkoff_shopID.png" alt=""
                 title="ShopID в Лк руководителя">
        </div>
        <div class="image">
            <img width="30%" src="/assets/modules/tinkoff/img/tinkoff_ShowcaseID.png" alt=""
                 title="ShowcaseID в Лк руководителя">
        </div>
        <div class="image">
            <img width="30%" src="/assets/modules/tinkoff/img/tinkoff_promoCode.png" alt=""
                 title="promoCode в Лк руководителя">
        </div>
    </div>
    <div style="margin-bottom: 20px">
        <h4>Для того чтобы настроить кнопку кредитования Tinkoff Банка вам потребуется:</h4>
        <div style="font-size: 14px;line-height: 22px;">
            <p>
                <b>1)</b> Зайти в личный кабинет руководителя, ввести телефон, код из СМС и пароль.<br>
                <b>2)</b> Перейти в личном кабинете <b>Тинькофф Бизнеса</b> → меню <b>«Продукты и сервисы»</b> → <b>«Кредитование
                    в магазинах»</b>.<br>
                <b>3)</b> Вам необходим <b>ShopID</b>, где его найти:<br>
                <b> - 3.1)</b> В личном кабинете руководителя перейдите в раздел <b>«Компании»</b> и нажмите на название
                нужной.<br>
                <b> - 3.2)</b> <b>ShopID</b> указан под юридическим адресом.<br>
                <b>4)</b> Вам необходим <b>ShowcaseID</b>, где его найти:<br>
                <b> - 4.1)</b> В личном кабинете руководителя перейдите в раздел <b>«Магазины»</b> и кликните на нужный
                магазин.<br>
                <b> - 4.2)</b> <b>ShowcaseID</b> указан под ссылкой на сайт.<br>
                <b>5)</b> Если вы хотите работать не только с кредитами. То вам необходим <b>promoCode</b>:<br>
                <b> - 4.1)</b> В личном кабинете руководителя перейдите на вкладку <b>«Рассрочки»</b> → <b>«Интернет-магазины»</b>.<br>
                <b> - 4.2)</b> Выберите рассрочку или кредит, который хотите отключить или подключить, и щелкните
                тумблер рядом с нужным вариантом.<br>
            </p>
        </div>
    </div>
    <form class="form-tinkoff_button">
        <div style="display: flex; flex-direction: column;">
            <input type="hidden" name="id" value="<?= isset($data->id) ? $data->id : '' ?>">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">ShopID:</label>
                <div class="col-sm-7">
                    <input value="<?= isset($data->SHOP_ID) ? $data->SHOP_ID : '' ?>" type="text"
                           name="SHOP_ID" id="SHOP_ID"
                           placeholder="Введите shopId" class="form-control">
                    <p><b>*</b> уникальный идентификатор компании</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">ShowcaseID:</label>
                <div class="col-sm-7">
                    <input value="<?= isset($data->SHOWCASE_ID) ? $data->SHOWCASE_ID : '' ?>" type="text"
                           name="SHOWCASE_ID" id="SHOWCASE_ID"
                           placeholder="Введите showcaseId " class="form-control">
                    <p><b>*</b> уникальный идентификатор сайта</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Кредит/Рассрочка(колл-мес):</label>
                <div class="col-sm-7">
                    <input
                            value="<?= isset($data->promoCode) ? ($data->promoCode == 'default' ? '' : $data->promoCode) : '' ?>"
                            type="text"
                            name="promoCode" id="promoCode"
                            placeholder="Введите promoCode" class="form-control">
                    <p><b>*</b> специальный <b>promoCode</b> для оформления рассрочек. Если вы хотите работать только с
                        кредитами оставьте поле пустым. </p>
                    <p>Так же устанавливаются ограничения в <b>ЛК на сайте Tinkoff Банка</b></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Формат вывода:</label>
                <div class="col-sm-7">
                    <select class="form-control" name="view" id="tinkoff_view">
                        <? foreach ($viewSel as $key => $val) { ?>
                            <option
                                    value="<?= $key ?>" <?= isset($data->view) && $data->view == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <? } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Текст в кнопке:</label>
                <div class="col-sm-7">
                    <input value="<?= isset($data->buttonName) ? $data->buttonName : '' ?>" type="text"
                           name="buttonName"
                           placeholder="Введите текст" class="form-control">
                    <p><b>*</b> по умолчанию если оставить поле пустым будет <b>"Купить в кредит"</b></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Формат вывода:</label>
                <div class="col-sm-7">
                    <select class="form-control" name="buttonStyle" id="">
                        <? foreach ($buttonStyle as $key => $val) { ?>
                            <option
                                    value="<?= $key ?>" <?= isset($data->buttonStyle) && $data->buttonStyle == $key ? 'selected' : '' ?>><?= $val ?></option>
                        <? } ?>
                    </select>
                    <p><b>*</b> по умолчанию среднего размера.</p>
                </div>
            </div>
        </div>
        <button class="btn btn-success" type="submit">Сохранить параметры</button>
    </form>
    <div style="margin: 25px 0 25px 0;">
        <div style="display: block"><b>Пример кнопки на проекте после сохранения параметров:</b></div>
        <div style="display: block;margin-top: 15px;">
            <button
                    type='button'
                    class='TINKOFF_BTN_YELLOW <?= isset($data->buttonStyle) ? $data->buttonStyle : '' ?> credTinkoff'><?= isset($data->buttonName) ? $data->buttonName : '' ?></button>
        </div>
    </div>
</section>
<script>
    let btnCredTinkoff = document.querySelector('.credTinkoff');
    let btnpromoCode = document.querySelector('#tinkoff_promoCode');
    let btnpromoCode_val = document.querySelector('#promoCode').value;
    let btnshopId = document.querySelector('#SHOP_ID').value;
    let btnshowcaseId = document.querySelector('#SHOWCASE_ID').value;
    let btnview = document.querySelector('#tinkoff_view');
    let btnview_val = btnview.options[btnview.selectedIndex].value;

    if (btnpromoCode_val === '') {
        btnpromoCode_val = 'default'
    }

    function credClick() {
        tinkoff.createDemo(
            {
                sum: 100500,
                items: [{name: 'iphone 11', price: 100000, quantity: 1}, {name: 'Чехол', price: 500, quantity: 1}],
                promoCode: btnpromoCode_val,
                shopId: btnshopId,
                showcaseId: btnshowcaseId,
            },
            {view: btnview_val}
        )
    }

    btnCredTinkoff.addEventListener('click', credClick)

</script>