<?php

class Events_model extends Model {

  protected $table = 'task_events';
  protected $fields = Array(
      'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
      'taskid' => 'INT NOT NULL',
      'createdon' => 'TIMESTAMP',
      'createdby' => 'INT',
      'dsc' => 'MEDIUMTEXT',
      'closed' => 'BOOL',
      'deadline' => 'DATE',
      'cost' => 'INT',
      'elapsedtime' => 'INT'
  );
  
  protected $orderby = 'deadline desc';
  private $task;
  
  public function save(){
    
    //обновляем параметры у таски
    $task = $this->getTask();
    
    $sync = Array('deadline','cost','closed');
    foreach( $sync as $field ){
      $val = $this->$field;
      if ( !empty($val) || is_numeric($val) ){
        $task->$field = $val;
      }
    }
    
    if ($this->elapsedtime){
      $elapsedtime = $task->elapsedtime;
      if ( empty($elapsedtime) ) $task->elapsedtime = 0;
      $task->elapsedtime += $this->elapsedtime;
    }
    
    if($this->elapsedtime && $this->hourcost){
      //берем время по задаче и пересчитываем её стоимость
      $task->cost = round( ($task->elapsedtime / 60) * $this->hourcost, -2 );
    }
    
    $task->save();
    
    parent::save();
  }
  
  public function getTask(){
    
    if ( !empty($this->task) ) return $this->task;
    return $this->task = $this->controller->loadModel('tasks')->getObject($this->taskid);
    
  }

}