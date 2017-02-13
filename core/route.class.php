<?php

class Route extends Controller{
    
    public function __construct(){
        //header('Content-Type: text/html; charset=utf-8', true);
        
        Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");
        header("Cache-Control: post-check=0,pre-check=0");
        header("Cache-Control: max-age=0");
        header("Pragma: no-cache");
        
    }
            
    function routeController(){
        
        $c = isset($_GET['c']) ? $_GET['c'] : 'main';
        $a = isset($_GET['a']) ? $_GET['a'] : 'write';
        
        if (!empty($_GET['q']) && !empty($_GET['f'])){ //hook
            
            $this->runMethod($_GET['q'], $_GET['f'], $_GET);
            
        }
        
        $this->runController($c, $a);
        
    }
    
    function runController($c, $action = 'write'){
        
        $c = $this->loadController($c);
        
        $this->out = $c->$action();
        
    }
    
    function write(){
        
        echo $this->out;
        
    }
    
}
