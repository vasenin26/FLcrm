<?php

class Event extends Controller {

  public function create() {

    $taskid = $this->getOption('taskid', $_REQUEST, false);
    if ($taskid === false)
      return $this->servicePage(404);

    $task = $this->loadModel('tasks')->getObject($taskid);
    if ($task === false)
      return $this->servicePage(404);

    $event = $this->loadModel('events');
    
    if (isset($_REQUEST['save'])) {
      $event->fromArray($_REQUEST);
      $event->save();

      $this->redirect(Array(
          'id' => $task->id,
              ), 'task', 'view');
    }

    $template = $this->loadTemplate('event/editor');
    $template->setPlaceholder('event', $event);
    $template->setPlaceholder('task', $task);

    return $template->parse();
  }


}
