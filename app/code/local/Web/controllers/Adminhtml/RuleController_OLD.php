<?php

class Company_Web_Adminhtml_RuleController extends Mage_Adminhtml_Controller_action
{


	public function indexAction() {	
		$this->loadLayout()->renderLayout();
	}


	// public function addruleformAction() {	
	// 	$this->loadLayout()->renderLayout();
	// }


	// public function addruleAction() {	

	// 	Mage::getSingleton('core/session')->unsAutoMsg();

	// 	$resource = Mage::getSingleton('core/resource');
	// 	$readConnection = $resource->getConnection('core_read');
	// 	$writeConnection = $resource->getConnection('core_write');

	// 	if ($data = $this->getRequest()->getPost()) {

	// 		$rname = $this->getRequest()->getParam('rname');
	// 		$rdesc = $this->getRequest()->getParam('rdesc');
	// 		$rtype = $this->getRequest()->getParam('rtype');

	// 		$level = $this->getRequest()->getParam('level');
	// 		$topcomp = $this->getRequest()->getParam('topcomp');
	// 		$rate = $this->getRequest()->getParam('rate');
	// 		$mode = $this->getRequest()->getParam('mode');

	// 		$gmode = $this->getRequest()->getParam('gmode');
	// 		$gprice = $this->getRequest()->getParam('gprice');
	// 		$gpricemode = $this->getRequest()->getParam('gpricemode');

	// 		$costprice = $this->getRequest()->getParam('costprice');
	// 		$costpmode = $this->getRequest()->getParam('costpmode');

	// 		$query = "insert into auto_rulemanager values('','$rname','$rdesc','$rtype')";

	// 		if($writeConnection->query($query)){
	// 			$query = "select ruleid from auto_rulemanager order by ruleid DESC LIMIT 1";
	// 			if($res = $readConnection->fetchCol($query)){
	// 				if(empty($res)){
	// 					$ruleid = 1;
	// 				}
	// 				else{
	// 					$ruleid = $res[0];	
	// 				}
	// 			}	
	// 			$query = "";
	// 			if($rtype == 'competitor'){
	// 				$query = "insert into auto_rule_competitor values($ruleid,'$level',$topcomp,'$rate','$mode','$gmode','$gprice','$gpricemode')";
	// 			}	
	// 			else{
	// 				$query = "insert into auto_rule_cost values($ruleid,'$costprice','$costpmode')";
	// 			}
	// 			if($writeConnection->query($query)){
	// 				$automsg = "Successfully added rule";		
	// 			}				
	// 		}
	// 		else{
	// 			$automsg = "Unable to add try again";	
	// 		}
		
	// 	Mage::getSingleton('core/session')->setAutoMsg($automsg);
		
	// 	$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
	// 	}
	// }
	
}

?>


