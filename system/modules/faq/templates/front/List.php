<?php
echo"<pre>";print_r('ss');echo"</pre>";
?>
<section class="pb_section pb_slant-white" id="section-faq">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-md-6 text-center mb-5">
                <h2>Часто задаваемые вопросы</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <div id="pb_faq" class="pb_accordion" data-children=".item">
                    <?php

                    if(is_array($data['Questions'])){
                        if(!empty($data['Questions'])){
                            ?><div class="questions"><?php
                            foreach ($data['Questions'] as $Question){
                                $Answer = \modules\faq\models\mAnswers::instance()->getAnswers(['faq_questions_id'=>$Question->id, 'best'=>1, 'status'=>1, 'getOne'=>true]);
                                ?>
                                <div class="item">
                                    <a href="/faq/post/<?= $Question->id ?>" class="pb_font-22 py-4"><?= $Question->title ?></a>
                                    <div id="pb_faq2" class="collapse" role="tabpanel">
                                        <div class="py-3">
                                            <p><?= (!empty($Answer->answer))?$Answer->answer:'Ответить' ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?></div><?php
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
            <div class="col-md-5">
                <h2 class="heading mb-3">Задать вопрос</h2>
                <div class="sub-heading">
                    <p class="mb-4">Задайте Ваш вопрос. Мы с радостью ответим на вопросы, которые у Вас появились</p>
                    </p>
                </div>
            </div>
            <div class="col-md-7 relative align-self-center">


                        <form class="bg-white rounded pb_form_v1 faqForm">
                            <h2 class="mb-4 mt-0 text-center">Задайте Ваш вопрос.</h2>
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
                                <input name="title" type="text" class="form-control pb_height-50 reverse" placeholder="Вопрос" value="">
                            </div>
                            <div class="form-group">
                                <textarea placeholder="Подробный текст" class="form-control" rows="7" name="questions"></textarea>
                            </div>
                            <?php \modules\recaptcha\widgets\wRecaptcha::showForm(); ?>
                            <div class="form-group">
                                <input type="submit" class="btn btn-primary btn-lg btn-block pb_btn-pill  btn-shadow-blue"
                                       value="Опубликовать вопрос">
                            </div>

                        </form>

            </div>
        </div>
    </div>
</section>
<style>
    .pb_accordion .item > a:after {
        display: none;
    }
</style>
