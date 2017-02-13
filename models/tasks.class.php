<?php

class Tasks_model extends Model {

  protected $table = 'tasks';
  protected $fields = Array(
      'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
      'projectid' => 'INT NOT NULL',
      'createdon' => 'TIMESTAMP',
      'createdby' => 'INT',
      'title' => 'VARCHAR(255)',
      'dsc' => 'MEDIUMTEXT',
      'deadline' => 'DATE',
      'closed' => 'BOOL',
      'closedon' => 'DATE',
      'closedby' => 'INT',
      'cost' => 'INT',
      'paidon' => 'INT',
      'elapsedtime' => 'INT'
  );
  
  protected $orderby = 'deadline desc';
  private $project;
  
  public function save(){
    parent::save();
    
    //если у проекта нет открытых задачь - закрываем его
    $project = $this->getProject();
    $tasks = $project->getTasks(true);
    
    if( $tasks->getCount() == 0 ){
      $project->close();
    }
    else{
      $project->closed = 0;
    }
    
    //обновляем суммарную стоимость работы по проекту
    $sql = "select sum(cost) from {$this->getTableName()} where projectid = ?";
    $query = $this->DB->query($sql, Array($this->projectid));
    
    $sum = $query->fetchColumn();
    $project->cost = $sum;
    $project->save();
  }
  
  public function getEvents(){
    $model = $this->controller->loadModel('events');
    $model->addRules( Array('taskid' => $this->id ));
    return $model->getCollection();
  }
  
  public function getProject(){
    if ( !empty($this->project) ) return $this->project;
    return $this->project = $this->controller->loadModel('projects')->getObject($this->projectid);
  }

}