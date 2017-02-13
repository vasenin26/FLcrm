<?php

class People extends Controller {

    public function write() {

        $peoples = $this->loadModel('customers');
		
		if(isset($_REQUEST['s'])){
			$peoples->filter($_REQUEST['s']);
		}
        
        $peoples = $peoples->getCollection();
        $item = $this->loadTemplate('people/item');
        
        $o = '';
        
        while($obj = $peoples->fetch()){
            $item->setPlaceholders($obj->toArray());
            $o .= $item->parse();
        }

        $template = $this->loadTemplate('people/collection');
        //$template->setPlaceholders();
        
        return $template->parse(Array(
            'items' => $o
        ));
        
    }

    public function update() {

        $peoples = $this->loadModel('customers');
        if (isset($_REQUEST['id'])) {
            $obj = $peoples->getObject($_REQUEST['id']);
        }
        if (empty($obj))
            $obj = $peoples->newObject();

        if (isset($_REQUEST['save'])) {
            $obj->fromArray($_REQUEST);
            $obj->save();
            
            $this->redirect(Array(
               'id' => $obj->id
            ));
        }
        
        $psh = $obj->getValues();
        
        //привязанные устройства
        
        $template = $this->loadTemplate('people/editor');
        $template->setPlaceholders($psh);
        
        return $template->parse();
    }
    
    public function drop(){
        
        $template = $this->loadTemplate('query');
        
        if( empty($_GET['id']) ) return $this->write();
        
        $obj = $this->loadModel('customers')->getObject($_GET['id']);
        
        if ($obj === false) return $this->write();
        
        if (!empty($_GET['confirm'])){
            $obj->remove();
            return $this->loadTemplate('success')->parse();
        }
        
        $psh['name'] = $obj->name . ' ('.$obj->org.')';
        $psh['drop_link'] = '/?c=people&a=drop&id='.$obj->id.'&confirm=1';
        
        return $template->parse($psh);
        
    }
    
    public function link(){
        
        $peopleid = $this->getOption('peopleid', $_REQUEST);
        $deviceid = $this->getOption('deviceid', $_REQUEST);
        
        if ($peopleid && $deviceid){
            
            $connect = $this->loadModel('peopledev');
            $connect->link($peopleid, $deviceid);
                
            $this->redirect(Array(
                'id' => $deviceid
            ), 'device', 'update' );
            
        }
        
        $template = $this->loadTemplate('people/collection');
        
        $peoples = $this->loadModel('peoples');
		
		if(isset($_REQUEST['s'])){
			$peoples->filter($_REQUEST['s']);
		}
        
        $peoples = $peoples->getCollection();
        $item = $this->loadTemplate('people/connect_item');
        
        $o = '';
        
        while($obj = $peoples->fetch()){
            $item->setPlaceholders($obj->toArray());
            $o .= $item->parse();
        }
        
        return $template->parse(Array(
            'items' => $o
        ));
        
    }
    
    public function unlink(){
        
        $template = $this->loadTemplate('query');
        
        $id = $this->getOption('linkid', $_REQUEST);
        
        if( empty($id) ) return $this->write();
        
        $obj = $this->loadModel('peopledev')->getObject($id);
        
        if ($obj === false) return $this->write();
        
        if (!empty($_GET['confirm'])){
            
            $deviceid = $obj->deviceid;
            
            $obj->remove();
                
            $this->redirect(Array(
                'id' => $deviceid
            ), 'device', 'update' );
        }
        
        $psh['name'] = $obj->id;
        $psh['drop_link'] = '/?c=people&a=unlink&linkid='.$obj->id.'&confirm=1';
        
        return $template->parse($psh);
        
    }

}
