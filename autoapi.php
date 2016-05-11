<?php



// if($step < 60){
// 	echo "1";
// }
// else{
// 	echo "0";
// }
// exit;


//set_time_limit(0);
ini_set('max_execution_time', 3000);
ini_set('memory_limit','512M');

require_once("app/Mage.php");

Mage::app('default'); 


// ============= Ruleupdate code starts here ===================

function find_closest( $needle, $haystack )
{
    //sort the haystack
    sort($haystack);                
    //get the size to be used later
    $haystack_size = count($haystack);                
    //pre-check, is the needle less than the lowest array value
    if ( $needle < $haystack[0] )
    {
        return $haystack[0];
        
    }                
    //loop through the haystack
    foreach ( $haystack AS $key => $val )
    {
        //if we have a match with the current value, return it
        if ( $needle == $val )
        {
            return $val;
           
        }                    
        //if we've hit the end of the array, return the max value
        if ( $key == $haystack_size - 1 )
        {
            return $val;
            
        }                    
        //now do the "between" check
        if ( $needle > $val && $needle < $haystack[$key+1] )
        {
            //find the closest.  If they're equidistant, the higher value gets precedence
            if ( $needle - $val < $haystack[$key+1] - $needle )
            {
                return $val;
               
            }
            else 
            {
                return $haystack[$key+1];
                
            }
        }
    }
}

function updaterule( $pid,$ruleid ){

$competitor_count=0;

$resource = Mage::getSingleton('core/resource');
$readConnection = $resource->getConnection('core_read');

$query="select count(status) as comp from auto_competitor where status='yes'";
$res = $readConnection->fetchAll($query);

foreach ($res as $row) {
$competitor_count = $row['comp'];
}

$query="select * from auto_rulemanager where ruleid=".$ruleid;
$res = $readConnection->fetchAll($query);        

if($res){
foreach ($res as $row) {
$rule_name = $row['name']; 
$rule_type = $row['type'];                 
}
//$rule_type=$res[0];
$price = 0;

// cost based type starts here.....................

if($rule_type == "cost"){
	$query="select * from auto_rule_cost where ruleid=".$ruleid;
	$res = $readConnection->fetchAll($query);       

	foreach ($res as $row) {                    
	$rate = $row['rate'];
	$mod = $row['mode'];    
	}

	if(is_numeric($rate)){
	$rate = $rate;
	}

	else {
	$rate = 0;
	}


	$product = Mage::getModel('catalog/product')->load($pid);                    
	$p_price = $product->getCost();     
	$product->setAuto_rulename($rule_name)->save();  
	$product->setAuto_ruleid($ruleid)->save();   
	$product->setAutomode('On')->save(); 

	$price = 0;

	if(isset($p_price)){
	    if($mod == "percent"){
	         $val = ($p_price*$rate)/100;                                                  
	         $price = $val+$p_price;
	    }
	    if($mod == "fixed"){
	        $price = $p_price+$rate;                  
	    }
	    $product->setPrice($price)->save();
	    $product->setStatus(1)->save(); 
	}  
}

// cost based type ends here...........


// Competitor based price starts here 

if($rule_type == "competitor"){

$query="select * from auto_rule_competitor where ruleid=".$ruleid;
$res = $readConnection->fetchAll($query);   

foreach ($res as $row) {

$level = $row['level'];
$topcomp = $row['topcomp'];
$rate = $row['rate'];
$mode = $row['mode'];

$gmode = $row['gmode'];
$grate = $row['grate'];
$gratemode = $row['gcostmode'];
}

    $product = Mage::getModel('catalog/product')->load($pid);
        $p_price = $product->getPrice();
        $p_cost = $product->getCost(); 
        $product->setAuto_rulename($rule_name)->save();  
        $product->setAuto_ruleid($ruleid)->save();          
        $competitorval = array();

        for($i=1; $i<=$competitor_count; $i++){

            $comp = "comp_".$i;
            $attribute = $product->getResource()->getAttribute($comp);
            if ($attribute)
            {                                                                        
                $competitorval[] = $attribute ->getFrontend()->getValue($product);
            }

        }
        
        $topcomps_array = array();  
        $count = count($competitorval);

        for($i=0; $i<$count; $i++){                                
            $topcomps_array[] = find_closest ($p_price, $competitorval);                                
        }

        $compkey = (int)($topcomp)-1;  
        $compkey = (int)$compkey;
        $competitor_price = $topcomps_array[$compkey];

        // to store competitor id for product
        $key = array_search($competitor_price, $competitorval);
        $query = "select id from auto_competitor where status='yes' order by id";
        $res = $readConnection->fetchCol($query);
        $product_compid = $res[$key];
        $product->setCompetitor($product_compid)->save();
         
        if($competitor_price > 0 ){

        if($level == "E"){ 

            if( $competitor_price > 0 &&  $competitor_price > $p_cost ){
                $price = $competitor_price;                                    
                $product->setPrice($price)->save();
                $product->setStatus(1)->save(); 
            }
            else{
                    if( $gmode == "disable" ){
                        $product->setStatus(2)->save();      
                    }
                     if( $gmode == "setprice" ){
                        if($gratemode == "fixed"){
                            $price = $p_cost+$grate;
                            $product->setPrice($price)->save();
                            $product->setStatus(1)->save(); 
                        }  
                        if($gratemode == "percent"){
                            $price = ($p_cost*$grate)/100;
                            $price = $p_cost+$price;
                            $product->setPrice($price)->save();
                            $product->setStatus(1)->save(); 
                        }    
                    }
             }
        }
        else {
       
            if($mode == "percent"){
                 
                 if($competitor_price > 0){

                    $val = ($competitor_price*$rate)/100; 

                    if($level == "A"){
                       $price = $competitor_price+$val;
                    }

                    if($level == "B"){
                        $price = $competitor_price-$val;
                    }

                    if($price > $p_cost){                                            
                        $product->setPrice($price)->save();
                         $product->setStatus(1)->save(); 
                         $product->setAutomode('On')->save();
                    }

                     else{
                            if( $gmode == "disable" ){
                                $product->setStatus(2)->save();      
                            }
                             if( $gmode == "setprice" ){
                                if($gratemode == "fixed"){
                                    $price = $p_cost+$grate;
                                    $product->setPrice($price)->save();
                                    $product->setStatus(1)->save(); 
                                }  
                                if($gratemode == "percent"){
                                    $price = ($p_cost*$grate)/100;
                                    $price = $p_cost+$price;
                                    $product->setPrice($price)->save();
                                    $product->setStatus(1)->save(); 
                                }    
                            }
                            $product->setAutomode('Guardian Mode')->save();
                        }

                 }

            }


           if($mode == "fixed"){

                if($competitor_price != ""){
                    if($level == "A"){
                            $price = $competitor_price+$rate;
                    }
                    if($level == "B"){
                        $price = $competitor_price-$rate;
                    }
                    if($price > $p_cost){
                        $product->setPrice($price)->save();
                        $product->setStatus(1)->save();
                        $product->setAutomode('On')->save(); 
                    }
                    else{
                            if( $gmode == "disable" ){
                                $product->setStatus(2)->save();      
                            }
                             if( $gmode == "setprice" ){
                                if($gratemode == "fixed"){
                                    $price = $p_cost+$grate;
                                    $product->setPrice($price)->save();
                                    $product->setStatus(1)->save(); 
                                }  
                                if($gratemode == "percent"){
                                    $price = ($p_cost*$grate)/100;
                                    $price = $p_cost+$price;
                                    $product->setPrice($price)->save();
                                    $product->setStatus(1)->save(); 
                                }    
                            }
                             $product->setAutomode('Guardian Mode')->save(); 
                    }
                }  

           }

        }

      }

      else{                           
        $product->setAuto_rulename($rule_name)->save();
        $product->setAuto_ruleid($ruleid)->save();
        $product->setAutomode('Off')->save();
      }  

}

}



} // updaterule() ends here


// ============= Ruleupdate code starts here ===================




function string_compare($str_a, $str_b)
{
    $length = strlen($str_a);
    $length_b = strlen($str_b);
 
    $i = 0;
    $segmentcount = 0;
    $segmentsinfo = array();
    $segment = '';
    while ($i < $length)
    {
        $char = substr($str_a, $i, 1);
        if (strpos($str_b, $char) !== FALSE)
        {
            $segment = $segment.$char;
            if (strpos($str_b, $segment) !== FALSE)
            {
                $segmentpos_a = $i - strlen($segment) + 1;
                $segmentpos_b = strpos($str_b, $segment);
                $positiondiff = abs($segmentpos_a - $segmentpos_b);
                $posfactor = ($length - $positiondiff) / $length_b; // <-- ?
                $lengthfactor = strlen($segment)/$length;
                $segmentsinfo[$segmentcount] = array( 'segment' => $segment, 'score' => ($posfactor * $lengthfactor));
            }
            else
            {
                 $segment = '';
                 $i--;
                 $segmentcount++;
             }
         }
         else
         {
             $segment = '';
            $segmentcount++;
         }
         $i++;
     }
 
     // PHP 5.3 lambda in array_map
     $totalscore = array_sum(array_map(function($v) { return $v['score'];  }, $segmentsinfo));
     return $totalscore;
}

		$apikey = trim(Mage::getStoreConfig('webtext/webtext_group/webtext_input',Mage::app()->getStore()));

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$writeConnection = $resource->getConnection('core_write');

		$query = "select * from auto_competitor where status='yes' order by id";
		$res = $readConnection->fetchAll($query); // query to get competitors which are enabled

		$prodcut_arr = array();

		
		//$products = Mage::getModel('catalog/product')->getCollection();

		$step = $_REQUEST['step'];
		$step = (int)$step;

		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection->setOrder('entity_id', 'ASC');
		$collection->getSelect()->limit(10,$step);

		$t=0;
        

		// To make all the product competitors attribute value to 0 before updating them
		foreach ($collection as $product) {

			$id = $product->getId();						
			$product = Mage::getModel('catalog/product')->load($id);
			$product->setCompetitor('0')->save();
			$prodcut_arr[$id] = $product->getSku();
			for($k=1; $k<=count($res); $k++){
				$set = "setComp_".$k; 
				$product->$set(0)->save();				
			}			
		$t++;
		}

		// Updating the product competitors atrribute value starts here
		$i=1;
		foreach ($res as $row) {
		$comp_id = $row['id'];		

		$agents = explode(":",$row['agent_id']);
		$agent_arr = array();

		foreach ($agents as $agentid) {					
							
		$request_url = "https://api.mozenda.com/rest?WebServiceKey=".$apikey."&Service=Mozenda10&Operation=View.GetItems&ViewID=".$agentid;

        //Mage::log($request_url, null, 'autopilotlogs.log');	

        $myFile = "autopilotlog.txt";

        $fh = fopen($myFile, 'a') or die("can't open file");
        $curdate = date("F j, Y, H:i:s"); 
        $stringData = $curdate." ".$request_url."\n";
        fwrite($fh, $stringData);        
        fclose($fh);


		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $request_url);
		curl_setopt($curl, CURLOPT_TIMEOUT, 130);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($curl);
		curl_close($curl);		
		$xml = new SimpleXMLElement($response);
		if($xml){
		$model_numbers = $xml->ItemList->Item;
		$update_arr =array();
		$set = "setComp_".$i;
		foreach ($prodcut_arr as $pid => $psku) {
			$product = "";
				
			foreach ($model_numbers as $model) {
				if($model->Model){ 
					if($model->Model == $psku){ 
							$patten = "/$psku/i";		
							if(preg_match($patten, $model->Model, $matches, PREG_OFFSET_CAPTURE)){
								$comp_price = $model->Price;
								$update_arr[$pid]= $comp_price;
								
							}							
						 }
				}
				elseif ($model->Modelo) {
					if($model->Modelo == $psku){ 
						$patten = "/$psku/i";		
						if(preg_match($patten, $model->Modelo, $matches, PREG_OFFSET_CAPTURE)){
							$comp_price = $model->Price;
							$update_arr[$pid]= $comp_price;
							
						}						
					}
				}
				else{
					$patten = "/$psku/i";		
					if(preg_match($patten, $model->Product, $matches, PREG_OFFSET_CAPTURE)){
						$comp_price = $model->Price;
						$update_arr[$pid]= $comp_price;
						
					}
				}		
			}
			
		}
		
		if(!empty($update_arr)){		
		foreach($update_arr as $key => $val){			
			 $product = Mage::getModel('catalog/product')->load($key);
			 $product->$set($val)->save();
			 $product->setCompetitor($comp_id)->save();
		}

		}

		}
		
		
		}

		$i++;
		
		}


// Ruleupdate() is function called for each product. ( Note: Ruleupdate() is called here after api prices are updated )  =============
foreach ($collection as $product) {

			$id = $product->getId();						
			$product = Mage::getModel('catalog/product')->load($id);

			$attribute = $product->getResource()->getAttribute('auto_ruleid');
			$rid= $attribute ->getFrontend()->getValue($product);
				
			if($rid !=0 && $rid != ""){
				updaterule($id,$rid);
			}

}
// Ruleupdate() ends here =================================== 


		if( $t == 10 ){
			echo "1";
		}
		else{
			echo "0";
		}

?>