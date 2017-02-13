<div class="panel panel-default panel-info">
  <div class="panel-heading">
    <?= $item->title ?>
  </div>
  <? if( $item->dsc ) : ?>
  <div class="panel-body">
    <div class="pre"><?= $item->dsc ?></div>
  </div>
  <? endif; ?>
  <div class="panel-footer">
    <div class="row">
      <div class="col-md-2">
        <a class="btn btn-default" href="<?= $this->makeUrl(Array('c' => 'task', 'a' => 'view', 'id' => $item->id), true) ?>"><em class="glyphicon glyphicon-eye-open"></em> Посмотреть</a>
      </div>
      <div class="col-md-3">
        <label>Создана:</label> <?= $item->createdon ?>
      </div>
      <div class="col-md-4">
        <label>Потрачено времени:</label> <?= self::makeTime($item->elapsedtime) ?>
      </div>
      <div class="col-md-3">
        <label>Бюджет:</label> <?= $item->cost ?> руб.
      </div>
    </div>
  </div>
</div>