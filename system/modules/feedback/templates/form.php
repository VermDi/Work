<div class="row">
    <div class="col-sm-12 panel-heading">
        <div class="col-sm-12">
            <div class="panel-heading">
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="/feedback/send" id="sumbit_form">
                        <fieldset>
                            <legend>Текст фидбека</legend>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Ваше имя</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="Ваше имя" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email"  class="col-sm-2 control-label">Ваш email</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="email" name="email" placeholder="Ваш email" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="category"  class="col-sm-2 control-label">Ваш телефон</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="email" name="phone" placeholder="Ваш телефон" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="text" class="col-sm-2 control-label">Ваше сообщение</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="text" name="message"></textarea>
                                </div>
                                <input type="hidden" name="form_id" value="17"> 
                                <input type="hidden" name="redirect" value="/news/"> 
                            </div>
                        </fieldset>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success pull-right">Отправить сообщение</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
