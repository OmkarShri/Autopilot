<?php

class Company_Web_Adminhtml_WebController extends Mage_Adminhtml_Controller_action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('web/items')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function competitorAction() {
		$this->_initAction()
			->renderLayout();
	}


	public function compmanagerAction(){

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');
		
		$query = 'SELECT * FROM auto_competitor';
		$results = $readConnection->fetchAll($query);

		if ($data = $this->getRequest()->getPost()) {			
			$check = $this->getRequest()->getParam('status');
			
			foreach ($results as $row) {					
				if(in_array($row['id'], $check)){					
					$query = "UPDATE auto_competitor SET status = 'yes' WHERE id =".$row['id'];
				}
				else {
					$query = "UPDATE auto_competitor SET status = 'no' WHERE id =".$row['id'];
				}
				$writeConnection->query($query);
			}

		}

		$this->_redirect('*/*/');
	}


	public function addcomplinkAction(){
		$this->loadLayout()->renderLayout();
	}


	public function addcompetitorAction(){
		Mage::getSingleton('core/session')->unsAutoMsg();
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');
		
		if ($data = $this->getRequest()->getPost()) {
			$agentid = trim($this->getRequest()->getParam('agents'));
			$compname = trim($this->getRequest()->getParam('compname'));
			$status = trim($this->getRequest()->getParam('compstatus'));
			
			$query = "insert into auto_competitor values('','$agentid','$compname','','$status')";
			
			if($writeConnection->query($query)){
				$automsg = "successfully added competitor";
			}
			else{
				$automsg = "Unable to add try again";	
			}
			Mage::getSingleton('core/session')->setAutoMsg($automsg);
		}
		
		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
		//$this->_redirect('*/*/*/');
	}


	public function editcompetitorlinkAction(){

		Mage::getSingleton('core/session')->unsEditcompid();
		$editid = Mage::app()->getRequest()->getParam('id');
		Mage::getSingleton('core/session')->setEditcompid($editid);
		$this->loadLayout()->renderLayout();

	}

	public function editcompetitorAction(){

		Mage::getSingleton('core/session')->unsAutoMsg();
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');
		
		if ($data = $this->getRequest()->getPost()) {
			$compid = trim($this->getRequest()->getParam('compid'));
			$agentid = trim($this->getRequest()->getParam('agents'));
			$compname = trim($this->getRequest()->getParam('compname'));
			$status = trim($this->getRequest()->getParam('compstatus'));
			
			$query = "update auto_competitor set competitor='".$compname."',agent_id='".$agentid."', status='".$status."' where id=$compid";
			
			if($writeConnection->query($query)){
				$automsg = "successfully updated competitor";
			}
			else{
				$automsg = "Unable to update try again";	
			}
			Mage::getSingleton('core/session')->setAutoMsg($automsg);
		}
		
		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
		//$this->_redirect('*/*/*/');

	}

	public function delcompetitorAction(){
		$delid = Mage::app()->getRequest()->getParam('id');
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');

		$query = "delete from auto_competitor where id=$delid";
			
		$writeConnection->query($query);

		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));

	}


	public function runapiAction() {
		$this->loadLayout()->renderLayout();
	}

	public function runruleAction() {
		$this->loadLayout()->renderLayout();
	}


	public function authenticateAction(){
		$this->loadLayout()->renderLayout();	
	}

	public function authuserAction(){

		$pass = Mage::app()->getRequest()->getParam('auth');
		$pass = md5($pass);

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "select password from auto_auth where user='admin'";
		$res = $readConnection->fetchCol($query);

		$dbpass = $res[0];

		if($pass == $dbpass){
			Mage::getSingleton('core/session')->setAutoSucAuth("success");
			//$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
			$url =  Mage::helper("adminhtml")->getUrl("web/adminhtml_productlist"); 
			$this->_redirectUrl($url);
		}else{
			Mage::getSingleton('core/session')->setAutoErrAuth("Invalid Password");
			$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
		}

	}

	public function authlogoutAction(){
		Mage::getSingleton('core/session')->unsAutoSucAuth(); 
		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));		
	}

	public function authuserchangepassAction(){
		$pass = Mage::app()->getRequest()->getParam('changepwd');
		
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$query = "update auto_auth set password =MD5('".$pass."') where user='admin'";
		if($writeConnection->query($query)){
			Mage::getSingleton('core/session')->setAutoErrAuth("Password changed successfully");
		}
		$this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));
	}

	
}