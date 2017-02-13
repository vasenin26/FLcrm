
<html>
  <head>
    <?= $this->controller->runMethod('main', 'head') ?>
  </head>
  <body>
    <?= $this->controller->runMethod('main', 'menu') ?>

    <div class="container">

      <div class="page-header">
        <h1>Новая задача</h1>
      </div>

      <form class="form-horizontal" method="POST" action="/?c=task&a=create">
        <input type="hidden" id="id" name="projectid" class="form-control" value="<?= $project->id ?>">
        <div class="form-group">
          <label class="col-md-4 control-label" for="type">Название проекта</label>
          <div class="col-md-8">
            <?= $project->name ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="type">Заголовок задачи</label>
          <div class="col-md-8">
            <input type="text" id="name" name="title" class="form-control" value="<?=$this->encode($task->title)?>" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="dsc">Описание задачи</label>
          <div class="col-md-8">
            <textarea id="dsc" name="dsc" rows="12" class="form-control"><?= $this->encode($task->dsc) ?></textarea>
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="cost">Оценочная стоимость</label>
          <div class="col-md-8">
            <input type="text" id="cost" name="cost" class="form-control" value="<?= $task->cost ?>" maxlength="100">
          </div>
        </div>
        <div class="form-group">
          <label class="col-md-4 control-label" for="deadline">Дедлайн</label>
          <div class="col-md-3">
            <div class='input-group date' id='deadline'>
              <input type='text' class="form-control" data-format="yyyy-MM-dd HH:mm:ss" name="deadline" value="<?= $task->deadline ?>" >
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
        </div>
        <script>
          $('#deadline').datetimepicker({
            locale: 'sql'
          });
        </script>
        <div class="form-group">
          <label class="col-md-4 control-label" for="deadline">Дата закрытия проекта</label>
          <div class="col-md-3">
            <div class='input-group date' id='closedon'>
              <input type='text' class="form-control" data-format="yyyy-MM-dd HH:mm:ss" name="closedon" value="<?= $task->closedon ?>" >
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div>
          </div>
          <div class="col-md-3">
            <div class="checkbox">
              <input name="paid" type="hidden" value="0">
              <label><input type="checkbox" id="notify" name="paid" value="1" <?= ($task->сlosed ? 'checked="checked"' : '') ?>>Задача закрыта</label>
            </div>
          </div>
        </div>
        <script>
          $('#closedon').datetimepicker({
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
