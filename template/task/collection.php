
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
        <h1>Задачи по проекту <?= $project->name ?></h1>
      </div>


      <?= $this->controller->loadTemplate('projects/task')->fetchCollection($tasks) ?>




      <!--
          
          <div class="panel panel-default panel-info">
            <div class="panel-heading">
              Выплаты по проекту
            </div>
            <div class="panel-body">

              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>
                      Дата
                    </th>
                    <th>
                      Сумма
                    </th>
                    <th>
                      Комментарий
                    </th>
                  </tr>
                </thead>
                <tbody>
      <?= $this->controller->loadTemplate('projects/ticket')->fetchCollection($project->getPayments()) ?>
                </tbody>
              </table>

            </div>
            <div class="panel-footer">
              <a class="btn btn-primary" href="<?= $this->makeUrl(Array('c' => 'projects', 'a' => 'addpayment')) ?>"><em class="glyphicon glyphicon-plus"></em> Добавить выплату</a>
            </div>
          </div>
          
      
      -->
    </div><!-- /.container -->

  </body>
</html>
