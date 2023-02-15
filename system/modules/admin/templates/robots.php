<div class="col-sm-12">
    <div class="panel">
        <div class="panel-heading">
            <a href="/admin" class="btn btn-default btn-sm">Главная админ панели</a>
        </div>
        <div class="panel-body">


            <blockquote>Файл <b>Robots.txt</b> — текстовый файл, расположенный на сайте, который предназначен для
                роботов поисковых
                систем. В этом файле веб-мастер может указать параметры индексирования своего сайта как для всех роботов
                сразу, так
                и для каждой поисковой системы по отдельности.
            </blockquote>
            <?
            $robots_file = $_SERVER['DOCUMENT_ROOT'] . "/robots.txt";
            if (!file_exists($robots_file)) {
                $h = fopen($robots_file, "w+");
                $robots_text = file_get_contents($robots_file);
                fclose($h);
            } else {
                $robots_text = file_get_contents($robots_file);
            }
            ?>
            <div class="row">
                <div class="col-sm-4">

                    <form method="post" action="">
                        <div>
                            <textarea style="width: 280px;height: 300px;" name="text"><?= $robots_text ?></textarea>
                        </div>
                        <br/>

                        <input type="submit" value="Сохранить изменения" name="submit"
                               class="btn btn-sm btn-success"/>
                    </form>

                </div>
                <div class="col-sm-8">

                    <p style="font-weight: bold;">Страндартный robots.txt</p>
                    <div class="pageInfo">
                        User-agent: Yandex<br/>
                        Disallow: /admin<br/>
                        Disallow: /user/login<br/>
                        Host: <?= $_SERVER['SERVER_NAME'] ?><br/><br/>

                        User-agent: *<br/>
                        Disallow: /admin<br/>
                        Disallow: /user/login
                    </div>
                    <br/>
                    <p style="font-weight: bold;">Закрыть сайт от индиксации</p>
                    <div class="pageInfo">
                        User-agent: *<br/>
                        Disallow: /
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
