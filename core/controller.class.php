<?php

class Controller {

  protected $out = '';
  protected $path = BASE_PATH;
  protected $models = Array();
  protected $title = 'База работ';

  public function __construct($path = BASE_PATH) {
    $this->path = $path;
  }

  public function loadModel($name) {

    if (!class_exists('model')) {
      include( BASE_PATH . '/core/model.class.php' );
    }

    $path = BASE_PATH . '/models/' . $name . '.class.php';
    if (!is_file($path)) {
      die('Not fount model:' . $path);
    }

    $model = trim(substr($name, strrpos($name, '/')), '/');
    $class_name = $model . '_model';

    if (!class_exists($class_name)) {
      include( $path );
    }

    return new $class_name($this);
  }

  public function loadTemplate($path) {

    if (!class_exists('Template')) {
      include( CORE_PATH . '/template.class.php' );
    }

    return new Template($path, $this);
  }

  public function loadController($c) {

    $path = BASE_PATH . '/controllers/' . $c . '.class.php';
    if (!is_file($path)) {
      die('Not fount controller:' . $path);
    }

    $controller = trim(substr($c, strrpos($c, '/')), '/');
    if (!class_exists($controller)) {
      include( $path );
    }

    return new $controller($path);
  }

  public function runMethod($controller, $method = 'widget', $prop = Array()) {

    $c = $this->loadController($controller);

    if (method_exists($c, $method)) {
      return call_user_func_array(Array($c, $method), Array($prop));
    }

    return;
  }

  public function redirect($data = Array(), $c = false, $a = false) {

    $url = $this->makeUrl($data, $c, $a );
    
    header('HTTP/1.1 200 OK');
    header('Location: ' . $url);

    exit;
  }
  
  public function makeUrl($data = Array(), $c = false, $a = false){
    
    if (empty($c))
      $c = $_GET['c'];
    if (empty($a)) {
      $a = $_GET['a'];
    }

    $data = array_merge($data, Array(
        'c' => $c,
        'a' => $a
    ));

    array_walk($data, function(&$val, $key) {
      $val = $key . '=' . $val;
    });

    return '/?'.join('&', $data);
    
  }

  protected function servicePage($code, $message = false) {

    switch ($code) {

      case 404:
        header('HTTP/1.1 404 OK');
        echo $this->loadTemplate('services/404');
        exit;
        break;
    }
  }

  public function getOption($key, $options, $default = Null) {

    if (isset($options[$key]))
      return $options[$key];
    return $default;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setTitle($title) {
    $this->title = $title;
  }

  public function write() {
    return $this->out;
  }

}
