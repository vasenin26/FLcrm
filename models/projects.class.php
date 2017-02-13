<?php

class Projects_model extends Model {

  protected $table = 'projects';
  protected $fields = Array(
      'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
      'createdon' => 'TIMESTAMP',
      'createdby' => 'INT',
      'customerid' => 'INT',
      'name' => 'VARCHAR(255)',
      'dsc' => 'MEDIUMTEXT',
      'deadline' => 'DATE',
      'closed' => 'BOOL',
      'closedon' => 'DATE',
      'closedby' => 'INT',
      'cost' => 'INT',
      'paidon' => 'INT',
      'elapsedtime' => 'INT'
  );
  protected $joines = Array(
      'customers.id' => 'projects.customerid'
  );
  protected $orderby = 'createdon desc';

  public function save(){

    $customerid = $this->customerid; // несуществующее совйство всегда пустое, даже если есть магический метод
    if (!is_numeric($customerid) && !empty($customerid)) {
      $customer = $this->controller->loadModel('customers')->getCustromer($customerid);
      $this->customerid = $customer->id;
    }

    parent::save();
  }

  public function getProfit($dateof = false, $dateto = false) {

    if ($dateof === false) {
      $dateof = date('Y-m-d 00:00:00');
    }
    if ($dateto === false) {
      $dateto = date('Y-m-d 00:00:00', time() + 84600);
    }


    $sql = 'SELECT SUM(cost) as prifit FROM ' . $this->getTableName() . ' WHERE createdon >= ? && createdon <= ?';

    $query = $this->DB->query($sql, Array(
        $dateof, $dateto
    ));

    $profit = $query->fetchColumn();

    if (!is_numeric($profit))
      $profit = 0;

    return $profit;
  }
  
  public function getUnclosed(){
    
    $obj = new self($this->controller);
    $obj->addRules(Array('closed' => 0, 'OR:closed:is' => Null));

    return $obj->bindGraph();
    
  }
  
  public function getPayments(){
    
    $model = $this->controller->loadModel('payments');
    $model->addRules(Array('projectid' => $this->id));
    $model->setOrder('createdon desc');
    
    return $model->getCollection();
    
  }
  
  public function getTasks( $onlyOpen = false ){
    
    $model = $this->controller->loadModel('tasks');
    if ($onlyOpen){
      $model->addRules( Array('AND::' => Array('closed:!=' => 1 , 'OR:closed:is' => Null ) ) );
    }
    $model->addRules( Array('projectid' => $this->id ));
    return $model->getCollection();
    
  }
  
  public function close(){
    
    $this->closed = 1;
    $this->closedon = $this->closed ? date('Y-m-d') : Null;

    $this->save();
    
  }
  
  public function elapsedTime(){
    $tasks = $this->getTasks();
    $hours = $tasks->bindGraph(Array('sum(elapsedtime) as sum_elapsedtime'));
    $hours = $hours->fetch();
    //print_r($hours);
    return $hours->sum_elapsedtime;
  }

}
