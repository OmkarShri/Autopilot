<?php

class Company_Web_Adminhtml_ProductlistController extends Mage_Adminhtml_Controller_action
{

	public function indexAction(){
		$this->loadLayout()->renderLayout();
	}

	 public function massStatusAction()
    {


         // function starts here

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

        $pids = $this->getRequest()->getParam('pid');       
        $ruleid = $this->getRequest()->getParam('rule');
        $competitor_count=0;

        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query="select count(status) as comp from auto_competitor where status='yes'";
        $res = $readConnection->fetchAll($query);


        foreach ($res as $row) {
            $competitor_count = $row['comp'];
        }
       

        $query="select type from auto_rulemanager where ruleid=".$ruleid;
        $res = $readConnection->fetchCol($query);        
       
        if($res){

            $rule_type=$res[0];
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

                foreach ($pids as $pid) {
                    $product = Mage::getModel('catalog/product')->load($pid);                    
                    $p_price = $product->getCost();                                      
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
                    }   
                }

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

                foreach ($pids as $pid) {

                        $product = Mage::getModel('catalog/product')->load($pid);
                            $p_price = $product->getPrice();
                            $p_cost = $product->getCost();   
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
                                //$topcomps_array[] = Mage::getSingleton('web/web')->findClosest($p_price, $competitorval);
                                $topcomps_array[] = find_closest ($p_price, $competitorval);
                                $competitorval = array_diff($competitorval, $topcomps_array);
                            }


                             $compkey = (int)($topcomp)-1;  
                             $compkey = (int)$compkey;
                             $competitor_price = $topcomps_array[$compkey];



                            if($level == "E"){ 

                                 if( isset($competitor_price) &&  $competitor_price > $p_cost ){
                                    $price = $competitor_price;
                                    $product->setPrice($price)->save();
                                 }
                                 else{
                                        if( $gmode == "disable" ){
                                            $product->setStatus(2)->save();      
                                        }
                                         if( $gmode == "setprice" ){
                                            if($gratemode == "fixed"){
                                                $price = $p_cost+int($grate);
                                                $product->setPrice($price)->save();
                                            }  
                                            if($gratemode == "percent"){
                                                $price = ($p_cost*int($grate))/100;
                                                $price = $p_cost+$price;
                                                $product->setPrice($price)->save();
                                            }    
                                        }
                                 }

                            }
                            else{
                           
                                if($mode == "percent"){
                                     
                                     if(isset($competitor_price)){


                                        $val = ($competitor_price*$rate)/100;                                                  
                                        if($level == "A"){
                                           $price = $competitor_price+$val;

                                        }
                                        if($level == "B"){
                                            $price = $competitor_price-$val;
                                        }

                                        if($price > $p_cost){                                            
                                            $product->setPrice($price)->save();
                                        }

                                         else{
                                                if( $gmode == "disable" ){
                                                    $product->setStatus(2)->save();      
                                                }
                                                 if( $gmode == "setprice" ){
                                                    if($gratemode == "fixed"){
                                                        $price = $p_cost+int($grate);
                                                        $product->setPrice($price)->save();
                                                    }  
                                                    if($gratemode == "percent"){
                                                        $price = ($p_cost*int($grate))/100;
                                                        $price = $p_cost+$price;
                                                        $product->setPrice($price)->save();
                                                    }    
                                                }
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
                                        }
                                        else{
                                                if( $gmode == "disable" ){
                                                    $product->setStatus(2)->save();      
                                                }
                                                 if( $gmode == "setprice" ){
                                                    if($gratemode == "fixed"){
                                                        $price = $p_cost+int($grate);
                                                        $product->setPrice($price)->save();
                                                    }  
                                                    if($gratemode == "percent"){
                                                        $price = ($p_cost*int($grate))/100;
                                                        $price = $p_cost+$price;
                                                        $product->setPrice($price)->save();
                                                    }    
                                                }
                                        }
                                    }  

                               }


                        }


                        }

                    }

                } 

                $this->_redirectReferer($this->getRequest()->getServer('HTTP_REFERER'));

        } // mass function ends here

} // class ends here

                
      