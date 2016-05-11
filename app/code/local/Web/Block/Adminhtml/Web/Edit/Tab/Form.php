<?php

class Company_Web_Block_Adminhtml_Web_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('web_form', array('legend'=>Mage::helper('web')->__('Competitor information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('web')->__('Competitor'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

   //    $fieldset->addField('filename', 'file', array(
   //        'label'     => Mage::helper('web')->__('File'),
   //        'required'  => false,
   //        'name'      => 'filename',
	  // ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('web')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('web')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('web')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'text', array(
          'name'      => 'content',
          'label'     => Mage::helper('web')->__('Products'),
          'title'     => Mage::helper('web')->__('Products'),              
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getWebData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getWebData());
          Mage::getSingleton('adminhtml/session')->setWebData(null);
      } elseif ( Mage::registry('web_data') ) {
          $form->setValues(Mage::registry('web_data')->getData());
      }
      return parent::_prepareForm();
  }
}