
<html>
  <head>
    <?= $this->controller->runMethod('main', 'head') ?>
  </head>
  <body>
    <?= $this->controller->runMethod('main', 'menu') ?>

    <div class="container">

      <div class="page-header">
        <h1>404</h1>
      </div>

      <p class="alert alert-error">
        Страница не обнаружена
      </p>

    </div><!-- /.container -->


  </body>
</html>
