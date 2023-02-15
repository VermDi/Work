/**
 * Created by Евгения on 10.10.2017.
 */
var progress = $(".loading-progress").progressTimer({
    timeLimit: 15,
    onFinish: function () {
        $('.result').text('Процесс завершен. Сейчас вы будете перенаправлены на страницу авторизации');
        setTimeout(function () {
            location.href = '/user/login'
        }, 5000)
    }
});

$.ajax({
    url: "/migrations/up/progress"
}).error(function () {
    progress.progressTimer('error', {
        errorText: 'ERROR!',
        onFinish: function () {
            alert('There was an error processing your information!');
        }
    });
}).done(function () {
    $.post('/user/permissionscan/scan', {}, function (response) {
        if (response.result) {
            $.when($.post('/user/permissionscan/addadmin')).done(function () {
                progress.progressTimer('complete');
            });
        }
    });
});
//
// function scan() {
//     $.post('/user/permissionscan/scan', {}, function (response) {
//         if (response.result) {
//             $.when($.post('/user/permission/addadmin')).done(function () {
//                 progress.progressTimer('complete');
//             });
//         }
//     });
// }

// $.when($.ajax({url: "/migrations/up/progress"}), $.post('/user/permissionscan/scan'), $.post('/user/permissionscan/addadmin')).done(function () {
//     progress.progressTimer('complete');
// }).error(function () {
//     progress.progressTimer('error', {
//         errorText: 'ERROR!',
//         onFinish: function () {
//             alert('There was an error processing your information!');
//         }
//     });
// });
