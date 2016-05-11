<?php

class Company_Autoapi_Model_Autoapi extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('autoapi/autoapi');
    }
}