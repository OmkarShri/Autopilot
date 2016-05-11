<?php
require_once("app/Mage.php");
Mage::app('default'); 

$pid = $_REQUEST['pid'];
$ruleid =$_REQUEST['rule'];

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

// function ends here

//$pids = $this->getRequest()->getParam('pid');       

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

//foreach ($pids as $pid) {
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
//}

}

// cost based type ends here...........

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

//foreach ($pids as $pid) {

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

   //}

}

}

echo "1";


?>