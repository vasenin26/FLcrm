<?php

class Projects extends Controller {

  public function view() {

    $projectid = $this->getOption('id', $_REQUEST, false);
    if ($projectid === false)
      return $this->servicePage(404);

    $obj = $this->loadModel('projects')->getObject($projectid);
    if ($obj === false)
      return $this->servicePage(404);

    $this->setTitle('Проект: ' . $obj->name);

    $elapsedTime = $obj->elapsedTime();
    if ($elapsedTime) {
      $hourCost = round($obj->cost / ( $elapsedTime / 60 ));
    } else {
      $hourCost = 0;
    }

    $template = $this->loadTemplate('projects/view');
    $template->setPlaceholder('project', $obj);
    $template->setPlaceholder('elapsedTime', $elapsedTime);
    $template->setPlaceholder('hourCost', $hourCost);
    $template->setPlaceholder('tasks', $obj->getTasks());

    return $template->parse();
  }

  public function update() {

    $projects = $this->loadModel('projects');
    if (isset($_REQUEST['id'])) {
      $project = $projects->getObject($_REQUEST['id']);
    }
    if (empty($project))
      $project = $projects->newObject();

    $project->fromArray($_REQUEST);

    if (isset($_REQUEST['save'])) {
      $project->save();

      $this->redirect(Array(
          'id' => $project->id,
              ), false, 'view');
    }

    $psh = $project->getValues();

    $device = $this->loadModel('customers')->getObject($project->customerid);

    $psh['customername'] = $device === false ? false : $device->name;

    $customers = $this->loadModel('customers')->getStatList();
    $customers_array = Array();

    while ($customer = $customers->fetch()) {
      $customers_array[] = $customer->name;
    }

    $psh['customers'] = json_encode($customers_array);

    $template = $this->loadTemplate('projects/editor');
    $template->setPlaceholders($psh);

    return $template->parse();
  }

  public function drop() {

    $template = $this->loadTemplate('query');

    if (empty($_GET['id']))
      return $this->write();

    $obj = $this->loadModel('projects')->getObject($_GET['id']);

    if ($obj === false)
      return $this->write();

    if (!empty($_GET['confirm'])) {
      $obj->remove();
      return $this->loadTemplate('success')->parse();
    }

    $psh['name'] = $obj->type;
    $psh['drop_link'] = $this->makeUrl(Array('confirm' => 1, 'id' => $obj->id));

    return $template->parse($psh);
  }

  public function collection($prop = Array()) {

    $dateof = date('Y-m-d 00:00:00');
    $dateto = date('Y-m-d 00:00:00', time() + 84600);

    $projects = $this->loadModel('jobs');
    $projects->addRules(Array(
        'jobs.createdon:>=' => $dateof,
        'jobs.createdon:<=' => $dateto
    ));

    $projects = $projects->bindGraph();
    $template = $this->loadTemplate('job/item');

    $o = '';

    while ($project = $projects->fetch()) {
      $template->setPlaceholders($project->toArray());
      $o .= $template->parse();
    }

    return $o;
  }

  public function write() {

    $template = $this->loadTemplate('projects/collection');

    $dateof = str_replace('.', '-', $this->getOption('dateof', $_REQUEST, date('Y-m-1 00:00:00')));
    $dateto = str_replace('.', '-', $this->getOption('dateto', $_REQUEST, date('Y-m-31 00:00:00')));

    $projects = $this->loadModel('projects');
    $projects->addRules(Array(
        'projects.createdon:>=' => $dateof,
        'projects.createdon:<=' => $dateto
    ));

    $projects = $projects->bindGraph();
    $item = $this->loadTemplate('projects/item');

    $o = '';

    while ($project = $projects->fetch()) {
      $item->setPlaceholders(Array('item' => $project));
      $o .= $item->parse();
    }

    $psh = Array(
        'profit' => $projects->getProfit($dateof, $dateto),
        'items' => $o,
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
  
  public function closetask(){
    
    $projectid = $this->getOption('id', $_REQUEST, false);
    if ($projectid === false)
      return $this->servicePage(404);

    $obj = $this->loadModel('projects')->getObject($projectid);
    if ($obj === false)
      return $this->servicePage(404);
    
    $tasks = $obj->getTasks();
    
    while( $task = $tasks->fetch() ){
      $event = $this->loadModel('events');
      $event->fromArray(Array(
        'taskid' => $task->id,
        'closed' => true
      ));
      $event->save();
    }
    
    $this->redirect(Array(
          'id' => $projectid,
              ), 'projects', 'view');
    
  }

}
