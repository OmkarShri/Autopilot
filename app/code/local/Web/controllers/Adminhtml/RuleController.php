<?php

class Company_Web_Adminhtml_RuleController extends Mage_Adminhtml_Controller_action
{

	public function indexAction() {	
		$this->loadLayout()->renderLayout();
	}



	public function addruleformAction() {	
		$this->loadLayout()->renderLayout();
	}


	public function addruleAction(){	

		Mage::getSingleton('core/session')->unsAutoMsg();

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');

		if ($data = $this->getRequest()->getPost()) {

			$rname = $this->getRequest()->getParam('rname');
			$rdesc = $this->getRequest()->getParam('rdesc');
			$rtype = $this->getRequest()->getParam('rtype');

			$level = $this->getRequest()->getParam('level');
			$topcomp = $this->getRequest()->getParam('topcomp');
			$rate = $this->getRequest()->getParam('rate');
			$mode = $this->getRequest()->getParam('mode');

			$gmode = $this->getRequest()->getParam('gmode');
			$gprice = $this->getRequest()->getParam('gprice');
			$gpricemode = $this->getRequest()->getParam('gpricemode');

			$costprice = $this->getRequest()->getParam('costprice');
			$costpmode = $this->getRequest()->getParam('costpmode');

			$query = "insert into auto_rulemanager values('','$rname','$rdesc','$rtype')";

			if($writeConnection->query($query)){
				$query = "select ruleid from auto_rulemanager order by ruleid DESC LIMIT 1";
				if($res = $readConnection->fetchCol($query)){
					if(empty($res)){
						$ruleid = 1;
					}
					else{
						$ruleid = $res[0];	
					}
				}	
				$query = "";
				if($rtype == 'competitor'){
					$query = "insert into auto_rule_competitor values($ruleid,'$level',$topcomp,'$rate','$mode','$gmode','$gprice','$gpricemode')";
				}	
				else{
					$query = "insert into auto_rule_cost values($ruleid,'$costprice','$costpmode')";
				}
				if($writeConnection->query($query)){
					$automsg = "Successfully added rule";		
				}				
			}
			else{
				$automsg = "Unable to add try again";	
			}
		
			Mage::getSingleton('core/session')->setAutoMsg($automsg);
		
		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
		}
	}


	public function editruleformAction(){
		$this->loadLayout()->renderLayout();
	}


	public function editruleAction(){

		Mage::getSingleton('core/session')->unsAutoMsg();

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');

		if ($data = $this->getRequest()->getPost()) {

			$editid = $this->getRequest()->getParam('editid'); 
			$rname = $this->getRequest()->getParam('rname');
			$rdesc = $this->getRequest()->getParam('rdesc');
			$rtype = $this->getRequest()->getParam('rtype');

			$level = $this->getRequest()->getParam('level');
			$topcomp = $this->getRequest()->getParam('topcomp');
			$rate = $this->getRequest()->getParam('rate');
			$mode = $this->getRequest()->getParam('mode');

			$gmode = $this->getRequest()->getParam('gmode');
			$gprice = $this->getRequest()->getParam('gprice');
			$gpricemode = $this->getRequest()->getParam('gpricemode');

			$costprice = $this->getRequest()->getParam('costprice');
			$costpmode = $this->getRequest()->getParam('costpmode');

			$query = "update auto_rulemanager set name='$rname', ruledesc='$rdesc', type='$rtype' where ruleid=$editid"; 
			
			if($writeConnection->query($query)){

				$query = "";
				if($rtype == 'competitor'){
					$query = "delete from auto_rule_competitor where ruleid=$editid";
					$writeConnection->query($query);
					$query = "insert into auto_rule_competitor values($editid,'$level',$topcomp,'$rate','$mode','$gmode','$gprice','$gpricemode')";							
					$writeConnection->query($query);
					$automsg = "Successfully updated rule";		
				}	
				else{		
					$query = "select ruleid from auto_rule_cost where ruleid=$editid";
					$res = $readConnection->fetchCol($query);
						if(empty($res)){
							$query = "insert into auto_rule_cost values($editid,'$costprice','$costpmode')";
							$writeConnection->query($query);
						}
						else{
							$query = "delete from auto_rule_cost where ruleid=$editid";
							$writeConnection->query($query);					
							$query = "insert into auto_rule_cost values($editid,'$costprice','$costpmode')";
							$writeConnection->query($query);	
						}					
						$automsg = "Successfully updated rule";		
				}				
			}
			else{
				$automsg = "Unable to update try again";	
			}
		
			Mage::getSingleton('core/session')->setAutoMsg($automsg);
		}
		
		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
		
	}


	public function delruleAction(){

		$this->loadLayout()->renderLayout();

		// $resource = Mage::getSingleton('core/resource');
		// $readConnection = $resource->getConnection('core_read');
		// $writeConnection = $resource->getConnection('core_write');

		// $id = $this->getRequest()->getParam('id');

		// $query = "delete from auto_rulemanager where ruleid=$id";
		// $writeConnection->query($query);

		// $query = "delete from auto_rule_competitor where ruleid=$id";
		// $writeConnection->query($query);

		// $query = "delete from auto_rule_cost where ruleid=$id";
		// $writeConnection->query($query);

		// $automsg = "Successfully deleted the rule";
		// Mage::getSingleton('core/session')->setAutoMsg($automsg);


		// // To set the rule to OFF for the products

		// $collection = Mage::getModel('catalog/product')->getCollection();
		// foreach ($collection as $product) {
		// 	$_product = Mage::getModel('catalog/product')->load($product->getId());  
		// 	$attribute = $_product->getResource()->getAttribute('auto_ruleid');
		// 	if ($attribute)
		// 	{
		// 	    $attribute_value = $attribute->getFrontend()->getValue($_product);
		// 	    if($attribute_value == $id){
		// 	    	 $_product->setAuto_rulename('None')->save();
		// 	         $_product->setAuto_ruleid(0)->save();
		// 	         $_product->setAutomode('Off')->save();
		// 		}
		// 	}
		// }	

		// // To set the rule to OFF for the products ends here

		// $this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));

	}



}





