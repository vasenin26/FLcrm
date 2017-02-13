
<html>
  <head>
    <?= $this->controller->runMethod('main', 'head') ?>
  </head>
  <body>
    <?= $this->controller->runMethod('main', 'menu') ?>

    <div class="container">

      <div class="page-header">
        <h1>Событие к задаче #<?= $task->id ?></h1>
      </div>

      <form class="form-horizontal" method="POST" action="/?c=event&a=create">
        <input type="hidden" id="taskid" name="taskid" class="form-control" value="<?= $task->id ?>">
        <div class="form-group">
          <label class="col-md-4 control-label" for="type">Название проекта</label>
          <div class="col-md-8">
            <?= $task->getProject()->name ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="dsc">Описание задачи</label>
          <div class="col-md-8">
            <textarea id="dsc" name="dsc" rows="12" class="form-control"><?= $this->encode($event->dsc) ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="cost">Изменить стоимость</label>
          <div class="col-md-8">
            <input type="text" id="cost" name="cost" class="form-control" value="<?= $event->cost ?>" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="cost">Добавить время (минуты)</label>
          <div class="col-md-2">
            <input type="text" id="cost" name="elapsedtime" class="form-control" value="<?= $event->elapsedtime ?>" maxlength="100">
          </div>
          <label class="col-md-4 control-label" for="cost">Пересчитать стоимость</label>
          <div class="col-md-2">
            <input type="text" id="cost" name="hourcost" class="form-control" maxlength="100" value="330" placeholder="руб./чаc">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="deadline">Дедлайн</label>
          <div class="col-md-3">
            <div class='input-group date' id='deadline'>
              <input type='text' class="form-control" data-format="yyyy-MM-dd HH:mm:ss" name="deadline" value="" >
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="checkbox">
              <input name="closed" type="hidden" value="0">
              <label><input type="checkbox" id="notify" name="closed" value="1" <?= ($event->сlosed ? 'checked="checked"' : '') ?>>Задача закрыта</label>
            </div>
          </div>
        </div>
        <script>
          $('#deadline').datetimepicker({
            locale: 'sql'
          });
        </script>
        <div class="form-group">
          <div class="col-md-offset-4 col-md-8">
            <button type="submit" class="btn btn-primary" name="save" value="true">Сохранить</button>
          </div>
        </div>
      </form>

    </div><!-- /.container -->

  </body>
</html>
