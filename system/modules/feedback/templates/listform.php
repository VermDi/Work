<?php

use core\Request;
use modules\feedback\models\mFeedbackFields;

?>
<div class="row">
    <div class="col-sm-12 panel-heading">
        <?= $data['topmenu']; ?>
        <div class="col-sm-12">
            <form class="form-horizontal" method="POST" action="/feedback/admin/saveallmail">
                <fieldset>
                    <div class="form-group">
                        <div class="col-sm-4 pull-right">
                            <label for="name" class="control-label">Общий email уведомлений, если не задан у
                                формы</label>
                            <input type="text" class="form-control" name="allmail" value="<?= $data['allmail']; ?>">
                            <button type="submit">Схранить</button>
                        </div>
                    </div>
                </fieldset>

            </form>
            <div class="panel-body">
                <?php
                if (!empty($data['forms'])) {
                    ?>
                    <table class="table table-bordered table-striped table-hover" id="result">
                        <thead>
                        <tr class="info">
                            <td>ID формы</td>
                            <td>Название формы</td>
                            <td>Доступные поля формы</td>
                            <td>Email уведомлений</td>
                            <td>Управление</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($data['forms'] as $value) { ?>
                        <tr class="form-item" data-id="<?= $value->id; ?>">
                            <td>
                                <?= $value->id; ?>
                            </td>
                            <td>
                                <?= $value->name; ?>
                            </td>
                            <td>
                                <ul>
                                    <?php foreach (json_decode($value->fields, TRUE) as $key_1 => $value_1) { ?>
                                        <li><strong><?= $value_1['name_in_form'] ?></strong></li>
                                    <?php } ?>
                                </ul>
                            </td>
                            <td>
                                <?= $value->email; ?>
                            </td>
                            <td>
                                <a href="/feedback/admin/addform/<?= $value->id; ?>" class="btn btn-warning btn-xs"><i
                                        class="fa fa-edit"></i></a>
                                <a href="/feedback/admin/delete/<?= $value->id; ?>" class="btn btn-xs btn-danger"
                                   onclick="return confirm('Вместе с удалением формы, удалятся также и все Feedbacks, Вы уверены?') ? true : false;"><i
                                        class="fa fa-trash-o"></i></a>
                            </td>
                            <?php } ?>
                        </tr>
                        </tbody>
                    </table>
                    <div class="advices col-sm-12 clearfix">
                        <div class="col-sm-12">
                            <h4 class="text-success">Пример заполненной формы без action:</h4>
                            <?
                            if (file_exists(__DIR__ . '/example_new.txt')) {
                                echo nl2br(htmlspecialchars(file_get_contents(__DIR__ . '/example_new.txt')));
                            }
                            ?>
                        </div>
                        <div class="text-info col-sm-5 pull-left">
                            <h4>Для создания формы Вам необходимо использовать следующие данные:</h4>
                            <p class="text-primary">Метод отправки формы - <span class="text-success">POST</span></p>
                            <p class="text-primary">Обработчик формы - <span class="text-success">/feedback/send</span>
                            </p>
                            <p class="text-primary">Возможно указания редикта после отправки пользователем сообщения, с
                                помощью скрытого поля с названием <span class="text-success">redirect</span></p>
                            <p class="text-danger">Обязательное условие - <span class="text-danger">указание скрытого поля, с ID формы</span>
                            </p>
                        </div>
                        <div class="col-sm-5 pull-right">
                            <h4 class="text-success">Пример корректно заполненной формы:</h4>
                            <div id="form-html-wrap">
                            <?
                            if ($form && $fields && $php_str){
                                $form_html = include (__DIR__ . '/form_html.txt');
                            }
                            else if (file_exists(__DIR__ . '/example.txt')) {
                                echo file_get_contents(__DIR__ . '/example.txt');
                            }
                            ?>
                            </div>
                        </div>
                    </div>
                    <h2>Пример ajax</h2>
                    <p>Вставляем код</p>
                    <?php
                    $formText = '
                                    <!-- Подключаем js -->
                                    <script src="/assets/modules/feedback/js/sendform.js"></script>
                                    
                                    <form method="post" action="#" data-tosend="/feedback/sendjson" class="FeedbackForm">
                                    
                                    <!-- Обязательные поля -->
                                    <input type="hidden" name="text_success"
                                           value="Спасибо! Мы вам перезвоним, в самое короткое время!">
                                    <input type="hidden" name="form_id" value="26">
                                    
                                    <!-- Добавляем свои поля -->
                                    <input type="text" name="name" class="form-control" placeholder="Имя">
                                    
                                    
                                    <button type="submit" class="btn btn-success">Отправить</button>
                                </form>';
                    ?>
                    <p><?= nl2br(htmlspecialchars($formText)) ?></p>
                <?php } else { ?>
                    <div class="warning">Feedback на сайте нет!</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>