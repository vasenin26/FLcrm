<?php

class Device extends Controller {

    public function write() {


        $template = $this->loadTemplate('device/collection');
        //$template->setPlaceholders();
        
        return $template->parse();
        
    }
    
    public function collection(){
        
        $devs = $this->loadModel('devices');
        $devs = $devs->getCollection();
        $template = $this->loadTemplate('device/item');
        
        $o = '';
        
        while($dev = $devs->fetch()){
            $template->setPlaceholders($dev->getValues());
            $o .= $template->parse();
        }
        
        return $o;
        
    }
    
    public function statistic(){
        
        
        $devs = $this->loadModel('devices');
        $devs = $devs->getStatList();
        
        $item = $this->loadTemplate('device/statistic_item');
        
        $o = '';
        
        while($dev = $devs->fetch()){
            $item->setPlaceholders($dev->toArray());
            $o .= $item->parse();
        }
        
        return $this->loadTemplate('device/statistic')->parse(Array(
            'items' => $o,
            'counter' => $devs->getCounter()
        ));
        
    }
    
    public function update(){
        $devs = $this->loadModel('devices');
        if (isset($_REQUEST['id'])) {
            $obj = $devs->getObject($_REQUEST['id']);
        }
        if (empty($obj))
            $obj = $devs->newObject();

        if (isset($_REQUEST['save'])) {
            $obj->fromArray($_REQUEST);
            $obj->save();
            
            $this->redirect(Array(
               'id' => $obj->id
            ));
        }
        
        $psh = $obj->getValues();
        
        //привязанные работы
        
        $jobs = $this->loadModel('jobs');
        $jobs->addRules(Array(
            'jobs.deviceid:=' => $obj->id
        ));
        $jobs = $jobs->bindGraph();
        
        $job_item = $this->loadTemplate('device/jobitem');
        
        $o = '';
        
        while( $job = $jobs->fetch() ){
            $o .= $job_item->parse($job->toArray());
        }
        
        $psh['jobs'] = $o;
        
        //привязанные котрагенты
       
        $peoples = $this->loadModel('peopledev');
        $peoples->addRules(Array(
            'peopledev.deviceid:=' => $obj->id
        ));
        $peoples = $peoples->bindGraph();
        
        $people_item = $this->loadTemplate('device/peopleitem');
        
        $o = '';
        
        while( $people = $peoples->fetch() ){
            $o .= $people_item->parse($people->toArray());
        }
        
        $psh['peoples'] = $o;
        
        $template = $this->loadTemplate('device/editor');
        return $template->parse($psh);
    }
    
    public function drop(){
        
        $template = $this->loadTemplate('query');
        
        if( empty($_GET['id']) ) return $this->write();
        
        $obj = $this->loadModel('devices')->getObject($_GET['id']);
        
        if ($obj === false) return $this->write();
        
        if (!empty($_GET['confirm'])){
            $obj->remove();
            return $this->loadTemplate('success')->parse();
        }
        
        $psh['name'] = $obj->name . '('.$obj->id.')';
        $psh['drop_link'] = '/?c=device&a=drop&id='.$obj->id.'&confirm=1';
        
        return $template->parse($psh);
        
    }

}
