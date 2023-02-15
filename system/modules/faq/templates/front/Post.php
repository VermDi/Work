
<section class="pb_section pb_slant-white" id="section-faq">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-6 text-center mb-5">
                <h2><?= $data['Question']->title ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <div class="question" style="padding-bottom: 30px;">
                    <div class="item">
                        <div class="answer-user"><?= $data['Question']->fio ?></div>
                        <div class="answer-date"><?= date('d-m-Y', strtotime($data['Question']->date)) ?></div>
                        <div class="answer-content"><?= nl2br($data['Question']->questions) ?></div>
                    </div>
                </div>

                <div id="pb_faq" class="pb_accordion" data-children=".item">
                    <?php

                    if(is_array($data['Answers'])){
                        if(!empty($data['Answers'])){
                            ?><div class="answers"><?php
                            foreach ($data['Answers'] as $Answer){
                                $answer = nl2br($Answer->answer);
                                ?>
                                <div class="item<?= (!empty($Answer->best))?' answer-best':'' ?>">
                                    <div class="answer-user"><?= $Answer->fio ?></div>
                                    <div class="answer-date"><?= date('d-m-Y', strtotime($Answer->date)) ?></div>
                                    <div class="answer-content"><?= $answer ?></div>
                                </div>
                                <?php
                            }
                            ?></div><?php
                        } else {
                    ?><div class="answers">
                            <div class="item">
                                <div class="answer-user"></div>
                                <div class="answer-date"></div>
                                <div class="answer-content">Нет ответов</div>
                            </div>
                            </div><?php
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- END section -->
<section class="pb_cover_v3 overflow-hidden cover-bg-indigo cover-bg-opacity text-left pb_gradient_v1 pb_slant-light"
         id="section-home">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            <div class="col-md-8 col-md-offset-3 relative align-self-center">


                <form class="bg-white rounded pb_form_v1 answerForm">
                    <input type="hidden" name="faq_questions_id" value="<?= $data['Question']->id ?>">
                    <h2 class="mb-2 mt-0 text-center">Ответить</h2>
                    <?php
                    if(!empty(\modules\user\models\USER::current()->fio)){
                        ?>
                        <div class="form-group">
                            <input disabled name="fio" type="text" class="form-control pb_height-50 reverse" placeholder="ФИО" value="<?= (!empty(\modules\user\models\USER::current()->fio))?\modules\user\models\USER::current()->fio:'' ?>">
                        </div>
                        <input type="hidden" name="fio" value="<?= (!empty(\modules\user\models\USER::current()->fio))?\modules\user\models\USER::current()->fio:'' ?>">
                        <?php
                    } else {
                        ?>
                        <div class="form-group">
                            <input name="fio" type="text" class="form-control pb_height-50 reverse" placeholder="ФИО">
                        </div>
                        <?php
                    }
                    ?>

                    <?php
                    if(!empty(\modules\user\models\USER::current()->email)){
                        ?>
                        <div class="form-group">
                            <input disabled name="email" type="text" class="form-control pb_height-50 reverse"
                                   placeholder="Email" value="<?= \modules\user\models\USER::current()->email ?>">
                        </div>

                        <input type="hidden" name="email" value="<?= \modules\user\models\USER::current()->email ?>">
                        <?php
                    } else {
                        ?>
                        <div class="form-group">
                            <input name="email" type="text" class="form-control pb_height-50 reverse"
                                   placeholder="Email">
                        </div>
                        <?php
                    }
                    ?>
                    <div class="form-group">
                        <textarea placeholder="Ответ" class="form-control" rows="7" name="answer"></textarea>
                    </div>
                    <?php \modules\recaptcha\widgets\wRecaptcha::showForm(); ?>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-lg btn-block pb_btn-pill  btn-shadow-blue"
                               value="Опубликовать ответ">
                    </div>

                </form>

            </div>
        </div>
    </div>
</section>
<style>
    .question .item{
        padding: 10px;
        padding-top: 50px;
        padding-bottom: 50px;
        border-bottom: 1px solid #ccc;
        margin-bottom: 30px;
        background: #665fee0a;
    }
    .answers .item {
        padding: 10px;
        padding-bottom: 30px;
        border-bottom: 1px solid #ccc;
        margin-bottom: 30px;
    }
    .answer-user {
        font-size: 1.2em;
        font-weight: bold;
        float: left;
    }
    .answer-date {
        font-size: 1em;
        /*font-weight: bold;*/
        float: right;
    }
    .answer-content {
        clear: both;
        padding-top: 20px;
    }
    .answer-best {
        background: #00ca4c17;
    }
</style>