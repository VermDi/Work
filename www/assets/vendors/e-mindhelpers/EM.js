/**
 *
 * @type {{text: {}, html: {}, ajax: {}, events: {}}}
 */
const $EM = {
    text: {
        /**
         * Метод принимает на вход индификатор поля, который будет контролироваться на ввод текста
         * На текущий момент это русский и английский. После указания, вводимый текст, автоматом будет менять на нужный
         * по сути будет автозамена текста
         *
         * @example $EM.text.toLang('.english','eng');
         * @example $EM.text.toLang('.russian','ru');
         * \
         * @param target
         * @param toLang
         */
        toLang: function (target, toLang) {

            let all = document.querySelectorAll(target);
            let mapRu = {
                'q': 'й',
                'w': 'ц',
                'e': 'у',
                'r': 'к',
                't': 'е',
                'y': 'н',
                'u': 'г',
                'i': 'ш',
                'o': 'щ',
                'p': 'з',
                '[': 'х',
                ']': 'ъ',
                'a': 'ф',
                's': 'ы',
                'd': 'в',
                'f': 'а',
                'g': 'п',
                'h': 'р',
                'j': 'о',
                'k': 'л',
                'l': 'д',
                ';': 'ж',
                '\'': 'э',
                'z': 'я',
                'x': 'ч',
                'c': 'с',
                'v': 'м',
                'b': 'и',
                'n': 'т',
                'm': 'ь',
                ',': 'б',
                '.': 'ю',
                'Q': 'Й',
                'W': 'Ц',
                'E': 'У',
                'R': 'К',
                'T': 'Е',
                'Y': 'Н',
                'U': 'Г',
                'I': 'Ш',
                'O': 'Щ',
                'P': 'З',
                '{': 'Х',
                '}': 'Ъ',
                'A': 'Ф',
                'S': 'Ы',
                'D': 'В',
                'F': 'А',
                'G': 'П',
                'H': 'Р',
                'J': 'О',
                'K': 'Л',
                'L': 'Д',
                ':': 'Ж',
                '|': 'Э',
                'Z': '?',
                'X': 'ч',
                'C': 'С',
                'V': 'М',
                'B': 'И',
                'N': 'Т',
                'M': 'Ь',
                '<': 'Б',
                '>': 'Ю'
            };
            let mapEng = {
                'й': 'q',
                'ц': 'w',
                'у': 'e',
                'к': 'r',
                'е': 't',
                'н': 'y',
                'г': 'u',
                'ш': 'i',
                'щ': 'o',
                'з': 'p',
                'х': '[',
                'ъ': ']',
                'ф': 'a',
                'ы': 's',
                'в': 'd',
                'а': 'f',
                'п': 'g',
                'р': 'h',
                'о': 'j',
                'л': 'k',
                'д': 'l',
                'ж': ';',
                'э': '\'',
                'я': 'z',
                'ч': 'x',
                'с': 'c',
                'м': 'v',
                'и': 'b',
                'т': 'n',
                'ь': 'm',
                'б': ',',
                'ю': '.',
                'Й': 'Q',
                'Ц': 'W',
                'К': 'R',
                'Е': 'T',
                'Н': 'Y',
                'Г': 'U',
                'Ш': 'I',
                'Щ': 'O',
                'З': 'P',
                'Х': '[',
                'Ъ': ']',
                'Ф': 'A',
                'Ы': 'S',
                'В': 'D',
                'А': 'F',
                'П': 'G',
                'Р': 'H',
                'О': 'J',
                'Л': 'K',
                'Д': 'L',
                'Ж': ';',
                'Э': '\'',
                '?': 'Z',
                'Ч': 'X',
                'С': 'C',
                'М': 'V',
                'И': 'B',
                'Т': 'N',
                'Ь': 'M',
                'Б': ',',
                'Ю': '.'
            };
            let curLang = "";
            if (toLang === 'eng') {
                curLang = mapEng;
            }
            if (toLang === 'ru') {
                curLang = mapRu;
            }
            for (let i = 0; i < all.length; ++i) {
                if (all[i].value !== undefined) {
                    all[i].onkeyup = function () {
                        let str = this.value;
                        let r = '';
                        for (let i = 0; i < str.length; i++) {
                            r += curLang[str.charAt(i)] || str.charAt(i);
                        }
                        this.value = r;
                    };
                }
            }
        },
        translitText: function (word, url = null) {
            var answer = ""
                , a = {};

            a["Ё"] = "YO";
            a["Й"] = "I";
            a["Ц"] = "TS";
            a["У"] = "U";
            a["К"] = "K";
            a["Е"] = "E";
            a["Н"] = "N";
            a["Г"] = "G";
            a["Ш"] = "SH";
            a["Щ"] = "SCH";
            a["З"] = "Z";
            a["Х"] = "H";
            a["Ъ"] = "'";
            a["ё"] = "yo";
            a["й"] = "i";
            a["ц"] = "ts";
            a["у"] = "u";
            a["к"] = "k";
            a["е"] = "e";
            a["н"] = "n";
            a["г"] = "g";
            a["ш"] = "sh";
            a["щ"] = "sch";
            a["з"] = "z";
            a["х"] = "h";
            a["ъ"] = "'";
            a["Ф"] = "F";
            a["Ы"] = "I";
            a["В"] = "V";
            a["А"] = "a";
            a["П"] = "P";
            a["Р"] = "R";
            a["О"] = "O";
            a["Л"] = "L";
            a["Д"] = "D";
            a["Ж"] = "ZH";
            a["Э"] = "E";
            a["ф"] = "f";
            a["ы"] = "i";
            a["в"] = "v";
            a["а"] = "a";
            a["п"] = "p";
            a["р"] = "r";
            a["о"] = "o";
            a["л"] = "l";
            a["д"] = "d";
            a["ж"] = "zh";
            a["э"] = "e";
            a["Я"] = "Ya";
            a["Ч"] = "CH";
            a["С"] = "S";
            a["М"] = "M";
            a["И"] = "I";
            a["Т"] = "T";
            a["Ь"] = "'";
            a["Б"] = "B";
            a["Ю"] = "YU";
            a["я"] = "ya";
            a["ч"] = "ch";
            a["с"] = "s";
            a["м"] = "m";
            a["и"] = "i";
            a["т"] = "t";
            a["ь"] = "'";
            a["б"] = "b";
            a["ю"] = "yu";
            if (url !== null) {
                a[" "] = "-";
            }
            for (i in word) {
                if (word.hasOwnProperty(i)) {
                    if (a[word[i]] === undefined) {
                        answer += word[i];
                    } else {
                        answer += a[word[i]];
                    }
                }
            }
            return answer;
        },
    },
    html: {
        /**
         *
         * Метод для работы с DOM (HTML)
         * На данный момент реализован один метод, которые показывает tooltips
         * @example Создаем любой элемент у которого указываем атрибут em-tooltip и в нем текст
         * @example Затем инициируем метод. И получаем тултипы
         * @example $EM.html.tooltip();
         *
         * @type {html}
         */
        tooltip: function () {
            let showingTooltip = false;
            document.onmouseover = function (e) {
                let target = e.target;

                let tooltip = target.getAttribute('em-tooltip');
                if (!tooltip) return;

                let tooltipElem = document.createElement('div');
                tooltipElem.className = 'tooltip';
                tooltipElem.style = 'position: fixed;padding: 10px 20px; border: 1px solid #b3c9ce;border-radius: 4px;text-align: center;font: italic 14px/1.3 sans-serif;color: #333;background: #fff;box-shadow: 3px 3px 3px rgba(0, 0, 0, .3);';
                tooltipElem.innerHTML = tooltip;
                document.body.appendChild(tooltipElem);

                let coords = target.getBoundingClientRect();

                let left = coords.left + (target.offsetWidth - tooltipElem.offsetWidth) / 2;
                if (left < 0) left = 0; // не вылезать за левую границу окна

                let top = coords.top - tooltipElem.offsetHeight - 5;
                if (top < 0) { // не вылезать за верхнюю границу окна
                    top = coords.top + target.offsetHeight + 5;
                }

                tooltipElem.style.left = left + 'px';
                tooltipElem.style.top = top + 'px';

                showingTooltip = tooltipElem;
            };

            document.onmouseout = function () {

                if (showingTooltip) {
                    document.body.removeChild(showingTooltip);
                    showingTooltip = null;
                }

            };
        },
        /**
         * Асинхронная загрузка js скриптов в код
         * @param path
         * @return {HTMLScriptElement}
         */
        jsAsyncLoad: function (path) {
            var script = document.createElement("script");
            script.src = path;
            script.async = false;
            document.body.appendChild(script);
            return script; //возвращаем объект
        },
        /**
         *
         * Тостеры, позволяет показывать всплывашки
         * применять так:
         * $EM.html.toast.show({position: 'top-right', text:'COOL!<br>COOL!<br>IN COOL!'})
         */
        toast: {
            position: 'top-right', ///можно bottom-left и т.д. первое вертикаль, второе горизонталь
            bgColor: 'rgba(27, 138, 64,0.7)', //любой цвет в любом формате
            color: 'white',
            lifeTime: 5000, // время показа в милисекундах
            isCss: 0,
            text: "",
            toastContainer: null,
            show: function (options, txt = null) {
                for (key in options) {
                    if (typeof this[key] !== undefined) {
                        this[key] = options[key];
                    }
                }
                /**
                 *
                 * @type {HTMLStyleElement}
                 */
                if (this.isCss === 0) {
                    var style = document.createElement("style");
                    style.innerHTML = "" +
                        ".notAnimate {transform: scale(0);}" +
                        ".animate{\n" +
                        "  transform: scale(1);\n" +
                        "}";
                    document.body.appendChild(style);
                    this.isCss = 1;
                    this.toastContainer = document.createElement("div");
                    this.toastContainer.style.position = "fixed";
                    this.toastContainer.style.zIndex = "99999";
                    this.toastContainer.style.position = "fixed";
                    let postition = this.position.split('-');
                    if (postition[0].toLowerCase() === 'top') {
                        this.toastContainer.style.top = "3%";
                    } else {
                        this.toastContainer.style.bottom = "3%";
                    }
                    if (postition[1].toLowerCase() === 'left') {
                        this.toastContainer.style.left = "2%";
                    } else {
                        this.toastContainer.style.right = "2%";
                    }
                    document.body.appendChild(this.toastContainer);
                }
                /**
                 *
                 * @type {HTMLDivElement}
                 */
                var newDiv = document.createElement("div");
                newDiv.style.backgroundColor = this.bgColor;
                newDiv.style.position = "relative";
                let rand = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
                newDiv.setAttribute("id", rand);
                newDiv.innerHTML = this.text;
                newDiv.classList.add('notAnimate');
                newDiv.style.transition = "0.7s";
                newDiv.style.color = this.color;
                newDiv.style.padding = "15px";
                this.toastContainer.appendChild(newDiv);
                setTimeout(function () {
                    newDiv.classList.add('animate');
                }, 10);

                setTimeout(function () {
                    newDiv.classList.remove('animate');
                    setTimeout(function () {
                        document.getElementById(rand).remove();
                    }, 1000);

                }, this.lifeTime);

            },
        },
        /*--------------------------*/

    },
    ajax: {
        response: {},
        promise: {},
        url: "",
        method: "GET",
        error: null,
        resultat: null,
        headers: null,
        files: [],
        txts: [],
        /**
         * Отправка данных методом пост
         *
         * $EM.ajax.post(url,data).then(function (response) {});
         *
         * @param url
         * @param data
         * @returns {*}
         */
        post: function (url, data) {
            if (data !== undefined) {
                this.data = data;
            }
            this.url = url;
            this.method = 'POST';
            return this.send();
        },

        /**
         * Отправка данных методом гет
         * @param url
         * @returns {*}
         */
        get: function (url) {
            this.url = url;
            this.method = 'GET';
            return this.send();
        },
        /**
         * Сброс всего
         */
        reset: function () {
            this.response = {};
            this.promise = {};
            this.url = "";
            this.method = "GET";
            this.error = null;
            this.resultat = null;
            this.headers = null;
            return this;
        },
        /**
         * Взводим заголовки
         */
        setHeaders: function (headers) {
            this.headers = headers;
            return this;
        },
        /**
         * Универсальная отправка данных
         * @param url
         * @param method
         * @param data
         * @return Promise
         */
        send: function (url, method, data) {
            if (url !== undefined) this.url = url;
            if (data !== undefined) this.data = data;
            if (method !== undefined) this.method = method;

            if (this.method === 'POST') {
                if (this.headers === null && !this.data instanceof FormData) {
                    /*
                     * ДЛЯ ПОСТА НУЖНЫ ЗАГОЛОВКИ
                     * */
                    this.headers = {
                        'Accept': 'application/json, text/plain, */*',
                        "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8"
                    };
                }
                if (!this.data instanceof FormData) {
                    this.headers = null;
                    this.data = Object.keys(this.data).map(key => encodeURIComponent(key) + '=' + encodeURIComponent(this.data[key])).join('&');
                }
            }
            if (this.method == "GET") {
                this.data = null;
            }
            /*
             * ОТПРАВЛЯЕМ Файл
             */
            return fetch(this.url,
                {
                    method: this.method,
                    body: this.data,
                    headers: (this.headers != null) ? this.headers : {},
                    credentials: 'include' //без этого поля запрос будет не авторизованный!
                })
                .then((response) => {
                    this.response = response;
                    return this.response.text();
                })
                .then((body) => {
                    return this.resultat = body;
                })
                .catch(function (err) {
                    console.log('Fetch Error', err);
                });

        },
        runScripts: function (obj) {
            let head = document.getElementsByTagName('head')[0];
            let scripts = obj.getElementsByTagName('script');
            // console.log(this.files);
            // console.log(this.txts);
            for (let i = 0; i < scripts.length; i++) {

                if (scripts[i].src !== '') {
                    /* создадим индекс чтобы больше не делать вставки того что уже есть */

                    if (this.files.indexOf($EM.helpers.crypto.hash(scripts[i].src)) === -1) {
                        console.log("--", this.files.indexOf($EM.helpers.crypto.hash(scripts[i].src)), "-");
                        this.files.push($EM.helpers.crypto.hash(scripts[i].src));
                        let script = document.createElement('script');
                        script.type = "text/javascript";
                        script.defer = true;
                        script.src = scripts[i].src;
                        //script.async = true;
                        head.appendChild(script);
                        scripts[i].src = ''; //remove НЕЛЬЗЯ!
                    }
                } else {
                    if (this.txts.indexOf($EM.helpers.crypto.hash(scripts[i].innerHTML)) === -1) {
                        console.log("--", this.files.indexOf($EM.helpers.crypto.hash(scripts[i].src)), " HASH ! -");
                        this.txts.push($EM.helpers.crypto.hash(scripts[i].innerHTML));
                        let script = document.createElement('script');
                        script.type = "text/javascript";
                        script.innerHTML = scripts[i].innerHTML;
                        // script.async = true;
                        script.defer = true;
                        head.appendChild(script);
                        scripts[i].innerHTML = '';
                    }
                }
            }

        },

        setToTargetAtReady: function (targetId, resp, callback) {

            if (typeof targetId === 'string') {
                document.querySelector(targetId).insertAdjacentHTML('beforeend', resp);
                this.runScripts(document.querySelector(targetId));
            } else {
                targetId.insertAdjacentHTML('beforeend', resp);
                this.runScripts(targetId);
            }
            if (typeof callback === "function") {
                callback();
            }
        },
        /**
         * Загрузка данных аяксом в нужный элемент
         * @param targetId
         * @param url
         * @param postData
         * @param callback
         */
        load: function (targetId, url, postData = null, callback) {
            this.reset();
            if (url !== undefined) this.url = url;
            if (postData !== null) {
                this.data = postData;
                this.method = 'POST';
            } else {
                this.method = 'GET';
            }
            this.send(url, this.method, this.data).then(response => this.setToTargetAtReady(targetId, response, callback));
        },
    },
    helpers: {
        /**
         * Метод событий, позволяет обрабатывать определенные события. На данный момент реализован 1 метод
         * транслирование вводимой информации в нужные контейнер
         *
         * Пример 1.
         * @example Создаем текстовое поле <input type='text' name='myName' em-toTarget='#id'>
         * @example Создаем получателя <div id='id'></div>
         * @example Затем инициируем метод $EM.helpers.init();
         * @example Радуемся, так как при вводе, текст транслируется в указанный див
         *
         * Пример 2.
         * @example Создаем текстовое поле текст арияю или DIV
         * @example Ставим ему класс  <div class='em-ckeditor'></div>
         * @example Затем инициируем метод $EM.helpers.init();
         * @example Радуемся указанный объект редактируется с помощью CKEDITORA
         *
         * Пример 3.
         * @example Создаем input поле
         * @example Ставим ему класс  <input type='text' class='em-counter' value=''>
         * @example Затем инициируем метод $EM.helpers.init();
         * @example Радуемся, так как при вводе в указанное поле, рядом будет отображатья кол-во введенных симмволов
         *
         * * Пример 4.
         * @example Создаем input поле
         * @example Ставим ему класс  <input type='text' class='em-dadataAdress' value=''>
         * @example Затем инициируем метод $EM.helpers.init();
         * @example Радуемся, так как при вводе в указанное поле, рядом будет отображатья кол-во введенных симмволов
         *
         */
        /**
         * Навешивает события на элементы
         */
        init: function (ajax = false) {
            /**
             * Взводит метку, которая указывает куда транслировать вводимый текст по атрибуту em-toTarget
             */
            function toTarget() {
                let objects = document.querySelectorAll("[em-toTarget]");
                for (let i = 0; i < objects.length; ++i) {
                    /*
                     ************** INPUT *******************
                     */
                    if (objects[i].value !== undefined) {
                        objects[i].addEventListener('keyup', function () {
                            let target = document.querySelector(this.getAttribute('em-toTarget'));
                            if (target.value !== undefined) {
                                target.value = (this.value !== undefined) ? this.value : this.innerHTML;
                            } else {
                                target.innerHTML = (this.value !== undefined) ? this.value : this.innerHTML;
                            }
                        });
                    }
                    /*
                     *********************** OVER ELEMENTS *******************
                     */
                    if (objects[i].value === undefined) {
                        objects[i].addEventListener('change', function () {
                            let target = document.querySelector(this.getAttribute('em-toTarget'));
                            if (target.value !== undefined) {
                                target.value = (this.value !== undefined) ? this.value : this.innerHTML;
                            } else {
                                target.innerHTML = (this.value !== undefined) ? this.value : this.innerHTML;
                            }
                        });
                    }
                }
            }

            function onlyNumbers() {
                let all = document.querySelectorAll('.em-price');
                all.forEach(function (item) {
                    item.onkeydown = function (e) {
                        let s = e.keyCode;
                        return (s > 47 && s < 58) || (s > 95 && s <= 105) || (s > 36 && s < 41) || s == 8 || s == 190 || s == 110;
                    };
                });
            }

            function loadCkeditor() {
                /*
                 Подключаем визуальный редактор встроенный.
                 */
                var ckeditors = $(".em-ckeditor");
                if (ckeditors.length > 0) {
                    $EM.helpers.inc.js("/assets/vendors/ckeditor/ckeditor.js");
                    $EM.helpers.inc.js("/assets/vendors/ckeditor/adapters/jquery.js", function () {
                        if (typeof CKEDITOR !== 'undefined') {
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

                    });
                }
            }

            function counters() {
                var counters = $(".em-counter");
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
            }

            /*
                Подключаем подсказки DaData для адреса
            */
            function adresses() {
                let adressSuggetions = $(".em-dadataAdress");
                if (adressSuggetions.length > 0) {
                    $EM.helpers.inc.css('https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/css/suggestions.min.css');
                    $EM.helpers.inc.js("https://cdn.jsdelivr.net/npm/suggestions-jquery@19.8.0/dist/js/jquery.suggestions.min.js", function () {
                        adressSuggetions.suggestions({
                            token: "08c4aa6efee18fe8fee413dd0dbb31689df26513",
                            type: "ADDRESS",
                            /* Вызывается, когда пользователь выбирает одну из подсказок */
                            onSelect: function (suggestion) {
                                console.log(suggestion);
                            }
                        });

                    });

                }
            }

            /**
             * Это метод считает кол-во символов
             */
            function ajaxInit() {
                toTarget(); //дублирование вводимого текста в нужное поле
                counters(); //счетчик символов  по вводимым данным
                loadCkeditor(); //подключаем ckeditor
                adresses(); //подключаем адреса DADATA
                onlyNumbers();
            }

            if (ajax === false) {
                document.addEventListener("DOMContentLoaded", function () {
                    ajaxInit();
                });
            } else {
                ajaxInit();
            }
        },

        inc: {
            js: function (path, onDone) {
                let $exists = false;
                let script = null;
                let Done = typeof onDone === "function" ? onDone : function () {
                };
                document.querySelectorAll('script').forEach(function (item) {
                    if (item.src === path) {
                        $exists = true;
                        return false;
                    }
                });
                if ($exists) {
                    Done();
                } else {
                    script = document.createElement("script");
                    script.src = path;
                    script.async = false;
                    script.onload = Done;
                    document.body.appendChild(script);
                }

                return script;
            },
            css: function (path, onDone) {
                let $exists = false;
                let css = null;
                let Done = typeof onDone === "function" ? onDone : function () {
                };

                document.querySelectorAll('link').forEach(function (item) {
                    if (item.href === path) {
                        $exists = true;
                        return false;
                    }
                });
                if ($exists) {
                    Done();
                } else {
                    css = document.createElement("link");
                    css.href = path;
                    css.type = 'text/css';
                    css.async = false;
                    css.rel = 'stylesheet';
                    css.onload = Done;
                    document.body.appendChild(css);
                }
                return css;
            }
        },
        crypto: {
            hash: function (s) {
                var hash = 0,
                    i, char;
                if (s.length == 0) return hash;
                for (i = 0, l = s.length; i < l; i++) {
                    char = s.charCodeAt(i);
                    hash = ((hash << 5) - hash) + char;
                    hash |= 0; // Convert to 32bit integer
                }
                return hash;
            }
        }
    }
};