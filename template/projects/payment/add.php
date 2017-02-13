
<html>
  <head>
    <?= $this->controller->runMethod('main', 'head') ?>
  </head>
  <body>
    <?= $this->controller->runMethod('main', 'menu') ?>

    <div class="container">

      <div class="page-header">
        <h1>Добавление платежа</h1>
      </div>

      <form class="form-horizontal" method="POST" action="/?c=projects&a=addpayment">
        <div class="form-group">
          <label class="col-md-4 control-label" for="device">Новый платеж</label>
          <div class="col-md-8">
            <?= $project->name ?>
            <input type="hidden" id="projectid" name="id" class="form-control" value="<?= $project->id ?>">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="deviceid">Размер платежа</label>
          <div class="col-md-4">
            <input type="text" id="value" name="value" class="form-control" value="" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="dsc">Комментарий к платежу</label>
          <div class="col-md-8">
            <textarea id="dsc" name="dsc" rows="12" class="form-control"></textarea>
          </div>
        </div>
        <div class="form-group">
          <div class="col-md-offset-4 col-md-8">
            <button type="submit" class="btn btn-primary" name="save" value="true">Добавить</button>
          </div>
        </div>
      </form>

    </div><!-- /.container -->

  </body>
</html>


