<?php

class Payments_model extends Model {

  protected $table = 'payments';
  protected $fields = Array(
      'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
      'createdon' => 'TIMESTAMP',
      'projectid' => 'INT',
      'dsc' => 'MEDIUMTEXT',
      'value' => 'INT',
  );
  protected $joines = Array(
      'projects.id' => 'payments.projectid',
      'customers.id' => 'projects.customerid',
  );
  protected $orderby = 'createdon desc';

  public function save(){

    //добавляем платеж к проекту
    
    $project = $this->controller->loadModel('projects')->getObject($this->projectid);
    
    if( $project === false ) return false;
    $project->paidon += $this->value;
    $project->save();

    return parent::save();
  }

  public function remove() {

    //отнимаем платеж от проекта
    
    $project = $this->controller->loadModel('projects')->getObject($this->projectid);
    
    if( $projectid === false ) return false;
    $project->paidon -= $this->value;
    $provect->save();

    return parent::remove();
    
  }
  
  public function getSum(){
    
    $sql = 'SELECT SUM(value)'
            . ' FROM ' . $this->table
            . $this->_parseRules($this->rules);
    
    $query = $this->DB->query($sql, $this->psh);
    return $query->fetchColumn();
    
  }

}
