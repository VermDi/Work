/**
 * Created by Евгения on 21.09.2017.
 */
$document.on('blur', ".check-email", function () {
    var $this = $(this);
    if($this.val()!=='admin'){
        if ($this.val() !== '') {
            var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
            if (pattern.test($this.val())) {
                user.data = {'email': $this.val()};
                user.checkEmail(function (data) {
                    if (data === 'OK') {
                        $this.parent().addClass('has-error');
                        $this.parent().find('.help-block').text('Такой пользователь существует');
                        $this.parents('form').find('.add-user-button').addClass('disabled');
                    } else {
                        $this.parent().removeClass('has-error');
                        $this.parent().find('.help-block').text('');
                        $this.parents('form').find('.add-user-button').removeClass('disabled');
                    }
                });

            } else {
                $this.parent().addClass('has-error');
                $this.parent().find('.help-block').text('Не верно');
                $this.parents('form').find('.add-user-button').addClass('disabled');
            }
        } else {
            $this.parent().addClass('has-error');
            $this.parent().find('.help-block').text('Поле не должно быть пустым');
            $this.parents('form').find('.add-user-button').addClass('disabled');
        }
    }

});

$document.on('keypress', ".check-email", function () {
    var $this = $(this);
    $this.parent().removeClass('has-error');
    $this.parent().find('.help-block').text('');
    $this.parents('form').find('.add-user-button').addClass('disabled');
});