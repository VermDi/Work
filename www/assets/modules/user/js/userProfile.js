var $document = $(document);
var $modal = $("#source-modal");
var $birthday = $('#birthday');
let $sourceModal=$("#source-modal");

$(document).ready(function () {
    var $phone_number = $('#phone-number');
    //$phone_number.inputmask("99-9999999");  //static mask
    $phone_number.inputmask({"mask": "+7(999) 999-9999"}); //specifying options
    // $(selector).inputmask("9-a{1,3}9{1,3}"); //mask with dynamic syntax
});
$sourceModal.on('show.bs.modal',function () {
    $(this).find('.modal-dialog').addClass('modal-lg');
});
$(function () {
    // $.datepicker.setDefaults($.extend($.datepicker.regional["ru"]));
    // $birthday.datepicker({
    //     inline: true,
    //     changeYear: true,
    //     changeMonth: true,
    //
    //     format: 'd.m.Y',
    //     date: $birthday.val(),
    //     current: $birthday.val(),
    //
    //     starts: 1,
    //     position: 'r',
    //
    //     onBeforeShow: function () {
    //         $birthday.DatePickerSetDate($birthday.val(), true);
    //         $birthday.datepicker({
    //             changeMonth: true,
    //             changeYear: true
    //         })
    //     },
    //     onChange: function (formated, dates) {
    //         $birthday.val(formated);
    //     }
    // })
});
$document.on('click', '.user-profile', function () {
    $sourceModal.modal({'remote': '/user/profile/' + $(this).data('id') + '/modal'});
});
$document.on('click', '.avatar', function () {
    $sourceModal.modal({'remote': '/user/profile/avatarform'});

});

$modal.on('hidden.bs.modal', function () {
    $(this).removeData('bs.modal');
});

$modal.on('loaded.bs.modal', function () {
    jQuery.event.props.push('dataTransfer');
    var $dropFiles = $("#drop-files");
    var defaultUploadBtn = $('#uploadbtn');
    var dataArray = [];
    $dropFiles.on('drop', function (e) {
        var files = e.dataTransfer.files;
        if (files.length === 1) {
            loadInView(files);
        } else {
            alert('Вы не можете загружать больше 1 изображения1!');
            files.length = 0;
            return;
        }
    });

    defaultUploadBtn.on('change', function () {
        var files = $(this)[0].files;
        if (files.length === 1) {
            loadInView(files);
            $('#frm').each(function () {
                this.reset();
            });
        } else {
            alert('Вы не можете загружать больше 1 изображения!');
            files.length = 0;
        }
    });

    function loadInView(files) {
        var images = $.map(files, function (file) {
            return imageLoader(URL.createObjectURL(file));
        });

        $.when.apply(null, images).done(function () {
            $.each(arguments, function (i, img) {

                dataArray.push({name: img.name, value: img.src, 'coords': {}});
                var height;
                if (width <= img.width) {
                    height = Math.round(width * img.height / img.width);
                    resize(img, width, height).done(function (canvas) {
                        dataArray[0].value = canvas.toDataURL('image/jpeg', 1);
                        $(img).attr({width: width, height: height});
                        $('.for-image').html([canvas]);
                        $dropFiles.removeClass('drop-files');
                        $('#upload-button').removeAttr('disabled');
                        var jcrop_api;

                        jQuery(function ($) {
                            var heightJcrop = 100;
                            var widthJcrop = 100;
                            var size;
                            user.getParameters(function (data) {
                               // console.log(data);
                                size = data.value.split('x');
                                widthJcrop = size[0];
                                heightJcrop = size[1];
                                $('#image').Jcrop({
                                    aspectRatio: widthJcrop / heightJcrop,
                                    onChange: updateCoords,
                                    onSelect: updateCoords
                                }, function () {
                                    jcrop_api = this;
                                    jcrop_api.setSelect([0, 0, widthJcrop, heightJcrop]);
                                });

                                function updateCoords(c) {
                                    dataArray[0].coords = {'x': c.x, 'y': c.y, 'w': c.w, 'h': c.h};
                                    $('#x').val(c.x);
                                    $('#y').val(c.y);
                                    $('#w').val(c.w);
                                    $('#h').val(c.h);
                                }
                            });


                        });
                    });
                } else {
                    var wwidth = img.width;
                    var hheight = img.height;
                    var canvas = document.createElement('canvas');
                    canvas.width = wwidth;
                    canvas.height = hheight;
                    canvas.setAttribute('id', 'image');
                    canvas.getContext('2d').drawImage(img, 0, 0, wwidth, hheight);
                    dataArray[0].value = canvas.toDataURL('image/jpeg', 1);
                    $(img).attr({width: wwidth, height: hheight});
                    $('.for-image').html([canvas]);
                    $dropFiles.removeClass('drop-files');
                    $('#upload-button').removeAttr('disabled');
                    var jcrop_api;
                    jQuery(function ($) {
                        var heightJcrop = 100;
                        var widthJcrop = 100;
                        var size;
                        user.getParameters(function (data) {
                            size = data.value.split('x');
                            widthJcrop = size[0];
                            heightJcrop = size[1];
                            $('#image').Jcrop({
                                aspectRatio: widthJcrop / heightJcrop,
                                onChange: updateCoords,
                                onSelect: updateCoords
                            }, function () {
                                jcrop_api = this;
                                jcrop_api.setSelect([0, 0, widthJcrop, heightJcrop]);
                            });

                            function updateCoords(c) {
                                dataArray[0].coords = {'x': c.x, 'y': c.y, 'w': c.w, 'h': c.h};
                                $('#x').val(c.x);
                                $('#y').val(c.y);
                                $('#w').val(c.w);
                                $('#h').val(c.h);
                            }
                        });


                    });
                }


            });
        });

        return false;
    }

    $('.sendImg').on('click', function () {

        $.ajax({
            'url': '/user/profile/upload',
            'type': 'POST',
            'dataType': 'json',
            'data': dataArray[0]
        }).done(function (data) {
            if (data.status === 'OK') {
                $modal.modal('hide');
                location.reload();
                //var $avatar = $('.avatar');
                // $avatar.find('img').attr('src')
                // $avatar.find('div:first-child').find('img').remove();
                // var img = document.createElement('img');
                // img.setAttribute('src', data.img);
                // document.querySelector('.avatar div:first-child').appendChild(img);
            }
        });


    });

    $dropFiles.on('dragenter', function () {
        $(this).css({'box-shadow': 'inset 0px 0px 20px rgba(0, 0, 0, 0.1)', 'border': '1px dashed #bb2b2b'});
        return false;
    });

    $dropFiles.on('drop', function () {
        $(this).css({'box-shadow': 'none', 'border': 'none'});
        return false;
    });
});


var width = 560;

function taskQueue(capacity) {
    var running = 0;
    var queue = [];

    function release() {
        if (queue.length) {
            var task = queue.shift();
            task(release);
        } else {
            running -= 1;
        }
    }

    return function (task) {
        if (running < capacity) {
            running += 1;
            task(release);
        } else {
            queue.push(task);
        }
    };
}


function protect(img) {
    var ratio = img.width / img.height;

    var maxSquare = 5000000;  // ios max canvas square
    var maxSize = 4096;  // ie max canvas dimensions

    var maxW = Math.floor(Math.sqrt(maxSquare * ratio));
    var maxH = Math.floor(maxSquare / Math.sqrt(maxSquare * ratio));
    if (maxW > maxSize) {
        maxW = maxSize;
        maxH = Math.round(maxW / ratio);
    }
    if (maxH > maxSize) {
        maxH = maxSize;
        maxW = Math.round(ratio * maxH);
    }
    if (img.width > maxW) {
        var canvas = document.createElement('canvas');
        canvas.width = maxW;
        canvas.height = maxH;
        canvas.getContext('2d').drawImage(img, 0, 0, maxW, maxH);
        img = canvas;
    }

    return img;
}

var resizeQueue = taskQueue(1);

function resize(img, w, h) {
    var df = $.Deferred();
    resizeQueue(function (release) {
        setTimeout(function () {
            img = protect(img);
            // console.log(img);
            var steps = Math.ceil(Math.log2(img.width / w));
            var sW = w * Math.pow(2, steps - 1);
            var sH = h * Math.pow(2, steps - 1);
            var x = 2;

            function run() {
                if (!(steps--)) {
                    df.resolve(img);
                    release();
                    return;
                }

                setTimeout(function () {
                    var canvas = document.createElement('canvas');
                    canvas.width = sW;
                    canvas.height = sH;
                    canvas.setAttribute('id', 'image');
                    canvas.getContext('2d').drawImage(img, 0, 0, sW, sH);

                    img = canvas;
                    // console.log(img);
                    sW = Math.round(sW / x);
                    sH = Math.round(sH / x);
                    run();
                }, 0);
            }

            run();

        }, 0);
    });
    return df.promise();
}


function imageLoader(src) {
    var df = $.Deferred();
    var img = new Image();
    img.onload = function () {
        df.resolve(img);
    };
    img.onerror = function () {
        df.reject(img);
    };
    img.src = src;
    return df.promise();
}

$document.on('click', '.delete-my-account', function () {
    if (confirm('Вы уверены что хотите удалить свои данные')) {
        $.ajax({
            type: 'POST',
            'url': '/user/deleteaccount'
        }).done(function (response) {
            if (response === 'OK') {
                location.href = '/user/login'
            }
        })
    }
})