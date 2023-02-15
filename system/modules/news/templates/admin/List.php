<div class="panel">
    <div class="panel-heading">Управление новостями</div>
    <div class="panel-body">
        <div class="col-sm-2">
            <div class="clearfix"><span class="btn btn-xs btn-success" onclick="showCategoryForm(0);"><i
                            class="fa fa-plus"></i> Категорию</span>
            </div>
            <div class="clearfix" id="CategoryList">
                <div class="NewsList" onclick="showCatArticle(this);"
                     data-id="0">Без категории (0)
                </div>
                <?php if (is_array($categories)) {
                    foreach ($categories as $cat) {
                        ?>
                        <div class="NewsList clearfix" id="category<?= $cat->id; ?>" onclick="showCatArticle(this);"
                             data-id="<?= $cat->id; ?>">
                            <div class="pull-left"><?= $cat->name; ?></div>
                            <div class="pull-right">
                                <i class="fa fa-edit" onclick="showCategoryForm(<?= $cat->id; ?>);"></i>
                                <i class="fa fa-trash" onclick="delCategory(<?= $cat->id; ?>);"></i></div>
                        </div>
                        <?
                    }
                } ?>
            </div>
        </div>
        <div class="col-sm-10">
            <div class="clearfix"><span class="btn btn-xs btn-success" id="addArticle" data-activecategory="0"
                                        onclick="showArticleForm(0);"><i
                            class="fa fa-plus"></i> Новость</span></div>
            <div class="clearfix">
                <table id="articleList" class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th><i class="fa fa-eye"></i></th>
                        <th><i class="fa fa-edit"></i></th>
                        <th><i class="fa fa-trash"></i></th>
                        <th><i class="fa fa-image"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Modal Window -->
<div class="modal fade " id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" id="modalInner">
        <div class="modal-content" id="modalInner" style="background-color: white;">

        </div>
    </div>
</div>
<!-- / Modal Window -->
<script>
    /* MODAL BLOCK */
    function showModal() {
        $("#myModal").modal();
    }

    function closeModal() {
        $("#myModal").modal('hide');
    }

    /* / MODAL BLOCK */

    /* LOAD BLOCK */
    function loadForm(url) {
        $.getJSON(url, function (data) {
            document.getElementById('modalInner').innerHTML = data.body;
            if (CKEDITOR) {
                $('.ckeditor').ckeditor({
                    filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                    filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
                    filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
                });
            } else {
                console.log('noEditor');
            }
        });
        showModal();

    }

    function showArticleForm(id) {
        loadForm("/news/admin/articleform/" + id + "/" + document.getElementById('addArticle').dataset.activecategory);
    }

    function showCategoryForm(id) {
        loadForm("/news/admin/categoryform/" + id);
    }

    /*
     Показывает новости раздела
     */
    function showCatArticle(obj) {
        let cats = document.querySelectorAll('.NewsList');
        cats.forEach(function (item) {
            item.classList.remove('active');
        });
        obj.classList.add('active');
        /*
         Установим действующий раздел
         */
        document.getElementById('addArticle').dataset.activecategory = obj.dataset.id;
        /*
         Перегружает новости
         */
        loadArticles(obj.dataset.id);
    }

    /* LOAD BLOCK */

    function loadArticles(catId) {
        const urlToLoadArticles = "/news/admin/getlist/" + catId;
        if (typeof window.TableNews != "undefined") {
            window.TableNews.ajax.url(urlToLoadArticles);
            window.TableNews.ajax.reload();
        } else {
            window.TableNews = $('#articleList').DataTable({
                "ajax": urlToLoadArticles,
                "lengthMenu": [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
                "iDisplayLength": 50,
                rowId: 'id',
                "columns": [
                    {
                        "data": "id"
                    },
                    {
                        "data": "name"
                    },
                    {
                        "data": "visible"
                    },
                    {
                        "data": "edit"
                    },
                    {
                        "data": "trash"
                    },
                    {
                        "data": "image"
                    }

                ],
                "language": {
                    "url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
                }
            });
        }
    }

    /*
     Первичная загрузка
     */
    document.addEventListener("DOMContentLoaded", function (event) {
        if ($('#articleList').length > 0) {
            loadArticles(0);
        }
    });

    function send(form) {
        $(form).trigger('form-pre-serialize');
        var formData = new FormData(form);
        $.ajax({
            url: form.action,
            type: form.method,
            dataType: 'json',
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                if (typeof data.success != "undefined") {
                    closeModal();
                    if (data.type == 'article') {
                        loadArticles(document.getElementById('addArticle').dataset.activecategory);
                    }
                    if (data.type == 'cat') {
                        let list = document.querySelector('#CategoryList');
                        if (document.querySelector('#category' + data.data.id)) {
                            document.querySelector('#category' + data.data.id + ' > div.pull-left').innerHTML = data.data.name;
                        } else {
                            list.innerHTML = list.innerHTML + " <div class=\"NewsList clearfix\" onclick=\"showCatArticle(this);\"\n" +
                                "                             data-id=\"" + data.data.id + "\">" +
                                "<div class=\"pull-left\">" + data.data.name + "</div>\n" +
                                "                            <div class=\"pull-right\">\n" +
                                "                                <i class=\"fa fa-edit\" onclick=\"showCategoryForm(" + data.data.id + ");\"></i>\n" +
                                "                                <i class=\"fa fa-trash\" onclick=\"delCategory(" + data.data.id + ");\"></i></div></div>";
                        }

                    }
                    $.toast({
                        heading: 'Успешно',
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                } else {
                    $.toast({
                        heading: 'Что то пошло не так.',
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                }
            },
            error: function (error) {
                console.log(error);
                $.toast({
                    heading: error.responseText,
                    text: '',
                    position: 'top-right',
                    icon: 'info'
                });
            }
        });
        return false;
    }

    function delArticle(id) {
        if (confirm('Удалить ?')) {
            $.ajax({
                url: '/news/admin/deletearticle/' + id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    window.TableNews.row('#' + id).remove().draw();
                },
                error: function (error) {
                    console.log(error);
                    $.toast({
                        heading: error.responseText,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                }
            });
        }
    }

    function delCategory(id) {
        if (confirm('Удалить ?')) {
            $.ajax({
                url: '/news/admin/deletecategory/' + id,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    document.querySelector('#category' + id).remove();
                },
                error: function (error) {
                    console.log(error);
                    $.toast({
                        heading: error.responseText,
                        text: '',
                        position: 'top-right',
                        icon: 'info'
                    });
                }
            });
        }
    }

    function delImg(id, obj) {
        $.ajax({
            url: '/news/admin/deleteimage/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                obj.remove();
                document.querySelector('#ImgArticle').remove();
                loadArticles(document.getElementById('addArticle').dataset.activecategory);
                $.toast({
                    heading: 'Удалил',
                    text: '',
                    position: 'top-right',
                    icon: 'info'
                });
            },
            error: function (error) {
                console.log(error);
                $.toast({
                    heading: error.responseText,
                    text: '',
                    position: 'top-right',
                    icon: 'info'
                });
            }
        });
    }

</script>
<style>
    .NewsList {
        width: 100%;
        padding: 3px;
        cursor: pointer;
    }

    .NewsList:hover {
        background-color: #f1f2f7;
    }

    .active {
        background-color: #2a3542;
        color: white;
    }

</style>