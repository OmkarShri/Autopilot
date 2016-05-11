<?php 
class Company_Web_Model_Api extends Mage_Core_Model_Abstract
{

protected function _construct()
{
	//$this->_init("web/api");
}

public function getmessage()
	{	
		
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');
		$request_url = trim(Mage::getStoreConfig('webtext/webtext_group/webtext_input',Mage::app()->getStore()));


		// curl request
		//$request_url = "https://api.mozenda.com/rest?WebServiceKey=61832716-3B49-48EF-9611-A712629EBED4&Service=Mozenda10&Operation=Agent.GetList";

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $request_url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 130);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$response = curl_exec($curl);
		curl_close($curl);    
		// curl request

		$xml = new SimpleXMLElement($response);
		
		if($xml){
			$agents = array();
			$agents = $xml->AgentList->Agent;
			if(count($agents) > 0){
				foreach ($agents as $agent){
				  $ag_name = $agent->Name;
			      $ag_id = $agent->AgentID;
			  	}
			}
		}
		
	}


}

?>