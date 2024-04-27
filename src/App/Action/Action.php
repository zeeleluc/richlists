<?php
namespace App\Action;

use App\Object\BaseObject;

class Action extends BaseObject
{
    private $_action;

    private $_result = array();

    public function setAction($action)
    {
        $this->_action = $action;
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function setResult($result)
    {
        $this->_result = $result;
    }

    public function getResult()
    {
        return $this->_result;
    }
}
