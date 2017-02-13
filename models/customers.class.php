<?php

class Customers_model extends Model {

    protected $table = 'customers';
    protected $fields = Array(
        'id' => 'INT PRIMARY KEY AUTO_INCREMENT',
        'createdon' => 'TIMESTAMP',
        'name' => 'VARCHAR(255) UNIQUE',
        'phone' => 'CHAR(11)',
        'skype' => 'CHAR(36)',
        'email' => 'CHAR(36)',
        'org' => 'VARCHAR(255)',
        'dsc' => 'MEDIUMTEXT'
    );
    protected $orderby = 'id desc';
	
	public function filter($f){
		
		$this->addRules(Array(
			'name:LIKE' => '%'.$f.'%',
			'OR:phone:LIKE' => '%'.$f.'%',
			'OR:org:LIKE' => '%'.$f.'%',
			'OR:dsc:LIKE' => '%'.$f.'%'
		));
		
	}
    
    public function getStatList(){
        
        $sql = 'SELECT id, name, count(id) as c FROM '.$this->table.' GROUP BY id ORDER BY c DESC, name ';
        
        $query = $this->DB->query($sql);
        
        $c = get_class($this);
        $obj = new $c($this->controller);
        $obj->setCollection($query);
        
        return $obj;
        
    }
    
    //id or name
    public function getCustromer($mixed){
      if(is_numeric($mixed)){
        $obj = $this->getObject($mixed);
      }
      else{
        $model = new self($this->controller);
        $model->addRules(Array('name' => $mixed));
        $collection = $model->getCollection();
        
        $obj = $collection->fetch();
        if ($obj === false){
          $obj = new self($this->controller);
          $obj->name = $mixed;
          $obj->save();
        }
      }
      return $obj;
    }
    
    public function getPayments(){
      $sql = 'SELECT projects.name as project, payments.createdon as date, payments.value as value FROM `customers` left join `projects` on projects.customerid = customers.id left join payments on payments.projectid = projects.id where customers.id = ?';
      $query = $this->DB->query($sql, Array($this->id));
      return $query;
    }

}
