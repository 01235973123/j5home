<?php
/**
 * @version		1.1.1
 * @package		Joomla
 * @subpackage	OS Property
 * @author		Dang Thuc Dam
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */

// no direct access
defined( '_JEXEC' ) or die ;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class os_offline extends os_payment 
{
	/**
	 * Order Status
	 *
	 * @var unknown_type
	 */
	var $order_status = 0;
	/**
	 * Constructor functions, init some parameter
	 *
	 * @param object $params
	 */
	function os_offline($params) {
		parent::setName('os_offline');		
		parent::os_payment();				
		parent::setCreditCard(false);		
    	parent::setCardType(false);
    	parent::setCardCvv(false);
    	parent::setCardHolderName(false);	
    	$this->order_status = $params->get('order_status');
	}	
	/**
	 * Process payment 
	 *
	 */
	function processPayment($row, $data) 
	{
		$mainframe = Factory::getApplication() ;
		$configClass = OSPHelper::loadConfig();
		$jinput = Factory::getApplication()->input;
		$itemid = $jinput->getint('Itemid');
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_orders where id = '$row->id'");
		$order = $db->loadObject();
		$direction = $order->direction;
		if($this->order_status == 0)
		{
			//do nothing
			$msg = array();
			$db->setQuery("Update #__osrs_orders set payment_made = '0',order_status = 'P' where id = '$row->id'");
			$db->execute();
			$msg[] = Text::_('OS_ORDER_HAS_BEEN_STORED');
			if($direction == 1){
				$msg[] = Text::_('OS_PROPERTY_WILL_BE_APPROVED_AFTER_YOU_MAKE_PAYMENT');
			}elseif($direction==2){
				$msg[] = Text::_('OS_PROPERTIES_WILL_BE_UPGRADED_AFTER_YOU_MAKE_PAYMENT');
			}elseif($direction==3){
				$msg[] = Text::_('OS_PROPERTY_WILL_BE_APPROVED_AFTER_YOU_MAKE_PAYMENT');
			}
		}
		else
		{
			//do nothing
			$msg = array();
			$db->setQuery("Update #__osrs_orders set payment_made = '1',order_status = 'S' where id = '$row->id'");
			$db->execute();
			OspropertyPayment::paymentComplete($row->id);
			$msg[] = Text::_('OS_PAYMENT_COMPLETED');
			if($direction == 1){
				if($configClass['general_approval'] == 1){
					$msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
				}else{
					$msg[] = Text::_('OS_WE_WILL_CHECK_AND_PUBLISH_THE_PROPERTY_AS_SOON_AS_POSSIBLE');
				}
			}elseif($direction == 2){
				$msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
			}elseif($direction == 3){
				$msg[] = Text::_('OS_YOUR_REQUEST_HAS_BEEN_APPROVED');
			}
		}

		if($direction == 1)
		{
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '$row->id'");
			$pid = $db->loadResult();
			$order_id = base64_encode($row->id);
			$url = Route::_(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&new=1&&id=$pid&order_id=$order_id&Itemid=".$itemid, false, false);
			$mainframe->redirect($url);	
		}
		elseif($direction == 2 || $direction == 3) 
		{
		    $needs = array();
			$db->setQuery("Select pid from #__osrs_order_details where order_id = '".$row->id."'");
			$pid = $db->loadResult();
			OspropertyListing::thankyouPage($option, $pid, $msg);
        }
	}		
}