<?php
ini_set('max_execution_time', 3000);
ini_set('memory_limit','512M');

require_once("app/Mage.php");

Mage::app('default'); 

$pid = $_REQUEST['pid'];
$ruleid = $_REQUEST['rule'];


$_product = Mage::getModel('catalog/product')->load($pid);  
$attribute = $_product->getResource()->getAttribute('auto_ruleid');
if ($attribute)
{
    $attribute_value = $attribute->getFrontend()->getValue($_product);
    if($attribute_value == $ruleid){
    	 $_product->setAuto_rulename('None')->save();
         $_product->setAuto_ruleid(0)->save();
         $_product->setAutomode('Off')->save();
	}
}

if(!empty($_GET['pid'])){
	echo "1";
}
else{
	echo "0";	
}

?>