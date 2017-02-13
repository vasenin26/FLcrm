<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of template
 *
 * @author Леонид
 */
class Template {

  private $path = '';
  private $psh = Array();
  private $controller = false;

  public function __construct($template, $controller) {

    $this->controller = $controller;

    $paths = Array(
        '',
        BASE_PATH . '/core/template/',
        BASE_PATH . '/template/',
    );

    foreach ($paths as $p) {

      $exts = Array('html', 'tpl', 'php');

      foreach ($exts as $ext) {

        $file = $p . $template . '.' . $ext;
        if (is_file($file)) {
          $this->path = $file;
          return;
        }
      }
    }

    die('Не удалось найти шаблон: ' . $template);
  }

  public function setPlaceholders($psh) {
    $this->psh = array_merge($this->psh, $psh);
  }

  public function setPlaceholder($name, $value) {
    $this->psh[$name] = $value;
  }

  public function parse($psh = Array()) {

    $this->setPlaceholders($psh);

    foreach ($this->psh as $key => $value) {
      $$key = $value;
    }

    ob_start();
    include( $this->path );
    $out = ob_get_contents();
    ob_end_clean();

    return $out;
  }
  
  public function encode($value){
    return htmlspecialchars($value);
  }

  public function makeUrl($params = Array(), $reset = false) {
    
    $base = $reset ? Array() : $_REQUEST;

    $params = array_merge($base, $params);

    $s = Array();
    foreach ($params as $key => $value) {
      $s[] = $key . '=' . $value;
    }

    return '/?' . join('&', $s);
  }
  
  public function __toString(){
    return $this->parse();
  }
  
  public function fetchCollection($collection, $psh = Array()){
    
    $out = '';
    
    while($item = $collection->fetch()){
      $psh['item'] = $item;
      $out .= $this->parse( $psh );
    }
    
    return $out;
    
  }
  
  static function getCase($_number, $_case1, $_case2, $_case3)
  {
      $base = $_number - floor($_number / 100) * 100;
      $result = null;

      if ($base > 9 && $base < 20) {
          $result = $_case3;

      } else {
          $remainder = $_number - floor($_number / 10) * 10;

          if (1 == $remainder) $result = $_case1;
          else if (0 < $remainder && 5 > $remainder) $result = $_case2;
          else $result = $_case3;
      }

      return $result;
  }
  
  static function makeTime($sec){

    $hour = floor( $sec / 60 );
    $minuts = ( $sec % 60);
    
    $time = '';
    if( $hour > 0 ){
       $time .= $hour . ' ' .self::getCase($hour, 'час', 'часа', 'часов'). ' ';
    }
    $time .= $minuts . ' ' .self::getCase($minuts, 'минута', 'минуты', 'минут');
    
    return $time;
  
  }
}
