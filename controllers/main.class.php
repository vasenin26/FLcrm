<?php

class main extends Controller {

  public function write() {
    
    $template = $this->loadTemplate('main');
    
    //выплаты в этом месяце
    
    $month = date('Y-m');
    
    $payments = $this->loadModel('payments');
    $payments->addRules(Array(
       'createdon:>=' => $month.'-1 00:00:00',
       'createdon:<=' => $month.'-31 23:59:59',
    ));
    
    $template->setPlaceholder('profit', $payments->getSum() );

    //не закрытые проекты
    
    $unclosed = $this->loadModel('projects')->getUnclosed();
    $template->setPlaceholder('unclosed', $unclosed );

    return $template->parse();
  }

  public function menu() {

    $template = $this->loadTemplate('menu');
    return $template->parse();
  }

  public function head() {

    $template = $this->loadTemplate('head');
    return $template->parse();
  }

}
