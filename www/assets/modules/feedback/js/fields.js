$(document).ready(function () {
    var i=1;
    var max_fields = 10; //maximum input boxes allowed
    var wrapper = $(".input_fields_wrap"); //Fields wrapper
    var add_button = $(".add_field_button"); //Add button ID

    var x = 1; //initlal text box count
    $(add_button).click(function (e) { //on add input button click
        e.preventDefault();
        if (x < max_fields) { //max input box allowed
            x++; //text box increment
            $(wrapper).append('<div class = "additional_field"><hr><div class="form-group"><label for="name" class="col-sm-2 control-label">Название поля</label>\n\
    <div class="col-sm-8"><input type="text" class="form-control" name="fields['+i+'][name]" ></div><a href="#" class="remove_field">Удалить</a></div>\n\
<div class="form-group"><label for="name" class="col-sm-2 control-label">Input name</label><div class="col-sm-8"><input type="text" class="form-control" name="fields['+i+'][name_in_form]" ></div></div></div>')
            i++}
    });

    $(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
        e.preventDefault();
        $(this).parents('.additional_field').remove();
        x--;
    })
});