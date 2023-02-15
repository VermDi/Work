/**
 * Created by dulentcov-smishko on 19.10.2018.
 */
function showDebugPanel(obj) {
    /*
     Подсветим меню
     */
    var menu_li = document.querySelectorAll('.DebugPanelHeading > li');
    for (var i = 0, len = menu_li.length; i < len; i++) {
        menu_li[i].classList.remove('menu_active');
    }
    obj.classList.add('menu_active');
    /*
     Выберим то что нужно показать
     */
    var body = document.getElementById(obj.id + '_BODY');

    /*
     Все скроем
     */
    var closeElement = document.getElementsByClassName('DebugPanelBody');
    for (var i = 0, len = closeElement.length; i < len; i++) {
        closeElement[i].classList.add('closeDebugPanel');
    }

    /*
     Если у элемента есть пукнт, то откроем его
     */
    if (body != null) {
        body.classList.toggle('closeDebugPanel');
    }
}

function closeDebugPanel() {
    document.getElementById('BodyBlock').classList.toggle('closeDebugPanel');
}
