<?php

class Model {

  protected $table = '';
  protected $fields = Array();
  protected $values = Array();
  protected $DB;
  protected $psh = Array();
  protected $collection = false;
  protected $joines = Array();
  protected $orderby = 'id asc';
  protected $groupby = false;
  protected $rules = Array();
  public $controller;

  public function __construct(&$controller, $values = false) {
    $this->DB = DB::getInstance();

    //проверяем наличие таблицы

    if (!$this->DB->table_exist($this->table)) {

      $fields = Array();

      foreach ($this->fields as $key => $rule) {
        $fields[] = $key . ' ' . $rule;
      }

      $sql = 'CREATE TABLE ' . $this->table . ' (' . join(', ', $fields) . ')';
      $this->DB->query($sql);
    }

    $this->controller = $controller;
    if ( is_array($values) ){
      $this->values = $values;
    }
    
  }

  public function newObject() {
    $c = get_class($this);
    return new $c($this->controller);
  }

  public function getObject($id) {

    /*
      $fields = Array();

      foreach ($this->fields as $key => $rule) {
      $fields[] = $key;
      }


      $sql = 'SELECT ' . join(', ', $fields) . ' FROM ' . $this->table . ' WHERE id = ?';
      $query = $this->DB->query($sql, Array($id));

      $values = $query->fetch();

      if ($values === false)
      return false;

      $c = get_class($this);
      $obj = new $c($this->controller);

      $obj->fromArray($values);
     */ 
    
    $model = $this->newObject();
    $model->addRules( Array($this->getTableName().'.id' => $id ) );
    $collection = $model->bindGraph();
    
    $obj = $collection->fetch();

    return $obj;
  }

  public function getCollection() {

    $fields = $this->getFieldsName();

    $sql = 'SELECT ' . join(', ', $fields)
            . ' FROM ' . $this->table
            . $this->_parseRules($this->rules)
            . ( $this->groupby === false ? '' : ' GROUP BY ' . $this->groupby )
            . ' ORDER BY ' . $this->orderby;

    $query = $this->DB->query($sql, $this->psh);

    $c = get_class($this);
    $obj = new $c($this->controller);
    $obj->addRules($this->rules);
    $obj->setCollection($query);

    return $obj;
  }

  public function bindGraph($select = false) {

    $tables = Array();

    $order = $this->orderby;

    if (strpos($order, '.') === false)
      $order = $this->table . '.' . $order;

    if ($select === false) {
      $select = Array();
      $this->_addTable($this->table, $select);
    }

    $joines = $this->_getJoines($select);

    $sql = 'SELECT ' . join(', ', $select)
            . ' FROM ' . $this->table
            . $joines
            . $this->_parseRules($this->rules)
            . ( $this->groupby === false ? '' : ' GROUP BY ' . $this->groupby )
            . ' ORDER BY ' . $order;

    $query = $this->DB->query($sql, $this->psh);

    $c = get_class($this);
    $obj = new $c($this->controller);
    $obj->addRules($this->rules);
    $obj->setCollection($query);

    return $obj;
  }

  public function fetch() {

    if ($this->collection === false)
      return false;

    $values = $this->collection->fetch();

    if ($values === false)
      return false;

    $c = get_class($this);
    $obj = new $c($this->controller, $values);

    return $obj;
  }

  public function fromArray($array = Array()) {
    unset($array['id']);
    $this->values = array_merge($this->values, $array);
    return true;
  }

  public function __set($name, $value) {
    $this->values[$name] = $value;
  }

  public function __get($name) {
    if (isset($this->values[$name]))
      return $this->values[$name];

    $name = $this->getTableName() . '_' . $name;
    if (isset($this->values[$name]))
      return $this->values[$name];

    return Null;
  }

  public function getValues() {
    $fields = Array();
    foreach ($this->fields as $key => $rule) {
      $fields[$key] = $this->_getValideField($key);
    }
    return $fields;
  }

  public function toArray() {
    return array_merge($this->getValues(), $this->values);
  }

  public function save() {
    $fields = $this->getValues();
    unset($fields['id']);

    if (is_null($this->id) || !is_numeric($this->id)) {
      $cols = $this->getFieldsName($fields);
      $psh = str_repeat('?,', count($fields) - 1) . '?';
      $sql = 'INSERT ' . $this->table . '(' . join(',', $cols) . ') VALUES (' . $psh . ')';
    } else {
      $cols = $this->getFieldsName($fields);
      $fields[] = $this->id;

      $psh = Array();
      foreach ($cols as $col) {
        $psh[] = $col . '=?';
      }
      $psh = join(',', $psh);

      $sql = 'UPDATE ' . $this->table . ' SET ' . $psh . ' WHERE id = ?';
    }

    $values = Array();
    foreach ($fields as $val) {
      $values[] = $val;
    }

    $query = $this->DB->query($sql, $values);
    $save = $query->fetch();

    if (is_null($this->id) || !is_numeric($this->id)) {
      $this->id = $this->DB->lastInsertId();
    }

    return true;
  }

  public function remove() {

    $sql = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
    $this->DB->query($sql, Array($this->id));

    return true;
  }

  public function setCollection($collection) {

    if (get_class($collection) !== 'PDOStatement')
      return false;

    $this->collection = $collection;

    return true;
  }

  public function setOrder($order) {
    return $this->orderby = $order;
  }

  public function getFieldsName($fields = false) {

    if ($fields === false)
      $fields = $this->fields;

    $f = Array();
    foreach ($fields as $key => $val) {
      $f[] = $key;
    }

    return $f;
  }

  public function getTableName() {
    return $this->table;
  }

  public function query($sql, $psh = Array()) {
    return $this->DB->query($sql, $psh);
  }

  public function getCount() {

    $sql = 'SELECT count(id) as c FROM ' . $this->table
            . $this->_parseRules($this->rules);

    $count = $this->DB->query($sql, $this->psh);
    $c = $count->fetchColumn();

    if (!is_numeric($c))
      $c = 0;

    return $c;
  }

  private function _addTable($table_name, &$select) {

    $feilds = $this->controller->loadModel($table_name)->getFieldsName();

    foreach ($feilds as $key) {
      $select[] = $table_name . '.' . $key . ' AS ' . $table_name . '_' . $key;
    }

    return;
  }

  private function _getJoines(&$select) {

    $join = '';
    foreach ($this->joines as $t1 => $t2) {
      $table = explode('.', $t1);
      $this->_addTable($table[0], $select);
      $join .= ' LEFT JOIN ' . $table[0] . ' ON ' . $t1 . ' = ' . $t2;
    }
    return $join;
  }

  public function addRules($rules) {
    if (!is_array($rules))
      return false;

    $this->rules = array_merge($this->rules, $rules);
  }

  protected function _parseRules($rules = Array(), $raw = false, $psh = false) {

    $where_array = Array();
    if ($psh === false)
      $psh = Array();

    foreach ($rules as $rule => $value) {

      $rule = explode(':', $rule);
      $rule = array_reverse($rule);
      $rule = call_user_func_array(Array($this, '_validateRule'), $rule);

      if (is_array($value) && $rule[2] != 'IN') {
        $holder = '(' . $this->_parseRules($value, true, $psh) . ')';
        unset($rule[2]); // drop rule;
        unset($rule[1]); // drop col;
      } elseif ($rule[2] == 'IN') {
        if (count($value) == 1) {
          $psh[] = $value[0];
          $rule[2] = '=';
          $holder = '?';
        } else {
          $psh = array_merge($psh, $value);
          $holder = '(' . str_repeat('?,', count($value) - 1) . '?)';
        }
      } elseif ($rule[2] == 'IS' || $rule[2] == 'IS NOT') {
        $holder = 'NULL';
      } else {

        /* --------------------- внимание дыркааа! ------------------------------- */

        if (preg_match('/^(func:)/', $value) === 1) {
          $holder = preg_replace('/^(func:)/', '', $value);
        } else {
          $psh[] = $value;
          $holder = '?';
        }
      }

      if (count($where_array) == 0) {
        unset($rule[0]); // drop contact;
      }

      $where_array[] = join(' ', $rule) . ' ' . $holder;
    }

    if (count($where_array) == 0)
      return '';

    if (!$raw)
      array_unshift($where_array, ' WHERE');

    $this->psh = array_merge($this->psh, $psh);

    return join(' ', $where_array);
  }

  private function _validateRule($rule, $col = Null, $contact = Null) {

    if (is_null($col)) {
      $col = $rule;
      $rule = '=';
    }
    if (is_null($contact)) {
      $contact = '&&';
    }

    return Array($contact, $col, $rule);
  }

  private function _getValideField($key) {

    if (empty($this->fields[$key]))
      return Null;

    $rule = $this->fields[$key];
    $val = $this->$key;

    if (preg_match('/INT/', $rule)) {
      if (!is_numeric($val))
        return Null;
    }
    if (preg_match('/CHAR/', $rule) || preg_match('/VARCHAR/', $rule)) {
      if (!is_null($val) && preg_match('/NULL/', $rule) && preg_match('/NOT NULL/', $rule) === false)
        return Null;
    }

    return $val;
  }

  public function group($col) {

    if (!isset($this->fields[$col]))
      return false;

    $this->groupby = $col;
    return true;
  }

}

class DB {

  private $dsn = "mysql:host=localhost;dbname=crm;charset=UTF8";
  private $database = 'crm';
  private $user = 'root';
  private $pass = 'master';
  public $pdo = false;
  protected static $_instance;

  private function __construct() {
    $this->pdo = new PDO($this->dsn, $this->user, $this->pass);
  }

  private function __clone() {
    
  }

  public function table_exist($table) {
    $sql = 'SHOW tables WHERE Tables_in_' . $this->database . ' = ?';
    $query = $this->query($sql, Array($table));

    $tables = $query->fetchAll();

    return count($tables) == 1;
  }

  public function query($sql, $psh = Array()) {

    $query = $this->pdo->prepare($sql);
    $query->execute($psh);

    if ($query->errorCode() !== '00000') {
      echo '<pre>';
      print_r($sql);
      echo "\n";
      print_r($psh);
      echo "\n";
      $error = $query->errorInfo();
      print_r($error);
      die;
    }

    return $query;
  }

  public function lastInsertId() {
    return $this->pdo->lastInsertId();
  }

  public static function getInstance() {
    // проверяем актуальность экземпляра
    if (null === self::$_instance) {
      // создаем новый экземпляр
      self::$_instance = new self();
    }
    // возвращаем созданный или существующий экземпляр
    return self::$_instance;
  }

}
