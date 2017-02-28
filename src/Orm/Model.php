<?php

namespace DSchoenbauer\Orm;

/**
 * Description of Model
 *
 * @author David Schoenbauer
 */
class Model {
    private $_id;
    private $_data;
    use  \Zend\EventManager\EventManagerAwareTrait;
    
    function accept(VisitorInterface $visitor){
        $visitor->visitModel($this);
        return $this;
    }
    
    
    function getId() {
        return $this->_id;
    }

    function getData() {
        return $this->_data;
    }

    function setId($id) {
        $this->_id = $id;
        return $this;
    }

    function setData($data) {
        $this->_data = $data;
        return $this;
    }


}
