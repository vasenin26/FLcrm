<?php

class Task extends Controller {

  public function view() {

    $projectid = $this->getOption('id', $_REQUEST, false);
    if ($projectid === false)
      return $this->servicePage(404);

    $obj = $this->loadModel('tasks')->getObject($projectid);
    if ($obj === false)
      return $this->servicePage(404);

    $this->setTitle('Задача: ' . $obj->name);

    $template = $this->loadTemplate('task/view');
    $template->setPlaceholder('task', $obj);

    return $template->parse();
  }

  public function create() {

    $projectid = $this->getOption('projectid', $_REQUEST, false);
    if ($projectid === false)
      return $this->servicePage(404);

    $project = $this->loadModel('projects')->getObject($projectid);
    if ($project === false)
      return $this->servicePage(404);

    $task = $this->loadModel('tasks');
    
    if (isset($_REQUEST['save'])) {
      $task->fromArray($_REQUEST);
      $task->save();

      $this->redirect(Array(
          'a' => 'view',
          'id' => $task->id,
              ), false, 'view');
    }

    $template = $this->loadTemplate('task/editor');
    $template->setPlaceholder('task', $task);
    $template->setPlaceholder('project', $project);

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
    $psh['drop_link'] = $this->makeUrl(Array('comfirm' => 1, 'id' => $obj->id));

    return $template->parse($psh);
  }

  public function collection() {
    
    $projectid = $this->getOption('projectid', $_GET);
    $project = $this->loadModel('projects')->getObject($projectid);

    $tasks = $project->getTasks();
    
    $template = $this->loadTemplate('task/collection');
    $template->setPlaceholder('project', $project);
    $template->setPlaceholder('tasks', $tasks);

    return $template->parse();
  }

}
