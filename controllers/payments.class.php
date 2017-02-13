<?php

class Payments extends Controller {

  public function write() {

    $template = $this->loadTemplate('payments/collection');

    $dateof = str_replace('.', '-', $this->getOption('dateof', $_REQUEST, date('Y-m-1 00:00:00')));
    $dateto = str_replace('.', '-', $this->getOption('dateto', $_REQUEST, date('Y-m-31 00:00:00')));

    $payments = $this->loadModel('payments');
    $payments->addRules(Array(
        'payments.createdon:>=' => $dateof,
        'payments.createdon:<=' => $dateto
    ));

    $payments = $payments->bindGraph();
      
    $psh = Array(
        'profit' => $payments->getSum(),
        'items' => $payments,
        'dateof' => $dateof,
        'dateto' => $dateto,
    );

    return $template->parse($psh);
  }

  public function addpayment() {

    $projectid = $this->getOption('id', $_REQUEST, false);
    if ($projectid === false)
      return $this->servicePage(404);

    $obj = $this->loadModel('projects')->getObject($projectid);
    if ($obj === false)
      return $this->servicePage(404);

    if (isset($_REQUEST['save'])) {

      $payment = $this->loadModel('payments')->newObject();
      $payment->projectid = $projectid;
      $payment->fromArray($_REQUEST);
      $payment->save();

      $this->redirect(Array('id' => $projectid), 'projects', 'view');
    }


    return $this->loadTemplate('projects/payment/add')->parse(Array('project' => $obj));
  }
  
  public function payments(){

    $template = $this->loadTemplate('projects/payments');

    $dateof = str_replace('.', '-', $this->getOption('dateof', $_REQUEST, date('Y-m-1 00:00:00')));
    $dateto = str_replace('.', '-', $this->getOption('dateto', $_REQUEST, date('Y-m-31 00:00:00')));
    $projectid = $this->getOption('projectid', $_REQUEST, false);

    $projects = $this->loadModel('payments');
    $projects->addRules(Array(
        'payments.createdon:>=' => $dateof,
        'payments.createdon:<=' => $dateto,
        'projects.id:=' => $projectid
    ));

    $payments = $projects->bindGraph();

    $psh = Array(
        'items' => $payments,
        'dateof' => $dateof,
        'dateto' => $dateto,
    );

    return $template->parse($psh);
  
  }

  public function close() {

    $projectid = $this->getOption('id', $_REQUEST, false);
    if ($projectid === false)
      return $this->servicePage(404);

    $obj = $this->loadModel('projects')->getObject($projectid);
    if ($obj === false)
      return $this->servicePage(404);


    $obj->closed = !$obj->closed;
    $obj->closedon = $obj->closed ? date('Y-m-d') : Null;

    $obj->save();
  }

}
