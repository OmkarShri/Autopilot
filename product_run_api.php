<?php

require_once("app/Mage.php");
Mage::app('default'); 


// $collection = Mage::getModel('catalog/product')->getCollection();
// $collection->setOrder('entity_id', 'ASC');
// $collection->getSelect()->limit(10,0); // will bring back the first 20 products

// foreach ($collection as $product) {
// 	//echo $product->getId()."<br>";	
// }
?>
<div style="width:500px; margin:0 auto; text-align:center;margin-top:20%;" id="ajax-cont">
<p>Please wait while it is loading, don't refresh or close the page...</p>
<p><img src="<?php echo 'media/jqueryload.gif'; ?>" alt="" width="50px"/></p>
</div>



<script>

window.onload=function(){
runcode("0");
};

function runcode(nxt)
{
var next=nxt;
var xmlhttp;
if (window.XMLHttpRequest)
  {// code for IE7+, Firefox, Chrome, Opera, Safari
  xmlhttp=new XMLHttpRequest();
  }
else
  {// code for IE6, IE5
  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
xmlhttp.onreadystatechange=function()
  {
  if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
    	
    	if(xmlhttp.responseText == "1"){    		
    		//document.getElementById("myDiv").innerHTML=xmlhttp.responseText;
    		next = parseInt(next)+parseInt(10);
    		
    		runcode(next);
    	}
      if(xmlhttp.responseText == "0"){
        document.getElementById('ajax-cont').innerHTML="<p>Successfully Completed....</p>"
      }
    }
  }
xmlhttp.open("GET","autoapi.php?step="+next,true);
xmlhttp.send();
}
</script>
