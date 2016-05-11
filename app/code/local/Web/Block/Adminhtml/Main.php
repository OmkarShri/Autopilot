<?php
class Company_Web_Block_Adminhtml_Main extends Mage_Adminhtml_Block_Widget_Grid_Container
{
   public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        // ie. foo_bar/adminhtml_baz
        $this->_blockGroup = 'company_web';
        $this->_controller = 'adminhtml_main';
        $this->_headerText = Mage::helper('web')->__('Main');
         
        parent::__construct();
    }
}