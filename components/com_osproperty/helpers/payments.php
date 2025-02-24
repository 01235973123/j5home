<?php
/*------------------------------------------------------------------------
# payments.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
//Paypal payment gateways
use Joomla\CMS\Factory;

class Paypal{                    
   var $ipn_log;                    
   var $ipn_log_file;               
   var $ipn_response;                  
   var $ipn_data = array();         
   var $params = array();
   var $test=false;
   var $godaddy_hosting=false;  
   var $has_html=false;   
   function Paypal($config) { 
   	  global $mainframe,$configClass;
   	  $db = Factory::getDBO();
   	  $this->test = $configClass['general_paypal_testmode'];
   	  if($this->test==0)   	  	
      	$this->paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
      else 
      	$this->paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
      		      
      $this->ipn_log_file = JPATH_ROOT.DS.'components'.DS.'com_osproperty'.DS.'helpers'.DS.'ipn_log.txt';
      $this->ipn_log = true;
      $this->ipn_response = '';
      $this->add_field('rm','2');           // Return method = POST
      $this->add_field('cmd','_xclick'); 
   }
   function add_field($field, $value) {
      $this->params["$field"] = $value;
   }
   
   
   /**
    * Process payment with paypal payment gateway
    *
    * @param array $data posted data from the form
    * @param object $rowOrder store order information
    * @param object $rowItem store information of the order being processed
    */   
   function processPayment($order,$items,$agent,$itemid){   
   		global $root_link,$configs,$configClass;
   		$root_link = Uri::root();
   		$db = Factory::getDBO();
   		$paypal_id = $configClass['general_paypal_account'];
   		$curr	   = $configClass['general_currency_default'];
   		$db->setQuery("Select currency_code from #__osrs_currencies where id = '$curr'");
   		$curr = $db->loadResult();
		$this->add_field('business', $paypal_id);
		$this->add_field('return', $root_link."index.php?option=com_osproperty&task=payment_paypalreturn&order_id=$order->id&Itemid=$itemid");
		$this->add_field('cancel_return', $root_link."index.php?option=com_osproperty&task=payment_paypalcancel&order_id=$order->id&Itemid=$itemid");				
		$this->add_field('notify_url', $root_link."index.php?option=com_osproperty&task=payment_paypalnotify");		
		$this->add_field('item_name', Text::_('Upgrade Featured Properties').": ".$order->items);		
		$this->add_field('amount', "$order->total");
		$this->add_field('custom', "$order->id");	
		$this->add_field('currency_code',$curr);	
		//print_r($this);
		$this->submit_paypal_post();				
   }
         
   function submit_paypal_post() {
      echo "<center><h3>".Text::_('Please wait while redirecting to PayPal to process your payment...')."</h3></center>\n";
      echo "<form method=\"post\" name=\"formRegister\" action=\"".$this->paypal_url."\">\n";
      foreach ($this->params as $name => $value) {
         echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">";
      }
	?>
			<script type="text/javascript">
				function rg_direc_to_paypal(){
					document.formRegister.submit();
				}
				setTimeout("rg_direc_to_paypal()",5000);
			</script>
	<?php
      echo "</form>\n";
   }	
   
   
   /**
    * Process cancel payment
    *
    */
   
   function cancelPayment(){
   		global $root_link,$config,$database;
		$jinput = Factory::getApplication()->input;
   		$id = $jinput->getInt('order_id');
   		OspropertyPayment::cancelPayment($order_id);
   }
   
   /**
    * Display the page after users making payment from paypal
    *
    */
   
   function displayReturnPage($rowOrder){   
   		global $mosConfig_live_site,$Itemid;		
		OspropertyPayment::returnPayment($rowOrder->id);
   }
   
   /**
    * Process notification post from paypal
    *
    */
   function paypalNotify(){
   		//global $database, $config;   		
   		$db = Factory::getDBO();
   		/*
   		$config = new JConfig();
   		$mailfrom = $config->mailfrom;
   		$fromname = $config->fromname;
   		$mailer = Factory::getMailer();
   		$mailer->sendMail($mailfrom,$fromname,'dev@test.com','1','1');
   		*/
   		if($this->validate_ipn()){
   			$v = $db->insertID();
   			//$db->setQuery("Update #__cc_verify set verify = '1'");
   			//$db->query();
   			$orderId=$this->ipn_data["custom"];
   			$transactionId=$this->ipn_data["txn_id"];
   			$amount=$this->ipn_data["mc_gross"];
   			//Check some condition
   			$success=true;
   			if($amount<=0)
   				$success=false;
   			if($transactionId!=""){
   				//Check dulicate transaction
   				$sql="SELECT count(id) FROM #__osrs_orders WHERE transaction_id='$transactionId'";
   				$db->setQuery($sql);
   				$total=$db->loadResult();
   				if($total){
   					$success=false;
   				}   				
   			}
   			if($success || $this->test){
   				$sql="Update #__osrs_orders Set order_status='S',transaction_id = '$transactionId' where id=$orderId";
   				$db->setQuery($sql);
   				$db->query();
		    	OspropertyPayment::paymentComplete($orderId);
   			}
   		}				
   }
   
   function validate_ipn() {
   	  $err_num="";
   	  $err_str="";
      $url_parsed=parse_url($this->paypal_url);        
      $post_string = '';    
      foreach ($_POST as $field=>$value) { 
         $this->ipn_data["$field"] = $value;
         $post_string .= $field.'='.urlencode($value).'&'; 
      }
      $post_string.="cmd=_notify-validate";
      $fp = fsockopen($url_parsed[host],"80",$err_num,$err_str,30); 
      if(!$fp) {
         $this->log_ipn_results(false);       
         return false;
      } else {
         fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n"); 
         fputs($fp, "Host: $url_parsed[host]\r\n"); 
         fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n"); 
         fputs($fp, "Content-length: ".strlen($post_string)."\r\n"); 
         fputs($fp, "Connection: close\r\n\r\n"); 
         fputs($fp, $post_string . "\r\n\r\n"); 
         while(!feof($fp)) { 
            $this->ipn_response .= fgets($fp, 1024); 
         } 
         fclose($fp);
      }
     if (eregi("VERIFIED",$this->ipn_response)) {
         $this->log_ipn_results(true);
       	 return true;       
      } else {
         $this->log_ipn_results(false);   
         return true;
      }
   }
      
   function log_ipn_results($success) {
      if (!$this->ipn_log) return;
      $text = '['.date('m/d/Y g:i A').'] - '; 
      if ($success) $text .= "SUCCESS!\n";
      	else $text .= 'FAIL: '.$this->last_error."\n"; 
      $text .= "IPN POST Vars from Paypal:\n";
      foreach ($this->ipn_data as $key=>$value) {
         $text .= "$key=$value, ";
      }
      $text .= "\nIPN Response from Paypal Server:\n ".$this->ipn_response;
      $fp=fopen($this->ipn_log_file,'a');
      fwrite($fp, $text . "\n\n"); 
      fclose($fp);  // close file
   }
}  
?>