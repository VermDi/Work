/**
 * Created by Евгения on 15.06.2017.
 */
var upp = ['', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
var low = ['', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
var dig = ['', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
var $document = $(document);

function rnd(x, y, z) {
    var num;
    do {
        num = parseInt(Math.random() * z);
        if (num >= x && num <= y) break;
    } while (true);
    return (num);
}

function gen_pass() {
    var pswrd = '';
    var znak, s;
    var k = 0;
    var n = 10;
    var pass = [];
    var w = rnd(30, 80, 100);
    for (var r = 0; r < w; r++) {
        znak = rnd(1, 26, 100);
        pass[k] = upp[znak];
        k++;
        znak = rnd(1, 26, 100);
        pass[k] = low[znak];
        k++;
        znak = rnd(1, 10, 100);
        pass[k] = dig[znak];
        k++;
    }
    for (var i = 0; i < n; i++) {
        s = rnd(1, k - 1, 100);
        pswrd += pass[s];
    }
    return pswrd;
}

$document.on('click', '#genPass', function () {
    var password = gen_pass();
    $(this).parents('.input-group').find('#password').val(password);
});

$document.ready(function () {
    if ($("#email").val() !== '') {
        $document.find('form').find('.add-user-button').removeClass('disabled');
    }

    var $modal = $("#source-modal");

    $modal.on('shown.bs.modal', function () {
        var $role = $("#role");
        $role.select2();
        $document.on('click', '.save-user', function () {
            //  console.log('her');
            var $form = $("#user-form");
            $form.validate({
                /*rules: {
                    email: {
                        email: true,
                        required: true,
                        remote: {
                            'url': '/user/checkunique',
                            type: 'POST',
                            data: {
                                email: function () {
                                    return $form.find('#email').val();
                                },
                                'id': $form.find('#id').val()
                            }
                        }
                    }
                },
                messages: {
                    password: "Обязательно к заполнению",
                    email: {
                        email: 'Неверный формат',
                        required: "Обязательно к заполнению",
                        remote: "Такой e-mail уже используется"
                    }
                },*/
                tooltip_options: {
                    email: {trigger: 'focus'},
                    password: {placement: 'bottom', trigger: 'focus'}
                },
                focusInvalid: true,

                submitHandler: function (form) {
                    var data = new FormData($form.get(0));
                    $.ajax({
                        'url': '/user/save',
                        'type': 'POST',
                        'data': data,
                        'dataType': 'json',
                        cache: false,
                        processData: false, // Не обрабатываем файлы (Don't process the files)
                        contentType: false, // Так jQuery скажет серверу что это строковой запрос
                        success: function (response) {
                          //  console.log(response)
                            if (response.status === 'OK') {
                                location.reload();
                            } else {
                                alert(response.message);
                                console.log('error:' + response.message);
                            }
                        }
                    });
                },
                invalidHandler: function (form, validator) {
                    var invalidElement = $(validator.errorList[0].element);
                    invalidElement.focus();
                    invalidElement.parents('.form-group').removeClass('has-success').addClass('has-error')
                }


            });
            if ($form.find('#id').val() !== "") {
                $form.find("#password").rules('remove', 'required');
            } else {
                $form.find("#password").rules('add', {required: true})
            }
            $('#user-form').submit();

        });

    });
});





$document.on('click', '.delete-user', function () {
    if (confirm('Вы уверены что хотите удалить пользователя и всех его дочерних пользователей?')) {
        location.href = '/user/delete/' + $(this).data('id')
    }
});