
<html>
  <head>
    <?= $this->controller->runMethod('main', 'head') ?>

    <script>
      var customers = <?= $customers ?>;
    </script>

  </head>
  <body>
    <?= $this->controller->runMethod('main', 'menu') ?>

    <div class="container">

      <div class="page-header">
        <h1><?= $project->name ?></h1>
      </div>

      <div class="panel panel-default <?= ($project->closed ? 'panel-success' : 'panel-info') ?>">
        <div class="panel-heading">
          <div class="row">
               <div class="col-md-4">
              Информация о проекте
            </div>
            <div class="col-md-4">
              <label>Статус проект:</label> <?= ($project->closed ? 'Закрыт' : 'Открыт') ?>
            </div>
          </div>
        </div>
        <div class="panel-body">

          <div class="row">

            <div class="col-md-3">
              <label>Заказчик:</label> <?= $project->customers_name ?>
            </div>

            <?php if ($project->cost): ?>
              <div class="col-md-3">
                <label>Стоимость работ:</label> <?= $project->cost ?> руб.
              </div>
            <?php endif; ?>

            <?php if ($project->cost - $project->paidon > 0): ?>
              <div class="col-md-3">
                <label>Невыплаченно:</label> <?= ($project->cost - $project->paidon ) ?> руб.
              </div>
            <?php endif; ?>

          </div>
          <div class="row">

            <div class="col-md-3">
              <label>Создан:</label> <?= $project->createdon ?>
            </div>
            <div class="col-md-3">
              <label>Дедлайн:</label> <?= $project->deadline ?>
            </div>
            <?php if ($elapsedTime): ?>
            <div class="col-md-6">
                <label>Затрачено времени:</label> <?= self::makeTime($elapsedTime) ?>
                <label>Стоимость часа:</label> <?= $hourCost ?> руб.
            </div>
            <?php endif; ?>

          </div>
        </div>
        <div class="panel-footer">
          <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'task', 'a' => 'create', 'projectid' => $project->id), true) ?>"><em class="glyphicon glyphicon-plus"></em> Добавить задачу</a>
          <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'addpayment')) ?>"><em class="glyphicon glyphicon-plus"></em> Добавить выплату</a>
          <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'task', 'a' => 'collection', 'projectid' => $project->id), true) ?>"><em class="glyphicon glyphicon-tasks"></em> Все задачи</a>
          <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'payments', 'projectid' => $project->id), true) ?>"><em class="glyphicon glyphicon-eye-open"></em> Статистика по выплатам</a>
          <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'update')) ?>"><em class="glyphicon glyphicon-pencil"></em> Редактировать</a>
          <a class="btn btn-success" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'closetask')) ?>">закрыть все задачи <em class="glyphicon glyphicon-flash"></em></a>
          <a class="btn btn-danger pull-right" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'drop')) ?>"><em class="glyphicon glyphicon-remove"></em> Удалить</a>
        </div>
      </div>

      <?php if ($project->dsc) : ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            Описание
          </div>
          <div class="panel-body pre"><?= $project->dsc ?></div>
        </div>
      <?php endif; ?>

      <h2>Открытые задачи
      </h2>

      <?= $this->controller->loadTemplate('projects/task')->fetchCollection($project->getTasks(true)) ?>


    </div><!-- /.container -->

  </body>
</html>
