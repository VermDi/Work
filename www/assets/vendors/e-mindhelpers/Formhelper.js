/**
 * Created by E_dulentsov on 11.11.2017.
 */

/**
 * Работает на JQ требует его подключения, все остально тоже требуется но упрощается
 * .emind-ckeditor - инициирует визулаьный редактор системы
 * .emind-counter - добавляет к полю счетчик (в конце поля)
 * .emind-dadataAdress - Добавляет подсказку от ДАДАТЫ
 */
var js = {};
js.loadedModules = {};
js.include = function (path) {
    var script = document.createElement("script");
    script.src = path;
    script.async = false;
    document.body.appendChild(script);
    return script; //возвращаем объект
};
var css = {};
css.include = function (path) {
    var css = document.createElement("link");
    css.href= path;
    css.type='text/css';
    css.async = false;
    css.rel = 'stylesheet';
    document.body.appendChild(css);
    return css; //возвращаем объект
}

function init() {
    /*
     Подключаем визуальный редактор встроенный.
     */
    var ckeditors = $(".emind-ckeditor");
    if (ckeditors.length > 0) {
        js.include("/assets/vendors/ckeditor/ckeditor.js");
        js.include("/assets/vendors/ckeditor/adapters/jquery.js").onload = function () {
            if (typeof CKEDITOR != 'undefined') {
                ckeditors.each(function () {
                        $(this).ckeditor({
                            filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                            filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                            filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
                        });
                    }
                );
            } else {
                console.log('-= Для работы должен быть редактор ckeditor =-');
            }
        }
    }
    /*
     Подключаем счетчики
     */
    var counters = $(".emind-counter");
    if (counters.length > 0) {
        counters.each(function () {
            var counter_span = document.createElement("span"); //создаем к нему span
            var counter_id = "counter_for_" + this.name; //создай id для обращения
            counter_span.id = counter_id;
            counter_span.classList.add('pull-right');
            this.parentNode.insertBefore(counter_span, this.nextSibling); //подпихиваем элемент
            // и при изменении его, подпихиваем данные
            this.onChange = this.onkeydown = this.onblur = this.onfocus = function () {
                counter_span.innerHTML = this.value.length;
            };
        });
    }
    /*
        Подключаем подсказки DaData для адреса
     */
    var adressSuggetions = $(".emind-dadataAdress");
    if (adressSuggetions.length > 0) {
        console.log('FOUND');
        css.include('https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css');
        js.include("https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/js/jquery.suggestions.min.js").onload = function () {
            console.log('LOADED');
            $(".emind-dadataAdress").suggestions({
                token: "08c4aa6efee18fe8fee413dd0dbb31689df26513",
                type: "ADDRESS",
                /* Вызывается, когда пользователь выбирает одну из подсказок */
                onSelect: function (suggestion) {
                    console.log(suggestion);
                }
            });

        }
    }
    console.log("=====INIT====");


}

$(function () {
    init();
});
