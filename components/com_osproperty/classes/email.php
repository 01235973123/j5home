<?php
/*------------------------------------------------------------------------
# email.php - Ossolution Property
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
use Joomla\CMS\Mail\MailHelper;


class OspropertyEmail{
	/**
	 * Display Email default class
	 *
	 */
	static function display(){
		//nothing
	}
	
	/**
	 * Send email
	 *
	 * @param unknown_type $pid
	 * @param unknown_type $email_key
	 */
	static function sendEmail($pid,$email_key,$sendto)
	{
		global $mainframe,$configClass;
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
		$db = Factory::getDbo();
		$notify_email = $configClass['notify_email'];
		
		//$db->setQuery("Select * from #__osrs_configuration");
		//$configs = $db->loadObjectList();
		
		$auto_approval = $configClass['general_approval'];
		$db->setQuery("Select * from #__osrs_properties where id = '$pid'");
		$property = $db->loadObject();
		
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		
		if($sendto == 0)
		{
			$agent_id = $property->agent_id;
			$db->setQuery("Select user_id from #__osrs_agents where id = '$agent_id'");
			$user_id = $db->loadResult();
			$user_language = OSPHelper::getUserLanguage($user_id);
			$language_prefix = OSPHelper::getFieldSuffix($user_language);
			$db->setQuery("Select * from #__osrs_agents where user_id = '$user_id'");
			$agent = $db->loadObject();
			$emailto = $agent->email;
			$user = Factory::getUser($user_id);
			if($emailto != ""){
				$emailto = $user->email;
			}
		}else{
			$emailto = $notify_email;
		}
		
		if($emailto != "")
		{
			$db->setQuery("Select * from #__osrs_emails where email_key like '$email_key' and published = '1'");
			$email = $db->loadObject();
			if($email->id > 0)
			{
				$subject = $email->{'email_title'.$language_prefix};
				$content = stripslashes($email->{'email_content'.$language_prefix});
				if(!OSPHelper::isEmptyMailContent($subject,$content)){
					$subject = $email->{'email_title'};
					$content = stripslashes($email->{'email_content'});
				}
				
				
				$db->setQuery("Select name from #__osrs_agents where id = '$property->agent_id'");
				$agent_name = $db->loadResult();
				ob_start();
				OspropertyListing::propertyDetails($pid);
				$body = ob_get_contents();
				ob_end_clean();
				//replace details
				$content = str_replace("{property_details}",$body,$content);
				//replace customer
				$content = str_replace("{customer}",$agent_name,$content);
				
				//replace link
				$link = Uri::root()."administrator/index.php?option=com_osproperty&task=properties_edit&cid[]=".$pid;
				$link = "<a href='".$link."'>".$link."</a>";
				$content = str_replace("{link}",$link,$content);
				
				if($auto_approval == 0){
					$information = Text::_("OS_WE_WILL_CHECK_THE_PROPERTY_AS_SOON_AS_POSSIBLE");
				}else{
					$information = Text::_("OS_THE_PROPERTY_HAS_BEEN_PUBLISHED");
				}
				$content = str_replace("{information}",$information,$content);
				
				$site_name = $configClass['general_bussiness_name'];
				
				$content = str_replace("{site_name}",$site_name,$content);
				
				$itemid = OSPRoute::getPropertyItemid($pid);
				$detail_link = Route::_("index.php?option=com_osproperty&task=property_details&id=".$pid."&Itemid=".$itemid);
				$detail_link = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$detail_link;
				$detail_link = "<a href='".$detail_link."'>".$detail_link."</a>";
				$content = str_replace("{details_link}",$detail_link,$content);
				$mailer = Factory::getMailer();
				try
				{
					$mailer->sendMail($emailfrom,$site_name,$emailto,$subject,$content,1);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
			}
		}
	}
	
	/**
	 * Send contact form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $contact
	 */
	static function sendContactEmail($option,$contact){
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		$language_prefix = OSPHelper::getFieldSuffix();
		
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		$sitename  = $configClass['general_bussiness_name'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'new_message_received' and published = '1'");
		$email = $db->loadObject();
		
		if($email->id > 0){
			$subject = $email->{'email_title'.$language_prefix};
			$message = stripslashes($email->{'email_content'.$language_prefix});
			if(!OSPHelper::isEmptyMailContent($subject,$message)){
				$subject = $email->{'email_title'};
				$message = stripslashes($email->{'email_content'});
			}
			
			$subject = str_replace("{visitor_name}",$contact['author'],$subject);
			$subject = str_replace("{site_name}",$sitename,$subject);
			
			$contact['email'] = "<a href='mailto:".$contact['email']."'>".$contact['email']."</a>";
			$siteUrl = "<a href='".Uri::root()."' target='_blank'>".Uri::root()."</a>";
			$message = str_replace("{visitor_name}",$contact['author'],$message);
			$message = str_replace("{site_name}",$sitename,$message);
			$message = str_replace("{subject}",$contact['title'],$message);
			$message = str_replace("{contact_email}",$contact['email'],$message);
			$message = str_replace("{message}",$contact['message'],$message);
			$message = str_replace("{site_url}",$siteUrl,$message);
			$message = str_replace("{received_name}",$contact['receiver'],$message);
			$mailer = Factory::getMailer();
            if(MailHelper::isEmailAddress($contact['emailto'])) 
			{
				try
				{
					$mailer->sendMail($emailfrom, $sitename, $contact['emailto'], $subject, $message, 1);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
            }
		}
	}
	
	/**
	 * Send email to friend
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailopt
	 */
	static function sendFriendEmail($option,$emailopt){
		global $configClass;
		$db = Factory::getDbo();
		$language_prefix = OSPHelper::getFieldSuffix();
		
		$config = new JConfig();
		$emailfrom = $config->mailfrom;
		
		$sitename  = $configClass['general_bussiness_name'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'tell_friend' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$subject = $email->{'email_title'.$language_prefix};
			$message = stripslashes($email->{'email_content'.$language_prefix});
			if(!OSPHelper::isEmptyMailContent($subject,$message)){
				$subject = $email->{'email_title'};
				$message = stripslashes($email->{'email_content'});
			}

			$message = str_replace("{friend_name}",$emailopt['friend_name'],$message);
			$message = str_replace("{name}",$emailopt['your_name'],$message);
			$message = str_replace("{link}",$emailopt['link'],$message);
			$message = str_replace("{message}",$emailopt['message'],$message);
			$message = str_replace("{site_name}",$emailopt['site_name'],$message);
			$mailer = Factory::getMailer();
            if(MailHelper::isEmailAddress($emailopt['friend_email'])) 
			{
				try
				{
					$mailer->sendMail($emailfrom, $sitename, $emailopt['friend_email'], $subject, $message, 1);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
            }
		}
	}
	
	
	/**
	 * Send comment email
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailopt
	 */
	static function sendCommentEmail($option,$emailopt){
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		$language_prefix = OSPHelper::getFieldSuffix();
		
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		$sitename  = $configClass['general_bussiness_name'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'comment_send_after_ad' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$subject = $email->{'email_title'.$language_prefix};
			$message = stripslashes($email->{'email_content'.$language_prefix});
			if(!OSPHelper::isEmptyMailContent($subject,$message)){
				$subject = $email->{'email_title'};
				$message = stripslashes($email->{'email_content'});
			}
			
			
			$message = str_replace("{username}",$emailopt['agentname'],$message);
			$message = str_replace("{author}",$emailopt['author'],$message);
			$message = str_replace("{title}",$emailopt['title'],$message);
			$message = str_replace("{message}",$emailopt['message'],$message);
			$message = str_replace("{rate}",$emailopt['rate'],$message);
			$message = str_replace("{link}",$emailopt['link'],$message);
			$message = str_replace("{site_name}",$sitename,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$emailopt['agentemail'],$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}
	}
	
	
	static function sendAdministratorCommentEmail($option,$emailopt){
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		$language_prefix = OSPHelper::getFieldSuffix();
		
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		$sitename  = $configClass['general_bussiness_name'];
		$db->setQuery("Select * from #__osrs_emails where email_key like 'comment_add_send_to_admin' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$subject = $email->{'email_title'.$language_prefix};
			$message = stripslashes($email->{'email_content'.$language_prefix});
			if(!OSPHelper::isEmptyMailContent($subject,$message)){
				$subject = $email->{'email_title'};
				$message = stripslashes($email->{'email_content'});
			}
			
			$message = str_replace("{listing}",$emailopt['pro_name'],$message);
			$message = str_replace("{author}",$emailopt['author'],$message);
			$message = str_replace("{title}",$emailopt['title'],$message);
			$message = str_replace("{message}",$emailopt['message'],$message);
			$message = str_replace("{rate}",$emailopt['rate'],$message);
			$message = str_replace("{link}",$emailopt['link'],$message);
			$message = str_replace("{site_name}",$sitename,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$configClass['notify_email'],$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}
	}
	
	
	/**
	 * Send Approximates Email
	 *
	 * @param unknown_type $option
	 * @param unknown_type $pid
	 */
	static function sendApproximatesEmail($option,$pid){
		global $mainframe,$configClass;
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/route.php");
		$db = Factory::getDbo();
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		$sitename  = $configClass['general_bussiness_name'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'approximates_email' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$db->setQuery("Select a.id,a.user_id,a.name,a.email from #__osrs_agents as a inner join #__osrs_properties as b on b.agent_id = a.id where b.id = '$pid'");
			$agent = $db->loadObject();
			$agentname 	= $agent->name;
			$agentemail = $agent->email;
			$agentid	= $agent->id;
			$user_id 	= $agent->user_id;
			
			if($user_id > 0){
				$user_language = OSPHelper::getUserLanguage($user_id);
				$language_prefix = OSPHelper::getFieldSuffix($user_language);
			
				$subject = $email->{'email_title'.$language_prefix};
				$message = stripslashes($email->{'email_content'.$language_prefix});
				if(!OSPHelper::isEmptyMailContent($subject,$message)){
					$subject = $email->{'email_title'};
					$message = stripslashes($email->{'email_content'});
				}
				
				$days = $configClass['approximates_days'];
				$db->setQuery("Select * from #__osrs_expired where pid = '$pid'");
				$expired = $db->loadObject();

				$itemid = OSPRoute::getPropertyItemid($pid);
				$detail_link = Route::_("index.php?option=com_osproperty&task=property_details&id=".$pid."&Itemid=".$itemid);
				$detail_link = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$detail_link;
				
				$details_link = "<a href='".$detail_link."'>".$detail_link."</a>";
				
				$update_link = Uri::root()."index.php?option=com_osproperty&task=agent_editprofile";
				$update_link = "<a href='".$update_link."'>".Text::_('OS_UPDATE_LINK')."</a>";
				
				$message = str_replace("{username}",$agentname,$message);
				$message = str_replace("{update_link}",$update_link,$message);
				$message = str_replace("{details_link}",$details_link,$message);
				$message = str_replace("{days}",$days,$message);
				$message = str_replace("{expire_date}",HelperOspropertyCommon::loadTime($expired->expired_time,2),$message);
				$message = str_replace("{site_name}",$sitename,$message);
				$mailer = Factory::getMailer();
				try
				{
					if($mailer->sendMail($emailfrom,$sitename,$agentemail,$subject,$message,1)){
						//after send email, update the send inform
						$db->setQuery("Update #__osrs_expired set send_inform = '1' where pid = '$pid'");
						$db->execute();
						$db->setQuery("Delete from #__osrs_queue where id = '$row->id'");
						$db->execute();
					}
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
			}
		}
	}
	
	/**
	 * Send expired email
	 *
	 * @param unknown_type $option
	 * @param unknown_type $pid
	 */
	static function sendExpiredEmail($option,$pid,$itemid){
		global $mainframe,$configClass;
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
		$db = Factory::getDbo();
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		$sitename  = $configClass['general_bussiness_name'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'listing_expired_email' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			
			$db->setQuery("Select a.id, a.user_id,a.name,a.email,b.pro_name from #__osrs_agents as a inner join #__osrs_properties as b on b.agent_id = a.id where b.id = '$pid'");
			$agent = $db->loadObject();
			$agentname 	= $agent->name;
			$agentid	= $agent->id;
			$user_id	= $agent->user_id;
			$agentemail = $agent->email;
			$property 	= $agent->pro_name;
					
			if($user_id > 0){
				$user_language = OSPHelper::getUserLanguage($user_id);
				$language_prefix = OSPHelper::getFieldSuffix($user_language);
			
				$subject = $email->{'email_title'.$language_prefix};
				$message = stripslashes($email->{'email_content'.$language_prefix});
				if(!OSPHelper::isEmptyMailContent($subject,$message)){
					$subject = $email->{'email_title'};
					$message = stripslashes($email->{'email_content'});
				}
				
				
				$details_link = "<a href='".Uri::root()."index.php?option=com_osproperty&task=property_details&id=$pid'>".Uri::root()."index.php?option=com_osproperty&task=property_details&id=$pid</a>";
				
				$link = Uri::root()."index.php?option=com_osproperty&task=agent_editprofile";
				$update_link = "<a href='".$link."'>".Text::_('OS_UPDATE_LINK')."</a>";
				
				$message = str_replace("{username}",$agentname,$message);
				$message = str_replace("{update_link}",$update_link,$message);
				$message = str_replace("{details_link}",$details_link,$message);
				$message = str_replace("{site_name}",$sitename,$message);
				$message = str_replace("{listing}",$property,$message);
				$mailer = Factory::getMailer();
				try
				{
					if($mailer->sendMail($emailfrom,$sitename,$agentemail,$subject,$message,1))
					{
						$db->setQuery("Update #__osrs_expired set send_expired = '1' where pid = '$pid'");
						$db->execute();
						$db->setQuery("Delete from #__osrs_queue where id = '$itemid'");
						$db->execute();
					}
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
			}
		}
	}
	
	
	/**
	 * Send expired feature email
	 *
	 * @param unknown_type $option
	 * @param unknown_type $pid
	 */
	static function sendExpiredFeatureEmail($option,$pid){
		global $mainframe,$configClass;
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
		$db = Factory::getDbo();
		$emailfrom = $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		$sitename  = $configClass['general_bussiness_name'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'featured_expire_listing' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			//$subject = $email->email_title;
			//$message = stripslashes(nl2br($email->email_content));
			$db->setQuery("Select a.id,a.user_id, a.name,a.email,b.pro_name from #__osrs_agents as a inner join #__osrs_properties as b on b.agent_id = a.id where b.id = '$pid'");
			$agent 		= $db->loadObject();
			$agentid 	= $agent->id;
			$agentname 	= $agent->name;
			$agentemail = $agent->email;
			$user_id	= $agent->user_id;
			$property = $agent->pro_name;
			
			if($user_id > 0){
				$user_language = OSPHelper::getUserLanguage($user_id);
				$language_prefix = OSPHelper::getFieldSuffix($user_language);
			
				$subject = $email->{'email_title'.$language_prefix};
				$message = stripslashes($email->{'email_content'.$language_prefix});
				if(!OSPHelper::isEmptyMailContent($subject,$message)){
					$subject = $email->{'email_title'};
					$message = stripslashes($email->{'email_content'});
				}
				
				$details_link = "<a href='".Uri::root()."index.php?option=com_osproperty&task=property_details&id=$pid'>".Uri::root()."index.php?option=com_osproperty&task=property_details&id=$pid</a>";
				
				$link = Uri::root()."index.php?option=com_osproperty&task=agent_editprofile";
				$update_link = "<a href='".$link."'>".Text::_('OS_UPDATE_LINK')."</a>";
				
				$message = str_replace("{username}",$agentname,$message);
				$message = str_replace("{update_link}",$update_link,$message);
				$message = str_replace("{details_link}",$details_link,$message);
				$message = str_replace("{site_name}",$sitename,$message);
				$message = str_replace("{listing}",$property,$message);
				$mailer = Factory::getMailer();
				try
				{
					if($mailer->sendMail($emailfrom,$sitename,$agentemail,$subject,$message,1))
					{
						$db->setQuery("Update #__osrs_expired set send_featured = '1' where pid = '$pid'");
						$db->execute();
						$db->setQuery("Delete from #__osrs_queue where id = '$row->id'");
						$db->execute();
					}
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
			}
		}
	}
	
	/**
	 * Send Payment Complete Information
	 *
	 * @param unknown_type $option
	 * @param unknown_type $order
	 * @param unknown_type $items
	 * @param unknown_type $coupon
	 */
	static function sendPaymentCompleteEmail($order){
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		$configClass = OSPHelper::loadConfig();

		$emailfrom				= $configClass['general_bussiness_email'];
		if($emailfrom == ""){
			$config				= new JConfig();
			$emailfrom			= $config->mailfrom;
		}
		$sitename				= $configClass['general_bussiness_name'];
		$notify					= $configClass['notify_email'];

		//send email to user
		if($order->payment_method == "os_offline"){
			$db->setQuery("Select * from #__osrs_emails where email_key like 'offline_payment' and published = '1'");
		}else{
			$db->setQuery("Select * from #__osrs_emails where email_key like 'payment_accept' and published = '1'");
		}
		$email = $db->loadObject();
		if($email->id > 0){
			$subject			= $email->email_title;
			$message			= stripslashes($email->email_content);
			
			$agent_id			= $order->agent_id;
			$created_by			= $order->created_by;

			if($created_by == 0){
				$db->setQuery("Select * from #__osrs_agents where id = '$agent_id'");
				$agent = $db->loadObject();
				$agentname		= $agent->name;
				$agentemail		= $agent->email;
			}else{
				$db->setQuery("Select * from #__osrs_companies where id = '$agent_id'");
				$agent = $db->loadObject();
				$agentname		= $agent->company_name;
				$agentemail		= $agent->email;
			}
			
			if(($order->payment_method != "") and ($order->total > 0)){
				$db->setQuery("Select title from #__osrs_plugins where name like '$order->payment_method'");
				$payment_method = $db->loadResult();
			}else{
				$payment_method = "N/A";
			}

			$message			= str_replace("{username}",$agentname,$message);
			$message			= str_replace("{gateway}",$payment_method,$message);
			$message			= str_replace("{txn}",$order->transaction_id,$message);

			$query = "Select a.pro_name,a.id as pid from #__osrs_properties as a"
					." inner join #__osrs_order_details as b on b.pid = a.id"
					." where b.order_id = '$order->id'";
			$db->setQuery($query);
			$properties			= $db->loadObjectList();
			$propertyArr		= array();
			for($j=0;$j<count($properties);$j++){
				$property		= $properties[$j];
				$propertyArr[]  = $property->pro_name;
			}

			switch($order->direction){
				case "0":
					$direction = Text::_('OS_NEW_PROPERTY_POSTED')."(".implode(", ",$propertyArr).")";
				break;
				case "1":
					$direction = Text::_('OS_FEATURED_UPGRADE')."(".implode(", ",$propertyArr).")";
				break;
				case "2":
					$direction = Text::_('OS_EXTEND_LIVE_TIME')."(".implode(", ",$propertyArr).")";
				break;
			}
			$message = str_replace("{item}",$direction,$message);
			$message = str_replace("{price}",OSPHelper::generatePrice($order->curr,$order->total),$message);
			$message = str_replace("{date}",HelperOspropertyCommon::loadTime($order->created_on,2),$message);
			$message = str_replace("{site_name}",$sitename,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$agentemail,$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}

		//send email to administrator
		$db->setQuery("Select * from #__osrs_emails where email_key like 'payment_inform_to_administrator' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$subject			= $email->email_title;
			$message			= stripslashes($email->email_content);
			
			$agent_id			= $order->agent_id;
			$created_by			= $order->created_by;

			if($created_by == 0){
				$db->setQuery("Select * from #__osrs_agents where id = '$agent_id'");
				$agent = $db->loadObject();
				$agentname		= $agent->name;
				$agentemail		= $agent->email;
			}else{
				$db->setQuery("Select * from #__osrs_companies where id = '$agent_id'");
				$agent = $db->loadObject();
				$agentname		= $agent->company_name;
				$agentemail		= $agent->email;
			}
			
			if(($order->payment_method != "") and ($order->total > 0)){
				$db->setQuery("Select title from #__osrs_plugins where name like '$order->payment_method'");
				$payment_method = $db->loadResult();
			}else{
				$payment_method = "N/A";
			}

			$message			= str_replace("{username}",$agentname,$message);
			$message			= str_replace("{gateway}",$payment_method,$message);
			$message			= str_replace("{txn}",$order->transaction_id,$message);

			$query = "Select a.pro_name,a.id as pid from #__osrs_properties as a"
					." inner join #__osrs_order_details as b on b.pid = a.id"
					." where b.order_id = '$order->id'";
			$db->setQuery($query);
			$properties			= $db->loadObjectList();
			$propertyArr		= array();
			for($j=0;$j<count($properties);$j++){
				$property		= $properties[$j];
				$propertyArr[]  = $property->pro_name;
			}

			switch($order->direction){
				case "0":
					$direction = Text::_('OS_NEW_PROPERTY_POSTED')."(".implode(", ",$propertyArr).")";
				break;
				case "1":
					$direction = Text::_('OS_FEATURED_UPGRADE')."(".implode(", ",$propertyArr).")";
				break;
				case "2":
					$direction = Text::_('OS_EXTEND_LIVE_TIME')."(".implode(", ",$propertyArr).")";
				break;
			}

			$subject = str_replace("{item}",$direction,$subject);

			$message = str_replace("{item}",$direction,$message);
			$message = str_replace("{price}",OSPHelper::generatePrice($order->curr,$order->total),$message);
			$message = str_replace("{date}",HelperOspropertyCommon::loadTime($order->created_on,2),$message);
			$message = str_replace("{site_name}",$sitename,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$notify,$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}
	}
	
	/**
	 * Send agent approval request
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailOpt
	 */
	static function sendAgentApprovalRequest($option,$emailOpt){
		global $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$emailfrom = $configClass['general_bussiness_email'];
		$sitename  = $configClass['general_bussiness_name'];
		$notify_email = $configClass['notify_email'];
		$db->setQuery("Select * from #__osrs_emails where email_key like 'request_approval_agent' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0)
		{
			$subject = $email->email_title;
			$message = stripslashes($email->email_content);
			$message = str_replace("{customer}",$emailOpt[0]->customer,$message);
			$link = "<a href='".Uri::root()."administrator/index.php?option=com_osproperty&task=agent_edit&cid[]=".$emailOpt[0]->agent_id."'>".Uri::root()."administrator/index.php?option=com_osproperty&task=agent_edit&cid[]=".$emailOpt[0]->agent_id."</a>";
			$message = str_replace("{link}",$link,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$notify_email,$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}
	}
	
	/**
	 * Send properties approval request
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailOpt
	 */
	static function sendPropertyApprovalRequest($option,$emailOpt){
		global $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$emailfrom = $configClass['general_bussiness_email'];
		$sitename  = $configClass['general_bussiness_name'];
		$notify_email = $configClass['notify_email'];
		$db->setQuery("Select * from #__osrs_emails where email_key like 'request_approval_property' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$subject = $email->email_title;
			$message = stripslashes($email->email_content);
			$message = str_replace("{customer}",$emailOpt[0]->customer,$message);
			$message = str_replace("{property}",$emailOpt[0]->property,$message);
			$link = "<a href='".Uri::root()."administrator/index.php?option=com_osproperty&task=properties_list'>".Uri::root()."administrator/index.php?option=com_osproperty&task=properties_list</a>";
			$message = str_replace("{link}",$link,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$notify_email,$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}
	}
	
	/**
	 * Send the notification to administrator when user create new company at frontend of OS Property
	 * In case field : auto_approval_company_register = 0;
	 *
	 * @param unknown_type $company_id
	 */
	static function sendCompanyRequestApproval($company_id){
		global $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		
		$db->setQuery("Select * from #__osrs_companies where id = '$company_id'");
		$company = $db->loadObject();
		
		$user_id = $company->user_id;
		$user = Factory::getUser($user_id);
		
		$emailfrom = $configClass['general_bussiness_email'];
		$sitename  = $configClass['general_bussiness_name'];
		$notify_email = $configClass['notify_email'];
		
		$db->setQuery("Select * from #__osrs_emails where email_key like 'new_company_registration' and published = '1'");
		$email = $db->loadObject();
		if($email->id > 0){
			$subject = $email->email_title;
			$message = stripslashes($email->email_content);
			$message = str_replace("{user}",$user->name,$message);
			$message = str_replace("{company_name}",$company->company_name,$message);
			$link = "<a href='".Uri::root()."administrator/index.php?option=com_osproperty&task=companies_edit&cid[]=".$company_id."'>".Uri::root()."administrator/index.php?option=com_osproperty&task=companies_edit&cid[]=".$company_id."</a>";
			$message = str_replace("{company_backend_url}",$link,$message);
			$mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$sitename,$notify_email,$subject,$message,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
		}
	}

	/**
	 * Send Agent activate email
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailOpt
	 */
	static function sendAgentActiveEmail($option,$emailOpt){
		global $mainframe,$configClass;
		$db = Factory::getDbo();
		
		$emailfrom = $configClass['general_bussiness_email'];
		$sitename  = $configClass['general_bussiness_name'];
		
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		
		$db->setQuery("Select user_id from #__osrs_agents where id = '".$emailOpt['agentid']."'");
		$user_id = $db->loadResult();
		
		if($user_id > 0){
			$user_language = OSPHelper::getUserLanguage($user_id);
			$language_prefix = OSPHelper::getFieldSuffix($user_language);
			
			$db->setQuery("SELECT * FROM #__osrs_emails WHERE `email_key` LIKE 'approval_agent_request' AND published = '1'");
			$email = $db->loadObject();
			if($email->id > 0){
				$subject = $email->{'email_title'.$language_prefix};
				$content = stripslashes($email->{'email_content'.$language_prefix});
				if(!OSPHelper::isEmptyMailContent($subject,$content)){
					$subject = $email->{'email_title'};
					$content = stripslashes($email->{'email_content'});
				}
				$message = $content;
				$subject = str_replace("{site_name}",$sitename,$subject);
				$message = str_replace("{agent}",$emailOpt['agentname'],$message);
				$message = str_replace("{site_name}",$sitename,$message);
				$mailer  = Factory::getMailer();
				try
				{
					$mailer->sendMail($emailfrom,$sitename,$emailOpt['agentemail'],$subject,$message,1);
				}
				catch (Exception $e)
				{
					Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
				}
			}
		}
	}

	/**
	 * Send activated email
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailOpt
	 */
	static function sendActivedEmail($option,$id,$email_type,$emailopt){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		
		$emailfrom = $configClass['general_bussiness_email'];
		$sitename  = $configClass['general_bussiness_name'];
		
		if($emailfrom == ""){
			$config = new JConfig();
			$emailfrom = $config->mailfrom;
		}
		
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$agent_id = $property->agent_id;
		$db->setQuery("Select user_id from #__osrs_agents where id = '$agent_id'");
		$user_id = $db->loadResult();
		if($user_id > 0){
			$user_language = OSPHelper::getUserLanguage($user_id);
			$language_prefix = OSPHelper::getFieldSuffix($user_language);
			
			$db->setQuery("Select * from #__osrs_emails where email_key like '$email_type' and published = '1'");
			$email = $db->loadObject();
			if($email->id > 0){
				$subject = $email->{'email_title'.$language_prefix};
				$content = stripslashes($email->{'email_content'.$language_prefix});
				if(!OSPHelper::isEmptyMailContent($subject,$content)){
					$subject = $email->{'email_title'};
					$content = stripslashes($email->{'email_content'});
				}
				
				$subject = str_replace("{site_name}",$sitename,$subject);
				$message = $content;
				$message = str_replace("{username}",$emailopt['agentname'],$message);
				$message = str_replace("{link}",$emailopt['link'],$message);
				$message = str_replace("{listing}",$emailopt['property'],$message);
				$message = str_replace("{site_name}",$sitename,$message);
				$mailer  = Factory::getMailer();
				$mailer->sendMail($emailfrom,$sitename,$emailopt['agentemail'],$subject,$message,1);
			}
		}
	}
}


?>
