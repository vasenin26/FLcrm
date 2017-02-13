
<html>
  <head>
    <?= $this->controller->runMethod('main', 'head') ?>
  </head>
  <body>
    <?= $this->controller->runMethod('main', 'menu') ?>

    <div class="container">

      <div class="page-header">
        <h1><?= $task->title ? $task->title :'Задача по проекту ' . $task->getProject()->name ?></h1>
      </div>

      <div class="panel panel-default panel-info">
        <div class="panel-heading">
          <div class="row">
            <div class="col-md-4">
              Информация о задаче
            </div>
            <div class="col-md-4">
              <label>Статус задачи:</label> <?= ($task->closed ? 'Закрыта' : 'Открыта') ?>
            </div>
          </div>
        </div>
        <div class="panel-body">

          <div class="row">

            <div class="col-md-4">
              <label>Создан</label> <?= $task->createdon ?>
            </div>
            <div class="col-md-4">
              <label>Дедлайн</label> <?= $task->deadline ?>
            </div>
            <div class="col-md-4">
              <label>Оценочная стоиомсть</label> <?= $task->cost ?> руб.
            </div>

          </div>
        </div>
        <div class="panel-footer clearfix">
          <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'view', 'id' => $task->projectid), true) ?>"><em class="glyphicon glyphicon-eve-open"></em> Перейти к проекту</a>
          <a class="btn btn-danger pull-right" href="<?= $this->makeUrl(Array('c' => 'task', 'a' => 'drop')) ?>"><em class="glyphicon glyphicon-remove"></em> Удалить</a>
        </div>
      </div>

      <?php if ($task->dsc) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            Описание
          </div>
          <div class="panel-body pre"><?= $task->dsc ?></div>
        </div>
      <?php endif; ?>

      <h2>
        Обсуждение задачи
      </h2>

      <?= $this->controller->loadTemplate('task/event')->fetchCollection($task->getEvents(), Array('task' => $task)) ?>

      <div class="row">
        <div class="col-md-12">
          <a class="btn btn-primary" href="<?= $this->makeUrl(Array('c' => 'event', 'a' => 'create', 'taskid' => $task->id), true) ?>"><em class="glyphicon glyphicon-plus"></em> Добавить событие</a>
        </div>
      </div>


    </div><!-- /.container -->

  </body>
</html>
