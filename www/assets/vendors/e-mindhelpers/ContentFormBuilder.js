const ContentFormBuilder = {
    baseDiv: null,
    contentDiv: null,
    initial: 0, //уже стартовал ?
    data: [], //дата для отправки
    _saveBtn: null,
    _widgets: {}, //сами виджеты
    _widgetsClasses: {}, //классы виджетов
    _typeWidget: "widget", //константа виджета
    _typeWys: 'wysiwyg', //контстанта визвига
    _typeCode: 'code', //контстанта блока с кодом! Осторожно
    _typeText: 'text', //константа текстового блока
    _debug: false,
    _target: null,


    /**
     * Навещиваем генератор на указанный элемент
     * @return {ContentFormBuilder}
     */
    init: function () {

        this.log('I am start init function');

        this.initial = 1;
        this.baseDiv = document.getElementById('ContentZone');
        this.setContentFormBuilderBlock();
        this.contentDiv = this.baseDiv.querySelector('#RestoreZone');
        return this;

    },
    save: function () {
        if (this._target == null) {
            this.log('НЕ указано куда');
            return;
        }
        let data = ContentFormBuilder.collectData();
        let xhr = new XMLHttpRequest();
        let body = 'content=' + encodeURIComponent(JSON.stringify(data));

        xhr.open("POST", this._target, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {//Call a function when the state changes.
            if(xhr.readyState == 4 && xhr.status == 200) {
                alert(JSON.parse(xhr.response).data);
            }
        };
        xhr.send(body);
    },

    setContentFormBuilderBlock: function () {


        this.log('Set up primary block');

        let txt = "<div class=\"panel\">\n" +
            "                        <div class=\"panel-heading\">\n" +
            "                        <span class=\"btn btn-xs btn-primary\" onclick=\"ContentFormBuilder.addWysiwyg();\">\n" +
            "                                    + Добавить Wysiwyg</span>\n" +
            "                            <span class=\"btn btn-xs btn-primary\" onclick=\"ContentFormBuilder.addTextBlock();\">\n" +
            "                                        + Добавить текстовое</span>\n" +
            // "                            <span class=\"btn btn-xs btn-primary\" onclick=\"ContentFormBuilder.addWidget();\"><i\n" +
            // "                                        class=\"fa fa-plus\"></i> Добавить Виджет</span>\n" +
            "                            <span class=\"btn btn-xs btn-primary\" onclick=\"ContentFormBuilder.addCodeBlock();\">\n" +
            "                                        + Добавить блок с кодом </span>\n" +
            "                        </div>\n" +
            "                        <div class=\"panel-body\" id=\"RestoreZone\">\n" +
            "                        </div>\n";
        if (this._saveBtn !== null) {
            txt = txt + "<div class='save-btn'>" +
                "<a href='/pages/admin' class='btn btn-xs btn-primary'>&larr; в админ панель</a><button type='submit' onclick='ContentFormBuilder.save(";
            txt = txt + ");'>Сохранить изменения</button> " +
                "</div>";
        }
        txt = txt + "                    </div>";
        this.baseDiv.innerHTML = txt;
    },
    /**
     * Добавляет виз редактор зону
     */
    addWysiwyg: function (data = "") {

        this.log('Add WysBlock to primary block');

        if (this.initial == 0) {
            this.init();
        }
        this.contentDiv.insertAdjacentHTML('beforeend', this.getBlock(this._typeWys, data));
        this.setWysiwyg();
        return this;

    },
    setWysiwyg: function () {
        if (CKEDITOR) {

            $('.ckeditor').ckeditor({
                disableNativeSpellChecker: false,// отключает запрет на стандартную проверку орфографии браузером
                extraPlugins: 'sourcedialog',
                removeButtons: 'Print,Form,TextField,Textarea,radio, checkbox,imagebutton,Button,SelectAll,Select,HiddenField',
                removePlugins: 'spellchecker, Form, TextField,Textarea, Button, Select, HiddenField , about, save, newpage, print,exportpdf, templates, scayt, flash, pagebreak, smiley,preview,find',
                filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr=',
            });
        } else {
            this.log('noEditor');
        }
    },
    /**
     * Добавляет тектовый блок
     */
    addTextBlock: function (data = "") {

        this.log('Add Text Block to primary block');

        if (this.initial == 0) {
            this.init();
        }
        this.contentDiv.insertAdjacentHTML('beforeend', this.getBlock(this._typeText, data));

        return this;
    },
    /**
     * Добавляет код
     */
    addCodeBlock: function (data = "") {

        this.log('Add Code Block to primary block');

        if (this.initial == 0) {
            this.init();
        }
        this.contentDiv.insertAdjacentHTML('beforeend', this.getBlock(this._typeCode, data));

        return this;
    },
    /**
     * Добавляет виджет
     */
    addWidget: function (data = "") {

        this.log('Add Widgets Block to primary block');

        if (this.initial == 0) {
            this.init();
        }
        this.contentDiv.insertAdjacentHTML('beforeend', this.getBlock(this._typeWidget, data));
        return this;
    },
    /**
     * Отображение виджета
     * */
    showWidgetForm: function ($data) {

        this.log('Start Show Widgets Form');

        /**
         * Виджет есть ?
         */
        let $readyData = $data.split(':');
        if (typeof $readyData[1] !== "undefined") {
            $readyData = $readyData[1].split(".");
        }
        /**
         * 0: "gallery"
         * 1: "Gallery"
         * 2: "show"         */

        if (typeof WIDGETS !== "undefined") {
            /**
             * А он объект ?
             */
            if (typeof WIDGETS === "object") {
                /**
                 * Выведем форму на основе его данных
                 * @type {string}
                 */
                let res = "";

                res += "<div class='row'> <div class='col-sm-2'>" +
                    "<label>Модуль<br><select name='module[]' class='moduleWidgetName' onchange='ContentFormBuilder.changeClassesInWidgetForm(this);'>";

                Object.getOwnPropertyNames(WIDGETS).forEach(function (item) {
                    ContentFormBuilder._widgets[WIDGETS[item]['module']] = WIDGETS[item]['module'];
                    if (typeof ContentFormBuilder._widgetsClasses[WIDGETS[item]['module']] === "undefined") {
                        ContentFormBuilder._widgetsClasses[WIDGETS[item]['module']] = {};
                    }
                    ContentFormBuilder._widgetsClasses[WIDGETS[item]['module']][WIDGETS[item]['class']] = WIDGETS[item]['methods'];

                });
                res = res + "<option value='0'> - </option>";
                Object.getOwnPropertyNames(ContentFormBuilder._widgets).forEach(function (item) {
                    res = res + "<option value='" + item + "'>" + ContentFormBuilder._widgets[item] + "</option>";
                });
                $hidden = "hidden";
                if (typeof $readyData[1] !== "undefined") {
                    $hidden = "";
                }

                res = res +
                    "</select></label>" +
                    "</div>" +
                    "<div class='col-sm-2'><label>Виджет<br>" +
                    "<select name='class[]' class='" + $hidden + " classWidgetName' onload='alert(11111);' onchange='ContentFormBuilder.changeMethodsInWidgetForm(this);'>" +
                    "</select></label>" +
                    "</div>" +
                    "<div class='col-sm-5'><label>Метод<br>" +
                    "<select name='method[]' class='" + $hidden + " methodWidgetName' onchange='ContentFormBuilder.chooseParameters(this);'>" +
                    "</select></label>" +
                    "</div>" +
                    "<div class='col-sm-3'><label>Параметры метода<br>" +
                    "<input type='text' class='parametersWidgetName " + $hidden + "' name='params[]' value=''></label>" +
                    "</div>" +
                    "</div>";
                return res;
            }
        }
        return "";

    },
    log: function (msg) {
        if (this._debug != false) {
            console.log(msg);
        }
    },
    /**
     * Выбрали модуль, подменяем классы.
     * */
    changeClassesInWidgetForm: function (obj) {
        this.log('Changes Classes function');
        let select = obj.parentElement.parentElement.parentElement.querySelector('.classWidgetName');
        let method = obj.parentElement.parentElement.parentElement.querySelector('.methodWidgetName');

        if (obj.value == 0) {
            select.innerHTML = "";
            select.classList.add('hidden');
            method.classList.add('hidden');
        } else {
            let classes = ContentFormBuilder._widgetsClasses[obj.value];
            let res = "<option value='0'> - </option>";
            Object.getOwnPropertyNames(classes).forEach(function (item) {
                res = res + "<option value='" + item + "'>" + item + "</option>";
            });
            select.innerHTML = res;
            select.classList.remove('hidden');
        }
    },
    /**
     Меняет метод выбранного виджета
     */
    changeMethodsInWidgetForm: function (obj) {

        this.log('Changes methods function');

        this.log(obj);
        let wModule = obj.parentElement.parentElement.parentElement.querySelector('.moduleWidgetName');
        this.log(wModule);

        let wClass = obj.parentElement.parentElement.parentElement.querySelector('.classWidgetName');
        this.log(wClass);
        let wMethod = obj.parentElement.parentElement.parentElement.querySelector('.methodWidgetName');
        this.log(wMethod);
        if (obj.value == 0) {
            wMethod.classList.add('hidden');
        } else {
            let $methods = ContentFormBuilder._widgetsClasses[wModule.value][wClass.value];
            let res = "";
            res = res + "<option value='0'> - </option>";

            Object.getOwnPropertyNames($methods).forEach(function (item) {
                res = res + "<option value='" + item + "'>" + $methods[item].name + "</option>";
            });
            wMethod.innerHTML = res;
            wMethod.classList.remove('hidden');
        }
    },
    /**
     * выбираем параметры выбранного метода и виджета
     * @param obj
     */
    chooseParameters: function (obj) {

        this.log('Changes parameters function');

        let wModule = obj.parentElement.parentElement.parentElement.querySelector('.moduleWidgetName');
        let wClass = obj.parentElement.parentElement.parentElement.querySelector('.classWidgetName');
        let wMethod = obj;
        let wParam = obj.parentElement.parentElement.parentElement.querySelector('.parametersWidgetName');
        //console.log(wModule.value,wClass.value,wMethod.value);
        if (typeof ContentFormBuilder._widgetsClasses[wModule.value][wClass.value][wMethod.value] !== "undefined" && ContentFormBuilder._widgetsClasses[wModule.value][wClass.value][wMethod.value].parameters.length > 0) {
            wParam.classList.remove('hidden');
        } else {
            wParam.classList.add('hidden');
        }
    },
    escapeHtml: function (text) {
        let map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return text.replace(/[&<>"']/g, function (m) {
            return map[m];
        });
    },
    /**
     * Возвращаем нужный блок.
     */
    getBlock: function ($type, $data = "") {


        // this.log('---START RETURN ' + $type + ' BLOCK and DATA:--');
        // this.log($data);
        // this.log('-----------------DATA END------------------');

        let txt = "";
        if ($type === ContentFormBuilder._typeWidget) {
            txt = "<div class=\"dinamic-block block-" + ContentFormBuilder._typeWidget + "\">\n" +
                "                            <div class=\"dinamic-block-head\">Выберите виджет <i class=\"fa fa-close\"\n" +
                "                                                                               onclick=\"ContentFormBuilder.removeBlock(this);\"></i><i class='fa fa-arrow-circle-down' onclick=\"ContentFormBuilder.moveDown(this);\"></i><i class='fa fa-arrow-circle-up' onclick=\"ContentFormBuilder.moveUp(this);\"></i>\n" +
                "                            </div>\n";

            txt = txt + this.showWidgetForm($data);
            txt = txt + "<div class=\"dinamic-block-content alreadyData \">" + $data + "</div>\n" +
                "                        </div>";
        }
        if ($type === ContentFormBuilder._typeCode) {
            txt = "<div class=\"dinamic-block block-" + ContentFormBuilder._typeCode + "\">\n" +
                "                            <div class=\"dinamic-block-head\">Блок с кодом (интерпретируется как html код) <i class=\"fa fa-close\"\n" +
                "                                                                               onclick=\"ContentFormBuilder.removeBlock(this);\"></i><i class='fa fa-arrow-circle-down' onclick=\"ContentFormBuilder.moveDown(this);\"></i><i class='fa fa-arrow-circle-up' onclick=\"ContentFormBuilder.moveUp(this);\"></i>\n" +
                "                            </div>\n";
            txt = txt + "<textarea class=\"dinamic-block-content \">" + this.escapeHtml($data) + "</textarea>\n" +
                "                        </div>";
        }

        if ($type === ContentFormBuilder._typeText) {
            txt = "<div class=\"dinamic-block block-" + ContentFormBuilder._typeText + "\" >\n" +
                "                            <div class=\"dinamic-block-head\">Тестовое поле <i class=\"fa fa-close\"\n" +
                "                                                                             onclick=\"ContentFormBuilder.removeBlock(this);\"></i><i class='fa fa-arrow-circle-down' onclick=\"ContentFormBuilder.moveDown(this);\"></i><i class='fa fa-arrow-circle-up' onclick=\"ContentFormBuilder.moveUp(this);\"></i>\n" +
                "                            </div>" +
                "                            <div class=\"dinamic-block-content\" contenteditable=\"true\" spellcheck=\"true\">" + $data + "</div>" +
                "                        </div>";
        }
        if ($type === ContentFormBuilder._typeWys) {
            txt = "<div class=\"dinamic-block block-" + ContentFormBuilder._typeWys + "\">\n" +
                "      <div class=\"dinamic-block-head\">Поле с визуальным редактором <i class=\"fa fa-close\"\n" +
                "                                                                            onclick=\"ContentFormBuilder.removeBlock(this);\"></i><i class='fa fa-arrow-circle-down' onclick=\"ContentFormBuilder.moveDown(this);\"></i><i class='fa fa-arrow-circle-up' onclick=\"ContentFormBuilder.moveUp(this);\"></i>\n" +
                "      </div>\n" +
                "      <div class=\"dinamic-block-content  ckeditor\" contenteditable=\"true\" spellcheck=\"true\">" + $data + "</div>\n" +
                "   </div>";
        }
        return txt;

    },

    /**
     * Удаляет блок
     */
    removeBlock: function ($obj) {

        this.log('Удаляю блок');

        $obj.parentNode.parentNode.remove();
        return this;
    },
    collectToGet: function ($to = null) {
        return encodeURIComponent(JSON.stringify(this.collectData($to = null)));
    },
    collectToFormData: function () {
        return JSON.stringify(this.collectData($to = null));
    },
    /**
     * Собирает инфу до отправки
     */
    collectData: function ($to = null) {

        this.log('Получаем дату из формы для отправки');

        if (this.initial == 0) {
            this.init();
        }
        this.clearData();


        if (this.contentDiv.querySelectorAll('.dinamic-block').length > 0) {
            this.contentDiv.querySelectorAll('.dinamic-block').forEach(function (obj) {

                let blockContent = null;
                let blockType = null;
                if (obj.classList.contains('block-' + ContentFormBuilder._typeWys)) {
                    /**
                     * Визвиг блок
                     * @type {string}
                     */
                    blockType = ContentFormBuilder._typeWys;
                    blockContent = obj.querySelector('.dinamic-block-content').innerHTML;
                }
                if (obj.classList.contains('block-' + ContentFormBuilder._typeText)) {
                    /**
                     * Текстовый блок
                     * @type {string}
                     */
                    blockType = ContentFormBuilder._typeText;
                    blockContent = obj.querySelector('.dinamic-block-content').innerHTML;
                }

                if (obj.classList.contains('block-' + ContentFormBuilder._typeCode)) {
                    /**
                     * Блок с кодом
                     * @type {string}
                     */
                    blockType = ContentFormBuilder._typeCode;
                    blockContent = obj.querySelector('.dinamic-block-content').value;
                }
                if (obj.classList.contains('block-' + ContentFormBuilder._typeWidget)) {
                    /**
                     * Собираем блок виджета :)
                     *
                     * @type {string}
                     */
                    blockType = ContentFormBuilder._typeWidget;
                    if (obj.parentElement.parentElement.querySelector('.moduleWidgetName').value !== '0') {
                        blockContent = "{widget:" + obj.parentElement.parentElement.querySelector('.moduleWidgetName').value +
                            "." + obj.parentElement.parentElement.querySelector('.classWidgetName').value +
                            "." + obj.parentElement.parentElement.querySelector('.methodWidgetName').value;
                        if (obj.parentElement.parentElement.querySelector('.parametersWidgetName').value.length > 1) {
                            blockContent += "." + obj.parentElement.parentElement.querySelector('.parametersWidgetName').value;
                        }
                        blockContent += "}";
                    } else {
                        /**
                         * Если не был выбран дочерний элемент, то мы оставляем виджет без изменений
                         * @type {string}
                         */
                        blockContent = obj.parentElement.parentElement.querySelector('.alreadyData').innerHTML;
                    }
                }

                let newObj = {
                    type: blockType,
                    content: blockContent,
                };
                ContentFormBuilder.data.push(newObj);

            })
        }
        return this.data;

    },
    /**
     * Перемещение обеъкта вверх по DOM! При этом объект не теряет свойств
     * @param obj
     */
    moveUp: function (obj) {
        let thisBlock = obj.parentNode.parentNode;
        let previousBlock = thisBlock.previousSibling;
        if (previousBlock !== null) {
            thisBlock.parentNode.insertBefore(thisBlock, previousBlock);
        }
    },
    /**
     * Перемещение обеъкта вниз по DOM! При этом объект не теряет свойств
     * @param obj
     */
    moveDown: function (obj) {
        let thisBlock = obj.parentNode.parentNode;
        let nextBlock = thisBlock.nextSibling;
        if (nextBlock !== null) {
            if (nextBlock !== null) {
                thisBlock.parentNode.insertBefore(nextBlock, thisBlock);
            }
        }


    },
    /**
     * Очищаем выбранное
     */
    clearData: function () {
        this.baseDiv = document.getElementById('ContentZone');
        this.contentDiv = this.baseDiv.querySelector('#RestoreZone');
        this.log('Очищаем дату');
        this.data = [];
        //this.initial = 0;
        return this;
    },
    /**
     * Проверка что это JSON
     * @param str
     * @return {*}
     */
    isJson: function (str) {

        this.log('проверка а это JSON');

        try {
            var obj = JSON.parse(str);
        } catch (e) {
            return false;
        }
        return obj;
    },
    /**
     * Восстанавливает данные из сериализованного массива.
     * @param data
     * @return {boolean}
     */
    restore: function (data, targetObject = null, saveBtn = null) {
        let needWys = false;
        if (saveBtn !== null) {
            this._saveBtn = 1;
        }
        this.log('Восстанавливаю информацию');

        /**
         * Очищаем дату, так как при ресторе она не нужна
         */
        this.clearData();
        /**
         * Далем инит, вдруг он еще не делался и полей тупо нет
         */
        if (this.initial == 0) {
            this.init();
        }
        /**
         * Определяем цель для восстановаления, она уже должны быть.. В инит если что создалась
         */
        let target;
        if (targetObject == null) {
            target = document.getElementById('RestoreZone');
        } else {
            target = targetObject;
        }
        /**
         * Убедимся что дата передана корректная, возможно нам уже передали объект и все круто.
         */
        let d;
        if (typeof data != "object") {
            d = this.isJson(data);
        } else {
            d = data;
        }
        let needToReturn = "";
        if (typeof d != 'object') {

            needToReturn = this.getBlock(ContentFormBuilder._typeWys, data);
            needWys = true;
        } else {
            d.forEach(function (item) {
                if (item.type == this._typeWys) {
                    needWys = true;
                }
                needToReturn = needToReturn + ContentFormBuilder.getBlock(item.type, item.content);
            });
        }
        target.innerHTML = needToReturn;
        if (needWys == true) {
            this.setWysiwyg();
        }
        return true;
    }
};