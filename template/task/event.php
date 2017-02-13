

<div class="panel panel-default panel-info">
  <div class="panel-heading">
    <div class="row">
      <div class="col-md-4">
        <label>Дата события:</label> <?= $item->createdon ?>
      </div>
    </div>
  </div>
  <? if ($item->dsc): ?>
    <div class="panel-body">
      <div class="pre"><?= $item->dsc ?></div>
    </div>
  <? endif; ?>
  <div class="panel-footer">
    <div class="row">
      
      <? if( $item->cost ) :?>
      <div class="col-md-4">
        <label>Стоимость:</label> <?= $item->cost ?> руб.
      </div>
      <? endif ;?>
      
      <? if( $item->elapsedtime ) :?>
      <div class="col-md-4">
        <label>Затраченное время (минут):</label> <?= $item->elapsedtime ?>
      </div>
      <? endif ;?>
      
      <? if( $item->deadline && $item->deadline != '0000-00-00' ) :?>
      <div class="col-md-4">
        <label>Дедлайн изменен на:</label> <?= $item->deadline ?>
      </div>
      <? endif ;?>
      
      <div class="col-md-4">
        <label>Статус:</label> <?= ($item->closed ? 'Закрыта' : 'Открыта') ?>
      </div>
      
    </div>
  </div>
</div>