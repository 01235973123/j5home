<?php

/*------------------------------------------------------------------------
# payment.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class OspropertyPayment{
	/**
	 * Payment process
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$id = $jinput->getInt('id',0);
		$print = $jinput->getInt('print',0);
		$itemid = $jinput->getInt('Itemid');
		$order_id = $jinput->getInt('order_id',0);
		switch ($task){
			case "payment_process":
				OspropertyPayment::payment_process($order_id,$itemid);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "payment_orderdetails":
				OspropertyPayment::orderDetails($option,$id,$print);
			break;
			case "payment_cancel":
				OspropertyPayment::cancelPayment($order_id);
			break;
			case "payment_complete":
				OspropertyPayment::paymentComplete($order_id);
			break;
			case "payment_confirm":
				OspropertyPayment::paymentNotify();
			break;
			case "payment_return":
				OspropertyPayment::returnPayment($order_id);
			break;
			//case "ordersHistory":
				//OspropertyPayment::ordersHistory($orders);
			//break;
            case "payment_failure":
                OspropertyPayment::paymentFailure();
                break;
		}
	}
	
	/**
	 * Payment Process
	 *
	 * @param unknown_type $option
	 */
	static function payment_process($id,$itemid)
	{
		global $jinput, $mainframe,$configClass;
		$db									= Factory::getDBO();
		$user								= Factory::getUser();
		$db->setQuery("Select * from #__osrs_orders where id = '$id'");
		$order								= $db->loadObject();
        if(HelperOspropertyCommon::isRegisteredAgent()) 
		{
            $agent_id						= HelperOspropertyCommon::getAgentID();
        }
		elseif(HelperOspropertyCommon::isCompanyAdmin())
		{
            $agent_id						= HelperOspropertyCommon::getCompanyId();
        }
        if($agent_id != $order->agent_id){
            //JError::raiseError( 500, Text::_('OS_YOU_HAVE_NOT_GOT_PERMISSION_GO_TO_THIS_AREA') );
			throw new Exception(Text::_('OS_YOU_HAVE_NOT_GOT_PERMISSION_GO_TO_THIS_AREA'), 500);
        }

		$db->setQuery("Select a.*,b.pro_name from #__osrs_order_details as a inner join #__osrs_properties as b on b.id = a.pid  where a.order_id = '$id'");
		$items = $db->loadObjectList();
		if(count($items) > 0){
			$itemArr = array();
			for($i=0;$i<count($items);$i++){
				$item = $items[$i];		
				$itemArr[]					= $item->pro_name;
			}
			$order->items = implode(",",$itemArr);
		}

		$data                               = array();
		$direction							= $order->direction;
		switch($direction)
		{
			case "1":
				$data['item_name']			= Text::_('OS_PAYMENT_FOR_NEW_PROPERTY')." -".$order->items."-";
			break;
			case "2":
				$data['item_name']			= Text::_('OS_PAYMENT_FOR_FEATURED_UPGRADE')." -".$order->items."-";
			break;
			case "3":
				$data['item_name']			= Text::_('OS_PAYMENT_FOR_PROPERTY_LIVETIME_EXTEND')." -".$order->items."-";
			break;
		}
		
		if($order->created_by == 0){ //is Agent
			$db->setQuery("Select * from #__osrs_agents where user_id ='$user->id'");
			$agent							= $db->loadObject();
		}else{ //is Company
			$db->setQuery("Select * from #__osrs_companies where user_id ='$user->id'");
			$agent = $db->loadObject();
			$agent->name					= $agent->company_name;
		}

        $data['payment_method'] 			= $order->payment_method;
        $data['x_card_num'] 				= base64_decode($order->x_card_num);
        $data['x_card_code'] 				= $order->x_card_code;
        // Guess card type based on card number
        if (!empty($data['x_card_num']) && empty($data['card_type']))
        {
            $data['card_type']              = OSPHelperCreditcard::getCardType($data['x_card_num']);
        }
        $data['card_holder_name'] 			= $order->card_holder_name;
        $data['exp_year'] 					= $order->exp_year;
        $data['exp_month'] 					= $order->exp_month;
        $data['card_type'] 					= $order->card_type;
        $data['address'] 					= $agent->address;
        $data['city'] 						= HelperOspropertyCommon::loadCityName($agent->city);
        $data['state'] 						= OSPHelper::loadSateName($agent->state);
		$data['zip']						= '';
		$data['stripeToken']                = $order->stripe_token;
		$data['nonce']                      = $order->nonce;
        $order_country						= $agent->country;
        if(intval($order_country) == 0){
            $order_country                  = HelperOspropertyCommon::getDefaultCountry();
        }
		if(intval($order_country) == 0){
            $order_country					= "US";
        }else{
            $db->setQuery("Select country_code from #__osrs_countries where id = '$order_country'");
            $order_country					= $db->loadResult();
        }
		$data['country']					= $order_country;
        $data['phone']						= $agent->phone;
        $order_name 						= $agent->name;
        $order_name_array					= explode(" ",$order_name);

		if(count($order_name_array) > 1)
		{
            $data['first_name']             = $order_name_array[0];
            $second_name                    = "";
            for($i=1;$i<count($order_name_array);$i++){
                $second_name			   .= $order_name_array[$i]." ";
            }
            $second_name = trim($second_name);
            $data['second_name']			= $second_name;
        }
		else
		{
            $data['first_name']             = $order_name;
            $data['second_name']            = "";
        }
        $data['amount']                     = $order->total;
        $data['gateway_amount']             = $order->total;
		$data['email']						= $agent->email;
        $db->setQuery("Select currency_code from #__osrs_currencies where id = '$order->curr'");
        $currency_code                      = $db->loadResult();
        $data['currency']					= $currency_code;
        if($configClass['active_payment'] == 1)
        {
            if ($order->payment_method == "")
            {
                //JError::raiseError(500, Text::_('OS_ERROR_IN_PAYMENT_PROCESS'));
				throw new Exception(Text::_('OS_ERROR_IN_PAYMENT_PROCESS'), 500);
            }
            else
            {
                require_once JPATH_COMPONENT . '/plugins/' . $order->payment_method . '.php';
                require_once JPATH_COMPONENT_ADMINISTRATOR.'/tables/order.php';
                $sql = 'SELECT params FROM #__osrs_plugins WHERE name="' . $order->payment_method . '"';
                $order_payment = $order->payment_method;
                $db->setQuery($sql);
                $plugin						= $db->loadObject();
                $params						= $plugin->params;
                $params						= new Registry($params);
                $paymentClass				= new $order_payment($params);
                $row                        = Table::getInstance('Order','OspropertyTable');
                $row->load((int)$order->id);
                $paymentClass->processPayment($row, $data);
            }
        }
	}
	
	
	/**
	 * Cancel Payment
	 *
	 * @param unknown_type $order_id
	 */
	static function cancelPayment($order_id){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_orders where id = '$order_id'");
		$order = $db->loadObject();
		$direction = $order->direction;

		$msg = array();
		$db->setQuery("Update #__osrs_orders set payment_made = '0',order_status = 'C' where id = '$row->id'");
		$db->execute();
		$msg[] = Text::_('OS_ORDER_HAS_BEEN_CANCELLED');
		if($direction == 0){
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '$row->id'");
			$pid = $db->loadResult();
			$order_id = base64_encode($row->id);
			$url = Route::_(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&new=1&&id=$pid&order_id=$order_id&Itemid=".$jinput->getInt('Itemid',0), false, false);
			OSPHelper::redirect($url);	
		}elseif($direction ==1){
			$needs = array();
			$needs[] = "aeditdetails";
			$needs[] = "agent_default";
			$needs[] = "agent_editprofile";
			$itemid = OSPRoute::getItemid($needs);
			if(count($msg) > 0){
				for($i=0;$i<count($msg);$i++){
					$msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
				}
				$msg = implode("<Br />",$msg);
			}
			$url = Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid);
			OSPHelper::redirect($url,$msg);
		}elseif($direction ==2){
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '$row->id'");
			$pid = $db->loadResult();
			$order_id = base64_encode($row->id);
			$url = Route::_(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&new=0&id=$pid&order_id=$order_id&Itemid=".$jinput->getInt('Itemid',0), false, false);
			OSPHelper::redirect($url);	
		}
	}
	
	/**
    * Process notification post from paypal
    *
    */
    static function paymentNotify(){
   		global $jinput, $mainframe,$configClass;
		$paymentMethod = $jinput->getString('payment_method', '');
		$method = os_payments::getPaymentMethod($paymentMethod) ;
		$method->verifyPayment();
    }
	
	/**
	 * Payment complete
	 *
	 * @param unknown_type $orderId
	 */
	static function paymentComplete($order_id,$direct_access=1){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$configClass = OSPHelper::loadConfig();
        $user = Factory::getUser();
		$db->setQuery("Select * from #__osrs_orders where id = '$order_id'");
		$order = $db->loadObject();
		if($direct_access == 1){
			if(HelperOspropertyCommon::isAgent()) {
				$agent_id = HelperOspropertyCommon::getAgentID();
			}elseif(HelperOspropertyCommon::isCompanyAdmin()){
				$agent_id = HelperOspropertyCommon::getCompanyId();
			}
			if($agent_id != $order->agent_id){
				//JError::raiseError( 500, Text::_('OS_YOU_HAVE_NOT_GOT_PERMISSION_GO_TO_THIS_AREA') );
				throw new Exception(Text::_('OS_YOU_HAVE_NOT_GOT_PERMISSION_GO_TO_THIS_AREA'), 500);
			}
		}

		//upgrade feature for properties
		$db->setQuery("Select * from #__osrs_order_details where order_id = '$order_id'");
		$items              = $db->loadObjectList();
		
		$cid = array();
		for($i=0;$i<count($items);$i++){
			$cid[]          = $items[$i]->pid;
		}
        if($order->direction == 1){ //new property
            $orderdetails   = $items[0];
            $pid            = $orderdetails->pid;
            $isFeatured     = $orderdetails->type;
            if($isFeatured == 1){
                HelperOspropertyCommon::setApproval("f",$pid);
                HelperOspropertyCommon::setExpiredTime($pid,"f",1);
            }else{
                HelperOspropertyCommon::setApproval("n",$pid);
                HelperOspropertyCommon::setExpiredTime($pid,"n",1);
            }
            //send Email to admin
            OspropertyEmail::sendEmail($pid,'new_property_inform',1);
            //send Email to agent
            OspropertyEmail::sendEmail($pid,'new_property_confirmation',0);
        }elseif($order->direction == 2){
			//upgrade featured
			OspropertyListing::upgradeProperties($cid);
		}elseif($order->direction == 3){ //edit property and extend the live time
			$pid = $cid[0];
			$db->setQuery("Select * from #__osrs_order_details where order_id = '$order->id'");
			$orderdetails = $db->loadObject();
			$type = $orderdetails->type;
            //$isFeatured = $property->isFeatured;
            if($type == 1){
                //set Feature and approval
                HelperOspropertyCommon::setApproval("f",$pid);
                //set Feature expired time
                HelperOspropertyCommon::setExpiredTime($pid,"f",0);
            }else{
                HelperOspropertyCommon::setApproval("n",$pid);
                HelperOspropertyCommon::setExpiredTime($pid,"n",0);
            }
		}
		//send notification email
		OspropertyEmail::sendPaymentCompleteEmail($order);
	}
	
	/**
	 * Return payment
	 *
	 * @param unknown_type $id
	 */
	static function returnPayment($order_id){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_orders where id = '$order_id'");
		$order = $db->loadObject();
		$direction = $order->direction;

		$msg = array();
		$msg[] = Text::_('OS_PAYMENT_COMPLETED');
		if($direction == 1)
		{
			if($configClass['general_approval'] == 1)
			{
				$msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
			}
			else
			{
				$msg[] = Text::_('OS_WE_WILL_CHECK_AND_PUBLISH_THE_PROPERTY_AS_SOON_AS_POSSIBLE');
			}
		}
		elseif($direction==2)
		{
			$msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
		}
		if($direction == 1)
		{
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '$order->id'");
			$pid = $db->loadResult();
			$order_id = base64_encode($order->id);
			$url = Route::_(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&new=1&&id=$pid&order_id=$order_id&Itemid=".$jinput->getInt('Itemid',0), false, false);
			OSPHelper::redirect($url);	
		}
		elseif($direction == 2)
		{
			$needs = array();
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '$order->id'");
			$pid = $db->loadResult();
			OspropertyListing::thankyouPage($option, $pid, $msg);
		}
		elseif($direction == 3)
		{
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '$order->id'");
			$pid = $db->loadResult();
			$order_id = base64_encode($order->id);
			$url = Route::_(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&new=0&id=$pid&order_id=$order_id&Itemid=".$jinput->getInt('Itemid',0), false, false);
			OSPHelper::redirect($url);	
		}
	}
	
	
	/**
	 * Order details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $order_id
	 */
	static function orderDetails($option,$order_id,$print){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$db->setQuery("Select * from #__osrs_orders where id = '$order_id'");
		$order = $db->loadObject();
		
		$db->setQuery("Select * from #__osrs_configuration");
		$configs = $db->loadObjectList();
		
		$coupon = array();
		if($order->coupon_id > 0){
			$db->setQuery("Select * from #__osrs_coupon where id = '$order->coupon_id'");
			$coupon = $db->loadObject();
		}
		
		$db->setQuery("Select a.*,b.pro_name from #__osrs_order_details as a inner join #__osrs_properties as b on b.id = a.pid where a.order_id = '$order_id'");
		$items = $db->loadObjectList();
		
		$db->setQuery("Select * from #__osrs_agents where id = '$order->agent_id'");
		$agent = $db->loadObject();
		
		HTML_OspropertyPayment::orderDetailsForm($option,$order,$configs,$coupon,$items,$agent,$print);
	}

	/**
	*	List all orders history of user
	**/
	static function ordersHistory($orders){
		global $jinput, $configClass,$lang_suffix;
		$db = Factory::getDbo();
		if(count($orders) > 0){
			foreach($orders as $order){
				$query = "Select a.pro_name$lang_suffix as pro_name,a.id as pid from #__osrs_properties as a"
						." inner join #__osrs_order_details as b on b.pid = a.id"
						." where b.order_id = '$order->id'";
				$db->setQuery($query);
				$properties = $db->loadObjectList();
				$property_str = "";
				for($j=0;$j<count($properties);$j++){
					$property =$properties[$j];
					$property_str .= $property->pro_name."<div class='clearfix'></div>";
				}
				$order->property = $property_str;
			}
			HTML_OspropertyPayment::listOrdersHistory($orders);
		}else{
			?>
			<div class="row-fluid">
				<div class="span12">
					<h4>
						<?php echo Text::_('OS_NO_ORDERS_HISTORY_FOUND');?>
					</h4>
				</div>
			</div>
			<?php
		}
	}

    /**
     * Payment failure
     */
	static function paymentFailure(){
	    global $bootstrapHelper,$mainframe;
        $reason = isset($_SESSION['reason']) ? $_SESSION['reason'] : '';
        if (empty($reason)) {
            $reason = Factory::getSession()->get('omnipay_payment_error_reason');
        }
        if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/paymentfailure.php')){
            $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
        }else{
            $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
        }
        $tpl->set('bootstrapHelper',$bootstrapHelper);
        $tpl->set('reason',$reason);
        $body = $tpl->fetch("paymentfailure.php");
        echo $body;
    }
}
