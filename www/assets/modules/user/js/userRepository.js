/**
 * Created by Евгения on 16.06.2017.
 */
var user={
    data:{},
    checkEmail:function (callback) {
        $.ajax({
            'url':'/user/checkemail',
            'type':'POST',
            'data':user.data
        }).done(callback);
    },
    getPermission:function (callback) {
        $.ajax({
            'url':'/user/permission/getbyrole',
            'type':'POST',
            'dataType':'json',
            'data':user.data
        }).done(callback)

    },
    getUserInfo:function (callback) {
        $.ajax({
            'url':'/user/getinfo',
            'type':'POST',
            'dataType':'json',
            'data':user.data
        }).done(callback)
    },
    deleteUser:function (callback) {
        $.ajax({
            'url':'/user/delete/'+user.data.id+'/1'
        }).done(callback)

    },
    getParameters:function (callback) {
        $.ajax({
            'url':'/user/parameters/get',
            'dataType':'json',
        }).done(callback)
    }
}