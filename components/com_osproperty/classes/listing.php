<?php
/*------------------------------------------------------------------------
# listing.php - Ossolution Property
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
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Document\Feed\FeedItem;
use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Mail\MailHelper;
use Joomla\Registry\Registry;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Plugin\PluginHelper;


class OspropertyListing
{
	static function display($option,$task)
	{
		global $bootstrapHelper, $mainframe,$jinput;

		$cid        = $jinput->get('cid',array(),'ARRAY');
		$cid		= ArrayHelper::toInteger($cid);
		$id         = $jinput->getInt('id',0);

		switch ($task)
		{
			case "property_details":
				OspropertyListing::details($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_new":
				echo HelperOspropertyCommon::buildToolbar('default');
				OspropertyListing::edit($option,0);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_edit":
				echo HelperOspropertyCommon::buildToolbar('default');
				OspropertyListing::edit($option,$id);
				HelperOspropertyCommon::loadFooter($option);
			break;
            case "property_edit_activelisting":
                echo HelperOspropertyCommon::buildToolbar('default');
                OspropertyListing::activelisting($option,$id);
                HelperOspropertyCommon::loadFooter($option);
            break;
			case "property_save":
				OspropertyListing::save($option,1);
			break;
			case "property_apply":
				OspropertyListing::save($option,0);
			break;
            case "property_saveandactive":
                OspropertyListing::save($option,2);
            break;
			case "property_upgrade":
				echo HelperOspropertyCommon::buildToolbar('default');
				OspropertyListing::upgrade_step1($option,$cid);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_removeUpgrade":
				OspropertyListing::removeUpgrade($option,$cid);
			break;
			case "property_confirmupgrade":
				OspropertyListing::confirmUpgrade($option,$cid);
			break;
			case "property_confirmupgradewithMembership":
				OspropertyListing::confirmUpgradewithMembership($option,$cid);
			break;
			case "property_paymentprocess":
				OspropertyListing::paymentprocess($option,$cid);
			break;
            case "property_membershipprocess":
                OspropertyListing::membershipprocess($cid);
            break;
			case "property_loadState":
				OspropertyListing::loadStates($option);
			break;
			case "property_favorites":
				OspropertyListing::favorites($option);
			break;
			case "property_emaildetails":
				OspropertyListing::propertyDetails($id);
			break;
			case "property_thankyou":
				OspropertyListing::thankyouPage($option,$id);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_print":
				OspropertyListing::printProperty($option,$id);
			break;
			case "property_type":
				OspropertyListing::propertyType($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_city":
				OspropertyListing::propertyCity($option,$id);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_tag":
				OspropertyListing::propertyTag($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_listing":
				global $bootstrapHelper, $configClass,$jinput;
				echo HelperOspropertyCommon::buildToolbar('default');
				$catIds			= $jinput->get('catIds',array(),'ARRAY');
				$catIds			= ArrayHelper::toInteger($catIds);
				$agent_id    	= $jinput->getInt('agent_id',0);
				$property_type	= $jinput->getInt('property_type',0);
				$keyword		= OSPHelper::getStringRequest('keyword','','post');
				$nbed			= $jinput->getInt('nbed',0);
				$nbath			= $jinput->getInt('nbath',0);
				$isfeatured		= $jinput->getInt('isfeatured',0);
				$isSold			= $jinput->getInt('isSold',0);
				$nrooms			= $jinput->getInt('nrooms',0);
				$orderby		= OSPHelper::getStringRequest('orderby','a.isFeatured desc,a.pro_name',''); //JRequest::getVar('orderby','a.isFeatured desc,a.pro_name','','string');
				$ordertype		= OSPHelper::getStringRequest('ordertype','','');//JRequest::getVar('ordertype','');
				$limitstart		= $jinput->getInt('limitstart',0);
				$limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
				$favorites		= $jinput->getInt('favorites',0);
				$price			= $jinput->getInt('price',0);
				$company_id		= $jinput->getInt('company_id',0);
				$city_id		= $jinput->getInt('city',0);
				$state_id		= $jinput->getInt('state_id',0);
				$country_id		= OSPHelper::getStringRequest('country_id',HelperOspropertyCommon::getDefaultCountry(),''); //JRequest::getVar('country_id',HelperOspropertyCommon::getDefaultCountry());
				$filterParams	= [];
				
				$u =& Uri::getInstance();
				?>
				<form method="POST" action="<?php echo $u->toString();?>" name="ftForm">
				<?php
				OspropertyListing::listProperties($option,$company_id,$catIds,$agent_id,$property_type,$keyword,$nbed,$nbath,$isfeatured,$isSold,$nrooms,$orderby,$ordertype,$limitstart,$limit,$favorites,$price,$filterParams,$city_id,$state_id,$country_id,0,1,-1);
				?>
				<input type="hidden" name="option" value="<?php echo $option?>" />
				<input type="hidden" name="task" value="property_listing" />
				<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
				</form>
				<?php
				//HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_showSharingForm":
				OspropertyListing::showSharingForm($id);
			break;
			case "property_submittellfriend":
				OspropertyListing::submitTellfriend($option,$id);
			break;
			case "property_submitcomment":
				OspropertyListing::submitComment($option,$id);
			break;
			case "property_pdf":
				OspropertyListing::exportpdf($option,$id);
			break;
			case "property_captcha":
				OspropertyListing::generateCaptcha($option);
			break;
			case "property_approvaldetails":
				OspropertyListing::approvalDetails($option,$id);
			break;
			case "property_requestapproval":
				OspropertyListing::requestApproval($option,$cid);
			break;
			case "property_processrequestapproval":
				OspropertyListing::processRequestApproval($option,$cid);
			break;
			
			case "property_search":
				OspropertyListing::search($option);
			break;
			case "property_setexpiredInPayment":
				OspropertyListing::setexpiredInPayment($option,$id);
			break;
			case "property_updateEnglandState":
				OspropertyListing::updateEnglandStates($option);
			break;
			case "property_streetview":
				OspropertyListing::showStreetView($option);
			break;
			case "property_advsearch":
				echo HelperOspropertyCommon::buildToolbar('locator');
				OspropertyListing::advSearch($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
            case "property_cancelalertemail":
                OspropertyListing::cancelAlertEmail();
            break;
			case "property_saveSearchList":
				OspropertyListing::saveSearchList($option);
			break;
			case "property_updateSearchList":
				OspropertyListing::updateSearchList($option);
			break;
			case "property_searchlist":
				echo HelperOspropertyCommon::buildToolbar('default');
				OspropertyListing::showSearchList($option);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_savelistname":
				OspropertyListing::saveListName($option);
			break;
			case "property_removesearchlist":
				OspropertyListing::removesearchlist($option);
			break;
			case "property_showRequestMoreDetails":
				OspropertyListing::showRequestMoreDetails($id);
			break;
			case "property_requestmoredetails":
				OspropertyListing::sendRequestDetails($option);
			break;
			case "property_generatelocation":
				OspropertyListing::generateLocation();
			break;
			case "property_changelayout":
				OspropertyListing::changeLayout();
			break;
			case "property_resetdata":
				OspropertyListing::resetData();
			break;
			case "property_generatephoto":
				OspropertyListing::generatePhoto();
			break;
			case "property_savephoto":
				OspropertyListing::savingPhoto();
			break;
			case "property_exportrss":
				OspropertyListing::exportRSS();
			break;
			case "property_reportForm":
				OspropertyListing::reportForm($id);
			break;
			case "property_doreportproperty":
				OspropertyListing::doreportproperty($id);
			break;
			case "property_unfeatured":
				OspropertyListing::unfeaturedproperty($id);
			break;
			case "property_skip":
				OspropertyListing::skip();
			break;
			case "property_getJson":
				OspropertyListing::getjson();
			break;
            case "property_editcomment":
                OspropertyListing::editComment();
            break;
            case "property_submiteditcomment":
                OspropertyListing::submitEditComment();
            break;
			case "property_stas":
				OspropertyListing::stasInformation();
			break;
			case "property_manageallproperties":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				OspropertyListing::manageAllProperties($option);
			break;
            case "property_changevalueisFeatured":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
                OspropertyListing::changeStatus($option,$id,'isFeatured');
            break;
            case "property_changevalueapproved":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
                OspropertyListing::changeStatus($option,$id,'approved');
                break;
			case "property_changevaluepublished":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
                OspropertyListing::changeStatus($option,$id,'published');
                break;
			
			case "property_publishproperties":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				OspropertyListing::changeStatuses($option,$cid,'published',1);
			break;
			case "property_unpublishproperties":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				OspropertyListing::changeStatuses($option,$cid,'published',0);
			break;
			case "property_editproperty":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				echo HelperOspropertyCommon::buildToolbar('default');
				OspropertyListing::edit($option,$cid[0],1);
				HelperOspropertyCommon::loadFooter($option);
			break;
			case "property_deleteproperties":
				if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
				}
				OspropertyListing::deleteProperties($option,$cid,1);
			break;
			case "property_showpano":
				OspropertyListing::showPanorama($id);
			break;
			case "property_test":
				OspropertyListing::test();
			break;
			case "property_gallery":
				OspropertyListing::gallery($id);
			break;
			case "property_resetfilterform":
				OspropertyListing::resetfilterform();
			break;
		}
	}

	public static function resetfilterform()
	{
		global $jinput, $mainframe;
		$session = Factory::getSession();
		$session->set('orderby','');
		$session->set('ordertype','');
		$session->set('state_id','');
		$session->set('city_id','');
		$session->set('country_id','');
		$session->set('isFeatured','');
		$session->set('isSold','');
		$session->set('catIds',array());
		$session->set('agenttype','');
		$session->set('type_id','');
		$session->set('min_price','');
		$session->set('max_price','');
		$session->set('price','');
		$session->set('use_filterform','');
		
		$return = $jinput->getString('return','');
		$return = base64_decode($return);
		$mainframe->redirect($return);
	}
	
	public static function splitQueries($sql)
	{
		// Initialise variables.
		$buffer		= [];
		$queries	= [];
		$in_string	= false;

		// Trim any whitespace.
		$sql = trim($sql);

		// Remove comment lines.
		$sql = preg_replace("/\n\#[^\n]*/", '', "\n".$sql);

		// Parse the schema file to break up queries.
		for ($i = 0; $i < strlen($sql) - 1; $i ++)
		{
			
			if ($sql[$i] == ";" && !$in_string) {
				$queries[] = substr($sql, 0, $i);
				$sql = substr($sql, $i +1);
				$i = 0;
			}

			if ($in_string && ($sql[$i] == $in_string) && $buffer[1] != "\\") {
				$in_string = false;
			}
			elseif (!$in_string && ($sql[$i] == '"' || $sql[$i] == "'") && (!isset ($buffer[0]) || $buffer[0] != "\\")) {
				$in_string = $sql[$i];
			}
			if (isset ($buffer[1])) {
				$buffer[0] = $buffer[1];
			}
			$buffer[1] = $sql[$i];			
		}

		// If the is anything left over, add it to the queries.
		if (!empty($sql)) {
			$queries[] = $sql;
		}

		return $queries;
	}
	
	function test(){
		global $bootstrapHelper, $mainframe;
        $Itemid = 0;
		jimport( 'joomla.document.feed.feed' );
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties");
		$rows = $db->loadObjectList();
		$doc		= Factory::getDocument();
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$title = $row->pro_name;
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');		
			$link = Route::_('index.php?option=com_edocman&view=document&id='.$row->id.'&Itemid='.$Itemid);			
			// feed item description text
			@$created = ($row->created ? date('r', strtotime($row->created)) : '');
			// load individual item creator class
			$item = new FeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->description 	= $row->pro_small_desc;
			$item->date			= @$created;
			$item->category		= "lalalaa";						
			$doc->addItem( $item );
		}
		
	}
	
	function generateLocation(){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from t_region");
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			$stateArr = [];
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$region_id = $row->pk_i_id;
				$region_name = $row->s_name;
				$db->setQuery("Select * from t_city where fk_i_region_id = '$region_id'");
				$cities = $db->loadObjectList();
				$city_str = "";
				if(count($cities) > 0){
					$cityArr = [];
					for($j=0;$j<count($cities);$j++){
						$city = $cities[$j];
						$cityArr[] = $city->s_name;
					}
					$city_str = implode(",",$cityArr);
				}
				$state_str = $region_name.":".$city_str;
				$stateArr[] = $state_str;
			}
		}
		
		if(count($stateArr) > 0){
			$return_value = implode("\n",$stateArr);
			$tmp_file = JPATH_ROOT."/tmp/bo_bolivia.txt";
			$fb = fopen($tmp_file,"w");
			fwrite($fb,$return_value);
			fclose($fb);
		}
	}
	
	/**
	 * Change layout
	 *
	 */
	function changeLayout(){
		global $bootstrapHelper, $mainframe,$jinput;
        $jinput = Factory::getApplication()->input;
		$viewtype = $jinput->getInt('viewtype',0);
		$url = $jinput->getString('url','');
		@setcookie('viewtypecookie',$viewtype,time()+3600,"/");
		OSPHelper::redirect($url);
	}
	
	/**
	 * Show Request More Details
	 *
	 * @param unknown_type $id
	 */
	static function showRequestMoreDetails($id)
	{
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
        $jinput = Factory::getApplication()->input;
		$captcha_value = $jinput->getString('c','');
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$title = "";
		if($property->ref != "" && $configClass['show_ref'] == 1){
			$title .= $property->ref.", ";
		}
		$title .= OSPHelper::getLanguageFieldValue($property,'pro_name');
		
		//$db->setQuery("Select * from #__osrs_themes where published = '1'");
		//$theme = $db->loadObject();
		//$themename = ($theme->name!= "")? $theme->name:"default";
		$themename = OSPHelper::getThemeName();
		$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
		$themeobj = $db->loadObject();
		if(file_exists(JPATH_COMPONENT."/templates/".$themename."/request.html.tpl.php")){
			$tpl = new OspropertyTemplate();
			$tpl->set('id',$id);
			$tpl->set('title',$title);
			$tpl->set('captcha_value',$captcha_value);
			$tpl->set('configClass',$configClass);
			$tpl->set('bootstrapHelper',$bootstrapHelper);
			$body = $tpl->fetch("request.html.tpl.php");	
			echo $body;	
		}
	}
	
	/**
	 * Send details request
	 *
	 * @param unknown_type $option
	 */
	static function sendRequestDetails($option)
		{
		global $mainframe,$configClass,$jinput;
		$db			= Factory::getDbo();
		$id			= $jinput->getInt('id',0);
		$itemid		= OSPRoute::getPropertyItemid($id);
		$user       = Factory::getUser();
		if($configClass['show_request_more_details'] == 0){
			$msg = Text::_('OS_THIS_FUNCTIONALITY_DOES_NOT_BE_ACTIVATED');
			$url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
			OSPHelper::redirect($url,$msg);
		}

        $passcaptcha        = 0;
        if($configClass['pass_captcha_with_logged_user'] == 1 && $user->id > 0)
		{
            $passcaptcha    = 1;
        }
        if($configClass['captcha_in_request_more_details'] == 1 && $passcaptcha == 0) 
		{
            
			$captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
			$res           = Captcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
			if (!$res)
			{
				$msg = Text::_('OS_YOU_SHOULD_PASS_SECURITY_STEP_FIRST');
				$url = Route::_('index.php?option=com_osproperty&task=property_details&id=' . $id . '&Itemid=' . $itemid);
				OSPHelper::redirect($url, $msg);
			}
			
        }

        $session = Factory::getSession();
        $pid = $session->get('pid',0);
        if(($pid == 0) or ($pid != $id)){
            $msg = Text::_('OS_EMAIL_CANT_BE_SENT');
            $url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
            OSPHelper::redirect($url,$msg);
        }

		$query = "Select a.email from #__osrs_agents as a inner join #__osrs_properties as b on b.agent_id = a.id where b.id = '$id'";
		$db->setQuery($query);
		$agent_email = $db->loadResult();
		//echo $agent_email;
		$subject = OSPHelper::getStringRequest('subject','','');//JRequest::getVar('subject','');
		$subject1 = $subject;
		$your_phone = OSPHelper::getStringRequest('your_phone','',''); //JRequest::getVar('your_phone','');
		$requestmessage = htmlspecialchars($_POST['requestmessage']);
		$requestyour_email = OSPHelper::getStringRequest('requestyour_email','','');//JRequest::getVar('requestyour_email','');
		$requestyour_name = OSPHelper::getStringRequest('requestyour_name','','');//JRequest::getVar('requestyour_name','');
		$sj = "";
		switch ($subject){
			case "1":
				$sj .= Text::_('OS_REQUEST_1');
			break;
			case "2":
				$sj .= Text::_('OS_REQUEST_2');
			break;
			case "3":
				$sj .= Text::_('OS_REQUEST_3');
			break;
			case "4":
				$sj .= Text::_('OS_REQUEST_4');
			break;
			case "5":
				$sj .= Text::_('OS_REQUEST_5');
			break;
			case "6":
				$sj .= Text::_('OS_REQUEST_6');
			break;
			case "7":
				$sj .= Text::_('OS_REQUEST_7');
			break;
			default:
				$sj .= Text::_('OS_REQUEST_MORE_INFOR');
			break;
		}
		$requestType = $subject;
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$pro_name = OSPHelper::getLanguageFieldValue($property,'pro_name');
		$ref 	  = $property->ref;
		if(($ref != "") and ($configClass['show_ref'] == 1)){
			$pro_name = "#".$ref." - ".$pro_name;
		}
		
		$subject = Text::_('OS_FROM')." ".$configClass['general_bussiness_name']." - ".$sj;
		$subject = Text::_('OS_REQUEST_MORE_INFOR')." {".$pro_name."} ".Text::_('OS_FROM')." {".$configClass['general_bussiness_name']."}";
		$msg .= Text::_('OS_CUSTOMER').": ".$requestyour_name." <BR />";
		$msg .= Text::_('OS_EMAIL').": ".$requestyour_email." <BR />";
		if($your_phone != ""){
			$msg .= Text::_('OS_PHONE').": ".$your_phone." <BR />";	
		}
		if($subject1 == 7){
			$checkin = $jinput->getString('requestmoredetailscheckin','');
			$checkout = $jinput->getString('requestmoredetailscheckout','');
			$guests = $jinput->getString('nguest','');
			if($checkin != ""){
				$msg .= Text::_('OS_CHECKIN').": ".$checkin."<BR />";
			}
			if($checkout != ""){
				$msg .= Text::_('OS_CHECKOUT').": ".$checkout."<BR />";
			}
			if($guests != ""){
				$msg .= Text::_('OS_NGUEST').": ".$guests."<BR />";
			}
		}
		$msg .= Text::_('OS_MESSAGE').": ".$requestmessage." <BR />";
		$msg .= Text::_('OS_PROPERTY')." ID: ".$id."<BR />";
		$url = Route::_("index.php?option=com_osproperty&task=property_details&id=".$id."&Itemid=".$itemid);
		$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$url;
		$msg .= Text::_('OS_PROPERTY')." URL: <a href='".$url."'>".$url."</a>";

		//store in db
		if($configClass['log_request'])
		{
			$request					= Table::getInstance('Requests','OspropertyTable');
			$request->id				= 0;
			$request->pid				= $id;
			$request->request_type		= $requestType;
			$request->request_name		= $requestyour_name;
			$request->request_email		= $requestyour_email;
			$request->request_phone		= $your_phone;
			$request->subject			= $sj;
			$request->request_body		= $msg;
			$request->number_adults		= $guests;
			$request->check_in			= $checkin;
			$request->check_out			= $checkout;
			$request->requested_on		= HTMLHelper::_('date', time() , 'Y-m-d');
			$request->store();
		}

		$emailfrom = $configClass['general_bussiness_email'];
        if($emailfrom == ""){
            $config = new JConfig();
            $emailfrom = $config->mailfrom;
        }
		$business_name = $configClass['general_bussiness_name'];

		$mailer = Factory::getMailer();
        $replyto = [];
        if(MailHelper::isEmailAddress($requestyour_email)) 
		{
            $replyto[] = $requestyour_email;
        }
        if(MailHelper::isEmailAddress($agent_email)) 
		{
			try
			{
				$mailer->sendMail($emailfrom, $business_name, $agent_email, $subject, $msg, 1, null, null, null, $replyto);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
        }
		if(($configClass['copy_admin'] == 1) && (MailHelper::isEmailAddress($configClass['notify_email'])))
		{
            $mailer = Factory::getMailer();
			try
			{
				$mailer->sendMail($emailfrom,$business_name,$configClass['notify_email'],$subject,$msg,1);
			}
			catch (Exception $e)
			{
				Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
			}
        }
		//update into database
		$db->setQuery("Update #__osrs_properties set total_request_info = total_request_info + 1  where id = '$id'");
		$db->execute();
		
		$tmpl = $jinput->getString('tmpl','');
		if($tmpl == "component"){
			?>
			<div class="padding20 width90pc">
				<h2>
					<?php echo Text::_('OS_YOUR_REQUEST_HAS_BEEN_SENT');?>
				</h2>
			</div>
			<?php
		}else{
			$msg = Text::_('OS_YOUR_REQUEST_HAS_BEEN_SENT');
			$url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
			OSPHelper::redirect($url,$msg);
			
		}
	}
	
	
	static function removesearchlist($option){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if(intval($user->id) == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$chkid = $jinput->get('cid',array(),'ARRAY');
		if(count($chkid) > 0){
			for($i=0;$i<count($chkid);$i++){
				$id = $chkid[$i];
				$db->setQuery("DELETE FROM #__osrs_user_list WHERE id = '$id'");
				$db->execute();
				$db->setQuery("DELETE FROM #__osrs_user_list_details WHERE list_id = '$id'");
				$db->execute();
			}
		}
		$msg = Text::_('OS_SEARCH_LIST_REMOVED');
		$needs = [];
		$needs[] = "rsearchlist";
		$needs[] = "property_searchlist";
		$itemid = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_searchlist&Itemid=".$itemid),$msg);
	}
	
	/**
	 * Show search list
	 *
	 * @param unknown_type $option
	 */
	static function showSearchList($option){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		$user = Factory::getUser();
		if(intval($user->id) == 0){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		$db->setQuery("Select * from #__osrs_user_list where user_id = '$user->id' order by created_on desc");
		$lists = $db->loadObjectList();
		HTML_OspropertyListing::searchList($option,$lists);
	}
	
	
	static function saveListName($option){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDbo();
		$i = $jinput->getInt('id',0);
		$list_id = $jinput->getInt('list_id',0);
		$list_name = OSPHelper::getStringRequest('list_name','',''); //JRequest::getVar('list_name','');
		$itemid = $jinput->getInt('Itemid',0);
		$db->setQuery("UPDATE #__osrs_user_list set list_name = '$list_name' where id = '$list_id'");
		$db->execute();
		
		$db->setQuery("Select * from #__osrs_user_list where id = '$list_id'");
		$list = $db->loadObject();
		?>
		<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_advsearch&list=1&list_id=<?php echo $list->id?>&Itemid=<?php echo $itemid;?>" title="<?php echo Text::_('OS_GET_SEARCH_RESULT')?>">
			<?php echo $list->list_name?>
		</a>
		<a href="javascript:showInputbox('<?php echo $i?>')">
			<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/edit.png">
		</a>
		<div id="div_input_<?php echo $i?>" class="nodisplay">
			<input type="text" name="list_name_<?php echo $i?>" class="inputbox" value="<?php echo $list->list_name?>" size="40">
			<a href="javascript:saveListName('<?php echo $i?>','<?php echo $list->id?>')">
				<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/savefavorites.png">
			</a>
		</div>
		<?php
		
	}

    /**
     * Cancel the alert email
     */
    static function cancelAlertEmail(){
        global $bootstrapHelper, $mainframe,$jinput;
        $db = Factory::getDbo();
        $list_id = $jinput->getString('list_id','');
        if($list_id == ""){
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }else{
            $list_id_array = explode("|",$list_id);
            $list_id = $list_id_array[1];
            $list_id_md5 = $list_id_array[0];
            if($list_id_md5 != md5($list_id)){
                throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
            }else{
                $db->setQuery("Update #__osrs_user_list set receive_email = '0' where id = '$list_id'");
                $db->execute();
                OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_WILL_NOT_RECEIVE_THE_ALERT_EMAILS_ANYMORE'));
            }
        }

    }
	
	/**
	 * Save search list
	 *
	 * @param unknown_type $option
	 */
	static function saveSearchList($option)
    {
        global $bootstrapHelper, $mainframe, $configClass,$jinput;
        $db = Factory::getDbo();
        $keyword        = OSPHelper::getStringRequest('keyword', '');
        $isFeatured     = $jinput->getInt('isFeatured', 0);
        $isSold         = $jinput->getInt('isSold', 0);
        $agent_type     = $jinput->getInt('agent_type', -1);
        $category_id    = $jinput->getInt('category_id', 0);
        $category_ids   = $jinput->get('category_ids',array(),'ARRAY');
        $amenities      = $jinput->get('amenities',array(),'ARRAY');
        $property_type  = $jinput->getInt('property_type', 0);
        $property_types = $jinput->get('property_types',array(),'ARRAY');
        $country_id     = $jinput->getString('country_id', HelperOspropertyCommon::getDefaultCountry());
        $city           = $jinput->getInt('city', 0);
        $state_id       = $jinput->getInt('state_id', 0);
        $nbed           = $jinput->getInt('nbed', 0);
        $nbath          = $jinput->getInt('nbath', 0);
        $price          = $jinput->getInt('price', '');
        $nroom          = $jinput->getInt('nroom', 0);
        $nfloors        = $jinput->getInt('nfloors', 0);
        $min_price      = $jinput->getInt('min_price', 0);
        $max_price      = $jinput->getInt('max_price', 0);
        $sqft_min       = $jinput->getInt('sqft_min', 0);
        $sqft_max       = $jinput->getInt('sqft_max', 0);
        $lotsize_min    = $jinput->getInt('lotsize_min', 0);
        $lotsize_max    = $jinput->getInt('lotsize_max', 0);
		$postcode		= $jinput->getInt('postcode',0);
		$radius			= $jinput->getInt('radius',0);
		$se_geoloc		= $jinput->getInt('se_geoloc',0);
        $address        = OSPHelper::getStringRequest('address', '', 'post');
        $sortby         = $jinput->getString('sortby', $configClass['adv_sortby']);
        $orderby        = $jinput->getString('orderby', $configClass['adv_orderby']);
        $lists['address_value'] = $address;
        $param = [];
        if ($address != "") {
            $param[] = "add:" . $address;
        }
        if ($category_id > 0) {
            $param[] = "catid:" . $category_id;
        }
        if (count($category_ids) > 0) {
            foreach ($category_ids as $cat_id) {
                $param[] = "catid:" . $cat_id;
            }
        }
        if (count($amenities) > 0) {
            foreach ($amenities as $amenity) {
                $param[] = "amenity:" . $amenity;
            }
        }
        if ($property_type > 0) {
            $param[] = "type:" . $property_type;
        }
        if (count($property_types) > 0) {
            foreach ($property_types as $type_id) {
                $param[] = "type:" . $type_id;
            }
        }
        if ($city > 0) {
            $param[] = "city:" . $city;
        }
        if ($country_id > 0) {
            $param[] = "country:" . $country_id;
        }
        if ($state_id > 0) {
            $param[] = "state:" . $state_id;
        }
        if ($nbath > 0) {
            $param[] = "nbath:" . $nbath;
        }
        if ($nbed > 0) {
            $param[] = "nbed:" . $nbed;
        }
        if ($nroom > 0) {
            $param[] = "nroom:" . $nroom;
        }
        if ($nfloors > 0) {
            $param[] = "nfloors:" . $nfloors;
        }
        if ($price > 0) {
            $param[] = "price:" . $price;
        }
        if ($agent_type > 0) {
            $param[] = "agent_type:" . $agent_type;
        }
        if ($min_price > 0) {
            $param[] = "min_price:" . $min_price;
        }
        if ($max_price > 0) {
            $param[] = "max_price:" . $max_price;
        }
        if ($sqft_min > 0) {
            $param[] = "sqft_min:" . $sqft_min;
        }
        if ($sqft_max > 0) {
            $param[] = "sqft_max:" . $sqft_max;
        }
        if ($lotsize_min > 0) {
            $param[] = "lotsize_min:" . $lotsize_min;
        }
        if ($lotsize_max > 0) {
            $param[] = "lotsize_max:" . $lotsize_max;
        }
        if ($isFeatured > 0) {
            $param[] = "featured:" . $isFeatured;
        }
        if ($isSold > 0) {
            $param[] = "sold:" . $isSold;
        }
        if ($keyword != "") {
            $param[] = "keyword:". $keyword;
        }
		if ($postcode > 0){
			$param[] = "postcode:".$postcode;
		}
		if ($radius > 0){
			$param[] = "radius:".$radius;
		}
		if ($se_geoloc == 1){
			$param[] = "se_geoloc:".$se_geoloc;
		}
		$param[] = "sortby:".$sortby;
		$param[] = "orderby:".$orderby;
		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0){
			$extrafieldSql = [];
			for($i=0;$i<count($groups);$i++){
				$group = $groups[$i];
				$db->setQuery("Select * from #__osrs_extra_fields where group_id = '$group->id' and published = '1' and searchable = '1' order by ordering");
				$fields = $db->loadObjectList();
				$group->fields = $fields;
				
				for($j=0;$j<count($fields);$j++){
					$field = $fields[$j];
					//check do search
					$check = HelperOspropertyFields::checkField($field);
					if($check){
						$dosearch = 1;
						$param[]  = HelperOspropertyFields::getFieldParam($field);
					}
				}
			}
		}
		
		$user = Factory::getUser();
		$list_name = "List: ".date("Y-m-d H:i:s",time());
		$created_on = date("Y-m-d H:i:s",time());
		$row = Table::getInstance('Searchlist','OspropertyTable');
		$row->id = 0;
		$row->user_id = $user->id;
		$row->list_name = $list_name;
		$row->created_on = $created_on;
        $current_language = Factory::getLanguage();
        $language_tag = $current_language->getTag();
        $row->lang = $language_tag;

		$row->store();
		
		$list_id = $db->insertid();
		
		if(count($param) > 0){
			$numArr = array('0','1','2','3','4','5','6','7','8','9');
			for($i=0;$i<count($param);$i++){
				$p = $param[$i];
				$pArr = explode(":",$p);
				$type = "";
				if(count($pArr) == 3){
					$name = $pArr[0];
					$type = $pArr[1];
					$value = $pArr[2];
					$firstChar = substr($name,0,1);
				}else{
					$name = $pArr[0];
					$value = $pArr[1];
					$firstChar = substr($name,0,1);
				}
				if(in_array($firstChar,$numArr)){
					//is the extra field
					$db->setQuery("INSERT INTO #__osrs_user_list_details (id,list_id,field_id,field_type,search_type,search_param) VALUES (NULL,'$list_id','$name','1','$type','$value')");
					$db->execute();
				}else{
					//is the normal field
					$db->setQuery("INSERT INTO #__osrs_user_list_details (id,list_id,field_id,field_type,search_type,search_param) VALUES (NULL,'$list_id','$name','0','$type','$value')");
					$db->execute();
				}
			}
		}
		$msg = Text::_('OS_NEW_SEARCH_LIST_CREATED');
		$needs = [];
		$needs[] = "ladvsearch";
		$needs[] = "property_advsearch";
		$itemid  = OSPRoute::getItemid($needs);
		$itemid  = OSPRoute::confirmItemid($itemid,"ladvsearch");
		if($itemid == 0){
			OSPRoute::confirmItemid($itemid,"property_advsearch");
		}
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_advsearch&list=1&list_id=$list_id&Itemid=".$itemid),$msg);
	}
	
	
	static function updateSearchList($option){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDbo();
        $keyword        = OSPHelper::getStringRequest('keyword', '','post');
        $isFeatured		= $jinput->getInt('isFeatured',0);
        $isSold			= $jinput->getInt('isSold',0);
        $list_id		= $jinput->getInt('list_id',0);
        $agent_type		= $jinput->getInt('agent_type',-1);
        $category_id 	= $jinput->getInt('category_id',0);
        $amenities		= $jinput->get('amenities',array(),'ARRAY');
        $category_ids   = $jinput->get('category_ids',array(),'ARRAY');
        $property_types = $jinput->get('property_types',array(),'ARRAY');
        $property_type	= $jinput->getInt('property_type',0);
        $country_id		= $jinput->getString('country_id',HelperOspropertyCommon::getDefaultCountry());
        $city			= $jinput->getInt('city',0);
        $state_id		= $jinput->getInt('state_id',0);
        $nbed			= $jinput->getInt('nbed',0);
        $nbath			= $jinput->getInt('nbath',0);
        $price			= $jinput->getInt('price','');
        $nroom 			= $jinput->getInt('nroom',0);
        $nfloors		= $jinput->getInt('nfloors',0);
        $min_price		= $jinput->getInt('min_price',0);
        $max_price		= $jinput->getInt('max_price',0);
        $sqft_min 		= $jinput->getInt('sqft_min',0);
        $sqft_max 		= $jinput->getInt('sqft_max',0);
        $lotsize_min 	= $jinput->getInt('lotsize_min',0);
        $lotsize_max 	= $jinput->getInt('lotsize_max',0);
		$postcode		= $jinput->getInt('postcode',0);
		$radius			= $jinput->getInt('radius',0);
		$se_geoloc		= $jinput->getInt('se_geoloc',0);
        $address        = OSPHelper::getStringRequest('address', '', 'post');
        $sortby			= OSPHelper::getStringRequest('sortby',$configClass['adv_sortby']);
        $orderby		= OSPHelper::getStringRequest('orderby',$configClass['adv_orderby']);
        $address		= OSPHelper::getStringRequest('address','','post');
		$lists['address_value'] = $address;
		$param = [];
		if($address != ""){
			$param[] = "add:".$address;
		}
		if($category_id > 0){
			$param[] = "catid:".$category_id;
		}
		if(count($category_ids) > 0){
			foreach ($category_ids as $cat_id){
				$param[] = "catid:".$cat_id;	
			}
		}
		if(count($amenities) > 0){
			foreach ($amenities as $amenity){
				$param[] = "amenity:".$amenity;	
			}
		}
		if($property_type > 0){
			$param[] = "type:".$property_type;
		}
		if(count($property_types) > 0){
			foreach ($property_types as $type_id){
				$param[] = "type:".$type_id;	
			}
		}
		if($city > 0){
			$param[] = "city:".$city;
		}
		if($country_id > 0){
			$param[] = "country:".$country_id;
		}
		if($state_id > 0){
			$param[] = "state:".$state_id;
		}
		if($nbath > 0){
			$param[] = "nbath:".$nbath;
		}
		if($nbed > 0){
			$param[] = "nbed:".$nbed;
		}
		if($nroom > 0){
			$param[] = "nroom:".$nroom;
		}
		if($nfloors > 0){
			$param[] = "nfloors:".$nfloors;
		}
		if($price > 0){
			$param[] = "price:".$price;
		}
		if($agent_type > 0){
			$param[] = "agent_type:".$agent_type;
		}
		if($min_price > 0){
			$param[] = "min_price:".$min_price;
		}
		if($max_price > 0){
			$param[] = "max_price:".$max_price;
		}
		if($sqft_min > 0){
			$param[] = "sqft_min:".$sqft_min;
		}
		if($sqft_max > 0){
			$param[] = "sqft_max:".$sqft_max;
		}
		if($lotsize_min > 0){
			$param[] = "lotsize_min:".$lotsize_min;
		}
		if($lotsize_max > 0){
			$param[] = "lotsize_max:".$lotsize_max;
		}
		if($isFeatured > 0){
			$param[] = "featured:".$isFeatured;
		}
		if($isSold > 0){
			$param[] = "sold:".$isSold;
		}
        if ($keyword != "") {
            $param[] = "keyword:". $keyword;
        }
		if ($postcode > 0){
			$param[] = "postcode:".$postcode;
		}
		if ($radius > 0){
			$param[] = "radius:".$radius;
		}
		if ($se_geoloc == 1){
			$param[] = "se_geoloc:".$se_geoloc;
		}
		$param[] = "sortby:".$sortby;
		$param[] = "orderby:".$orderby;
		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0){
			$extrafieldSql = [];
			for($i=0;$i<count($groups);$i++){
				$group = $groups[$i];
				$db->setQuery("Select * from #__osrs_extra_fields where group_id = '$group->id' and published = '1' and searchable = '1' order by ordering");
				$fields = $db->loadObjectList();
				$group->fields = $fields;
				
				for($j=0;$j<count($fields);$j++){
					$field = $fields[$j];
					//check do search
					$check = HelperOspropertyFields::checkField($field);
					if($check){
						$dosearch = 1;
						$param[]		 = HelperOspropertyFields::getFieldParam($field);
					}
				}
			}
		}
		
		$user = Factory::getUser();
		$list_name = "List: ".date("Y-m-d H:i:s",time());
		$created_on = date("Y-m-d H:i:s",time());
		$row = Table::getInstance('Searchlist','OspropertyTable');
		$row->id = $list_id;
		$row->user_id = $user->id;
		$row->list_name = $list_name;
		$row->created_on = $created_on;
        $current_language = Factory::getLanguage();
        $language_tag = $current_language->getTag();
        $row->lang = $language_tag;
		$row->store();
		
		$db->setQuery("DELETE FROM #__osrs_user_list_details WHERE list_id = '$list_id'");
		$db->execute();
		
		if(count($param) > 0){
			$numArr = array('0','1','2','3','4','5','6','7','8','9');
			for($i=0;$i<count($param);$i++){
				$p = $param[$i];
				$pArr = explode(":",$p);
				if(count($pArr) == 3){
					$name = $pArr[0];
					$type = $pArr[1];
					$value = $pArr[2];
					
				}else{
					$name = $pArr[0];
					$value = $pArr[1];
				}
				$firstChar = substr($name,0,1);
				if(in_array($firstChar,$numArr)){
					//is the extra field
					$db->setQuery("INSERT INTO #__osrs_user_list_details (id,list_id,field_id,field_type,search_type,search_param) VALUES (NULL,'$list_id','$name','1','$type','$value')");
					$db->execute();
				}else{
					//is the normal field
					$db->setQuery("INSERT INTO #__osrs_user_list_details (id,list_id,field_id,field_type,search_type,search_param) VALUES (NULL,'$list_id','$name','0','$type','$value')");
					$db->execute();
				}
			}
		}
		$msg = Text::_('OS_SEARCH_LIST_SAVED');
		$needs = [];
		$needs[] = "ladvsearch";
		$needs[] = "property_advsearch";
		$itemid  = OSPRoute::getItemid($needs);
		$itemid  = OSPRoute::confirmItemid($itemid,"ladvsearch");
		if($itemid == 0){
			OSPRoute::confirmItemid($itemid,"property_advsearch");
		}
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_advsearch&list=1&list_id=$list_id&Itemid=".$itemid),$msg);
	}
	
	/**
	 * Advanced search function
	 *
	 * @param unknown_type $option
	 */
	
	static function advSearch($option){
		global $bootstrapHelper, $mainframe,$configClass,$lang_suffix,$jinput,$bootstrapHelper;
		$session = Factory::getSession();
		$db = Factory::getDbo();
        $prefix_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://');
        $url = $prefix_url.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];


        $user = Factory::getUser();
        $agent_id_permission = 0;
        if($user->id > 0){
            if(HelperOspropertyCommon::isAgent()){
                $agent_id_permission = HelperOspropertyCommon::getAgentID();
            }
        }
		//condition in config
		//check to see if OS Calendar intergrated
		if(file_exists(JPATH_ROOT."/components/com_oscalendar/oscalendar.php")){
			if($configClass['integrate_oscalendar'] == 1)
			{
				$start_time		= $db->escape($jinput->getString('start_time',''));
				$end_time		= $db->escape($jinput->get('end_time',''));
				if($start_time != ""){
					@setcookie('start_time',$start_time,time() + 3600,'/');
				}
				else
				{
					@setcookie('start_time','',time() - 3600,'/');
				}
				if($end_time != "")
				{
					@setcookie('end_time',$end_time,time() + 3600,'/');
				}
				else
				{
					@setcookie('start_time','',time() - 3600,'/');
				}
			}
		}
		
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_ADVSEARCH'));
		$dosearch  		= 0;
		$list = $jinput->getInt('list',0);
		if($list == 1){
			$temp_category_ids = [];
			$temp_type_ids	   = [];
			$temp_amen_ids     = [];
			$list_id = $jinput->getInt('list_id',0);
			$db->setQuery("Select * from #__osrs_user_list_details where list_id = '$list_id'");
			$list_details = $db->loadObjectList();
			for($i=0;$i<count($list_details);$i++){
				$list_detail = $list_details[$i];
				switch ($list_detail->field_id){
                    case "keyword":
                        $jinput->set('keyword',$list_detail->search_param);
                        break;
					case "add":
                        $jinput->set('address',$list_detail->search_param);
					break;
					case "agent_type":
                        $jinput->set('agent_type',$list_detail->search_param);
					break;
					case "catid":
						$temp_category_ids[] = $list_detail->search_param;
					break;
					case "type":
						//JRequest::setVar('property_type',$list_detail->search_param);
						$temp_type_ids[] = $list_detail->search_param;
					break;
					case "amenity":
						$temp_amen_ids[] = $list_detail->search_param;
					break;
					case "country":
                        $jinput->set('country_id',$list_detail->search_param);
					break;
					case "state":
                        $jinput->set('state_id',$list_detail->search_param);
                        break;
                    case "city":
                        $jinput->set('city',$list_detail->search_param);
                        break;
                    case "nbath":
                        $jinput->set('nbath',$list_detail->search_param);
                        break;
                    case "nbed":
                        $jinput->set('nbed',$list_detail->search_param);
                        break;
                    case "price":
                        $jinput->set('price',$list_detail->search_param);
                        break;
                    case "min_price":
                        $jinput->set('min_price',$list_detail->search_param);
                        break;
                    case "max_price":
                        $jinput->set('max_price',$list_detail->search_param);
                        break;
                    case "nroom":
                        $jinput->set('nroom',$list_detail->search_param);
                        break;
                    case "nfloors":
                        $jinput->set('nfloors',$list_detail->search_param);
                        break;
                    case "sqft_min":
                        $jinput->set('sqft_min',$list_detail->search_param);
                        break;
                    case "sqft_max":
                        $jinput->set('sqft_max',$list_detail->search_param);
                        break;
                    case "lotsize_min":
                        $jinput->set('lotsize_min',$list_detail->search_param);
                        break;
                    case "lotsize_max":
                        $jinput->set('lotsize_max',$list_detail->search_param);
                        break;
                    case "featured":
                        $jinput->set('isFeatured',$list_detail->search_param);
                        break;
                    case "sold":
                        $jinput->set('isSold',$list_detail->search_param);
                        break;
                    case "sortby":
                        $jinput->set('sortby',$list_detail->search_param);
                        break;
                    case "orderby":
                        $jinput->set('orderby',$list_detail->search_param);
                        break;
					case "radius":
						$jinput->set('radius_search',$list_detail->search_param);
						break;
					case "postcode":
						$jinput->set('postcode',$list_detail->search_param);
						break;
					case "created_from":
						$jinput->set('created_from',$list_detail->search_param);
						break;
					case "created_to":
						$jinput->set('created_to',$list_detail->search_param);
						break;
					case "se_geoloc":
						$jinput->set('se_geoloc',$list_detail->search_param);
						break;
					default:
						HelperOspropertyFields::setFieldValue($list_detail);
					break;
				}
			}
			
            if(count($temp_category_ids) > 0){
                $jinput->set('category_ids',$temp_category_ids);
            }
            if(count($temp_type_ids) > 0){
                $jinput->set('property_types',$temp_type_ids);
            }
            if(count($temp_amen_ids) > 0){
                $jinput->set('amenities',$temp_amen_ids);
            }
		}
		
		$adv_type_ids = $configClass['adv_type_ids'];
		if(($adv_type_ids != "") && ($adv_type_ids != "0"))
		{
			$adv_type_ids = explode("|",$adv_type_ids);
			if(count($adv_type_ids) > 0)
			{
				$adv_type_ids = $adv_type_ids[0];
			}
		}
		
		$property_type	= $jinput->getInt('property_type',0);
		if($property_type > 0)
		{
			$adv_type		= $jinput->getInt('adv_type',$property_type);
		}
		else
		{
			$adv_type		= $jinput->getInt('adv_type',$adv_type_ids);
		}

		if($adv_type > 0)
		{
			$adv_type_ids = $adv_type ;
		}

		$property_types	= [];
		$property_types	= $jinput->get('property_types',array(),'ARRAY');
		$property_types	= ArrayHelper::toInteger($property_types);
		if(!in_array($property_type,$property_types) && $property_type > 0)
		{
			$property_types[count($property_types)] = $property_type;
		}

		if(count($property_types) == 0 && $adv_type_ids > 0)
		{
			$property_types[] = $adv_type_ids;
		}


        $category_id 	= $jinput->getInt('category_id',0);
        $category_ids	=  $jinput->get('category_ids',array(),'ARRAY');
		$category_ids	= ArrayHelper::toInteger($category_ids);
        if(!in_array($category_id,$category_ids)){
            $category_ids[count($category_ids)] = $category_id;
        }

        $agent_type		= $jinput->getInt('agent_type',-1);
        $country_id		= $jinput->getInt('country_id',HelperOspropertyCommon::getDefaultCountry());
        $city			= $jinput->getInt('city',0);
        $state_id		= $jinput->getInt('state_id',0);
        $nbed			= $jinput->getInt('nbed',0);
        $nbath			= $jinput->getInt('nbath',0);
        $price			= $jinput->getInt('price',0);
        $nroom 			= $jinput->getInt('nroom',0);
        $nfloors		= $jinput->getInt('nfloors',0);
		$address		= OSPHelper::getStringRequest('address','','get');
		if($address == ""){
			$address		= OSPHelper::getStringRequest('address','','post');
		}
		$address		= $db->escape($address);
		$keyword		= OSPHelper::getStringRequest('keyword','','get'); //JRequest::getVar('keyword','','','string');
		if($keyword == ""){
			$keyword		= OSPHelper::getStringRequest('keyword','','post'); 
		}		
		$keyword		= $db->escape($keyword);
        $isFeatured		= $jinput->getInt('isFeatured',0);
        $isSold			= $jinput->getInt('isSold',0);
        $sortby			= OSPHelper::getStringRequest('sortby',$configClass['adv_sortby']);
        $orderby		= OSPHelper::getStringRequest('orderby',$configClass['adv_orderby']);
        $orderbyArray   = array('a.pro_name','a.created','a.modified','a.price','a.isFeatured','a.square_feet');
		if($configClass['show_ref'] == 1){
			$orderbyArray[] = 'a.ref';
		}
        if(!in_array($sortby, $orderbyArray)){
            $sortby = "a.created";
            $orderby = "desc";
        }
        $min_price		= $jinput->getInt('min_price',0);
        $max_price   	= $jinput->getInt('max_price',0);
        $sqft_min		= $jinput->getInt('sqft_min',0);
        $sqft_max		= $jinput->getInt('sqft_max',0);
        $lotsize_min	= $jinput->getInt('lotsize_min',0);
        $lotsize_max	= $jinput->getInt('lotsize_max',0);
		$postcode		= OSPHelper::getStringRequest('postcode','');
		$se_geoloc      = $jinput->getInt('se_geoloc',0);
		$radius			= $jinput->getInt('radius_search',50);
		$created_from	= $jinput->getString('created_from','');
		$created_to		= $jinput->getString('created_to','');

		$amenities		= $jinput->get('amenities',array(),'ARRAY');
		$amenities		= ArrayHelper::toInteger($amenities);
		if(count($amenities) > 0){
			
			$amenities_str = implode(",",$amenities);
			
			if($amenities_str != ""){
				$amenities_sql = " AND a.id in (SELECT pro_id FROM #__osrs_property_amenities WHERE amen_id in ($amenities_str) group by pro_id having count(pro_id) = ".count($amenities).")";
				$dosearch = 1;
			}else{
				$amenities_sql = "";	
			}
		}else{
			$amenities_sql = "";
		}
        $limitstart		= $jinput->getInt('limitstart',0);
        $limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
			
		$lists['address_value'] = $address;
		$lists['keyword_value'] = $keyword;
		$param = [];
		if($address != ""){
			$dosearch = 1;
			$param[] = "add:".$address;
		}
		if($keyword != ""){
			$dosearch = 1;
			$param[] = "keyword:".$keyword;
		}
		if($category_id > 0){
			$dosearch = 1;
			$param[] = "catid:".$category_id;
		}
		if(count($category_ids) > 0){
			foreach ($category_ids as $cat_id){
				if($cat_id > 0){
					$dosearch = 1;
					$param[] = "catid:".$cat_id;
				}
			}
		}
		$types = [];
		if($property_type > 0){
			$dosearch = 1;
			$param[] = "type:".$property_type;
			$types[] = $property_type;
		}
		if(count($property_types) > 0){
			foreach ($property_types as $type_id){
				if($type_id > 0){
					$dosearch = 1;
					$param[] = "type:".$type_id;
					$types[] = $type_id;
				}
			}
		}
		if(($country_id > 0) and (HelperOspropertyCommon::checkCountry())){
			$dosearch = 1;
			$param[] = "country:".$country_id;
		}else{
			//$dosearch = 0;
			$param[] = "country:".$country_id;
		}
		if($city > 0){
			$dosearch = 1;
			$param[] = "city:".$city;
		}
		if($state_id > 0){
			$dosearch = 1;
			$param[] = "state:".$state_id;
		}
		if($nbath > 0){
			$dosearch = 1;
			$param[] = "nbath:".$nbath;
		}
		if($nbed > 0){
			$dosearch = 1;
			$param[] = "nbed:".$nbed;
		}
		if($nroom > 0){
			$dosearch = 1;
			$param[] = "nroom:".$nroom;
		}
		if($nfloors > 0){
			$dosearch = 1;
			$param[] = "nfloors:".$nfloors;
		}
		if($price > 0){
			$dosearch = 1;
			$param[] = "price:".$price;
		}
		if($min_price > 0){
			$dosearch = 1;
			$param[] = "min_price:".$min_price;
		}
		if($max_price > 0){
			$dosearch = 1;
			$param[] = "max_price:".$max_price;
		}
		if($sqft_min > 0){
			$dosearch = 1;
			$param[] = "sqft_min:".$sqft_min;
		}
		if($sqft_max > 0){
			$dosearch = 1;
			$param[] = "sqft_max:".$sqft_max;
		}
		if($lotsize_min > 0){
			$dosearch = 1;
			$param[] = "lotsize_min:".$lotsize_min;
		}
		if($lotsize_max > 0){
			$dosearch = 1;
			$param[] = "lotsize_max:".$lotsize_max;
		}
		if($isFeatured == 1){
			$dosearch = 1;
			$param[] = "featured:".$isFeatured;
		}
		if($isSold > 0){
			$dosearch = 1;
			$param[] = "sold:".$isSold;
		}
		if($agent_type >= 0){
			$dosearch = 1;
			$param[] = "agent_type:".$agent_type;
		}
		if($created_from != ""){
			$dosearch = 1;
			$param[] = "created_from:".$created_from;
		}
		if($created_to != ""){
			$dosearch = 1;
			$param[] = "created_to:".$created_to;
		}
		$search_radius_v = "";
		$search_radius_h = "";
		if($configClass['locator_radius_type'] == 0){
			$radius_unit_v = 3959;
		}else{
			$radius_unit_v = 6371;
		}
		if($se_geoloc == 1){
			$param[] = "mylocation:".$se_geoloc;
			$dosearch = 1;
			$user_latlog = explode('_', $_COOKIE["djcf_latlon"]);
			$search_radius_v = ', ( '.$radius_unit_v.' * acos( cos( radians('.$user_latlog[0].') ) * cos( radians( a.lat_add ) ) * cos( radians( a.long_add ) - radians('.$user_latlog[1].') ) + sin( radians('.$user_latlog[0].') ) * sin( radians( a.lat_add ) ) ) ) AS distance ';
			$search_radius_h = ' HAVING distance < '.$radius.' ';
			$lists['se_geoloc'] = $se_geoloc;
		} elseif($postcode != ""){
			$lists['postcode'] = $postcode;
			$param[] = "postcode:".$agent_type;
			$dosearch = 1;
			$geocode = HelperOspropertyGoogleMap::getLocationPostCode($postcode,$country_id);
			if($geocode){
				$search_radius_v = ', ( '.$radius_unit_v.' * acos( cos( radians('.$geocode['lat'].') ) * cos( radians( a.lat_add ) ) * cos( radians( a.long_add ) - radians('.$geocode['lng'].') ) + sin( radians('.$geocode['lat'].') ) * sin( radians( a.lat_add ) ) ) ) AS distance ';
				$search_radius_h = ' HAVING distance < '.$radius.' ';
			}else{
				Factory::getApplication()->enqueueMessage(Text::_('OS_SORRY_WE_CANT_FIND_COORDINATES_FROM_POSTCODE_WE_OMIITED_RANGE_RESTRICTION'),'notice');
			}
		}

		$param[] = "sortby:".$sortby;
		$param[] = "orderby:".$orderby;
	
		$lists['show_date_range'] = 0;
		$rangeDateQuery = "";
		if($configClass['integrate_oscalendar'] == 1){
			if(file_exists(JPATH_ROOT."/components/com_oscalendar/oscalendar.php")){
				if(($start_time != "") and ($end_time != "")){
					$dosearch = 1;
					$rangeDateQuery = OSCHelper::buildDateRangeQuery($start_time, $end_time);
				}
			}
		}
		//checked do search through extra field
		//get the list of the field groups
        $access_sql = ' and access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0)
		{
			$extrafieldSql = [];
			for($i=0;$i<count($groups);$i++)
			{
				$group = $groups[$i];
				$extraSql = "";
				if(count($types) > 0)
				{
					$extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id in (".implode(",",$types).")) ";
				}elseif($adv_type > 0){
					$extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id = '$adv_type')";
				}
				$db->setQuery("Select * from #__osrs_extra_fields where group_id = '$group->id' $extraSql and published = '1' and searchable = '1' $access_sql order by ordering");
				//echo $db->getQuery();
				$fields = $db->loadObjectList();
				$group->fields = $fields;
				if(count($fields) > 0){
					for($j=0;$j<count($fields);$j++){
						$field = $fields[$j];
						//check do search
						$check = HelperOspropertyFields::checkField($field);
						if($check){
							$dosearch = 1;
							$sql = HelperOspropertyFields::buildQuery($field);
							if($sql != ""){
								$extrafieldSql[] = $sql;
								$param[]		 = HelperOspropertyFields::getFieldParam($field);
							}
						}
					}
				}
			}
		}
		//build query for searching
		if($dosearch == 1)
		{
			// Query database
			$select = "SELECT distinct a.id ".$search_radius_v.", a.*, c.name as agent_name, c.mobile as agent_mobile, c.phone as agent_phone,c.photo as agent_photo, c.optin, d.id as type_id,d.type_name$lang_suffix as type_name, e.country_name";
			//$count  = "SELECT count(a.id) ".$search_radius_v;
			$from =	 " FROM #__osrs_properties as a"
					." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
					." INNER JOIN #__osrs_types as d on d.id = a.pro_type"
					." INNER JOIN #__osrs_states as g on g.id = a.state"
					." LEFT JOIN #__osrs_cities as h on h.id = a.city"
					." LEFT JOIN #__osrs_property_categories as i on i.pid = a.id"
					." LEFT JOIN #__osrs_countries as e on e.id = a.country";
			$where = " WHERE a.published = '1' AND a.approved = '1' ";
            if($agent_id_permission > 0){
                $where .= ' and ((a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')) or (a.agent_id = "'.$agent_id_permission.'"))';
            }else {
                $where .= ' and a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
            }
			if($sortby == "a.isFeatured"){
				$Order_by = " ORDER BY $sortby $orderby,a.created desc";
			}else{
				$Order_by = " ORDER BY $sortby $orderby";
			}
			
			if($isFeatured == 1){
				$where .= " AND a.isFeatured = '1'";
			}

			if(($configClass['active_market_status'] == 1) && ($isSold > 0))
			{
				switch ($isSold)
				{
					case "1":
					case "2":
					case "3":
						$where .= " AND a.isSold = '$isSold'";
					break;
					case "4":
					case "5":
					case "6":
						$where .= " AND a.isSold <> '$isSold'";
					break;
				}
			}

			if($address != ""){
				$address = str_replace(";","",$address);
				if(strpos($address,",")){
					$addressArr = explode(",",$address);
					if(count($addressArr) > 0){
						$where .= " AND (";
						foreach ($addressArr as $address_item){
							$where .= " a.ref like '%$address_item%' OR";
							$where .= " a.pro_name$lang_suffix like '%$address_item%' OR";
							$where .= " a.address like '%$address_item%' OR";
							$where .= " a.region like '%$address_item%' OR";
							$where .= " a.postcode like '%$address_item%' OR";
							$where .= " g.state_name$lang_suffix like '%$address_item%' OR";
							$where .= " h.city$lang_suffix like '%$address_item%' OR";
						}
						$where = substr($where,0,strlen($where)-2);
						$where .= " )";	
					}
				}else{
					$where .= " AND (";
					$where .= " a.ref like '%$address%' OR";
					$where .= " a.pro_name$lang_suffix like '%$address%' OR";
					$where .= " a.address like '%$address%' OR";
					$where .= " a.region like '%$address%' OR";
					$where .= " g.state_name$lang_suffix like '%$address%' OR";
					$where .= " h.city$lang_suffix like '%$address%' OR";
					$where .= " a.postcode like '%$address%'";
					$where .= " )";
				}
				$no_search = false;
			}
			
			if($keyword != "")
			{
				$where .= " AND (";
				$where .= " a.ref like '%$keyword%' OR";
				$where .= " a.pro_name$lang_suffix like '%$keyword%' OR";
				$where .= " a.pro_small_desc$lang_suffix like '%$keyword%' OR";
				$where .= " a.pro_full_desc$lang_suffix like '%$keyword%' OR";
				$where .= " a.note like '%$keyword%' OR";
				$where .= " a.postcode like '%$keyword%' OR";
				$where .= " g.state_name$lang_suffix like '%$keyword%' OR";
				$where .= " h.city$lang_suffix like '%$keyword%' OR";
				$where .= " a.ref like '%$keyword%'";
				
				$where .= " )";
				$no_search = false;
			}
			if (count($category_ids) >  0)
			{
				$categoryArr = [];
				foreach ($category_ids as $category_id)
				{
					if($category_id > 0)
					{
						$categoryArr = HelperOspropertyCommon::getSubCategories($category_id,$categoryArr);
						$no_search = false;
					}
				}
				$temp = [];
				if(count($categoryArr) > 0)
				{
					foreach($categoryArr as $cat_id)
					{
						$temp[] = " i.category_id = '$cat_id'";
					}
					$tempStr = implode(" or ",$temp);
					$where .= " AND (".$tempStr.")";
				}
			}

			if (count($property_types) >  0)
			{
				$no_search = false;
				//$type_ids = implode(",",$property_types);
				$tempArr = [];
				foreach ($property_types as $type_id)
				{
					if($type_id > 0)
					{
						$tempArr[] = "$type_id";
					}
				}
				if(count($tempArr) > 0)
				{
					$temp_sql = implode(",",$tempArr);
					$where .= " AND a.pro_type in (".$temp_sql.")";
				}
			}

			if ($country_id > 0)		{$where .= " AND a.country = '$country_id'";		$no_search = false;}
			if ($city > 0)				{$where .= " AND a.city = '$city'";					$no_search = false;}
			if ($state_id >0)			{$where .= " AND a.state = '$state_id'";			$no_search = false;}
			if ($nbed > 0)				{$where .= " AND a.bed_room >= '$nbed'";			$no_search = false;}
			if ($nbath > 0)				{$where .= " AND a.bath_room >= '$nbath'";			$no_search = false;}
			if ($nroom > 0)				{$where .= " AND a.rooms >= '$nroom'";				$no_search = false;}
			if ($nfloors > 0)			{$where .= " AND a.number_of_floors >= '$nfloors'";	$no_search = false;}
			if ($agent_type >= 0)		{$where .= " AND c.agent_type = '$agent_type'";		$no_search = false;}

			if($price > 0){
				$db->setQuery("Select * from #__osrs_pricegroups where id = '$price'");
				$pricegroup = $db->loadObject();
				$price_from = $pricegroup->price_from;
				$price_to	= $pricegroup->price_to;
				if($price_from  > 0){
					$where .= " AND (a.price >= '$price_from')";
				}
				if($price_to > 0){
					$where .= " AND (a.price <= '$price_to')";
				}
				$no_search = false;
			}
			
			if($min_price > 0){
				$where .= " AND a.price >= '$min_price'";
			}
			if($max_price > 0){
				$where .= " AND a.price <= '$max_price'";
			}
			if($sqft_min > 0){
				$where .= " AND a.square_feet >= '$sqft_min'";
				$lists['sqft_min'] = $sqft_min;
			}
			if($sqft_max > 0){
				$where .= " AND a.square_feet <= '$sqft_max'";
				$lists['sqft_max'] = $sqft_max;
			}
			if($lotsize_min > 0){
				$where .= " AND a.lot_size >= '$lotsize_min'";
				$lists['lotsize_min'] = $lotsize_min;
			}
			if($lotsize_max > 0){
				$where .= " AND a.lot_size <= '$lotsize_max'";
				$lists['lotsize_max'] = $lotsize_max;
			}
			if((isset($extrafieldSql)) AND (count($extrafieldSql)) > 0){
				$extrafieldSql = implode(" AND ",$extrafieldSql);
				if(trim($extrafieldSql) != ""){
					$where .= " AND ".$extrafieldSql;
				}
			}
			if($created_from != "")
			{
				$lists['created_from'] = $created_from;
				$where .= " and a.created >= '$created_from'";
			}
			if($created_to != "")
			{
				$lists['created_to'] = $created_to;
				$where .= " and a.created <= '$created_to'";
			}
			$where .= $amenities_sql;
			
			$where .= $rangeDateQuery;
			$where .= $search_radius_h;
			$query = "SELECT count(a.id) FROM (SELECT distinct a.id ".$search_radius_v." FROM #__osrs_properties a "
					." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
					." INNER JOIN #__osrs_types as d on d.id = a.pro_type"
					." INNER JOIN #__osrs_states as g on g.id = a.state"
					." LEFT JOIN #__osrs_cities as h on h.id = a.city"
					." LEFT JOIN #__osrs_property_categories as i on i.pid = a.id"
					." LEFT JOIN #__osrs_countries as e on e.id = a.country"
					. $where
					. " ) as a ";
			//echo $query;die();
			$db->setQuery($query);
			//echo $db->getQuery();
			$total = $db->loadResult();
			$pageNav = new OSPJPagination($total,$limitstart,$limit);
			$view_type_cookie = $jinput->getString('listviewtype','');
			if($view_type_cookie == ""){
				$view_type_cookie = $_COOKIE['viewtypecookie'];	
			}
			if($view_type_cookie == 2){
				$db->setQuery("Select * from #__osrs_themes where published = '1'");
				$theme = $db->loadObject();
				$themename = ($theme->name!= "")? $theme->name:"default";
				$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
				$themeobj = $db->loadObject();
				$params = $themeobj->params;
				$params = new Registry($params);
				$max_properties_google_map = $params->get('max_properties_map',50);
				$db->setQuery($select.' '.$from.' '.$where.' '.$Order_by,0,$max_properties_google_map);
			}else{
				$select_session = "Select distinct a.id".$search_radius_v;
				$db->setQuery($select_session.' '.$from.' '.$where.' '.$Order_by);
				//echo $db->getQuery();
				$sessionObjs = $db->loadColumn(0);
				//print_r($sessionObjs);
				if(count($sessionObjs) > 0){
					$session->set('advsearchresult',$sessionObjs);
				}else{
					$sessionVar = [];
					$session->set('advsearchresult',$sessionVar);
				}
				//print_r($session->get('advsearchresult'));
				$db->setQuery($select.' '.$from.' '.$where.' '.$Order_by,$pageNav->limitstart,$pageNav->limit);
				//echo "<BR />";
			}
			$rows = $db->loadObjectList();
            $session->set('advurl',$url);
			if(count($rows) > 0){
				$fields = HelperOspropertyCommon::getExtrafieldInList();
				//get the list of extra fields that show at the list
				for($i=0;$i<count($rows);$i++){//for
					$row = $rows[$i];
					$pro_name = OSPHelper::getLanguageFieldValue($row,'pro_name');
					$row->pro_name = $pro_name;
					
					$pro_small_desc = OSPHelper::getLanguageFieldValue($row,'pro_small_desc');
					$row->pro_small_desc = $pro_small_desc;
					
					$alias = $row->pro_alias;
					$new_alias = OSPHelper::generateAlias('property',$row->id,$row->pro_alias);
					if($alias != $new_alias){
						$db->setQuery("Update #__osrs_properties set pro_alias = '$new_alias' where id = '$row->id'");
						$db->execute();
					}
					
					$category_name = OSPHelper::getCategoryNamesOfPropertyWithLinks($row->id);
					$row->category_name = $category_name;
					
					$category_name = OSPHelper::getCategoryNamesOfProperty($row->id);
					$category_nameArr = explode(" ",$category_name);
					$row->category_name_short = "";
					//echo count($category_nameArr);
					//echo "<BR />";
					if(count($category_nameArr) > 4){
						for ($j=0;$j<4;$j++){
							$row->category_name_short .= $category_nameArr[$j]." ";
						}
						$row->category_name_short .= "...";
						//echo $row->category_name;
					}else{
						$row->category_name_short = $category_name;
					}
					
					$query = $db->getQuery(true);
					$query->select("*")->from("#__osrs_property_open")->where("pid='".$row->id."' and end_to > '".date("Y-m-d H:i:s",time())."'")->order("start_from");
					$db->setQuery($query);
					$openInformation = $db->loadObjectList();
					$row->openInformation = $openInformation;
					
					if($row->number_votes > 0){
						$rate = round($row->total_points/$row->number_votes,2);
						if($rate <= 1){
							$row->cmd = Text::_('OS_POOR');
						}elseif($rate <= 2){
							$row->cmd = Text::_('OS_BAD');
						}elseif($rate <= 3){
							$row->cmd = Text::_('OS_AVERGATE');
						}elseif($rate <= 4){
							$row->cmd = Text::_('OS_GOOD');
						}elseif($rate <= 5){
							$row->cmd = Text::_('OS_EXCELLENT');
						}
						$row->rate = $rate;
					}else{
						$row->rate = '';
						$row->cmd  = Text::_('OS_NOT_SET');
					}
					
					$db->setQuery("Select * from #__osrs_comments where pro_id = '$row->id' and published = '1' order by created_on desc");
					$row->commentObject = $db->loadObject();
					
					//get field data
					if(count($fields) > 0)
					{
						$fieldArr = [];
						$k 		  = 0;
						for($j=0;$j<count($fields);$j++)
						{
							$field = $fields[$j];
							if(OSPHelper::checkFieldWithPropertType($field->id,$row->id))
							{
								$value = HelperOspropertyFieldsPrint::showField($field,$row->id);
								if($value != "")
								{
									$tmp				= new stdClass();
									if($field->displaytitle == 1)
									{
										$tmp->label		= OSPHelper::getLanguageFieldValue($field,'field_label');
									}
									$tmp->fieldvalue	= $value;
									$fieldArr[$k]		= $tmp;
									$k++;
								}
							}
						}
						$row->fieldarr = $fieldArr;
					}
					//process photo
					$db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
					$count = $db->loadResult();
					if($count > 0){
						$row->count_photo = $count;
						$db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");	
						$picture = $db->loadResult();
						if($picture != ""){
						
							if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$picture)){
								$row->photo = Uri::root().'images/osproperty/properties/'.$row->id.'/medium/'.$picture;	
							}else{
								$row->photo = Uri::root()."/media/com_osproperty/assets/images/nopropertyphoto.png";
							}
						}else{
							$row->photo = Uri::root()."/media/com_osproperty/assets/images/nopropertyphoto.png";
						}
							
					}else{
						$row->count_photo = 0;
						$row->photo = $row->photo = Uri::root()."/media/com_osproperty/assets/images/nopropertyphoto.png";;
					}//end photo
					
					$count = 0;
					if($row->count_photo > 0){
						$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id'");
						$photos = $db->loadObjectList();
						$photoArr = [];
						for($j=0;$j<count($photos);$j++){
							$photoArr[$j] = $photos[$j]->image;
							if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$photos[$j]->image)){
								$count++;
							}
						}
						$row->photoArr = $photoArr;
						$row->count_photo = $count;
					}
					
					//get state
					//$db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$row->state'");
					$row->state_name = OSPHelper::loadSateName($row->state);//$db->loadResult();
					
					//get country
					$row->country_name = OSPHelper::getCountryName($row->country);
					//$db->setQuery("Select country_name from #__osrs_countries where id = '$row->country'");
					//$row->country_name = $db->loadResult();
					
					//rating
					if($configClass['show_rating'] == 1){
						if($row->number_votes > 0){
							$points = round($row->total_points/$row->number_votes);
							ob_start();
							?>
								<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-<?php echo $points;?>.png" />	
							<?php
							$row->rating = ob_get_contents();
							ob_end_clean();
							
						}else{
							ob_start();
							
							?>
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-0.png" />
							<?php
						
							$row->rating = ob_get_contents();
							ob_end_clean();
						} //end rating
					}
					
					//comments
					$db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$row->id'");
					$ncomment = $db->loadResult();
					if($ncomment > 0){
						$row->comment = $ncomment;
					}else{
						$row->comment = 0;
					}
					//show icon for featured, just added, just updated
					$row->just_added_ico		= "";
					$row->just_updated_ico		= "";
					$row->featured_ico			= "";
					$row->market_ico			= "";
					$created_on					= $row->created;
					$modified_on				= $row->modified;
					$created_on					= strtotime($created_on);
					$modified_on				= strtotime($modified_on);
					if($created_on > time() - 3*24*3600){ //new
						if($configClass['show_just_add_icon'] == 1){
							$row->just_added_ico = '<span class="justaddedproperty">'.Text::_("OS_JUSTADDED").'</span> ';
						}
					}elseif($modified_on > time() - 2*24*3600){
						if($configClass['show_just_update_icon'] == 1){
							$row->just_updated_ico = '<span class="justupdatedproperty">'.Text::_("OS_JUSTUPDATED").'</span> ';
						}
					}
					if($row->isFeatured == 1){
						$row->featured_ico = '<span class="featuredproperty">'.Text::_('OS_FEATURED').'</span> ';
					}
					if(($configClass['active_market_status'] == 1)&&($row->isSold > 0)){
						$row->market_ico = '<span class="marketstatuspropertydetails">'.OSPHelper::returnMarketStatus($row->isSold).'</span> ';
					}
				}//for
			}//if rows > 0
		}
		
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
		if($configClass['show_ref'] == 1){
			$optionArr[] = HTMLHelper::_('select.option','a.ref',Text::_('Ref'));
		}
		$optionArr[] = HTMLHelper::_('select.option','a.pro_name',Text::_('OS_PROPERTY_TITLE'));
		$optionArr[] = HTMLHelper::_('select.option','a.created',Text::_('OS_LISTDATE'));
		$optionArr[] = HTMLHelper::_('select.option','a.modified',Text::_('OS_MODIFIED'));
		$optionArr[] = HTMLHelper::_('select.option','a.price',Text::_('OS_PRICE'));
		if($configClass['use_squarefeet'] == 1){
			if($configClass['use_square'] == 0){
				$optionArr[] = HTMLHelper::_('select.option','a.square_feet',Text::_('OS_SQUARE_FEET'));
			}else{
				$optionArr[] = HTMLHelper::_('select.option','a.square_feet',Text::_('OS_SQUARE_METER'));
			}
		}
		$lists['sortby'] = HTMLHelper::_('select.genericlist',$optionArr,'sortby','class="'.$bootstrapHelper->getClassMapping('input-medium').' form-select ilarge"','value','text',$sortby);
		
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
		$optionArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
		$lists['orderby'] =  HTMLHelper::_('select.genericlist',$optionArr,'orderby','class="'.$bootstrapHelper->getClassMapping('input-medium').' form-select imedium"','value','text',$orderby);

        ob_start();
        OSPHelper::loadAgentTypeDropdownFilter($agent_type, $bootstrapHelper->getClassMapping('input-medium').' selectpicker','');
        $lists['agenttype'] = ob_get_contents();
        ob_end_clean();

		$lists['category'] = OSPHelper::listCategoriesInMultiple($category_ids,'');
		
		$document = Factory::getDocument();
		
		//$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/chosen/chosen.css");
		//property types
		//$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		$db->setQuery("SELECT id as value,type_name$lang_suffix as text FROM #__osrs_types where published = '1' ORDER BY ordering");
		$protypes = $db->loadObjectList();
		//$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$protypes,'property_types[]','class="'.$bootstrapHelper->getClassMapping('input-large').' chosen" multiple','value','text',$property_types);

		$lists['marketstatus'] = OSPHelper::buildDropdownMarketStatus($isSold);
		
		//price
		//$lists['price'] = HelperOspropertyCommon::generatePriceList($adv_type,$price);
		$lists['price_value'] = $price;
		$lists['adv_type'] = $adv_type;
		$lists['min_price'] = $min_price;
		$lists['max_price'] = $max_price;
		//$lists['price'] = $price;
		// number bath room
		$bathArr[] = HTMLHelper::_('select.option','',Text::_('OS_ANY'));
		for($i=1;$i<=5;$i++){
			$bathArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nbath'] = HTMLHelper::_('select.genericlist',$bathArr,'nbath',' class="'.$bootstrapHelper->getClassMapping('input-small').' form-select"','value','text',$nbath);
		
		
		//number bed room
		$lists['nbed'] = $nbed;
		$bedArr[] = HTMLHelper::_('select.option','',Text::_('OS_ANY'));
		for($i=1;$i<=5;$i++){
			$bedArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nbed'] = HTMLHelper::_('select.genericlist',$bedArr,'nbed','class="'.$bootstrapHelper->getClassMapping('input-small').' form-select"','value','text',$nbed);
		
		//number bed room
		$lists['room'] = $nroom;
		$roomArr[] = HTMLHelper::_('select.option','',Text::_('OS_ANY'));
		for($i=1;$i<=5;$i++){
			$roomArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nroom'] = HTMLHelper::_('select.genericlist',$roomArr,'nroom','class="'.$bootstrapHelper->getClassMapping('input-small').' form-select"','value','text',$nroom);
		
		
		//number bed floors
		$lists['nfloors'] = $nfloors;
		$floorArr[] = HTMLHelper::_('select.option','',Text::_('OS_ANY'));
		for($i=1;$i<=5;$i++){
			$floorArr[] = HTMLHelper::_('select.option',$i,$i.'+');
		}
		$lists['nfloor'] = HTMLHelper::_('select.genericlist',$floorArr,'nfloors','class="'.$bootstrapHelper->getClassMapping('input-small').' form-select"','value','text',$nfloors);
		
		
		//country
		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id,'country_id','onchange="change_country_company(this.value)"',Text::_('OS_ALL_COUNTRIES'),'',$bootstrapHelper->getClassMapping('input-large'));
		
		//$lists['state'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onchange="change_state(this.value,'.intval($city).')"',Text::_('OS_ALL_STATES'),'');
		//list city
		//$lists['city'] = HelperOspropertyCommon::loadCity($option,$state_id, $city);
		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state_id' id='state_id' value='".OSPHelper::returnDefaultState()."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onchange="change_state(this.value,'.intval($city).')"',Text::_('OS_ALL_STATES'),'');
		}
		
		if(OSPHelper::userOneState()){
			$default_state = OSPHelper::returnDefaultState();
		}else{
			$default_state = $state_id;
		}
		
		$lists['city'] = HelperOspropertyCommon::loadCity($option,$default_state,$city);
		
		$db->setQuery("Select * from #__osrs_amenities where published = '1' order by ordering");
		$amenities = $db->loadObjectList();
		$lists['amenities'] = $amenities;

		$radius_arr = array(5,10,20,50,100,200);
		$radiusArr = [];
		$radius_type = ($configClass['locator_radius_type'] == 0) ? Text::_('OS_MILES') : Text::_('OS_KILOMETRE');
		foreach ($radius_arr as $radius) {
			$radiusArr[] = HTMLHelper::_('select.option',$radius, $radius. ' '. $radius_type);
		}
		$lists['radius'] = HTMLHelper::_('select.genericlist',$radiusArr,'radius_search','class="'.$bootstrapHelper->getClassMapping('input-medium').' form-select imedium"', 'value', 'text',$radius_search);
		
		HTML_OspropertyListing::advSearchForm($option,$groups,$lists,$rows,$pageNav,$param,$adv_type,$dosearch);
	}	
	
	/**
	 * Show street view
	 *
	 * @param unknown_type $option
	 */
	static function showStreetView($option){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDbo();
		$id = $jinput->getInt('id');
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$row = $db->loadObject();
		$geocode = [];
		$geocode[0]->lat = $row->lat_add;
		$geocode[0]->long = $row->long_add;
		HelperOspropertyGoogleMap::loadStreetViewMap($geocode,"map_canvas");
	}
	
	
	/**
	 * List properties following by city
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function propertyCity($option,$city_id)
	{
		global $bootstrapHelper, $mainframe,$_jversion,$configClass,$jinput,$lang_suffix;
		$session = Factory::getSession();
		$session->set('advSearchresult',null);
		
        $catIds = $jinput->get('catIds',array(),'ARRAY');
		$catIds	= ArrayHelper::toInteger($catIds);
        $type_id = $jinput->getInt('type_id',0);

        //$category_id = $jinput->getInt('category_id',0);
        $category_id = [];
        $show_filter_agent = $jinput->getInt('show_filter_agent',0);
        $show_filter_state = $jinput->getInt('show_filter_state',1);
        $show_filter_keyword = $jinput->getInt('show_filter_keyword',0);
        $show_filter_bed = $jinput->getInt('show_filter_bed',0);
        $show_filter_bath = $jinput->getInt('show_filter_bath',0);
        $show_filter_price = $jinput->getInt('show_filter_price',0);
        $show_filter_room = $jinput->getInt('show_filter_room',0);
		
		$db			= Factory::getDbo();
		$menus		= Factory::getApplication()->getMenu();
		$menu		= $menus->getActive();
		$itemid		= $jinput->getInt('Itemid',0);

		if (is_object($menu)) 
		{
			OSPHelper::generateMetaTags($itemid);
	        $params = new Registry() ;
	        $params->loadString($menu->getParams()) ;

            if($type_id == 0){
                $type_id = $params->get('type_id', 0);
                if ($type_id == 0) {
                    $jinput->set('type_id', $type_id);
                }
            }
            if(count($catIds) == 0){
                $category_id = $params->get('catIds',0);
                if(count($catIds) == 0) {
                    $jinput->set('catIds',$category_id);
                }
            }
            if($show_filter_agent == 0){
                $show_filter_agent = $params->get('show_filter_agent',$show_filter_agent);
                if($show_filter_agent == 0){
                    $jinput->set('show_filter_agent',$show_filter_agent);
                }
            }
            if($show_filter_state == 0){
                $show_filter_state = $params->get('show_filter_state',$show_filter_state);
                if($show_filter_state == 0){
                    $jinput->set('show_filter_state',$show_filter_state);
                }
            }
            if($show_filter_keyword == 0){
                $show_filter_keyword = $params->get('show_filter_keyword',$show_filter_keyword);
                if($show_filter_keyword == 0){
                    $jinput->set('show_filter_keyword',$show_filter_keyword);
                }
            }
            if($show_filter_bed == 0){
                $show_filter_bed = $params->get('show_filter_bed',$show_filter_bed);
                if($show_filter_bed == 0){
                    $jinput->set('show_filter_bed',$show_filter_bed);
                }
            }
            if($show_filter_bath == 0){
                $show_filter_bath = $params->get('show_filter_bath',$show_filter_bath);
                if($show_filter_bath == 0){
                    $jinput->set('show_filter_bath',$show_filter_bath);
                }
            }
            if($show_filter_price == 0){
                $show_filter_price = $params->get('show_filter_price',$show_filter_price);
                if($show_filter_price == 0){
                    $jinput->set('show_filter_price',$show_filter_price);
                }
            }
            if($show_filter_room == 0){
                $show_filter_room = $params->get('show_filter_room',$show_filter_room);
                if($show_filter_room == 0){
                    $jinput->set('show_filter_room',$show_filter_room);
                }
            }

			$link = $menu->link;
			//if(strpos($link,"view=ltype") !== false)
			//{
			$db->setQuery("Select city from #__osrs_cities where id = '$city_id'");
			$city = $db->loadResult();
			OSPHelper::generateHeading(1,$city,0);
			OSPHelper::generateHeading(2,$city,0);
			//}
		}
		//echo $type_id;
		$db = Factory::getDbo();
		if($type_id > 0){
			$db->setQuery("Select * from #__osrs_types where id = '$type_id'");
			$type = $db->loadObject();
		}
		
		if($city_id > 0){
			$db->setQuery("Select state_id from #__osrs_cities where id = '$city_id'");
			$state_id = $db->loadResult();
			$db->setQuery("Select country_id from #__osrs_cities where id = '$city_id'");
			$country_id = $db->loadResult();
			$db->setQuery("Select city$lang_suffix as city from #__osrs_cities where id = '$city_id'");
			$city = $db->loadResult();
		}
		
		$country_id = $jinput->getInt('country_id',$country_id);
		$state_id = $jinput->getInt('state_id',$state_id);

		HTML_OspropertyListing::showPropertyCityListing($option,$type_id,$category_id,$show_filter_agent,$show_filter_state,$show_filter_keyword,$show_filter_bed,$show_filter_bath,$show_filter_price,$show_filter_room,$menu,$city_id,$state_id,$country_id);
	}
	
	/**
	 * Property Tag
	 *
	 * @param unknown_type $option
	 */
	static function propertyTag($option){
		global $bootstrapHelper, $mainframe,$configClass,$lang_suffix,$jinput;
		$session = Factory::getSession();
		$session->set('advsearchresult',null);
        $tag_id = $jinput->getInt('tag_id',0);
        $db = Factory::getDbo();
        if($tag_id > 0){
            $db->setQuery("Select keyword$lang_suffix as keyword from #__osrs_tags where id = '$tag_id'");
            $tag = $db->loadResult();
            $orderby		= OSPHelper::getStringRequest('orderby','a.created');
            $ordertype		= OSPHelper::getStringRequest('ordertype','desc');
            $limitstart		= $jinput->getInt('limitstart',0);
            $limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
            $favorites		= $jinput->getInt('favorites',0);
            $price			= $jinput->getInt('price',0);
            $agent_id		= $jinput->getInt('agent_id',0);
            OSPHelper::generateHeading(2,Text::_('OS_LIST_PROPERTIES_WITH_TAG')." [".$tag."]",1);
            ?>
            <div class="clearfix"></div>
            <form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=property_tag&tag_id='.$tag_id.'&Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
                <?php
                OspropertyListing::listProperties($option,0,null,0,0,'',0,0,0,0,0,$orderby,$ordertype,$limitstart,$limit,0,0,array(),0,0,0,0,1,-1,$tag_id);
                ?>
                <input type="hidden" name="option" value="<?php echo $option?>" />
                <input type="hidden" name="task" value="property_tag" />
                <input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
			<input type="hidden" name="max_properties" id="max_properties" value="" />
			<input type="hidden" name="show_filterform" id="show_filterform" value="1" />
			<input type="hidden" name="show_categoryfilter" id="show_categoryfilter" value="0" />
			<input type="hidden" name="show_propertytypefilter" id="show_propertytypefilter" value="0" />
			<input type="hidden" name="show_locationfilter" id="show_locationfilter" value="0" />
			<input type="hidden" name="show_pricefilter" id="show_pricefilter" value="0" />
			<input type="hidden" name="show_keywordfilter" id="show_keywordfilter" value="0" />
			<input type="hidden" name="show_agenttypefilter" id="show_agenttypefilter" value="0" />
			<input type="hidden" name="show_marketstatusfilter" id="show_marketstatusfilter" value="0" />	
			</form>
			<?php
		}
	}
	/**
	 * Property type
	 *
	 * @param unknown_type $option
	 */
	static function propertyType($option){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$session = Factory::getSession();
		$session->set('advsearchresult',null);
        $itemid		= $jinput->getInt('Itemid',0);
        if($itemid != $session->get('Itemid'))
		{
            //set session variable empty
            $session->set('orderby','');
            $session->set('ordertype','');
            $session->set('state_id','');
			$session->set('city_id','');
            $session->set('country_id','');
            $session->set('isFeatured','');
            $session->set('isSold','');
            $session->set('catIds',array());
            $session->set('agenttype','');
            $session->set('type_id','');
            $session->set('min_price','');
            $session->set('max_price','');
            $session->set('price','');
			$session->set('use_filterform','');
            $session->set('Itemid',$itemid);
        }
		$catIds						= [];
		$use_filterform				= $jinput->getInt('use_filterform',0);
		if($use_filterform == 0){
			$use_filterform			= $session->get('use_filterform');
		}
		$session->set('use_filterform',$use_filterform);
		if($use_filterform == 1){
			$catIds					= $session->get('catIds');
			$type_id				= $session->get('type_id');
		}

		$restart_filterform			= $jinput->getInt('restart_filterform',0);
		if($restart_filterform == 1){
			$catIds 				= $jinput->get('category_id',array(),'ARRAY');
			$type_id				= $jinput->get('type_id',0);
		}

		if($type_id == 0){
			$type_id 				= $jinput->getInt('type_id',0);
		}
        $property_type				= $jinput->getInt('property_type',-1);
        if($property_type > -1){
            $type_id				= $property_type;
        }
		
        if(count($catIds) == 0){
            $catIds 				= $jinput->get('catIds',array(),'ARRAY');
        }
		$catIds						= ArrayHelper::toInteger($catIds);
        if(count($catIds) == 0)
		{
            $category_id		 	= $jinput->get('category_id',array(),'ARRAY');
            $catIds					= [];
            if(is_array($category_id))
			{
                $catIds 			= $jinput->get('category_id',array(),'ARRAY');
            }
			else
			{
                $catIds[]			= $category_id;
            }
        }
		//if(count($catIds) == 0){
			//$catIds = $session->get('catIds');
		//}
		$catIds						= ArrayHelper::toInteger($catIds);
        $submitCategory				= $jinput->getInt('submitCategory',0);

        $isFeatured 				= $jinput->getInt('isFeatured',0);
        if($isFeatured == 0){
            $isFeatured = $session->get('isFeatured',0);
        }
        $isSold						= $jinput->getInt('isSold',-1);
        if($isSold == -1){
            $isSold = $session->get('isSold',0);
        }
        $orderby					= OSPHelper::getStringRequest('orderby','','post');
        if($orderby == ""){
            $orderby = $session->get('orderby');
        }
        $ordertype					= OSPHelper::getStringRequest('ordertype','','post');
        if($ordertype == ""){
            $ordertype = $session->get('ordertype');
        }
        $country_id					= $jinput->getInt('country_id',-1);
        if($country_id < 0){
            $country_id = $session->get('country_id');
        }
        $state_id					= $jinput->getInt('state_id',-1);
        if($state_id == -1){
            $state_id = $session->get('state_id');
        }
		$city_id					= $jinput->getInt('city_id',-1);
        if($city_id == -1){
            $city_id = $session->get('city_id');
        }
        $agenttype					= $jinput->getInt('agent_type',-2);
        if($agenttype == -2){
            $agenttype = $session->get('agenttype',-1);
        }
        //print_r($session);
        //$catIds						= $jinput->getArray('category_id',array());

        //$type_id					= $jinput->getInt('property_type',-1);
        if($type_id == -1){
            $type_id = $session->get('type_id');
        }

        $min_price					= $jinput->getFloat('min_price',-1);
        if($min_price == -1){
            $min_price = $session->get('min_price');
        }
        $max_price					= $jinput->getFloat('max_price',-1);
        if($max_price == -1){
            $max_price = $session->get('max_price');
        }
        $price					= $jinput->getFloat('price',-1);
        if($price == -1){
            $price = $session->get('price');
        }

        $show_filter_keyword 		= $jinput->getInt('show_filter_keyword',0);
        $company_id					= $jinput->getInt('company_id',0);
        $limitstart					= $jinput->getInt('limitstart','0');
        $limit						= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
        $max_properties				= $jinput->getInt('max_properties',0);
        $show_filterform			= $jinput->getInt('show_filterform',$configClass['show_searchform']);
        $show_categoryfilter		= $jinput->getInt('show_categoryfilter',1);
        $show_propertytypefilter	= $jinput->getInt('show_propertytypefilter',1);
        $show_locationfilter		= $jinput->getInt('show_locationfilter',1);
        $show_keywordfilter			= $jinput->getInt('show_keywordfilter',1);
        $show_pricefilter			= $jinput->getInt('show_pricefilter',1);
        $show_agenttypefilter		= $jinput->getInt('show_agenttypefilter',1);
		$show_marketstatusfilter	= $jinput->getInt('show_marketstatusfilter',1);

        //$city_id = $jinput->getInt('city',-1);
        if($city_id < 0){
            $city_id = $session->get('city_id');
        }
        $menus = Factory::getApplication()->getMenu();
        $menu = $menus->getActive();

        if (is_object($menu)) 
		{
			$menuquery = $menu->query;
			if(($use_filterform == 0) && ($restart_filterform == 0)){
				$type_id = $menuquery['type_id'];
				$jinput->set('type_id', $type_id);
			}
            $params = new Registry() ;
           // $params->loadString($menu->getParams());
			$params = $menu->getParams();
            if($type_id == -1){
                $type_id_param = $params->get('type_id', 0);
                $jinput->set('type_id', $type_id_param);
            }

            if($isFeatured == 0){
                $isFeatured_param = $params->get('isFeatured', 0);
                if ($isFeatured == 0) {
                    $isFeatured = $isFeatured_param;
                    $jinput->set('isFeatured', $isFeatured_param);
                }
            }

            if($isSold == 0)
			{
                $isSold_param = $params->get('isSold', 0);
                if ($isSold == 0) 
				{
                    $isSold = $isSold_param;
                    $jinput->set('isSold', $isSold_param);
                }
            }

            if($orderby == "")
			{
                $orderby = $params->get('orderby', 'a.pro_name');
                $jinput->set('orderby', $orderby);
            }

            if($ordertype == "")
			{
                $ordertype = $params->get('ordertype', 'desc');
                $jinput->set('ordertype', $ordertype);
            }

            if($country_id == 0)
			{
                $country_id_param = $params->get('country_id', 0);
                if ($country_id == 0) 
				{
                    $jinput->set('country_id', $country_id_param);
                }
            }

            //still == 0
            if($country_id == 0)
			{
                $country_id = OSPHelper::getStringRequest('country_id',HelperOspropertyCommon::getDefaultCountry());
            }

            if($company_id == 0)
			{
                $company_id = $jinput->getInt('company_id',0);
            }

            if((int)$state_id == 0 || (int)$state_id == -1)
			{
                $state_id_param = $params->get('state_id', 0);
                if ($state_id_param > 0) 
				{
                    $jinput->set('state_id', $state_id_param);
                    $state_id = $state_id_param;
                }
            }

			if((int)$city_id == 0 || (int)$city_id == -1)
			{
                $city_id_param = $params->get('city_id', 0);
                if ($city_id_param > 0) {
                    $jinput->set('city_id', $city_id_param);
                    $city_id = $city_id_param;
                }
            }

            if($show_filter_keyword == 0){
                $show_filter_keyword = $params->get('show_filter_keyword',$show_filter_keyword);
                if($show_filter_keyword == 0){
                    $jinput->set('show_filter_keyword',$show_filter_keyword);
                }
            }

            if($max_properties == 0){
                $max_properties = $params->get('max_properties',0);
                if($max_properties == 0) {
                    $jinput->set('max_properties',$max_properties);
                }
            }

			if((float)$min_price <= 0)
			{
				$min_price = $params->get('price_min_filter',0);
				if($min_price == 0) 
				{
                    $jinput->set('min_price',$min_price);
                }
			}

			if((float)$max_price <= 0)
			{
				$max_price = $params->get('price_max_filter',0);
				if($max_price == 0) 
				{
                    $jinput->set('max_price',$max_price);
                }
			}

            $show_filterform = $params->get('show_filterform');
            if($show_filterform != $configClass['show_searchform']){
                $jinput->set('show_filterform',$show_filterform);
            }

            if(($agenttype == -1) or ($agenttype == "")){
                $agenttype = $params->get('agenttype',-1);
            }

            $show_agenttypefilter 		= $params->get('show_agenttypefilter',1);
            $show_categoryfilter 		= $params->get('show_categoryfilter',1);
            $show_propertytypefilter 	= $params->get('show_propertytypefilter',1);
            $show_locationfilter		= $params->get('show_locationfilter',1);
            $show_keywordfilter 		= $params->get('show_keywordfilter',1);
            $show_pricefilter 			= $params->get('show_pricefilter',1);
			$show_marketstatusfilter	= $params->get('show_marketstatusfilter',1);
        }

        $db = Factory::getDbo();
        if($type_id > 0){
            $db->setQuery("Select * from #__osrs_types where id = '$type_id'");
            $type = $db->loadObject();
        }
		if($city_id > 0 && $state_id == 0){
			$db->setQuery("Select state_id from #__osrs_cities where id = '$city_id'");
			$state_id = $db->loadResult();
		}
        $session->set('orderby',$orderby);
        $session->set('ordertype',$ordertype);
        $session->set('state_id',$state_id);
		$session->set('city_id',$city_id);
        $session->set('country_id',$country_id);
        $session->set('isFeatured',$isFeatured);
        $session->set('isSold',$isSold);
        $session->set('city_id',$city_id);
        $session->set('catIds',$catIds);
        $session->set('agenttype',$agenttype);
        $session->set('type_id',$type_id);
        $session->set('min_price',$min_price);
        $session->set('max_price',$max_price);
        $session->set('price',$price);

		HTML_OspropertyListing::showPropertyTypeListing($option,$type,$catIds,$show_agenttypefilter,0,$show_keywordfilter,0,0,$show_pricefilter,0,$menu,$city_id,$state_id,$country_id,$isFeatured,$isSold,$orderby,$ordertype,$company_id,$max_properties,$show_filterform,$show_categoryfilter,$show_propertytypefilter,$show_locationfilter,$show_keywordfilter,$show_pricefilter,$show_agenttypefilter,$agenttype,$show_marketstatusfilter,$min_price,$max_price,$price);
	}
	
	/**
	 * List properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $category_id
	 * @param unknown_type $agent_id
	 * @param unknown_type $property_type
	 * @param unknown_type $keyword
	 * @param unknown_type $nbed
	 * @param unknown_type $nbath
	 * @param unknown_type $isfeatured
	 * @param unknown_type $orderby
	 * @param unknown_type $limitstart
	 * @param unknown_type $limit
	 */
	static function listProperties($option,$company_id,$category_ids,$agent_id,$property_type,$keyword,$nbed,$nbath,$isfeatured,$isSold,$nrooms,$orderby,$ordertype,$limitstart,$limit,$favorites,$price,$filterParams,$city_id,$state_id,$country_id,$max_properties,$show_filterform,$agenttype=-1,$tag_id = 0,$min_price=-1,$max_price=-1)
	{
		global $bootstrapHelper, $mainframe,$configClass,$lang_suffix,$jinput;
		$limitstart					= OSPHelper::getLimitStart();
		if(!$limit)
		{
			$limit					= $configClass['general_number_properties_per_page'];
		}
        $orderbyArray = array('a.pro_name','a.ref','a.created','a.modified','a.price','a.isFeatured','rand()','a.isFeatured desc, a.id','a.id','a.ordering');
        if(!in_array($orderby, $orderbyArray))
		{
            $orderby = "a.id";
            $ordertype = "desc";
        }

		if($orderby == "a.price" && $ordertype == "asc")
		{
			$orderby = " a.price_call desc, a.price";
		}

		$viewtype = $jinput->getInt('listviewtype',0);
		$url = $jinput->getString('url','');
		@setcookie('viewtypecookie',$viewtype,time()+3600,"/");
		
		if(intval($limit) == 0){
			$limit = $configClass['general_number_properties_per_page'];
		}
		$db = Factory::getDBO();
		$user = Factory::getUser();
        $agent_id_permission = 0;
        if($user->id > 0){
            if(HelperOspropertyCommon::isAgent()){
                $agent_id_permission = HelperOspropertyCommon::getAgentID();
            }
        }
		
        $companyQuery = "";
		if($company_id > 0){
			$companyQuery .= " and a.id in (Select id from #__osrs_properties where agent_id in (Select id from #__osrs_agents where company_id = '$company_id'))";
		}

		$query = "Select count(distinct a.id) from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_property_categories as g on g.pid = a.id"
				." WHERE 1=1";

        if($agent_id_permission > 0){
            $query .= ' and ((a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')) or (a.agent_id = "'.$agent_id_permission.'"))';
        }else{
            $query .= ' and a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        }

        $query .= $companyQuery;
		
		if($keyword != ""){
			$keyword = $db->escape($keyword);
			$query .= " AND (";
			$query .= " a.ref like '%$keyword%' OR";
			$query .= " a.pro_name$lang_suffix like '%$keyword%' OR";
			$query .= " a.ref like '%$keyword%' OR";
			$query .= " a.pro_small_desc$lang_suffix like '%$keyword%' OR";
			$query .= " a.pro_full_desc$lang_suffix like '%$keyword%' OR";
			$query .= " a.address like '%$keyword%' OR";

			$query .= " a.region like '%$keyword%' OR";
			//$query .= " a.province like '%$keyword%' OR";
			$query .= " a.postcode like '%$keyword%' OR";
			$query .= " a.bed_room like '%$keyword%' OR";
			$query .= " a.bath_room like '%$keyword%' OR";
			$query .= " a.parking like '%$keyword%'";
			$query .= " )";
		}
		if($country_id > 0){
			$query .= " AND a.country = '$country_id'";
		}
		if($state_id > 0){
			$query .= " AND a.state = '$state_id'";
		}elseif(OSPHelper::userOneState()){
			$query .= " AND a.state = '".OSPHelper::returnDefaultState()."'";
		}
		if($city_id > 0)
		{
			$query .= " AND a.city = '$city_id'";
		}
		if(is_array($category_ids) and (count($category_ids) > 0) and ($category_ids[0] != "0")){
			$temp = [];
			if(count($category_ids) > 0){
				foreach($category_ids as $cat_id){
					if((int)$cat_id > 0){
						$temp[] = " g.category_id = '$cat_id'";
					}
				}
				$tempStr = implode(" or ",$temp);
				if($tempStr != ""){
					$query .= " AND (".$tempStr.")";
				}
			}
		}

		if($agent_id > 0){
			$query .= " AND a.agent_id = '$agent_id'";
		}

		if($property_type > 0){
			$query .= " AND a.pro_type = '$property_type'";
		}

		if($nbath > 0){
			$query .= " AND a.bath_room >= '$nbath'";
		}
		if($nbed > 0){
			$query .= " AND a.bed_room >= '$nbed'";
		}
		if($nrooms > 0){
			$query .= " AND a.rooms >= '$nrooms'";
		}
		if($isfeatured > 0){
			$query .= " AND a.isFeatured = '$isfeatured'";
		}
		if($isSold > 0)
		{
			$query .= " AND a.isSold = '$isSold'";
		}

		if($price > 0){
			$db->setQuery("Select * from #__osrs_pricegroups where id = '$price'");
			$pricegroup = $db->loadObject();
			$price_from = $pricegroup->price_from;
			$price_to	= $pricegroup->price_to;
			if($price_from  > 0){
				$query .= " AND (a.price >= '$price_from')";
			}
			if($price_to > 0){
				$query .= " AND (a.price <= '$price_to')";
			}
		}

        if($min_price == -1){
            $min_price = $jinput->getFloat('min_price',0);
        }
        if($max_price == -1){
            $max_price = $jinput->getFloat('max_price',0);
        }
		
		$lists['min_price'] = $min_price;
		$lists['max_price'] = $max_price;
		
		if($min_price > 0){
			$query .= " AND a.price >= '$min_price'";
		}
		if($max_price > 0){
			$query .= " AND a.price <= '$max_price'";
		}
		
		if($favorites == 1){
			
			$query .= " AND a.id in (Select pro_id from #__osrs_favorites where user_id = '$user->id')";
		}
		
		if($company_id > 0){
			$query .= " and a.id in (Select id from #__osrs_properties where agent_id in (Select id from #__osrs_agents where company_id = '$company_id'))";
		}
		
		if(intval($agenttype) >=0){
			$query .= " and c.agent_type = '$agenttype'";
		}
		
		if($tag_id > 0){
			$query .= " and a.id in (Select pid from #__osrs_tag_xref where tag_id = '$tag_id')";
		}
		
		$query .= " and a.published = '1' and a.approved = '1' ".OSPHelper::publishDateSql('a');
		
		$db->setQuery($query);
		$total = $db->loadResult();
		//echo $total;
		if($max_properties > 0){
			if($total > $max_properties){
				$total = $max_properties;	
			}
			
			if($limitstart + $limit > $max_properties){
				$limit_number = $max_properties - $limitstart;
			}else{
				$limit_number = $limit;
			}
		}else{
			$limit_number = $limit;
		}

		if($configClass['overrides_pagination'] == 1){
			$pageNav = new OSPJPagination($total,$limitstart,$limit);
		}else{
			$pageNav = new Pagination($total,$limitstart,$limit);
		}
		
		$query = "Select a.*,c.name as agent_name,c.photo as agent_photo, c.mobile as agent_mobile, c.phone as agent_phone,c.email as agent_email, c.optin,d.id as typeid,d.type_name$lang_suffix as type_name,e.country_name from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." LEFT JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_property_categories as g on g.pid = a.id"
				." WHERE 1=1";
        if($agent_id_permission > 0){
            $query .= ' and ((a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')) or (a.agent_id = "'.$agent_id_permission.'"))';
        }else{
            $query .= ' and a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        }
		if($keyword != ""){
			$keyword = $db->escape($keyword);
			$query .= " AND (";
			$query .= " a.ref like '%$keyword%' OR";
			$query .= " a.pro_name$lang_suffix like '%$keyword%' OR";
			$query .= " a.ref like '%$keyword%' OR";
			$query .= " a.pro_small_desc$lang_suffix like '%$keyword%' OR";
			$query .= " a.pro_full_desc$lang_suffix like '%$keyword%' OR";
			$query .= " a.address like '%$keyword%' OR";
			$query .= " a.state like '%$keyword%' OR";
			$query .= " a.region like '%$keyword%' OR";
			$query .= " a.postcode like '%$keyword%' OR";
			$query .= " a.bed_room like '%$keyword%' OR";
			$query .= " a.bath_room like '%$keyword%' OR";
			$query .= " a.parking like '%$keyword%'";
			$query .= " )";
		}
		if($country_id > 0){
			$query .= " AND a.country = '$country_id'";
		}
		
		if($state_id > 0){
			$query .= " AND a.state = '$state_id'";
		}elseif(OSPHelper::userOneState()){
			$query .= " AND a.state = '".OSPHelper::returnDefaultState()."'";
		}
		if($city_id > 0){
			$query .= " AND a.city = '$city_id'";
		}

		if(is_array($category_ids) and (count($category_ids) > 0) and ($category_ids[0] != "0")){
			$temp = [];
			if(count($category_ids) > 0){
				foreach($category_ids as $cat_id){
					if((int)$cat_id > 0){
						$temp[] = " g.category_id = '$cat_id'";
					}
				}
				$tempStr = implode(" or ",$temp);
				if($tempStr != ""){
					$query .= " AND (".$tempStr.")";
				}
			}
		}
		
		if($agent_id > 0){
			$query .= " AND a.agent_id = '$agent_id'";
		}
		if($property_type > 0){
			$query .= " AND a.pro_type = '$property_type'";
		}
		if($nbath > 0){
			$query .= " AND a.bath_room >= '$nbath'";
		}
		if($nbed > 0){
			$query .= " AND a.bed_room >= '$nbed'";
		}
		if($nrooms > 0){
			$query .= " AND a.bath_room >= '$nrooms'";
		}
		if($isfeatured > 0){
			$query .= " AND a.isFeatured = '$isfeatured'";
		}
		if($isSold > 0)
		{
			$query .= " AND a.isSold = '$isSold'";
		}
		if($price > 0){
			$db->setQuery("Select * from #__osrs_pricegroups where id = '$price'");
			$pricegroup = $db->loadObject();
			$price_from = $pricegroup->price_from;
			$price_to	= $pricegroup->price_to;
			if($price_from  > 0){
				$query .= " AND (a.price >= '$price_from')";
			}
			if($price_to > 0){
				$query .= " AND (a.price <= '$price_to')";
			}
		}
		
		if($min_price > 0){
			$query .= " AND a.price >= '$min_price'";
		}
		if($max_price > 0){
			$query .= " AND a.price <= '$max_price'";
		}
		
		if($tag_id > 0){
			$query .= " and a.id in (Select pid from #__osrs_tag_xref where tag_id = '$tag_id')";
		}
		
		if($favorites == 1){
			$user = Factory::getUser();
			$query .= " AND a.id in (Select pro_id from #__osrs_favorites where user_id = '$user->id')";
		}
	
		if($company_id > 0){
			$query .= " and a.id in (Select id from #__osrs_properties where agent_id in (Select id from #__osrs_agents where company_id = '$company_id'))";
		}

		if(intval($agenttype) >=0){
			$query .= " and c.agent_type = '$agenttype'";
		}
		$query .= " and a.published = '1' and a.approved = '1' ".OSPHelper::publishDateSql('a');
		if($orderby == "")
		{
			$orderby = "a.id";
		}
        if($orderby == "rand()"){
            $ordertype = "";
        }

		$query .= " GROUP BY a.id ORDER BY $orderby $ordertype";
		
		$view_type_cookie = $jinput->getString('listviewtype','');
		if($view_type_cookie == ""){
			$view_type_cookie = $_COOKIE['viewtypecookie'];	
		}
		if($view_type_cookie == 2){
			$db->setQuery("Select * from #__osrs_themes where published = '1'");
			$theme = $db->loadObject();
			$themename = ($theme->name!= "")? $theme->name:"default";
			
			$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
			$themeobj = $db->loadObject();
			$params = $themeobj->params;
			$params = new Registry($params) ;
			$max_properties_google_map = $params->get('max_properties_map',50);
			$db->setQuery($query,0,$max_properties_google_map);	
		}elseif($view_type_cookie == 4){
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			if(count($rows) > 0){
				HelperOspropertyCommon::generateGoogleEarthKML($rows);
			}
			exit();
		}else{
			$db->setQuery($query,$pageNav->limitstart,$limit_number);
		}
		$rows = $db->loadObjectList();

		if(count($rows) > 0){
			$fields = HelperOspropertyCommon::getExtrafieldInList();
			//get the list of extra fields that show at the list
			for($i=0;$i<count($rows);$i++){//for
				$row = $rows[$i];
				
				$pro_name = OSPHelper::getLanguageFieldValue($row,'pro_name');
				$row->pro_name = $pro_name;
				$pro_small_desc = OSPHelper::getLanguageFieldValue($row,'pro_small_desc');
				$row->pro_small_desc = $pro_small_desc;
					
				OSPHelper::generateAlias('property',$row->id,$row->pro_alias);
				$alias = $row->pro_alias;
				$new_alias = OSPHelper::generateAlias('property',$row->id,$row->pro_alias);
				if($alias != $new_alias){
					$db->setQuery("Update #__osrs_properties set pro_alias = '$new_alias' where id = '$row->id'");
					$db->execute();
				}
				
				$category_name = OSPHelper::getCategoryNamesOfPropertyWithLinks($row->id);
				$row->category_name = $category_name;
				
				$category_name = OSPHelper::getCategoryNamesOfProperty($row->id);
				$category_nameArr = explode(" ",$category_name);
				$row->category_name_short = "";
				//echo count($category_nameArr);
				//echo "<BR />";
				if(count($category_nameArr) > 4){
					for ($j=0;$j<4;$j++){
						$row->category_name_short .= $category_nameArr[$j]." ";
					}
					$row->category_name_short .= "...";
					//echo $row->category_name;
				}else{
					$row->category_name_short = $category_name;
				}
				
				$query = $db->getQuery(true);
				$query->select("*")->from("#__osrs_property_open")->where("pid='".$row->id."' and end_to > '".date("Y-m-d H:i:s",time())."'")->order("start_from");
				$db->setQuery($query);
				$openInformation = $db->loadObjectList();
				$row->openInformation = $openInformation;
				
				if($row->number_votes > 0){
					$rate = round($row->total_points/$row->number_votes,2);
					if($rate <= 1){
						$row->cmd = Text::_('OS_POOR');
					}elseif($rate <= 2){
						$row->cmd = Text::_('OS_BAD');
					}elseif($rate <= 3){
						$row->cmd = Text::_('OS_AVERGATE');
					}elseif($rate <= 4){
						$row->cmd = Text::_('OS_GOOD');
					}elseif($rate <= 5){
						$row->cmd = Text::_('OS_EXCELLENT');
					}
					$row->rate = $rate;
				}else{
					$row->rate = '';
					$row->cmd  = Text::_('OS_NOT_SET');
				}
				
				$db->setQuery("Select * from #__osrs_comments where pro_id = '$row->id' and published = '1' order by created_on desc");
				$row->commentObject = $db->loadObject();
				
				
				//get field data
				if(count($fields) > 0)
				{
					$fieldArr = [];
					$k 		  = 0;
					for($j=0;$j<count($fields);$j++)
					{
						$field = $fields[$j];
						if(OSPHelper::checkFieldWithPropertType($field->id,$row->id))
						{
							$value = HelperOspropertyFieldsPrint::showField($field,$row->id);
							if($value != "")
							{
								$tmp						= new stdClass();		
								if($field->displaytitle == 1)
								{
									$tmp->label				= OSPHelper::getLanguageFieldValue($field,'field_label');
								}
								$tmp->fieldvalue			= $value;
								$fieldArr[$k]				= $tmp;
								$k++;
							}
						}
					}
					$row->fieldarr = $fieldArr;
				}
				//process photo
				$db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
				$count = $db->loadResult();
				if($count > 0){
					$row->count_photo = $count;
					$db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");	
					$picture = $db->loadResult();
					if($picture != "")
					{
					
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$picture)){
							$row->photo = Uri::root().'images/osproperty/properties/'.$row->id.'/medium/'.$picture;	
						}else{
							$row->photo = Uri::root()."/media/com_osproperty/assets/images/nopropertyphoto.png";
						}
					}else{
						$row->photo = Uri::root()."/media/com_osproperty/assets/images/nopropertyphoto.png";
					}
						
				}else{
					$row->count_photo = 0;
					$row->photo = $row->photo = Uri::root()."/media/com_osproperty/assets/images/nopropertyphoto.png";;
				}//end photo
				
				$count = 0;
				if($row->count_photo > 0){
					$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id'");
					$photos = $db->loadObjectList();
					$photoArr = [];
					for($j=0;$j<count($photos);$j++){
						$photoArr[$j] = $photos[$j]->image;
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/medium/'.$photos[$j]->image)){
							$count++;
						}
					}
					$row->photoArr = $photoArr;
					$row->count_photo = $count;
				}
				//get state
				//$db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$row->state'");
				$row->state_name = OSPHelper::loadSateName($row->state);//$db->loadResult();
				
				//get country
				//$db->setQuery("Select country_name from #__osrs_countries where id = '$row->country'");
				//$row->country_name = $db->loadResult();
				$row->country_name = OSPHelper::getCountryName($row->country);
				
				//rating
				if($configClass['show_rating'] == 1){
					if($row->number_votes > 0){
						$points = round($row->total_points/$row->number_votes);
						ob_start();
						
							?>
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-<?php echo $points;?>.png" />
							<?php
						
						$row->rating = ob_get_contents();
						ob_end_clean();
						
					}else{
						ob_start();
						
							?>
							<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-0.png" />
							<?php
						
						$row->rating = ob_get_contents();
						ob_end_clean();
					} //end rating
				}
				
				//comments
				$db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$row->id'");
				$ncomment = $db->loadResult();
				if($ncomment > 0){
					$row->comment = $ncomment;
				}else{
					$row->comment = 0;
				}
				

				//show icon for featured, just added, just updated
				$row->just_added_ico = "";
				$row->just_updated_ico = "";
				$row->featured_ico = "";
				$row->market_ico = "";
				$created_on = $row->created;
				$modified_on = $row->modified;
				$created_on = strtotime($created_on);
				$modified_on = strtotime($modified_on);
				if($created_on > time() - 3*24*3600){ //new
					if($configClass['show_just_add_icon'] == 1)
					{
						$row->just_added_ico = '<span class="justaddedproperty">'.Text::_("OS_JUSTADDED").'</span> ';
					}
				}
				elseif($modified_on > time() - 2*24*3600)
				{
					if($configClass['show_just_update_icon'] == 1)
					{
						$row->just_updated_ico = '<span class="justupdatedproperty">'.Text::_("OS_JUSTUPDATED").'</span> ';
					}
				}
				if($row->isFeatured == 1)
				{
					$row->featured_ico = '<span class="featuredproperty">'.Text::_('OS_FEATURED').'</span> ';
				}
				if($configClass['active_market_status'] == 1 && $row->isSold > 0)
				{
					$row->market_ico = '<span class="marketstatuspropertydetails">'.OSPHelper::returnMarketStatus($row->isSold).'</span> ';
				}
			}//for
		}//if rows > 0
		
		//show the query form
		//property types
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		$db->setQuery("Select id as value,type_name$lang_suffix as text from #__osrs_types where published = '1' order by ordering");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'property_type','class="input-medium form-select chosen ilarge"','value','text',$property_type);
		//categories
		//$lists['category'] = OSPHelper::listCategories($category_id,'');
		$lists['category'] = OSPHelper::checkboxesCategory('category_id[]',$category_ids);

		$lists['marketstatus'] = OSPHelper::buildDropdownMarketStatus($isSold);

		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id,'country_id','onChange="javascript:loadStateInListPage()"',Text::_('OS_ALL_COUNTRIES'),' class="input-medium form-select ilarge"');
		
		
		$lists['state'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onChange="javascript:changeCity(this.value,'.intval($city_id).') " class="input-large form-select ilarge"',Text::_('OS_ALL_STATES'),'');
		
		if(OSPHelper::userOneState()){
			$default_state = OSPHelper::returnDefaultState();
		}else{
			$default_state = $state_id;
		}

		$lists['city'] = HelperOspropertyCommon::loadCity($option,$default_state,$city_id,'','city_id');
		
		//number bed rooms
		$bedArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_MINIMUN_BED_ROOMS'));
		for($i=1;$i<=20;$i++){
			$bedArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['nbed'] = HTMLHelper::_('select.genericlist',$bedArr,'nbed','class="input-small form-select"','value','text',$nbed);
		
		//number bath rooms
		$bathArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_MINIMUN_BATH_ROOMS'));
		for($i=1;$i<=20;$i++){
			$bathArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['nbath'] = HTMLHelper::_('select.genericlist',$bathArr,'nbath','class="input-small form-select"','value','text',$nbath);
		
		//number rooms
		$roomsArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_MINIMUN_ROOMS'));
		for($i=1;$i<=20;$i++){
			$roomsArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['nrooms'] = HTMLHelper::_('select.genericlist',$roomsArr,'nrooms','class="input-small form-select"','value','text',$nrooms);
		
		//order by, order type
		$sortBy[] = HTMLHelper::_('select.option','a.pro_name',Text::_('OS_TITLE'));
		$sortBy[] = HTMLHelper::_('select.option','a.ref',Text::_('Ref #'));
		$sortBy[] = HTMLHelper::_('select.option','a.created',Text::_('OS_LIST_DATE'));
		$sortBy[] = HTMLHelper::_('select.option','a.modified',Text::_('OS_MODIFIED'));
		$sortBy[] = HTMLHelper::_('select.option','a.price',Text::_('OS_PRICE'));
		$sortBy[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
		if($orderby == " a.price_call desc, a.price")
		{
			$orderbySelected = "a.price";
		}
		$lists['sortby'] = HTMLHelper::_('select.genericlist',$sortBy,'orderby','class="input-small form-select ilarge"','value','text',$orderbySelected);
		
		$ordertypeArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
		$ordertypeArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
		$lists['ordertype'] = HTMLHelper::_('select.genericlist',$ordertypeArr,'ordertype','class="input-medium form-select imedium"','value','text',$ordertype);
		
		$lists['keyword'] = $keyword;
		
		$agentArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_AGENT'));
		if($company_id > 0)
		{
			$company_query = " and a.company_id = '$company_id'";
		}
		else
		{
			$company_query = " ";
		}

		$connector = ($configClass['joomlauser'] == 1) ? 'left' : 'inner';

		$db->setQuery("Select a.id as value, a.name as text from #__osrs_agents as a ".$connector." join #__users as b on b.id = a.user_id where a.published = '1' $company_query order by a.name");
		$agentlists = $db->loadObjectList();
		$agentArr = array_merge($agentArr,$agentlists);
		$lists['agent'] = HTMLHelper::_('select.genericlist',$agentArr,'agent_id','class = "input-small"','value','text',$agent_id);
		
		$lists['show_filterform'] = $show_filterform;
		$lists['price_value'] = $price;
		$lists['property_type'] = $property_type;
		
		$lists['agenttype'] = $agenttype;
		
		HTML_OspropertyListing::listProperties($option,$rows,$pageNav,$lists,$filterParams);
	}
	
	/**
	 * User favorites
	 *
	 * @param unknown_type $option
	 */
	static function favorites($option){
		global $bootstrapHelper, $mainframe,$configClass;
		$db = Factory::getDBO();
		if(!HelperOspropertyCommon::isUser()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_HAVE_NOT_GOT_PERMISSION_GO_TO_THIS_AREA'));
		}
		$document = Factory::getDocument();
		OSPHelper::generateHeading(1,$configClass['general_bussiness_name']." - ".Text::_('OS_MY_FAVORITES'));
		
		echo HelperOspropertyCommon::buildToolbar('property');
		
		$user = Factory::getUser();
		if(intval($user->id) > 0){
			$db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id'");
			$countFav = $db->loadResult();
		}
		HTML_OspropertyListing::favorites($option,$countFav);
	}
	
	
	/**
	 * Property details
	 *
	 * @param unknown_type $option
	 */
	static function details($option)
    {
		global $bootstrapHelper, $mainframe,$configClass,$ismobile,$lang_suffix,$languages,$jinput;
		$translatable   = Multilanguage::isEnabled() && count($languages);
		$db             = Factory::getDBO();
		$config         = Factory::getConfig();
		$pathway	    = $mainframe->getPathway();
		
		$id = $jinput->getInt('id',0);
		if($id == 0)
		{
			if($configClass['property_not_avaiable'] != "")
			{
				OSPHelper::redirect($configClass['property_not_avaiable']);
			}
			else
			{
				throw new Exception(Text::_('OS_PROPERTY_IS_NOT_AVAILABLE'), 404);
			}
		}
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();

		$preview        = $jinput->getInt('preview',0);

		if(($property->published == 0 || $property->approved == 0) && $preview == 0)
		{
			if($configClass['property_not_avaiable'] != "")
			{
				OSPHelper::redirect($configClass['property_not_avaiable']);
			}
			else
			{
				//JError::raiseError( 404, Text::_('OS_PROPERTY_IS_NOT_AVAILABLE') );
				throw new Exception(Text::_('OS_PROPERTY_IS_NOT_AVAILABLE'), 404);
			}
		}
		$pro_name       = OSPHelper::getLanguageFieldValue($property,'pro_name');
		$property->pro_name = $pro_name;
		$pro_small_desc = OSPHelper::getLanguageFieldValue($property,'pro_small_desc');
		$property->pro_small_desc = $pro_small_desc;
		$pro_full_desc  = OSPHelper::getLanguageFieldValue($property,'pro_full_desc');
		$property->pro_full_desc = $pro_full_desc;

		$document = Factory::getDocument();

		//find Itemid of property
		$needs = [];
		$needs[] = "property_details";
		$needs[] = $id;
		$property_item_id = OSPRoute::getItemid($needs);

		
		$show_meta = 1;
		$itemid = $jinput->getInt('Itemid',0);

		if($itemid != $property_item_id)
		{
			foreach ( $document->_links as $k => $array )
			{
				if ( $array['relation'] == 'canonical' )
				{
					unset($document->_links[$k]);
				}
			}
			$plink = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$property_item_id);
			$document->addCustomTag('<link rel="canonical" content="'.$plink.'" />');
		}

		if($itemid > 0)
		{
			$menus						= Factory::getApplication()->getMenu();
			$current_menu				= $menus->getItem($itemid);
			$current_menu_query			= $current_menu->query;
			$view						= $current_menu_query['view'];
			if($view == "ldetails")
			{
				$current_menu_params	= $current_menu->getParams();
				//$show_page_heading	= $current_menu_params['show_page_heading'];
				$page_heading = $current_menu_params['page_heading'];
				if($page_heading != "")
				{
					$property->pro_name = $page_heading;
				}
				$page_title = $current_menu_params['page_title'];
				if($page_title != "")
				{
					//page title
					OSPHelper::generateHeading(1,$page_title);
				}
				elseif(OSPHelper::getLanguageFieldValue($property,'pro_browser_title') != "")
                {
					OSPHelper::generateHeading(1,OSPHelper::getLanguageFieldValue($property,'pro_browser_title'));
				}
				else
				{
					//page title
					OSPHelper::generateHeading(1,OSPHelper::getLanguageFieldValue($property,'pro_name'));
				}

				$meta_description = $current_menu_params['menu-meta_description'];
				if($meta_description != ""){
					$document->setMetaData( "description", $meta_description ); 
					$show_meta = 0;
				}
			}
			elseif(OSPHelper::getLanguageFieldValue($property,'pro_browser_title') != "")
            {
				OSPHelper::generateHeading(1,OSPHelper::getLanguageFieldValue($property,'pro_browser_title'));
			}
			else
			{
				//page title
				OSPHelper::generateHeading(1,OSPHelper::getLanguageFieldValue($property,'pro_name'));
			}
		}
		elseif(OSPHelper::getLanguageFieldValue($property,'pro_browser_title') != "")
        {
			OSPHelper::generateHeading(1,OSPHelper::getLanguageFieldValue($property,'pro_browser_title'));
		}
		else
		{
			//page title
			OSPHelper::generateHeading(1,OSPHelper::getLanguageFieldValue($property,'pro_name'));
		};

		//store this property into cookie
		$session = Factory::getSession();
        $session->set('pid',$property->id);//add Property ID into session
		$recent_properties_viewed = [];
		$recent_properties_viewed_str = $session->get('recent_properties_viewed',''); //$_COOKIE['recent_properties_viewed'];
		if($recent_properties_viewed_str != ""){
			$recent_properties_viewed = explode(",",$recent_properties_viewed_str);
		}
		if(count($recent_properties_viewed) == 0){
			$recent_properties_viewed[] = $id;
		}else{
			if(!in_array($id,$recent_properties_viewed)){
				$recent_properties_viewed[] = $id;
			}else{
				$key = array_search($id,$recent_properties_viewed);
				unset($recent_properties_viewed[$key]);
				$recent_properties_viewed[] = $id;
			}
		}
		$session->set('recent_properties_viewed',implode(",",$recent_properties_viewed));
		$agent_id = $property->agent_id;
		$user = Factory::getUser();
		$owner = 0;
		if(intval($user->id) > 0)
		{
			$db->setQuery("Select count(a.id) from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where a.user_id = '$user->id'");
			$countagent = $db->loadResult();
			if($countagent > 0){
				$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
				$user_agent_id = $db->loadResult();
				if($user_agent_id == $agent_id){
					$owner = 1;
				}
			}
			//check to see if user is already comments for this property
            /*
			if($owner == 0){
				$db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$property->id' and user_id = '$user->id'");	
				$isAlreadyComment = $db->loadResult();
				if($isAlreadyComment > 0){
					$owner = 1;
				}
			}
            */
		}

		if($show_meta == 1)
		{
			// set meta keywords
			$keywords = "";
			if($property->id)
			{
				$query = "Select a.* from #__osrs_tags as a inner join #__osrs_tag_xref as b on b.tag_id = a.id where b.pid = '$property->id'";
				$db->setQuery($query);
				$tags = $db->loadObjectList();
			}

			if($translatable)
			{
				$metadesc = $property->{'metadesc'.$lang_suffix};
				
				if($metadesc == "")
				{
					$metadesc = $property->metadesc;
				}
				if(count($tags) > 0)
				{
					$tagArr = [];
					foreach ($tags as $tag)
					{
						$tagArr[] = $tag->{'keyword'.$lang_suffix};
					}
					if(count($tagArr) > 0){
						$keywords = implode(", ",$tagArr);
					}
				}
			}
			else
			{
				$metadesc = $property->metadesc;

				if(count($tags) > 0)
				{
					$tagArr = [];
					foreach ($tags as $tag)
					{
						$tagArr[] = $tag->keyword;
					}
					if(count($tagArr) > 0)
					{
						$keywords = implode(", ",$tagArr);
					}
				}
			}
			
			$orig_metakey = $document->getMetaData('keywords');
			if( $keywords != "" ) $document->setMetaData( "keywords", $keywords );

			$orig_metadesc = $document->getMetaData('description');
			if($metadesc == "")
			{
				$metadesc = $property->pro_small_desc;
			}
			if( $property->metadesc ) $document->setMetaData( "description", $metadesc );  
		}
		
		$access = $property->access;
        if(!in_array($access,Factory::getUser()->getAuthorisedViewLevels()) && (!HelperOspropertyCommon::isOwner($property->id)))
		{
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}
		
		//add pathway
		$catArr = [];
		$query = $db->getQuery(true);
		$query->select('category_id')->from('#__osrs_property_categories')->where('pid = "'.$property->id.'"');
		$db->setQuery($query);
		$property->category_id = $db->loadResult();
		$catArr = HelperOspropertyCommon::getCatList($property->category_id,$catArr);
		$catArr = array_reverse($catArr);
		if($configClass['include_categories'] == 1) {
			if(count($catArr) > 0){
				for($i=0;$i<count($catArr);$i++){
					$pathway->addItem($catArr[$i]->cat_name,Route::_('index.php?option=com_osproperty&task=category_details&id='.$catArr[$i]->id.'&Itemid='.$jinput->getInt('Itemid',0)));
				}
			}
		}
		$type_id = $property->pro_type;
		$db->setQuery("Select * from #__osrs_types where id = '$type_id'");
		$type = $db->loadObject();
		$type_name = OSPHelper::getLanguageFieldValue($type,'type_name');
		$needs = [];
		$needs[] = "ltype";
		$needs[] = "property_type";
		$needs[] = "type_id=".$type_id;
		$itemid  = OSPRoute::getItemid($needs);
        if($configClass['include_type'] == 1) {
            $pathway->addItem($type_name, Route::_('index.php?option=com_osproperty&view=ltype&type_id=' . $type_id . '&Itemid=' . $itemid));
        }
		$needs = [];
		$needs[] = "property_type";
		$needs[] = $property->id;
		$itemid  = OSPRoute::getItemid($needs);
        if($configClass['include_categories'] == 1) {
            $pathway->addItem(OSPHelper::getLanguageFieldValue($property, 'pro_name'), Route::_('index.php?option=com_osproperty&task=property_details&id=' . $property->id . '&Itemid=' . $itemid));
        }

		//increase hits
		$user_data = "";
		$user_data = $_COOKIE['osp_user_data'.$property->id];
		if($user_data == "")
		{
			$user_data = md5(time());
			setcookie('osp_user_data'.$property->id,$user_data,time() + 300);
			//increase hits
			$hits = $property->hits;
			$hits++;
			$db->setQuery("Update #__osrs_properties set hits = '$hits' where id = '$property->id'");
			$db->execute();

            $date           = Factory::getDate('now', $config->get('offset'));
            $hit_date       = $date->year."-".$date->month."-".$date->day;
            $db->setQuery("Insert into #__osrs_hit_statistic (id, pid, hit_date) values (NULL,'$property->id','$hit_date')");
            $db->execute();

			$hit_time = time();
			setcookie('osp_hit_time'.$property->id,$hit_time,time() + 300);
		}
		else
		{
			$hit_time = $_COOKIE['osp_hit_time'.$property->id];
			if($hit_time <= time() - 300)
			{
                $date           = Factory::getDate('now', $config->get('offset'));
                $hit_date       = $date->year."-".$date->month."-".$date->day;
                $db->setQuery("Insert into #__osrs_hit_statistic (id, pid, hit_date) values (NULL,'$property->id','$hit_date')");
                $db->execute();

				$hits = $property->hits;
				$hits++;
				$db->setQuery("Update #__osrs_properties set hits = '$hits' where id = '$property->id'");
				$db->execute();
			}
		}
		
		//get the list of photos
		$db->setQuery("Select image from #__osrs_photos where pro_id = '$property->id' order by ordering");
		$image = $db->loadResult();
		if($image != ""){
			if(file_exists(JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$image)){
				$link = Uri::root()."images/osproperty/properties/".$id."/medium/".$image;
				$document->addCustomTag('<link rel="image_src" href="'.$link.'" />');
				$document->addCustomTag('<meta property="og:image" content="'.$link.'" />');
			}
		}
        $document->addCustomTag( '<meta property="og:title" content="'.$pro_name.'" />' );
        $uri = Uri::getInstance();
        $document->addCustomTag( '<meta property="og:url" content="'.htmlspecialchars( $uri->toString() ).'" />' );
        $document->addCustomTag( '<meta property="og:type" content="website" />' );
		//$short_description = strip_tags(OSPHelper::getLanguageFieldValue($property,'pro_small_desc'));
		//$short_description = strip_tags(html_entity_decode(preg_replace( "/\r|\n/", "", $short_description )));
        $document->addCustomTag( '<meta property="og:description" content="'.$row->metadesc.'" />' );

		//pro_video
		if($property->pro_video != ""){
			$property->pro_video = "<div class='video-container'>".$property->pro_video."</div>";
		}
		
		//country;
		//$db->setQuery("select country_name from #__osrs_countries where id = '$property->country'");
		$property->country_name = OSPHelper::getCountryName($property->country);
		
		//state;
		//$db->setQuery("select state_name$lang_suffix as state_name from #__osrs_states where id = '$property->state'");
		$property->state_name = OSPHelper::loadSateName($property->state);//$db->loadResult();
		
		//cat;
		$db->setQuery("select * from #__osrs_categories where id = '$property->category_id'");
		$category = $db->loadObject();
		$property->category_name = OSPHelper::getCategoryNamesOfPropertyWithLinks($property->id);
		
		//type;
		$db->setQuery("select * from #__osrs_types where id = '$property->pro_type'");
		$property_type = $db->loadObject();
		$property->type_name = OSPHelper::getLanguageFieldValue($property_type,'type_name');
		
		
		$optionArr = [];
		$optionArr[] = Text::_('OS_GENERAL_AMENITIES');
		$optionArr[] = Text::_('OS_ACCESSIBILITY_AMENITIES');
		$optionArr[] = Text::_('OS_APPLIANCE_AMENITIES');
		$optionArr[] = Text::_('OS_COMMUNITY_AMENITIES');
		$optionArr[] = Text::_('OS_ENERGY_SAVINGS_AMENITIES');
		$optionArr[] = Text::_('OS_EXTERIOR_AMENITIES');
		$optionArr[] = Text::_('OS_INTERIOR_AMENITIES');
		$optionArr[] = Text::_('OS_LANDSCAPE_AMENITIES');
		$optionArr[] = Text::_('OS_SECURITY_AMENITIES');
		$l = 0;
        if($configClass['show_unselected_amenities'] == 1) {
            ob_start();
            foreach ($optionArr as $amen_cat) {
                $query = "Select * from #__osrs_amenities where published = '1' and category_id = '$l' order by ordering";
                $db->setQuery($query);
                $amenities = $db->loadObjectList();

                $query = "Select a.id from #__osrs_amenities as a"
                    . " inner join #__osrs_property_amenities as b on b.amen_id = a.id"
                    . " where a.published = '1' and b.pro_id = '$property->id' and a.category_id = '$l' order by a.ordering";
                $db->setQuery($query);
                $property_amenities = $db->loadColumn(0);
                $amens_str1 = "";
                if (count($amenities) > 0) 
				{
                    $amens_str = "";
                    $j = 0;
                    $k = 0;
                    if ($configClass['amenities_layout'] == 1) 
					{
                        $span = $bootstrapHelper->getClassMapping('span6');
                        $jump = 2;
                    } 
					else 
					{
                        $span = $bootstrapHelper->getClassMapping('span4');
                        $jump = 3;
                    }
                    ?>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
	                        <h4><?php echo $amen_cat?></h4>
						</div>
                    </div>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                        <?php
                        for ($i = 0; $i < count($amenities); $i++) 
						{
                            $k++;
                            $amen = $amenities[$i];
                            if (in_array($amen->id, $property_amenities)) 
							{
                                $style = "color:#99103A;";
                                $style1 = "";
                            } 
							else 
							{
                                $style = "color:#CCC;";
                                $style1 = $style;
                            }
                            ?>
                            <div class="<?php echo $span;?>" style="<?php echo $style1;?>">
								<?php
								if($amen->icon != "")
								{
									?>
									<i class="<?php echo $amen->icon;?>"></i>&nbsp;
									<?php
								}
								else
								{
									?>
									<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
</svg>
									<?php
								}
								?>
                                <?php echo OSPHelper::getLanguageFieldValue($amen, 'amenities');?>
                            </div>
                            <?php
                            if ($k % $jump == 0) 
							{
                                $k = 0;
                                echo "</div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'>";
                            }
                        }
                        ?>
                    </div>
                <?php
                }
                $l++;
            }
            $amens_str = ob_get_contents();
            ob_end_clean();
        }
		else
		{
			$amenityCategoryIcons = array('edicon-cog','edicon-lifebuoy','edicon-power-cord','edicon-bullhorn','edicon-fire','edicon-paint-format','edicon-podcast','edicon-image','edicon-lock');
			$icon = 0;
            ob_start();
            foreach ($optionArr as $amen_cat)
			{
                $query = "Select a.* from #__osrs_amenities as a"
                    ." inner join #__osrs_property_amenities as b on b.amen_id = a.id"
                    ." where a.published = '1' and b.pro_id = '$property->id' and a.category_id = '$l' order by a.ordering";
                $db->setQuery($query);
                $amens = $db->loadObjectList();
                $amens_str1 = "";
                if(count($amens) > 0){
                    $amens_str = "";
                    $j = 0;
                    $k = 0;
                    if($configClass['amenities_layout'] == 1)
					{
                        $span = $bootstrapHelper->getClassMapping('span6'); //"span6";
                        $jump = 2;
                    }
					else
					{
                        $span = $bootstrapHelper->getClassMapping('span4');
                        $jump = 3;
                    }
                    ?>
					<h4><?php echo $amen_cat?></h4>
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin nopadding amenitygroup">
						<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
							<?php
							for($i=0;$i<count($amens);$i++)
							{
								$k++;
								$amen = $amens[$i];
								?>
								<div class="<?php echo $span;?>">
									<?php
									if($amen->icon != "")
									{
										?>
										<i class="<?php echo $amen->icon;?>"></i>&nbsp;
										<?php
									}
									else
									{
										?>
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
	  <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
	</svg>
										<?php
									}
									?>
									<?php echo OSPHelper::getLanguageFieldValue($amen,'amenities');?>
								</div>
								<?php
								if($k % $jump == 0)
								{
									$k = 0;
									echo "</div><div class='".$bootstrapHelper->getClassMapping('row-fluid')."'>";
								}
							}
							?>
						</div>
					</div>
                <?php
                }
                $l++;
				$icon++;
            }
            $amens_str = ob_get_contents();
            ob_end_clean();
        }
		$property->amens_str = $amens_str;
		$property->amens_str1 = $amens_str;

		//get the field groups
        $access_sql = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$extra_field_groups = [];
		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
		$fieldgroups = $db->loadObjectList();
		$j = 0;
		if(count($fieldgroups) > 0){
			for($i=0;$i<count($fieldgroups);$i++){
				$fieldgroup = $fieldgroups[$i];

                $access_sql = ' and b.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
				$checkgroup = HelperOspropertyFields::checkFieldData($property->id, $fieldgroup->id);
				
				if($checkgroup == 1)
				{
					$extra_field_groups[$j]	= new stdClass();
					$extra_field_groups[$j]->group_name = OSPHelper::getLanguageFieldValue($fieldgroup,'group_name');
					$extra_field_groups[$j]->fields = HelperOspropertyFields::getFieldsData($property->id, $fieldgroup->id);
					$j++;
				}
			}
		}
		
		$property->extra_field_groups = $extra_field_groups;
		
		$agent = new stdClass();

		if((int)$property->agent_id > 0)
		{
			//agent information
			$db->setQuery("Select * from #__osrs_agents where id = '$property->agent_id'");
			$agent = $db->loadObject();
			

			//agent country;
			//$db->setQuery("select country_name from #__osrs_countries where id = '$agent->country'");
			$agent->country_name = OSPHelper::getCountryName($agent->country);
			
			//agent state;
			//$db->setQuery("select state_name$lang_suffix as state_name from #__osrs_states where id = '$agent->state'");
			$agent->state_name = OSPHelper::loadSateName($agent->state);//$db->loadResult();
		
			$db->setQuery("Select count(id) from #__osrs_properties where published = '1' and approved = '1' and agent_id = '$property->agent_id'");
			$agent->countlisting = $db->loadResult();
			$property->agent = $agent;
		}
		
		if($configClass['comment_active_comment'] == 1){
			$db->setQuery("Select * from #__osrs_comments where pro_id = '$id' and published = '1' order by created_on desc");
			$comments = $db->loadObjectList();
			$property->comments = $comments;
			$property->comment_raw = $comments;
		}

		$property->core_fields			= OSPHelper::showCoreFields($property);
		$property->core_fields1			= OSPHelper::showCoreFields1($property);
		
		//it in search result
		$session = Factory::getSession();
		$advsearchresult = $session->get('advsearchresult',array());
		
		if(count($advsearchresult) > 0){
			$property->pagination = 1;
			$key = array_search($id,$advsearchresult);
			//echo $key;
			//echo $id;
			if($key == 0){
				$prev = $advsearchresult[count($advsearchresult)-1];
				$next = $advsearchresult[1];
			}elseif($key == count($advsearchresult)-1){
				$prev = $advsearchresult[$key - 1];
				$next = $advsearchresult[0];
			}else{
				$prev = $advsearchresult[$key - 1];
				$next = $advsearchresult[$key + 1];
			}
			$property->next = $next;
			$property->prev = $prev;
			$itemid = OSPRoute::getPropertyItemid($property->next);
			$property->next_link = Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->next.'&Itemid='.$itemid);
			$db->setQuery("Select pro_type,pro_name,pro_name".$lang_suffix." from #__osrs_properties where id = '$property->next'");
			$nextObj = $db->loadObject();			
			$property->next_property = OSPHelper::getLanguageFieldValue($nextObj,"pro_name");
			$db->setQuery("Select type_name,type_name".$lang_suffix." from #__osrs_types where id = '$nextObj->pro_type'");
			$property->next_type = OSPHelper::getLanguageFieldValue($db->loadObject(),"type_name");
			
			$itemid = OSPRoute::getPropertyItemid($property->prev);
			$property->prev_link = Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->prev.'&Itemid='.$itemid);
			$db->setQuery("Select pro_type,pro_name,pro_name".$lang_suffix." from #__osrs_properties where id = '$property->prev'");
			$prevObj = $db->loadObject();	
			$property->prev_property = OSPHelper::getLanguageFieldValue($prevObj,"pro_name");
			$db->setQuery("Select type_name,type_name".$lang_suffix." from #__osrs_types where id = '$prevObj->pro_type'");
			$property->prev_type = OSPHelper::getLanguageFieldValue($db->loadObject(),"type_name");
		}else{
			$property->pagination = 0;
		}
		
		//allow to show the relate properties
		if($configClass['relate_properties'] == 1){
			$radius_search = $configClass['relate_distance'];
			if(intval($radius_search) == 0){
				$radius_search = 5;
			}
			if($configClass['locator_radius_type'] == 0){
				$multiFactor = 3959;
			}else{
				$multiFactor = 6371;
			}
			// Search the rows in the table
			$select = sprintf(", ( %s * acos( cos( radians('%s') ) * 
								cos( radians( pr.lat_add ) ) * cos( radians( pr.long_add ) - radians('%s') ) + 
								sin( radians('%s') ) * sin( radians( pr.lat_add ) ) ) ) 
								AS distance",
								$multiFactor,
								doubleval($property->lat_add),
								doubleval($property->long_add),
								doubleval($property->lat_add)
								);
			$where = sprintf("	HAVING distance < '%s'", doubleval($radius_search));
			
			$Order_by = " ORDER BY distance ASC, pr.isFeatured desc ";
			$sql =   " SELECT pr.* "
					."\n, ci.city AS city_name"
					."\n, st.state_name" 
					."\n, co.country_name"
					."\n, ty.type_name$lang_suffix, ty.id as type_id"
					.$select
					."\n FROM #__osrs_properties as pr"
					."\n LEFT JOIN #__osrs_cities AS ci ON ci.id = pr.city "
					."\n LEFT JOIN #__osrs_states AS st ON st.id = pr.state"
					."\n LEFT JOIN #__osrs_countries AS co ON co.id = pr.country"
					."\n INNER JOIN #__osrs_types AS ty ON ty.id = pr.pro_type"
					
					."\n WHERE 	pr.approved  = '1' and pr.published = '1'"
					."\n AND pr.access IN (" . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ")"
					."\n AND	pr.id 	   	<> '$property->id' AND 	pr.state 	 = '$property->state' ";
			$sql .= OSPHelper::generateRelatedProperties($property->id);
			if($configClass['relate_city'] == 1){
				$sql .= "\n AND 	pr.city		 = '$property->city'";
			}
			if($configClass['relate_category'] == 1){
				//$sql .= "\n AND 	pr.category_id		 = '$property->category_id'";
			}
			$sql .=	$where.$Order_by;
			if($configClass['max_relate'] > 0){
				$sql .= " LIMIT ".$configClass['max_relate'];
			}
			$db->setQuery($sql);
			//echo $db->getQuery();
			$relates = $db->loadObjectList();
			if (count($relates)){
				for($i=0;$i<count($relates);$i++){//for
					$relate = $relates[$i];
					$type_name = OSPHelper::getLanguageFieldValue($relate,'type_name');
					$relate->type_name = $type_name;
					$db->setQuery("select image from #__osrs_photos where pro_id = '$relate->id' order by ordering");
					$relate->photo = $db->loadResult();
					if ($relate->photo == ''){
						$relate->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
					}else {
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$relate->id.'/thumb/'.$relate->photo)){
							$relate->photo = Uri::root()."images/osproperty/properties/".$relate->id."/thumb/".$relate->photo;	
						}else{
							$relate->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";	
						}
					}

					if($configClass['load_lazy']){
						$relate->photosrc = Uri::root()."media/com_osproperty/assets/images/loader.gif";
					}else{
						$relate->photosrc = $relate->photo;
					}

					$needs = [];
					$needs[] = "property_details";
					$needs[] = $relate->id;
					$item_id = OSPRoute::getItemid($needs);
					$relate->itemid = $item_id;
					
				}//for
				$property->relate_properties = $relates;
			}// if count($relate) > 0
		}
		
		if($configClass['relate_property_type'] == 1){
			// Search the rows in the table
			$Order_by = " ORDER BY pr.isFeatured desc ";
			$sql =   " SELECT pr.* "
					."\n, ci.city AS city_name"
					."\n, st.state_name" 
					."\n, co.country_name"
					."\n, ty.type_name$lang_suffix, ty.id as type_id"
					."\n FROM #__osrs_properties as pr"
					."\n LEFT JOIN #__osrs_cities AS ci ON ci.id = pr.city "
					."\n LEFT JOIN #__osrs_states AS st ON st.id = pr.state"
					."\n LEFT JOIN #__osrs_countries AS co ON co.id = pr.country"
					."\n INNER JOIN #__osrs_types AS ty ON ty.id = pr.pro_type"
					."\n WHERE 	pr.approved  = '1' and pr.published = '1'" 
					."\n AND pr.id <> '$property->id' "
					."\n AND pr.access IN (" . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ")";
            $sql .= OSPHelper::generateRelatedProperties($property->id);
			if($configClass['relate_property_type'] == 1){
				$sql .= "\n AND 	pr.pro_type  = '$property->pro_type'";
			}
			$sql .=	$Order_by;
			if($configClass['max_relate'] > 0){
				$sql .= " LIMIT ".$configClass['max_relate'];
			}
			$db->setQuery($sql);
			//echo $db->getQuery();
			$relates = $db->loadObjectList();
			if (count($relates)){
				for($i=0;$i<count($relates);$i++){//for
					$relate = $relates[$i];
					$type_name = OSPHelper::getLanguageFieldValue($relate,'type_name');
					$relate->type_name = $type_name;
					$db->setQuery("select image from #__osrs_photos where pro_id = '$relate->id' order by ordering");
					$relate->photo = $db->loadResult();
					if ($relate->photo == ''){
						$relate->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
					}else {
						if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$relate->id.'/thumb/'.$relate->photo)){
							$relate->photo = Uri::root()."images/osproperty/properties/".$relate->id."/thumb/".$relate->photo;	
						}else{
							$relate->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";	
						}
					}

					if($configClass['load_lazy']){
						$relate->photosrc = Uri::root()."media/com_osproperty/assets/images/loader.gif";
					}else{
						$relate->photosrc = $relate->photo;
					}

					$needs = [];
					$needs[] = "property_details";
					$needs[] = $relate->id;
					$item_id = OSPRoute::getItemid($needs);
					$relate->itemid = $item_id;
				}//for
				$property->relate_type_properties = $relates;
			}// if count($relate) > 0
		}
		
		HTML_OspropertyListing::propertyDetails($option,$property,$configs,$owner);		
	}
	
	
	
	
	/**
	 * List photos of property
	 *
	 * @param unknown_type $option
	 * @param unknown_type $property_id
	 */
	static function listingPhotos($option,$property_id){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDBO();
		$db->setQuery("Select * from #__osrs_photos where pro_id = '$property_id' order by ordering");
		$photos = $db->loadObjectList();
		if(count($photos) > 0){
			return HTML_OspropertyListing::listingPhotos($option,$property_id,$photos);
		}else{
			return Text::_('No photo');
		}
	}
	
	/**
	 * Edit/Add new property
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function edit($option,$id, $type = 0)
	{
		global $bootstrapHelper, $mainframe,$configs,$configClass,$lang_suffix,$languages,$jinput;

		HTMLHelper::_('behavior.keepalive');
		$db = Factory::getDBO();
		$user = Factory::getUser();
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/classes/property.php");
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/helpers/extrafields.php");
		//check to see if user is agent
		if($user->id == 0){
			$returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$returnUrl = base64_encode($returnUrl);
			OSPHelper::redirect(Route::_('index.php?option=com_users&view=login&return='.$returnUrl),Text::_('OS_LOGIN_WARNING'));
		}

		if (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) 
		{
			if($type == 0)
			{
				if((!HelperOspropertyCommon::isRegisteredAgent()) && (!HelperOspropertyCommon::isRegisteredCompanyAdmin()))
				{
					$agent_id = OSPHelper::registerNewAgent($user,$configClass['default_user_type']);
				}

				if((HelperOspropertyCommon::isCompanyAdmin()) && ($configClass['company_admin_add_properties'] == 0))
				{
					OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
				}

				if($configClass['company_admin_add_properties'] == 1)
				{
					if((! HelperOspropertyCommon::isCompanyAdmin()) && (HelperOspropertyCommon::isRegisteredCompanyAdmin()) )
					{
						OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
					}
				}
				
				if($id > 0)
				{
					if((!HelperOspropertyCommon::isOwner($id)) && (!HelperOspropertyCommon::isCompanyOwner($id)))
					{
						OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
					}
				}
				
				if(($configClass['general_agent_listings'] != 1) && ($id == 0) && (HelperOspropertyCommon::isAgent()))
				{
					OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
				}
			}
		}

		$inputLargeClass	= $bootstrapHelper->getClassMapping('input-large');
		$inputMediumClass	= $bootstrapHelper->getClassMapping('input-medium');
		$inputSmallClass	= $bootstrapHelper->getClassMapping('input-small');
		$inputMiniClass		= $bootstrapHelper->getClassMapping('input-mini');

		$document = Factory::getDocument();
		//include css of theme2
		$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/theme2/style/style.css");
		$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/theme2/style/font.css");
		if($id > 0)
		{
			$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_EDIT_PROPERTY'));
			//check to see if current user is owner of the property
			if(($type == 0) && (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty')))
			{
				if(HelperOspropertyCommon::isAgent())
				{
					$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
					$agent_id = $db->loadResult();
					$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$agent_id' and id = '$id'");
					$count = $db->loadResult();
					if($count == 0){
						OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
					}
				}
				elseif(!HelperOspropertyCommon::isCompanyOwner($id))
                {
					OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));	
				}
			}
		}
		else
		{
			$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_ADD_NEW_PROPERTY'));
		}
		$session = Factory::getSession();
		$session->set('ownid',md5($user->id));

		//load Property Instance Table Object
		
		$row = Table::getInstance('Property','OspropertyTable');
		$amenitylists = [];
		if($id > 0){
			$row->load((int)$id);
			$db->setQuery("Select amen_id from #__osrs_property_amenities where pro_id = '$row->id'");
			$amenitylists = $db->loadOBjectList();
			$amenitylists1 = [];
			if(count($amenitylists) > 0){
				for($i=0;$i<count($amenitylists);$i++){
					$amenitylists1[$i] = $amenitylists[$i]->amen_id;
				}
				$amenitylists = [];
				$amenitylists = $amenitylists1;
			}
			
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering");
			$photos = $db->loadobjectList();
			$row->photo = $photos;
			$isNew = 0;
		}else{
			$row->published = 1;
			$row->access = 0;
			$row->price_call = 0;
            $row->isSold = 0;
			$row->isFeatured = 0;
			$row->show_address = 1;
			$isNew = 1;
		}
		
		if($row->approved == 0){
			$db->setQuery("Update #__osrs_expired set expired_time = ".$db->quote($db->getNullDate())." where pid = ".(int)$row->id);
			$db->execute();
		}
		if($row->isFeatured == 0){
			$db->setQuery("Update #__osrs_expired set expired_feature_time = ".$db->quote($db->getNullDate())." where pid = ".(int)$row->id);
			$db->execute();
		}
		
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		
		//$lists['state'] = HTMLHelper::_('select.genericlist',$optionArr,'published','class="input-mini"','value','text',$row->published);
		$lists['featured'] = HTMLHelper::_('select.genericlist',$optionArr,'isFeatured','class="'.$inputSmallClass.' chosen"','value','text',$row->isFeatured);
		$lists['approved'] = HTMLHelper::_('select.genericlist',$optionArr,'approved','class="'.$inputSmallClass.' chosen"','value','text',$row->approved);
		$optionArr1 = [];
		$optionArr1[] = HTMLHelper::_('select.option',1,Text::_('OS_YES'));
		$optionArr1[] = HTMLHelper::_('select.option',0,Text::_('OS_NO'));
		$lists['show_address'] = HTMLHelper::_('select.genericlist',$optionArr1,'show_address','class="'.$inputLargeClass.' form-select"','value','text',$row->show_address);
		$lists['price_call'] = HTMLHelper::_('select.genericlist',$optionArr,'price_call','class="'.$inputLargeClass.' form-select chosen" onChange="javascript:showPriceFields()"','value','text',$row->price_call);
		$lists['property_sold'] = HTMLHelper::_('select.genericlist',$optionArr,'isSold','class="'.$inputLargeClass.' chosen form-select"','value','text',$row->isSold);

		//agent
		
		//check to see if i am agent
		//$db->setQuery("Select count(id) from #__osrs_agents where user_id = '$user->id'");
		//$count = $db->loadResult();


		if(!HelperOspropertyCommon::isAgent())
		{
			$row->isAgent = 0;
			if(HelperOspropertyCommon::isCompanyAdmin()) 
			{
                //get company id
                $company_id = HelperOspropertyCommon::getCompanyId();
                $agentArr[] = HTMLHelper::_('select.option', '', Text::_('OS_SELECT_AGENT'));
				$connector = ($configClass['joomlauser'] == 1) ? 'left' : 'inner';
                $query = "Select a.id as value,a.name as text from #__osrs_agents as a ".$connector." join #__users as b on b.id = a.user_id where a.published = '1' and a.company_id = '$company_id'";
                $query .= " order by a.name";
                $db->setQuery($query);
                $agents = $db->loadObjectList();
                $agentArr = array_merge($agentArr, $agents);
                $lists['agent'] = HTMLHelper::_('select.genericlist', $agentArr, 'agent_id', 'class="' . $inputMediumClass . '"', 'value', 'text', $row->agent_id);
            }
            elseif($user->authorise('frontendmanage', 'com_osproperty'))
            {

                if($isNew)
                {
                    $agent_id       = OSPHelper::getDefaultAgent();
                }
                else
                {
                    $agent_id       = $row->agent_id;
                }
                $lists['agent'] = "<input type='hidden' name='agent_id' id='agent_id' value='" . $agent_id . "'>";
                $db->setQuery("Select name from #__osrs_agents where id = '$agent_id'");
                $agent_name = $db->loadResult();
                $lists['agentname'] = $agent_name;
            }
		}
		else
		{
			$row->isAgent = 1;
			$db->setQuery("Select id from #__osrs_agents where user_id = '$user->id'");
			$agent_id = $db->loadResult();
			$lists['agent'] = "<input type='hidden' name='agent_id' id='agent_id' value='$agent_id'>";

			$db->setQuery("Select name from #__osrs_agents where user_id = '$user->id'");
			$agent_name = $db->loadResult();
			$lists['agentname'] = $agent_name;
		}
		
		//property types
		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_PROPERTY_TYPE'));
		$db->setQuery("Select id as value,type_name$lang_suffix as text from #__osrs_types where published = '1' order by ordering");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'pro_type','class="'.$inputLargeClass.' chosen"','value','text',$row->pro_type);
		
		//categories
		$categoryIds = OSPHelper::getCategoryIdsOfProperty($row->id);
		$lists['category'] = OSPHelper::dropdownCategory('categoryIds[]',$categoryIds, $inputLargeClass.' chosen');
		
		$lists['country'] = HelperOspropertyCommon::makeCountryList($row->country,'country','onChange="javascript:loadState(this.value,\''.$row->state.'\',\''.$row->city.'\')"',Text::_('OS_SELECT_COUNTRY'),'',$inputLargeClass.' form-select');
		
		if(OSPHelper::userOneState()){
			$lists['state'] = "<input type='hidden' name='state' id='state' value='".OSPHelper::returnDefaultState()."'/>";
		}else{
			$lists['state'] = HelperOspropertyCommon::makeStateListAddProperty($row->country,$row->state,'state','onChange="javascript:loadCity(this.value,\''.$row->city.'\')"',Text::_('OS_SELECT_STATE'),'class="'.$inputLargeClass.' form-select"');
		}
		
		//$lists['city'] = HelperOspropertyCommon::loadCityAddProperty($option,$row->state,$row->city);
		if($id > 0){
			if(intval($row->state) == 0){
				$row->state = OSPHelper::returnDefaultState();
			}
			$lists['city'] = HelperOspropertyCommon::loadCityAddProperty($option,$row->state,$row->city,$inputLargeClass. ' form-select');
		}else{
			$default_state = 0;
			if(OSPHelper::userOneState()){
				$default_state = OSPHelper::returnDefaultState();
			}else{
				$default_state = $row->state;
			}
			$lists['city'] = HelperOspropertyCommon::loadCityAddProperty($option,$default_state,$row->city,$inputLargeClass. ' form-select');
		}

        //number bed rooms
        $lists['nbed'] = OSPHelper::dropdropBed('bed_room',$row->bed_room,$inputSmallClass.' chosen','','OS_BED');
        //number bath rooms
        $lists['nbath'] = OSPHelper::dropdropBath('bath_room',$row->bath_room,$inputSmallClass.' chosen','','OS_BATH');
        //number rooms
        $lists['nrooms'] = OSPHelper::dropdropRoom('rooms',$row->rooms,$inputSmallClass.' chosen','','OS_ROOMS');
        //number floors
        $lists['nfloors'] = OSPHelper::dropdropRoom('number_of_floors',$row->number_of_floors,$inputSmallClass.' chosen','','OS_FLOORS');
		
		$accessArr[] = HTMLHelper::_('select.option','0',Text::_('OS_PUBLIC'));
		$accessArr[] = HTMLHelper::_('select.option','1',Text::_('OS_REGISTERED'));
		$accessArr[] = HTMLHelper::_('select.option','2',Text::_('OS_SPECIAL'));
		$lists['access'] = HTMLHelper::_('select.genericlist',$accessArr,'access','class="'.$inputSmallClass.'" size="2"','value','text',$row->access);
		
		$db->setQuery("select * from #__osrs_amenities where published = '1' order by ordering");
		$amenities = $db->loadObjectList();
		
		
		$db->setQuery("Select * from #__osrs_fieldgroups where id in (Select group_id from #__osrs_extra_fields) and published = '1' order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0){
			for($i=0;$i<count($groups);$i++){
				$group = $groups[$i];
				//if($id > 0){
					//$extraSql = " and id in (Select fid from #__osrs_extra_field_types where type_id = '$row->pro_type') ";
				//}
				$db->setQuery("Select * from #__osrs_extra_fields where published = '1' and group_id = '$group->id' order by ordering");
				$fields = $db->loadObjectList();
				$groups[$i]->fields = $fields;
			}
		}
		
		$timeArr[] = HTMLHelper::_('select.option','',Text::_('OS_NOT_APPLICABLE'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_NIGHT',Text::_('OS_PER_NIGHT'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_WEEK',Text::_('OS_PER_WEEK'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_MONTH',Text::_('OS_PER_MONTH'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_SQUARE_FEET',Text::_('OS_PER_SQUARE_FEET'));
		$timeArr[] = HTMLHelper::_('select.option','OS_PER_SQUARE_METRE',Text::_('OS_PER_SQUARE_METRE'));
		$lists['time'] = HTMLHelper::_('select.genericlist',$timeArr,'rent_time','class="'.$inputLargeClass.' chosen"','value','text',$row->rent_time);
		//html
		
		$db->setQuery("Select * from #__osrs_neighborhoodname");
		$neighborhoods = $db->loadObjectList();
		
		if($row->id)
		{
        	$query = "Select a.* from #__osrs_tags as a inner join #__osrs_tag_xref as b on b.tag_id = a.id where b.pid = '$row->id'";
        	$db->setQuery($query);
            $lists['tags'] = $db->loadObjectList();
        }


        $translatable = Multilanguage::isEnabled() && count($languages);

        $db->setQuery("Select id from #__osrs_types");
        $types = $db->loadObjectList();
        if(count($types) > 0)
		{
        	foreach ($types as $type)
			{
        		$db->setQuery("Select fid from #__osrs_extra_field_types where type_id = '$type->id'");
        		$type->fields = $db->loadColumn(0);

				$db->setQuery("Select a.fid from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1' and b.required = '1'");
        		$type->required_fields = $db->loadColumn(0);

				$db->setQuery("Select b.field_name from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1' and b.required = '1'");
        		$type->required_fields_name = $db->loadColumn(0);

				$db->setQuery("Select b.field_label".$lang_suffix." as field_label from #__osrs_extra_field_types as a left join #__osrs_extra_fields as b on b.id = a.fid where a.type_id = '$type->id' and b.published = '1' and b.required = '1'");
        		$type->required_fields_label = $db->loadColumn(0);
        	}
        }
		$lists['types'] = $types;
		
		if($row->id > 0)
		{
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_price_history")->where("pid = '$row->id'");
			$db->setQuery($query);
			$lists['history'] = $db->loadObjectList();
			
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_history_tax")->where("pid = '$row->id'");
			$db->setQuery($query);
			$lists['tax'] = $db->loadObjectList();
			
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_open")->where("pid = '$row->id'")->order("start_from desc");
			$db->setQuery($query);
			$lists['open'] = $db->loadObjectList();
		}

		if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1)
		{
			$bathInfor = [];
			if($id > 0)
			{
				$bathLabelArray = array('OS_FULL','OS_THREE_QUARTER','OS_HALF','OS_QUARTER','OS_ENSUITE');
				foreach($bathLabelArray as $label)
				{
					$bathInfor[$label] = 0;
					$db->setQuery("Select `bath_value` from #__osrs_property_bath_values where pid = '$id' and bath_label like '$label'");
					$bath_value = $db->loadResult();
					if($bath_value > 0)
					{
						$bathInfor[$label] = $bath_value;
					}
				}
			}
			$lists['bathInfor'] = $bathInfor;
		}

		//expiration checking
		if($configClass['general_use_expiration_management'] == 1){
			$db->setQuery("Select * from #__osrs_expired where pid = '$row->id'");
			$row->expiration = $db->loadObject();
		}

        //payment for checking
		/*
        if($configClass['active_payment'] == 1){
			$paymentMethod = $jinput->getString('payment_method', os_payments::getDefautPaymentMethod(), 'post');
			if (!$paymentMethod) $paymentMethod = os_payments::getDefautPaymentMethod();
        }

		###############Payment Methods parameters###############################
		
		//Creditcard payment parameters		
		$x_card_num         = $jinput->getString('x_card_num', '');
		$expMonth           = $jinput->getString('exp_month', date('m')) ;
		$expYear            = $jinput->getString('exp_year', date('Y')) ;
		$x_card_code        = $jinput->getString('x_card_code', '');
		$cardHolderName     = $jinput->getString('card_holder_name', '') ;
		$lists['exp_month'] = HTMLHelper::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" class="input-mini"  ', $expMonth, '%02d') ;
		$currentYear = date('Y') ;
		$lists['exp_year'] = HTMLHelper::_('select.integerlist', $currentYear, $currentYear + 10 , 1, 'exp_year', ' id="exp_year" class="input-small" ', $expYear) ;
		$options =  array() ;
		$cardTypes = explode(',', $configClass['enable_cardtypes']);
		if (in_array('Visa', $cardTypes)) {
			$options[] = HTMLHelper::_('select.option', 'Visa', Text::_('OS_VISA_CARD')) ;			
		}
		if (in_array('MasterCard', $cardTypes)) {
			$options[] = HTMLHelper::_('select.option', 'MasterCard', Text::_('OS_MASTER_CARD')) ;
		}
		
		if (in_array('Discover', $cardTypes)) {
			$options[] = HTMLHelper::_('select.option', 'Discover', Text::_('OS_DISCOVER')) ;
		}		
		if (in_array('Amex', $cardTypes)) {
			$options[] = HTMLHelper::_('select.option', 'Amex', Text::_('OS_AMEX')) ;
		}		
		$lists['card_type'] = HTMLHelper::_('select.genericlist', $options, 'card_type', ' class="input-large" ', 'value', 'text') ;
		//Echeck
				
		$x_bank_aba_code    = $jinput->getString('x_bank_aba_code', '') ;
		$x_bank_acct_num    = $jinput->getString('x_bank_acct_num', '') ;
		$x_bank_name        = $jinput->getString('x_bank_name', '') ;
		$x_bank_acct_name   = $jinput->getString('x_bank_acct_name', '') ;
		$options = array() ;
		$options[] = HTMLHelper::_('select.option', 'CHECKING', Text::_('OS_BANK_TYPE_CHECKING')) ;
		$options[] = HTMLHelper::_('select.option', 'BUSINESSCHECKING', Text::_('OS_BANK_TYPE_BUSINESSCHECKING')) ;
		$options[] = HTMLHelper::_('select.option', 'SAVINGS', Text::_('OS_BANK_TYPE_SAVING')) ;
		$lists['x_bank_acct_type'] = HTMLHelper::_('select.genericlist', $options, 'x_bank_acct_type', ' class="input-large" ', 'value', 'text', OSPHelper::getStringRequest('x_bank_acct_type')) ;
		
		$methods = os_payments::getPaymentMethods(true, false) ;
		
		$lists['x_card_num'] = $x_card_num;
		$lists['x_card_code'] = $x_card_code;
		$lists['cardHolderName'] = $cardHolderName;
		$lists['x_bank_acct_num'] = $x_bank_acct_num;
		$lists['x_bank_acct_name'] = $x_bank_acct_name;
		$lists['methods'] = $methods;
		$lists['idealEnabled'] = false;
		$lists['paymentMethod'] = $paymentMethod;

		*/

		if($configClass['frontend_upload_type'] == 0){
			$document->addScript(Uri::root().'media/com_osproperty/assets/js/djuploader.js');
			$settings = [];
			$settings['max_file_size'] = '10240kb';
			$settings['chunk_size'] = '1024kb';
			$settings['resize'] = false;
			$settings['width'] = $configClass['max_width_size'];
			$settings['height'] = $configClass['max_height_size'];
			$settings['quality'] = 100;
			$settings['filter'] = 'jpg,png,gif,jpeg,webp';
			$settings['onUploadedEvent'] = 'injectUploaded';
			$settings['onAddedEvent'] = 'startUpload';
			$settings['label_generate'] = 1;
			//$settings['debug'] = true;
			$lists['uploader'] = DJUploadHelper::getUploader('uploader', $settings);
		}

		if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
		{
			include_once(JPATH_ADMINISTRATOR.'/components/com_osproperty/helpers/legacy/behavior.php');
			include_once(JPATH_ADMINISTRATOR.'/components/com_osproperty/helpers/legacy/sliders.php');
		}
        
		HTML_OspropertyListing::editListing($option,$row,$lists,$amenities,$amenitylists,$groups,$configs,$neighborhoods,$translatable,$isNew);
	}
	
	/**
	 * Change property status (Publish/Unpublish)
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function propertyChange($option,$cid,$state){
		global $bootstrapHelper, $mainframe, $jinput;
		$user = Factory::getUser();
		$db = Factory::getDBO();
		if(count($cid) > 0){
			foreach ($cid as $id){
				if(HelperOspropertyCommon::isOwner($id)){
					$db->setQuery("Update #__osrs_properties set published = '$state' where id = '$id'");
					$db->execute();
				}else{
					throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
				}
			}
		}
		
		$needs = [];
		$needs[] = "agent_editprofile";
		$needs[] = "agent_default";
		$needs[] = "aeditdetails";
		$itemid = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_editprofile&Itemid=".$itemid),Text::_('OS_STATUS_HAVE_BEEN_CHANGED'));
	}
	
	/**
	 * Save property
	 *
	 * @param unknown_type $option
	 */
	static function save($option,$save)
    {
		global $bootstrapHelper, $mainframe,$configClass,$bio,$languages,$lang_suffix,$jinput;
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');

		$user = Factory::getUser();
		$session = Factory::getSession();
		$ownid = $session->get('ownid');
		if($ownid  != md5($user->id))
		{
			throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
		}

		$limit_photo = $configClass['limit_upload_photos'];
		if(intval($limit_photo) == 0)
		{
			$limit_photo = 24;
		}
		$db = Factory::getDBO();
		$row = Table::getInstance('Property','OspropertyTable');
		$post = $jinput->post->getArray();

		$row->bind($post);
		$row->ref = (string) $row->ref;
		$id = $jinput->getInt('id',0);
		$isNew = ($id == 0 ? 1:0);
		$passcheck = 0;
		if($isNew == 0)
		{
			$extend = $jinput->getInt('extend',0);
			if($extend == 0)
			{
				$passcheck = 1;
			}
		}
		
		//request feature
		if($configClass['integrate_membership'] == 1)
		{
			//do nothing
		}
		elseif($configClass['active_payment'] == 1)
		{ //Active payment
			if($isNew==1)
			{
				$row->isFeatured = $jinput->getInt('isFeatured',0);
				$touchExpireTable = 1;//setup expired for this property
			}
			else
			{
				$db->setQuery("Select isFeatured from #__osrs_properties where id = '$id'");
				$featured = $db->loadResult();
				$row->isFeatured = $featured;
				$touchExpireTable = 0;
			}
		}
		else
		{
			if($isNew==1)
			{
				$row->isFeatured = 0;
				$touchExpireTable = 1;//setup expired for this property
			}
			else
			{
				$db->setQuery("Select isFeatured from #__osrs_properties where id = '$id'");
				$featured = $db->loadResult();
				$row->isFeatured = $featured;
				$touchExpireTable = 0;
			}
		}
		
		for($f = 0; $f < 10; $f++)
		{
			if($f == 0)
			{
				$fname = "";
			}
			else
			{
				$fname = $f;
			}
			$name = "pro_pdf_file".$fname;

			if(is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				if(!HelperOspropertyCommon::checkIsDocumentFileUploaded($name))
				{
					//do nothing
				}
				else
				{
					if($id > 0)
					{
						$db->setQuery("Select `".$name."` from #__osrs_properties where id = '$id'");
						$document_file = $db->loadResult();
					}
					if($document_file != "")
					{
						if(file_exists(JPATH_ROOT."/components/com_osproperty/document/".$document_file))
						{
							@unlink(JPATH_ROOT."/components/com_osproperty/document/".$document_file);
						}
						elseif(file_exists(JPATH_ROOT."/media/com_osproperty/document/".$document_file))
						{
							@unlink(JPATH_ROOT."/media/com_osproperty/document/".$document_file);
						}
					}
					$pro_pdf_file = time()."_".$_FILES[$name]['name'];
					move_uploaded_file($_FILES[$name]['tmp_name'],JPATH_ROOT."/media/com_osproperty/document/".$pro_pdf_file);
					$row->{$name} = $pro_pdf_file;
				}
			}
			$remove_pdf = $jinput->getInt('remove_pdf'.$fname,0);
			if($remove_pdf == 1)
			{
				if(file_exists(JPATH_ROOT."/components/com_osproperty/document/".$document_file))
				{
					@unlink(JPATH_ROOT."/components/com_osproperty/document/".$document_file);
				}
				elseif(file_exists(JPATH_ROOT."/media/com_osproperty/document/".$document_file))
				{
					@unlink(JPATH_ROOT."/media/com_osproperty/document/".$document_file);
				}
				$row->{$name} = "";
			}
		}

		//panorama
		$remove_panorama = $jinput->getInt('remove_panorama',0);
		//echo $remove_panorama;die();
		if($id > 0)
		{
			$db->setQuery("Select panorama from #__osrs_properties where id = '$id'");
			$panorama_file = $db->loadResult();
		}
		if(is_uploaded_file($_FILES['panorama']['tmp_name']))
		{
			if(is_uploaded_file($_FILES['panorama']['tmp_name']))
			{
				if($document_file != "")
				{ // remove old file
					@unlink(JPATH_ROOT."/images/osproperty/properties/panorama/".$document_file);
				}
				$panorama = time()."_".$_FILES['panorama']['name'];
				move_uploaded_file($_FILES['panorama']['tmp_name'],JPATH_ROOT."/images/osproperty/properties/panorama/".$panorama);
				$row->panorama = $panorama;
			}
		}
		elseif($remove_panorama == 1)
		{
			if($panorama_file != "")
			{ // remove old file
				@unlink(JPATH_ROOT."/images/osproperty/properties/panorama/".$document_file);
			}
			$panorama_file = "";
			$row->panorama = "";
		}
		elseif($id > 0)
		{
			$row->panorama = $panorama_file;
		}
		
		$pro_video				= $_POST['pro_video'];
		$row->pro_video			= $pro_video;
		$pro_small_desc			= $_POST['pro_small_desc'];
		$row->pro_small_desc	= $pro_small_desc;
		$pro_full_desc			= $_POST['pro_full_desc'];
		$row->pro_full_desc		= $pro_full_desc;
		$note					= $_POST['note'];
		$row->note				= $note;
		$metakey				= $_POST['metakey'];
		$row->metakey			= $metakey;
		$metadesc				= $_POST['metadesc'];
		$row->metadesc			= $metadesc;

		$agent_id				= 0;
		if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty'))
		{
			$agent_id = $jinput->getInt('agent_id',0);
		}
		if((HelperOspropertyCommon::isRegisteredAgent()) && ($agent_id == 0))
		{
			$agent_id = HelperOspropertyCommon::getAgentID();
            $row->company_id = 0;
			if($id == 0)
			{
				$row->posted_by = 0;
			}
            $posted_by = 0;
            $created_by = $agent_id;
		}
		else
		{
			$agent_id = $jinput->getInt('agent_id',0);
            $company_id = HelperOspropertyCommon::getCompanyId();
			if($id == 0){
				$row->company_id = $company_id;
				$row->posted_by = 1;
			}
            $posted_by = 1;
            $created_by = $company_id;
		}
		$row->agent_id = $agent_id;
		if($id == 0)
		{
			$row->created = date("Y-m-d",time());
			$row->created_by = $user->id;
			$row->hits =  0;
			$row->modified = $row->created;
		}
		else
		{
			$row->modified = date("Y-m-d",time());
			$row->modified_by = $user->id;
			$row->company_id  = $jinput->getInt('company_id',0);
		}
		//Auto approval or waiting for confirmation from admin
		if($isNew == 1)
		{
			if($configClass['general_approval']==1)
			{ //auto approval
				if($configClass['active_payment'] == 1)
				{
					$row->approved = 0;
				}
				else
				{
					$row->approved = 1;
				}
				$row->published = 1;
				$row->publish_up = date("Y-m-d");
			}
			else
			{
				$row->approved = 0;
				$row->published = 0;	
			}
		}
		//check to lat long
		$lat_add = OSPHelper::getStringRequest('lat_add','','post');
		$long_add = OSPHelper::getStringRequest('long_add','','post');
		if(($lat_add == "") || ($long_add == ""))
		{
			include_once(JPATH_SITE."/components/com_osproperty/helpers/googlemap.lib.php");
			$city = $jinput->getInt('city',0);
			if($city > 0)
			{
				$db->setQuery("Select city from #__osrs_cities where id = '$city'");
				$city_name = $db->loadResult();
			}
			$address_search = $jinput->getString('address','').", ".$city_name;
			$state = $jinput->getInt('state','');
			$db->setQuery("Select state_name from #__osrs_states where id = '$state'");
			$sname = $db->loadResult();
			$address_search .= ", ".$sname;
			$country = $jinput->getInt('country','');
			$db->setQuery("Select country_name from #__osrs_countries where id = '$country'");
			$cname = $db->loadResult();
			$address_search .= ", ".$cname;
			$return = HelperOspropertyGoogleMap::findAddress($option,'',$address_search,1);
			if($return[2] == "OK")
			{
				$lat_add = $return[0];
				$long_add = $return[1];
				$row->lat_add = $lat_add;
				$row->long_add = $long_add;
			}
		}
		else
		{
			$row->lat_add = $lat_add;
			$row->long_add = $long_add;
		}
		$row->built_on			= (int) $row->built_on;
		$row->remodeled_on		= (int) $row->remodeled_on;
		$row->floor_area_lower	= (float) $row->floor_area_lower;
		$row->floor_area_main_level = (float) $row->floor_area_main_level;
		$row->floor_area_upper  = (float) $row->floor_area_upper;
		$row->floor_area_total	= (float) $row->floor_area_total;
		$row->basement_size     = (float) $row->basement_size;
		$row->total_acres		= (float) $row->total_acres;
		$row->price				= (float) $row->price;
		$row->bath_room			= (float) $row->bath_room;
		$row->square_feet		= (float) $row->square_feet;
		$row->lot_size			= (float) $row->lot_size;
		$row->energy			= (float) $row->energy;
		$row->climate			= (float) $row->climate;
		$row->bed_room			= (int) $row->bed_room;
		$row->rooms				= (int) $row->rooms;
		$row->number_of_floors	= (int) $row->number_of_floors;
		$row->state				= (int) $row->state;
		$row->city				= (int) $row->city;
		$row->access			= (int) $row->access;
		$row->panorama			= (string) $row->panorama;
		$row->hits				= (int) $row->hits;
		for($i = 1; $i<10; $i++)
		{
			$row->{'pro_pdf_file'.$i} = (string)$row->{'pro_pdf_file'.$i};
		}
		//store into database
		if (!$row->store()) 
		{
			//JError::raiseError(500, $row->getError() );
			throw new Exception($row->getError(), 500);
		}
		//get id
		if($isNew == 1)
		{
			$id = $db->insertID();
            //auto ref?
            if($configClass['ref_field'] == 1)
			{
                $ref_prefix = $configClass['ref_prefix'];
                $db->setQuery("Update #__osrs_properties set ref = '".$ref_prefix.$id."' where id = '$id'");
                $db->execute();
            }
            //version 2.7.5
            //add ID of property into New Properties Table
            OSPHelper::addPropertyToQueue($id, 1);
		}
		else
		{
			OSPHelper::addPropertyToQueue($id , 0);
			if($row->ref == "")
			{
				if($configClass['ref_field'] == 1)
				{
					$ref_prefix = $configClass['ref_prefix'];
					$db->setQuery("Update #__osrs_properties set ref = '".$ref_prefix.$id."' where id = '$id'");
					$db->execute();
				}
			}
		}

		//create directories if they don't exists
		if(! Folder::exists(JPATH_ROOT."/images/osproperty/properties/".$id))
		{
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$id);
		}
		if(! Folder::exists(JPATH_ROOT."/images/osproperty/properties/".$id."/thumb"))
		{
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$id."/thumb");
		}
		if(! Folder::exists(JPATH_ROOT."/images/osproperty/properties/".$id."medium"))
		{
			Folder::create(JPATH_ROOT."/images/osproperty/properties/".$id."/medium");
		}
		
		//Update into Property table
		$categoryIds = $jinput->get('categoryIds',array(),'ARRAY');
		$categoryIds = ArrayHelper::toInteger($categoryIds);
		$db->setQuery("Delete from #__osrs_property_categories where pid = '$id'");
		$db->execute();
		if(count($categoryIds) > 0)
		{
			foreach ($categoryIds as $catid)
			{
				$db->setQuery("Insert into #__osrs_property_categories (id,pid,category_id) values (NULL,'$id','$catid')");
				$db->execute();
			}
		}
		
		//update other information
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable)
		{
			foreach ($languages as $language)
			{	
				$sef = $language->sef;
				
				$pro_name_language 			= OSPHelper::getStringRequest('pro_name_'.$sef,'');
				$pro_small_desc_language 	= $_POST['pro_small_desc_'.$sef];
				$pro_full_desc_language 	= $_POST['pro_full_desc_'.$sef];
				
				$metakey_language			= $_POST['metakey_'.$sef];
				$metadesc_language			= $_POST['metadesc_'.$sef];
				
				if($pro_name_language == "")
				{
					$pro_name_language = $row->pro_name;
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $id;
					$property->{'pro_name_'.$sef} = $pro_name_language;
					$property->store();
				}
				
				if($pro_small_desc_language == "")
				{
					$pro_small_desc_language = $row->pro_small_desc;
				}
				if($pro_small_desc_language != "")
				{
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $id;
					$property->access = $row->access;
					$property->category_id = $row->category_id;
					$property->{'pro_small_desc_'.$sef} = $pro_small_desc_language;
					$property->store();
				}
				
				if($pro_full_desc_language == "")
				{
					$pro_full_desc_language = $row->pro_full_desc;
				}
				if($pro_full_desc_language != ""){
					$property = Table::getInstance('Property','OspropertyTable');
					$property->id = $id;
					$property->access = $row->access;
					$property->category_id = $row->category_id;
					$property->{'pro_full_desc_'.$sef} = $pro_full_desc_language;
					$property->store();
				}
				
				if($metadesc_language == ""){
					$metadesc_language = $metadesc;
				}
				if($metadesc_language != ""){
					$db->setQuery("Update #__osrs_properties set `metadesc_".$sef."` = ".$db->quote($metadesc_language)." where  id = '$id'");
					$db->execute();
				}
				
				if($metakey_language == ""){
					$metakey_language = $metakey;
				}
				if($metakey_language != ""){
					$db->setQuery("Update #__osrs_properties set `metakey_".$sef."` = ".$db->quote($metakey_language)." where  id = '$id'");
					$db->execute();
				}
				
				$pro_alias = OSPHelper::getStringRequest('pro_alias_'.$sef);
				$pro_alias = OSPHelper::generateAliasMultipleLanguages('property',$id,$pro_alias,$sef);
				$db->setQuery("Update #__osrs_properties set pro_alias_".$sef." = '$pro_alias' where id = '$id'");
				$db->execute();
			}
		}

		//alias
		$pro_alias = OSPHelper::getStringRequest('pro_alias','','');
		if($pro_alias == ""){
			$pro_alias = OSPHelper::generateAlias('property',$id,'');
		}else{
			$pro_alias = OSPHelper::generateAlias('property',$id,$pro_alias);
		}
		$db->setQuery("Update #__osrs_properties set pro_alias = '$pro_alias' where id = '$id'");
		$db->execute();

		if($configClass['more_bath_infor']== 1 && $configClass['use_bathrooms']== 1)
		{
			$db->setQuery("Delete from #__osrs_property_bath_values where pid = '$id'");
			$db->execute();
			
			$bathLabelArray = array('OS_FULL','OS_THREE_QUARTER','OS_HALF','OS_QUARTER','OS_ENSUITE');
			foreach($bathLabelArray as $label)
			{
				$bathvalue = $jinput->getFloat(strtolower($label),0);
				if($bathvalue > 0)
				{
					$db->setQuery("Insert into #__osrs_property_bath_values (id,pid,`bath_label`,`bath_value`) values (NULL, $id, '$label','$bathvalue')");
					$db->execute();
				}
			}
		}
		
		//collect the id of the photos
		$photoIds = [];

		
		if($configClass['frontend_upload_type'] == 0){
			
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$id'")	;
			$item_images = $db->loadObjectList();

			//save picture ajax upload
			$img_ids = $jinput->get('img_id',array(),'array');
			$img_captions = $jinput->get('img_caption',array(),'array');
			$img_images = $jinput->get('img_image',array(),'array');

			$img_id_to_del='';
			foreach($item_images as $item_img){
				$img_to_del = 1;
				foreach($img_ids as $img_id){
					if($item_img->id==$img_id){
						$img_to_del = 0;
						break;
					}
				}
				if($img_to_del){
					$path_to_delete = JPATH_ROOT.'/images/osproperty/properties/'.$id.'/'.$item_img->name;
					if (File::exists($path_to_delete)){
						File::delete($path_to_delete);
					}
					$path_to_delete = JPATH_ROOT.'/images/osproperty/properties/'.$id.'/medium/'.$item_img->name;
					if (File::exists($path_to_delete)){
						File::delete($path_to_delete);
					}
					$path_to_delete = JPATH_ROOT.'/images/osproperty/properties/'.$id.'/thumb/'.$item_img->name;
					if (File::exists($path_to_delete)){
						File::delete($path_to_delete);
					}
					$img_id_to_del .= $item_img->id.',';
				}
			}
			if($img_id_to_del)
			{
				$query = "DELETE FROM #__osrs_photos WHERE pro_id=".$row->id." AND ID IN (".substr($img_id_to_del, 0, -1).") ";
				$db->setQuery($query);
				$db->execute();
			}

			$img_ord = 1;
			$db->setQuery("Select ordering from #__osrs_photos where pro_id = '$row->id' order by ordering desc limit 1");
			$img_ord = $db->loadResult();
			$img_ord = (int)$img_ord + 1;
			$img_to_insert = 0;
			$query_img = "INSERT INTO #__osrs_photos(`id`,`pro_id`,`image`,`image_desc`,`ordering`) VALUES ";
			$new_img_path = JPATH_SITE."/images/osproperty/properties/".$id."/";
			for($im = 0;$im<count($img_ids);$im++)
			{    		    		
				if($img_ids[$im])
				{ 	
					if($item_images[$img_ids[$im]]->ordering!=$img_ord || $item_images[$img_ids[$im]]->caption!=$img_captions[$im])
					{
						$query = "UPDATE #__osrs_photos SET ordering='".$img_ord."', image_desc='".$db->escape($img_captions[$im])."' WHERE pro_id=".$id." AND id=".$img_ids[$im]." ";
						$db->setQuery($query);
						$db->execute();
					}
				}
				else
				{    			
					$new_img_name = explode(';',$img_images[$im]);    			
					if(is_array($new_img_name))
					{
						$new_img_name_u =JPATH_ROOT.'/tmp/osupload/'.$new_img_name[0];
						if (File::exists($new_img_name_u))
						{
							if(getimagesize($new_img_name_u))
							{
								$new_img_n = $id.'_'.str_ireplace(' ', '_',$new_img_name[1]);
								$new_img_n = strtolower($new_img_n);
								$new_img_n = File::makeSafe($new_img_n);
									
								$new_path_check = $new_img_path.$new_img_n;
								$nimg= 0;
								while(File::exists($new_path_check))
								{
									$nimg++;
									$new_img_n = $id.'_'.$nimg.'_'.str_ireplace(' ', '_',$new_img_name[1]);
									$new_img_n = strtolower($new_img_n);
									$new_img_n = File::makeSafe($new_img_n);
									$new_path_check = $new_img_path.$new_img_n;
								} 
									
								rename($new_img_name_u, $new_img_path.$new_img_n);
								//echo $new_img_name_u;
								//echo "<BR />";
								//echo $new_img_path.$new_img_n;
								//die();
								$name_parts = pathinfo($new_img_n);
								$img_name = $name_parts['filename'];
								$img_ext = $name_parts['extension'];
								$query_img .= "(NULL,'".$id."','".$new_img_n."','".$db->escape($img_captions[$im])."','".$img_ord."'), ";

								$original_image_link = $new_img_path.$new_img_n;
								HelperOspropertyCommon::returnMaxsize($new_img_path.$new_img_n);
								//copy and resize
								//thumb
								$thumb_width = $configClass['images_thumbnail_width'];
								$thumb_height = $configClass['images_thumbnail_height'];
								$thumb_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$new_img_n;
								File::copy($original_image_link,$thumb_image_link);
								OSPHelper::resizePhoto($thumb_image_link,$thumb_width,$thumb_height);
								
								//medium
								$medium_width = $configClass['images_large_width'];
								$medium_height = $configClass['images_large_height'];
								$medium_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$new_img_n;
								File::copy($original_image_link,$medium_image_link);
								OSPHelper::resizePhoto($medium_image_link,$medium_width,$medium_height);

								$img_to_insert++;
							}
						}
					}
				}
				$img_ord++;
			}
			if($img_to_insert){
				$query_img = substr($query_img, 0, -2).';';
				$db->setQuery($query_img);
				$db->execute();
			}
		}else{
			//save photos and extra fields
			if($isNew == 0){
				$db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering");
				$photos = $db->loadObjectList();
				if(count($photos) > 0){
					for($i=0;$i<count($photos);$i++){
						$j = $i + 1;
						$photo = $photos[$i];
						$remove_name   = "remove_".$photo->id;
						$photo_name    = "photo_".$j;
						$desc_name     = "photodesc_".$j;
						$ordering_name = "ordering_".$j;
						$remove        = $jinput->getInt($remove_name,0);
						
						$photorecord   = Table::getInstance('Photo','OspropertyTable');
						$photorecord->id = $photo->id;
						$description   = $_POST[$desc_name];
						$photorecord->image_desc = $description;
						$photorecord->ordering   = $jinput->getInt($ordering_name,0);
						
						if(is_uploaded_file($_FILES[$photo_name]['tmp_name'])){
							if(!HelperOspropertyCommon::checkIsPhotoFileUploaded($photo_name)){
								//return to previous page
								//do nothing
							}else{
								$image_name = $_FILES[$photo_name]['name'];
								$image_name = OSPHelper::processImageName($id.time().$image_name);
								$original_image_link = JPATH_ROOT."/images/osproperty/properties/".$id.DS.$image_name;
								move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
								HelperOspropertyCommon::returnMaxsize($original_image_link);
								//copy and resize
								//thumb
								$thumb_width = $configClass['images_thumbnail_width'];
								$thumb_height = $configClass['images_thumbnail_height'];
								$thumb_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$image_name;
								File::copy($original_image_link,$thumb_image_link);
								OSPHelper::resizePhoto($thumb_image_link,$thumb_width,$thumb_height);
								
								//medium
								$medium_width = $configClass['images_large_width'];
								$medium_height = $configClass['images_large_height'];
								$medium_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$image_name;
								File::copy($original_image_link,$medium_image_link);
								OSPHelper::resizePhoto($medium_image_link,$medium_width,$medium_height);
								
								$photorecord->image = $image_name;
								
								//add into the array
								$photoIds[] = $photo->id;
							}
						}
						if($remove == 1){
							HelperOspropertyCommon::removePhoto($photo->id,1);
							$db->setQuery("Select image from #__osrs_photos where id = '$photo->id'");
							$image = $db->loadResult();
							$db->setQuery("Delete from #__osrs_watermark where pid = '$id' and image like '$image'");
							$db->execute();
						}else{
							//save the image
							$photorecord->store();
						}
					}
				}
			}//end edit photos of property
			
			//with new photos of the property
			$newphoto = $jinput->getInt('newphoto',0);
			for($i=$newphoto;$i<=$limit_photo;$i++){
				$j = $i + 1;
				$photo_name    = "photo_".$j;
				$desc_name     = "photodesc_".$j;
				$ordering_name = "ordering_".$j;
				$photorecord   = Table::getInstance('Photo','OspropertyTable');
				$photorecord->id = 0;
				$description   = $_POST[$desc_name];
				$photorecord->image_desc = $description;
				$photorecord->pro_id     = $id;
				$db->setQuery("Select ordering from #__osrs_photos where pro_id = '$id' order by ordering desc limit 1");
				$ordering = $db->loadResult();
				$photorecord->ordering   = $ordering +1;
				
				if(is_uploaded_file($_FILES[$photo_name]['tmp_name'])){
					if(!HelperOspropertyCommon::checkIsPhotoFileUploaded($photo_name)){
						//return to previous page
						//do nothing
					}else{	
						$image_name = $_FILES[$photo_name]['name'];
						$image_name = OSPHelper::processImageName($id.time().$image_name);
						$original_image_link = JPATH_ROOT."/images/osproperty/properties/".$id.DS.$image_name;
						move_uploaded_file($_FILES[$photo_name]['tmp_name'],$original_image_link);
						HelperOspropertyCommon::returnMaxsize($original_image_link);
						//copy and resize
						//thumb
						$thumb_width = $configClass['images_thumbnail_width'];
						$thumb_height = $configClass['images_thumbnail_height'];
						$thumb_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/thumb/".$image_name;
						File::copy($original_image_link,$thumb_image_link);
						OSPHelper::resizePhoto($thumb_image_link,$thumb_width,$thumb_height);
						
						//medium
						$medium_width = $configClass['images_large_width'];
						$medium_height = $configClass['images_large_height'];
						$medium_image_link = JPATH_ROOT."/images/osproperty/properties/".$id."/medium/".$image_name;
						File::copy($original_image_link,$medium_image_link);
						OSPHelper::resizePhoto($medium_image_link,$medium_width,$medium_height);
								
						$photorecord->image = $image_name;
						//save the image
						$photorecord->store();
						
						$new_photo_id = $db->insertID();
						$photoIds[] = $new_photo_id;
					}
				}
			}
			//end new photos of the property
		}

		$grabImages = $jinput->get('grabImages',array(),'ARRAY');
		self::saveGrabmages($id,$grabImages);
		//let save extra fields
		$db->setQuery("Select * from #__osrs_extra_fields where published = '1'");
		$fields = $db->loadObjectList();
		if(count($fields) > 0){
			$db->setQuery("Delete from #__osrs_property_field_value where pro_id = '$id'");
			$db->execute();
			for($i=0;$i<count($fields);$i++){
				$field = $fields[$i];
				HelperOspropertyFields::saveField($field,$id);
			}
		}
		//end save extra field
		
		//save convenience
		$amenities = $jinput->get('amenities',array(),'ARRAY');
		$db->setQuery("Delete from #__osrs_property_amenities where pro_id = '$id'");
		$db->execute();
		if(count($amenities) > 0){
			for($i=0;$i<count($amenities);$i++){
				$amen_id = $amenities[$i];
				$db->setQuery("Insert into #__osrs_property_amenities (id,pro_id,amen_id) values (NULL,'$id','$amen_id')");
				$db->execute();
			}
		}
		
		//update tags
		$query = $db->getQuery(true);
		$query->delete("#__osrs_tag_xref")->where("pid = '$id'");
		$db->setQuery($query);
		$db->execute();
		
		if($translatable){
			$keyword = $jinput->get('keyword',array(),'ARRAY');
			if(count($keyword) > 0){
				for($i=0;$i<count($keyword);$i++){
					$tag = htmlspecialchars($keyword[$i]);
					$tag = $db->escape($tag);
					if($tag != ""){
						$sql = "Select count(id) from #__osrs_tags where keyword like '$tag'";
						foreach ($languages as $language){	
							$sef = $language->sef;
							$temp_keyword = $jinput->get('keyword_'.$sef,array(),'ARRAY');
							$temp_keyword = htmlspecialchars($temp_keyword[$i]);
							$sql .= " and keyword_".$sef." like '$temp_keyword'";
						}
						$db->setQuery($sql);
						$count = $db->loadResult();
						if($count == 0){
							$tagobj = Table::getInstance('Tag','OspropertyTable');
							$tagobj->id = 0;
							$tagobj->keyword = $tag;
							$tagobj->published = 1;
							foreach ($languages as $language){	
								$sef = $language->sef;
								$temp_keyword = $jinput->get('keyword_'.$sef,array(),'ARRAY');
								$temp_keyword = htmlspecialchars($temp_keyword[$i]);
								$tagobj->{'keyword_'.$sef} = $temp_keyword;
							}
							$tagobj->store();
							$tagid = $db->insertID();
						}else{ 
							$sql = "Select id from #__osrs_tags where keyword like '$tag'";
							foreach ($languages as $language){	
								$sef = $language->sef;
								$temp_keyword = $jinput->get('keyword_'.$sef,array(),'ARRAY');
								$temp_keyword = htmlspecialchars($temp_keyword[$i]);
								$sql .= " and keyword_".$sef." like '$temp_keyword'";
							}
							$db->setQuery($sql);
							$tagid = $db->loadResult();
						}
						$db->setQuery("Insert into #__osrs_tag_xref (id,pid,tag_id) values (NULL,'$id','$tagid')");
						$db->execute();
					}
				}
			}
		}
		else
		{
			$keyword = $jinput->get('keyword',array(),'ARRAY');
			if(count($keyword) > 0)
			{
				foreach ($keyword as $tag)
				{
					$tag = htmlspecialchars($tag);
					if($tag != "")
					{
						$db->setQuery("Select count(id) from #__osrs_tags where keyword like '$tag'");
						$count = $db->loadResult();
						if($count == 0)
						{
							$tagobj = Table::getInstance('Tag','OspropertyTable');
							$tagobj->id = 0;
							$tagobj->keyword = $tag;
							$tagobj->published = 1;
							$tagobj->store();
							$tagid = $db->insertID();
						}
						else
						{ 
							$db->setQuery("Select id from #__osrs_tags where keyword like '$tag'");
							$tagid = $db->loadResult();
						}
						$db->setQuery("Insert into #__osrs_tag_xref (id,pid,tag_id) values (NULL,'$id','$tagid')");
						$db->execute();
					}
				}
			}
		}
		
		//save neighborhood
		$db->setQuery("Delete from #__osrs_neighborhood where pid = '$id'");
		$db->execute();
		$db->setQuery("Select id from #__osrs_neighborhoodname");
		$neighborhoods = $db->loadObjectList();
		if(count($neighborhoods) > 0)
		{
			for($i=0;$i<count($neighborhoods);$i++)
			{
				$neighborhood = $neighborhoods[$i];
				$neighborhood_checkbox_name = "nei_".$neighborhood->id;
				$neighborhood_checkbox_value = $jinput->getInt($neighborhood_checkbox_name,0);
				if($neighborhood_checkbox_value == 1)
				{
					$mins_name		= "mins_nei_".$neighborhood->id;
					$mins			= $jinput->getInt($mins_name,0);
					$distance_name	= "distance_nei_".$neighborhood->id;
					$distance		= $jinput->getFloat($distance_name, 0);
					$traffic_name	= "traffic_type_".$neighborhood->id;
					$traffic_type	= $jinput->getInt($traffic_name,'0');
					$db->setQuery("Insert into #__osrs_neighborhood (id,pid,neighbor_id,mins,traffic_type,distance) values (NULL,'$id','$neighborhood->id','$mins','$traffic_type','$distance')");
					$db->execute();
				}
			}
		}
		//update property history
		if($configClass['use_property_history']== 1){
			$query = $db->getQuery(true);
			$query->delete("#__osrs_property_price_history")->where("pid = '$id'");
			$db->setQuery($query);
			$db->execute();

            $history_date   = $jinput->get('history_date',array(),'ARRAY');
            $history_event  = $jinput->get('history_event',array(),'ARRAY');
            $history_price  = $jinput->get('history_price',array(),'ARRAY');
            $history_source = $jinput->get('history_source',array(),'ARRAY');
			if(count($history_date) > 0){
				for($i=0;$i<count($history_date);$i++){
					if(($history_date[$i] != "") and ($history_event[$i] != "") and ($history_price [$i] != "") and ($history_source[$i] != "")){
						$query = $db->getQuery(true);
						$columns = array('id','pid','date','event','price','source');
						$query->insert("#__osrs_property_price_history")->columns($columns)->values('NULL,'.$id.','.$db->quote($history_date[$i]).','.$db->quote($history_event[$i]).','.$db->quote($history_price [$i]).','.$db->quote($history_source[$i]).'');
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
			
			$query = $db->getQuery(true);
			$query->delete("#__osrs_property_history_tax")->where("pid = '$id'");
			$db->setQuery($query);
			$db->execute();

            $tax_year   			= $jinput->get('tax_year',array(),'ARRAY');
            $tax_value  			= $jinput->get('tax_value',array(),'ARRAY');
            $tax_change  			= $jinput->get('tax_change',array(),'ARRAY');
            $tax_assessment 		= $jinput->get('tax_assessment',array(),'ARRAY');
            $tax_assessment_change 	= $jinput->get('tax_assessment_change',array(),'ARRAY');
			if(count($tax_year) > 0){
				for($i=0;$i<count($tax_year);$i++){
					if(($tax_year[$i] != "") and ($tax_value[$i] != "")  ){
						if($tax_assessment [$i] == ""){
							$tax_assessment [$i] = 0;
						}
						if($tax_assessment_change [$i] == ""){
							$tax_assessment_change [$i] = 0;
						}
						$query = $db->getQuery(true);
						$columns = array('id','pid','tax_year','property_tax','tax_change','tax_assessment','tax_assessment_change');
						$query->insert("#__osrs_property_history_tax")->columns($columns)->values('NULL,'.$id.','.$db->quote($tax_year[$i]).','.$db->quote($tax_value[$i]).','.$db->quote($tax_change[$i]).','.$db->quote($tax_assessment[$i]).','.$tax_assessment_change[$i].'');
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}
		
		if($configClass['use_open_house'] == 1){
			$query = $db->getQuery(true);
			$query->delete("#__osrs_property_open")->where("pid = '$id'");
			$db->setQuery($query);
			$db->execute();

            $start_from = $jinput->get('start_from',array(),'ARRAY');
            $end_to     = $jinput->get('end_to',array(),'ARRAY');
			
			if(count($start_from) > 0){
				for($i=0;$i<count($start_from);$i++){
					if(($start_from[$i] != "") and ($end_to[$i]!= "")){
						$query = $db->getQuery(true);
						$columns = array('id','pid','start_from','end_to');
						$query->insert('#__osrs_property_open')->columns($columns)->values('NULL,'.$id.','.$db->quote($start_from[$i]).','.$db->quote($end_to[$i]).'');
						$db->setQuery($query);
						$db->execute();
					}
				}
			}
		}

		if($row->published == 1 && $row->approved == 1) 
		{
			OSPHelper::updateFacebook($row, $isNew);
			OSPHelper::updateTweet($row, $isNew);
		}
		if($configClass['images_use_image_watermarks'] == 1)
		{
			OSPHelper::generateWaterMark($id);
		}

		//trigger event when saving property
		PluginHelper::importPlugin('osproperty');
		//$dispatcher = JEventDispatcher::getInstance();
		if ($isNew == 1)
		{
			$mainframe->triggerEvent('onAfterStoreProperty', array($row));
		}

		//check to see if the system use expired mode
		//in that case, check if the property has been published automatically
		//we should insert the expired date into the database
		//in case user is supervisor, he will be redirected to the Manage All Properties
        if($isNew == 1) { //publish new property if payment disabled and auto approval is enabled
            OSPHelper::approvalPropertyNonPayment($id);
        }
        if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) 
		{
            if ($save == 1) 
			{
                $needs = [];
                $needs[] = "lmanageproperties";
                $needs[] = "property_manageallproperties";
                $itemid = OSPRoute::getItemid($needs);
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&view=lmanageproperties&Itemid=" . $itemid), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            } elseif($save == 0) {
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit&id=" . $id), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }
        }
		elseif(HelperOspropertyCommon::isAgent())
		{
            if ($save == 1) 
			{
                $needs = [];
                $needs[] = "aeditdetails";
                $needs[] = "agent_editprofile";
                $needs[] = "agent_default";
                $itemid = OSPRoute::getItemid($needs);
				if($itemid == $jinput->getInt('Itemid',0))
				{
					$itemid = 9999;
				}
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&view=aeditdetails&Itemid=" . $itemid), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            } 
			elseif($save == 0) 
			{
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit&id=" . $id ."&Itemid=" . $mainframe->input->getInt('Itemid')), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            } 
			elseif($save == 2)
			{
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit_activelisting&new=1&id=" . $id ."&Itemid=" . $mainframe->input->getInt('Itemid')), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }
        }
		elseif(HelperOspropertyCommon::isCompanyAdmin())
		{
            if ($save == 1) {
                $needs = [];
                $needs[] = "ccompanydetails";
                $needs[] = "company_edit";
                $itemid = OSPRoute::getItemid($needs);
				if($itemid == $jinput->getInt('Itemid',0))
				{
					$itemid = 9999;
				}
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&view=ccompanydetails&Itemid=" . $itemid), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            } elseif($save == 0) {
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit&id=" . $id ."&Itemid=" . $mainframe->input->getInt('Itemid')), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            } elseif($save == 2){
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit_activelisting&new=1&id=" . $id ."&Itemid=" . $mainframe->input->getInt('Itemid')), Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }
        }
	}

    /**
     * Active listing
     * @param $option
     * @param $id
     */
	static function activelisting($option,$id)
	{
	    global $mainframe,$configClass,$jinput, $bootstrapHelper;
        OSPHelper::generateHeading(1,Text::_('OS_ACTIVATE_LISTING'));
        $new                = $jinput->getInt('new',0);
        $type               = $jinput->getInt('type',0);
        if($type == 0) 
		{
            if ($new == 1) 
			{
                $type = 1;
            } 
			elseif ($new == 0) 
			{
                $type = 3;
            }
        }
        if($configClass['integrate_membership'] == 1)
		{
            $mainframe->redirect(Route::_('index.php?option=com_osproperty&task=membership_activelisting&type='.$type.'&new='.$new.'&id='.$id));
        }
	    if(OSPHelper::activePayment() && !OSPHelper::isApprovedProperty($id))
		{
	        $db                 = Factory::getDbo();
	        $db->setQuery("Select * from #__osrs_properties where id = '$id'");
	        $property           = $db->loadObject();

            //expiration checking
            if($configClass['general_use_expiration_management'] == 1)
			{
                $db->setQuery("Select * from #__osrs_expired where pid = '$property->id'");
                $property->expiration = $db->loadObject();
            }

            $paymentMethod = $jinput->getString('payment_method', os_payments::getDefautPaymentMethod(), 'post');
            if (!$paymentMethod) $paymentMethod = os_payments::getDefautPaymentMethod();
            ###############Payment Methods parameters###############################

            //Creditcard payment parameters
            $x_card_num         = $jinput->getString('x_card_num', '');
            $expMonth           = $jinput->getString('exp_month', date('m')) ;
            $expYear            = $jinput->getString('exp_year', date('Y')) ;
            $x_card_code        = $jinput->getString('x_card_code', '');
            $cardHolderName     = $jinput->getString('card_holder_name', '') ;
            $lists['exp_month'] = HTMLHelper::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" class="input-mini"  ', $expMonth, '%02d') ;
            $currentYear = date('Y') ;
            $lists['exp_year'] = HTMLHelper::_('select.integerlist', $currentYear, $currentYear + 10 , 1, 'exp_year', ' id="exp_year" class="input-small" ', $expYear) ;

            $methods = os_payments::getPaymentMethods(true, false) ;

            $lists['x_card_num']        = $x_card_num;
            $lists['x_card_code']       = $x_card_code;
            $lists['cardHolderName']    = $cardHolderName;
            $lists['methods']           = $methods;
            $lists['idealEnabled']      = false;
            $lists['paymentMethod']        = $paymentMethod;
            jimport('joomla.filesystem.file');
            if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/activelistingform.php'))
			{
                $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
            }
			else
			{
                $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
            }
            $itemid = $jinput->getInt('Itemid',0);
            $tpl->set('itemid',$itemid);
            $tpl->set('mainframe',$mainframe);
            $tpl->set('lists',$lists);
            $tpl->set('type',$type);
            $tpl->set('configClass',$configClass);
            $tpl->set('bootstrapHelper',$bootstrapHelper);
            $tpl->set('isNew',$new);
            $tpl->set('row',$property);
            $body = $tpl->fetch("activelistingform.php");
            echo $body;
        }
    }

	static function saveGrabmages($id,$grabImages){
		global $mainframe, $configClass;
		$db = Factory::getDbo();
		if(count($grabImages) > 0){
			$real_path_picture = JPATH_ROOT . DS . "images" . DS . "osproperty" . DS . "properties" . DS . $id . DS;
			foreach($grabImages as $gbimage){
				$db->setQuery("Select ordering from #__osrs_photos where pro_id = '$id' order by ordering desc");
				$ordering = $db->loadResult();
				$ordering++;
				//get file
				$photo_name = trim(pathinfo($gbimage, PATHINFO_BASENAME));
				$lfile = fopen($real_path_picture . $photo_name, "x");
				if (is_callable('curl_init')) {
					$picObj = OSPHelper::getImageFromUrl($gbimage);
					fwrite($lfile, $picObj);
					fclose($lfile);
				} else {
					$content = file_get_contents($gbimage);
					$fp = fopen($lfile, "w");
					fwrite($fp, $content);
					fclose($fp);
				}
				File::copy($real_path_picture . $photo_name, $real_path_picture . '/medium/' . $photo_name);
				File::copy($real_path_picture . $photo_name, $real_path_picture . '/thumb/' . $photo_name);
				//resize pictures
				$medium_width = $configClass['images_large_width'];
				$medium_height = $configClass['images_large_height'];
				// copy($original_image_link.DS.$photo['image'],$medium_image_link.DS.$photo['image']);
				OSPHelper::resizePhoto($real_path_picture . '/medium/' . $photo_name, $medium_width, $medium_height);
				$thumb_width = $configClass['images_thumbnail_width'];
				$thumb_height = $configClass['images_thumbnail_height'];
				//copy($original_image_link.DS.$photo['image'],$thumb_image_link.DS.$photo['image']);
				OSPHelper::resizePhoto($real_path_picture . '/thumb/' . $photo_name, $thumb_width, $thumb_height);

				$photorecord = Table::getInstance('Photo', 'OspropertyTable');
				$photorecord->id = 0;
				$photorecord->pro_id = $id;
				$photorecord->image = $photo_name;
				$photorecord->ordering = $ordering;
				$photorecord->store();
			}
		}
	}
	
	/**
	 * generator photo
	 *
	 */
	static function generatePhoto(){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/yuiloader-min.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/yahoo-dom-event.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/element-min.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/dragdrop-min.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/imagecropper-min.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/resize-min.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/event-min.js","text/javascript",true);
		$document->addScript(Uri::root()."media/com_osproperty/assets/js/yui/script/dom-min.js","text/javascript",true);
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/fonts-min.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/imagecropper.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/resize.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/yui.css");
		$document->addStyleSheet(Uri::root()."media/com_osproperty/assets/js/yui/style/dpSyntaxHighlighter.css");
		
		$id = $jinput->getInt('pid',0);
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$pro_name = OSPHelper::getLanguageFieldValue($property,'pro_name');
		$photoIds = OSPHelper::getStringRequest('photoIds','','');
		$save = $jinput->getInt('save',0);
		HTML_OspropertyListing::generatePhotoCrop("com_osproperty",$id,$photoIds,$save,$pro_name);
	}
	
	/**
	 * Save croping photos
	 *
	 */
	static function savingPhoto(){
        global $bootstrapHelper, $mainframe,$jinput;
        $db = Factory::getDbo();
        $pid = $jinput->getInt('pid',0);
        $isNew = $jinput->getInt('isNew',0);
        $photoIds = OSPHelper::getStringRequest('photoIds','','');
        $save = $jinput->getInt('save',0);
        if($photoIds != ""){
            $photoArr = explode(",",$photoIds);
            if(count($photoArr) > 0){
                for($i=0;$i<count($photoArr);$i++){
                    $photo_id = $photoArr[$i];
                    $db->setQuery("Select image from #__osrs_photos where id = '$photo_id'");
                    $photo_name = $db->loadResult();
                    //thumbnail first
                    $tb = $jinput->getInt('tb_'.$photo_id,0);
                    if($tb == 1){
                        $top    = $jinput->getInt('tb_t_'.$photo_id,0);
                        $left   = $jinput->getInt('tb_l_'.$photo_id,0);
                        $height = $jinput->getInt('tb_h_'.$photo_id,0);
                        $width  = $jinput->getInt('tb_w_'.$photo_id,0);
                        HelperOspropertyCommon::create_photo($top,$left,$height,$width,$photo_name,0,$pid);
                    }

                    $me = $jinput->getInt('me_'.$photo_id,0);
                    if($me == 1){
                        $top    = $jinput->getInt('me_t_'.$photo_id,0);
                        $left   = $jinput->getInt('me_l_'.$photo_id,0);
                        $height = $jinput->getInt('me_h_'.$photo_id,0);
                        $width  = $jinput->getInt('me_w_'.$photo_id,0);
                        HelperOspropertyCommon::create_photo($top,$left,$height,$width,$photo_name,1,$pid);
                    }
                }
            }
        }

        //generate water maker image
        OSPHelper::generateWaterMark($pid);

        $msg = Text::_('OS_ITEM_SAVED');
        if($isNew == 0){
            if($save == 1){
                $needs = [];
                $needs[] = "agent_editprofile";
                $needs[] = "agent_default";
                $needs[] = "lagents";
                $itemid = OSPRoute::getItemid($needs);
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&view=aeditdetails&Itemid=".$itemid),Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }else{
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit&id=".$pid),Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }
        }else{
            if($save == 1){
                OSPHelper::redirect(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&id=$pid&Itemid=".$jinput->getInt('Itemid',0));
            }else{
                OSPHelper::redirect(Uri::root()."index.php?option=com_osproperty&task=property_edit&id=".$pid,Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }
        }
    }
	
	/**
	 * Skip croping photos
	 *
	 */
    static function skip(){
        global $bootstrapHelper, $mainframe,$jinput;
        //generate water maker image
        $pid        = $jinput->getInt('pid',0);
        OSPHelper::generateWaterMark($pid);
        $pid        = $jinput->getInt('pid',0);
        $isNew      = $jinput->getInt('isNew',0);
        $save       = $jinput->getInt('save',0);
        if($isNew == 0){
            if($save == 1){
                $needs = [];
                $needs[] = "agent_editprofile";
                $needs[] = "agent_default";
                $needs[] = "lagents";
                $itemid = OSPRoute::getItemid($needs);

                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&view=aeditdetails&Itemid=".$itemid),Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }else{
                OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=property_edit&id=".$pid),Text::_('OS_ITEM_HAS_BEEN_SAVE'));
            }
        }else{
            OSPHelper::redirect(Uri::root()."index.php?option=com_osproperty&task=property_thankyou&id=$pid&Itemid=".$jinput->getInt('Itemid',0));
        }
    }
	
	/**
	 * Thank you function 
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
    public static function thankyouPage($option,$id, $msg = array()){
        global $bootstrapHelper, $mainframe,$configClass,$lang_suffix,$jinput;
        $configClass = OSPHelper::loadConfig();
        $document = Factory::getDocument();
        $new = $jinput->getInt('new',1);
        if($new == 1){
            $document->setTitle(Text::_('OS_PROPERTY_SAVED'));
        }else{
            $document->setTitle(Text::_('OS_PROPERTY_SAVED'));
        }
        $db = Factory::getDbo();

        $db->setQuery("Select a.*,c.country_name,d.type_name,e.state_name$lang_suffix as state_name from #__osrs_properties as a inner join #__osrs_countries as c on c.id = a.country inner join #__osrs_types as d on d.id = a.pro_type inner join #__osrs_states as e on e.id = a.state where a.id = '$id'");
        $property = $db->loadObject();

        $db->setQuery("Select * from #__osrs_photos where pro_id = '$id'");
        $photos = $db->loadObjectList();

        $db->setQuery("select b.* from #__osrs_property_amenities as a inner join #__osrs_amenities as b on b.id = a.amen_id where a.pro_id = '$id'");
        $amenities = $db->loadObjectList();

        $db->setQuery("Select * from #__osrs_fieldgroups where published = '1' order by ordering");
        $groups = $db->loadObjectList();
        if(count($groups) > 0){
            for($i=0;$i<count($groups);$i++){
                $group = $groups[$i];
                $db->setQuery("Select * from #__osrs_extra_fields where published = '1' and group_id = '$group->id' order by ordering");

                $fields = $db->loadObjectList();
                $groups[$i]->fields = $fields;
            }
        }

        $db->setQuery("Select * from #__osrs_expired where pid = '$id'");
        $expired = $db->loadObjectList();

        $db->setQuery("Select name from #__osrs_agents where id = '$property->agent_id'");
        $property->agentname = $db->loadResult();

        $order_id = OSPHelper::getStringRequest('order_id','');
        if($order_id != ""){
            $order_id = base64_decode($order_id);
            if(intval($order_id) > 0){
                $db->setQuery("Select * from #__osrs_orders where id = '$order_id'");
                $order = $db->loadObject();
                $msg = [];
                if($order->order_status == "P"){
                    $msg[] = Text::_('OS_ORDER_HAS_BEEN_STORED');
                }
                if($order->payment_made == 0){
                    $msg[] = Text::_('OS_PROPERTY_WILL_BE_APPROVED_AFTER_YOU_MAKE_PAYMENT');
                }else{
                    $msg[] = Text::_('OS_PAYMENT_MADE');
                }
                if(count($msg) > 0){
                    for($i=0;$i<count($msg);$i++){
                        $msg[$i] = "<i class='osicon-ok'></i>&nbsp;".$msg[$i];
                    }
                    $msg = implode("<Br />",$msg);
                    Factory::getApplication()->enqueueMessage($msg, 'message');
                }
            }
        }
        HTML_OspropertyListing::thankyouPage($option,$property,$expired,$photos,$amenities,$groups, $msg);
    }
	
	/**
	 * Upgrade Property step 1
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function upgrade_step1($option,$cid){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDBO();

        if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
            //done and redirect
            for ($i = 0; $i < count($cid); $i++) {
                $pid = $cid[$i];
                HelperOspropertyCommon::setApproval("f", $pid);
                HelperOspropertyCommon::setExpiredTime($pid, "f", 1);
            }
            $needs[]    = "lmanageproperties";
            $itemid     = OSPRoute::getItemid($needs);
            $msg        = [];
            $msg[]      = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
            if (count($msg) > 0) {
                for ($i = 0; $i < count($msg); $i++) {
                    $msg[$i] = "<i class='osicon-ok'></i>&nbsp;" . $msg[$i];
                }
                $msg    = implode("<Br />", $msg);
            }
            $url        = Route::_("index.php?option=com_osproperty&view=lmanageproperties&Itemid=" . $itemid);
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect($url);
        }else {

            OSPHelper::generateHeading(1, Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE'));
            if( !HelperOspropertyCommon::isAgent() && !HelperOspropertyCommon::isCompanyAdmin()){
                throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
            }
            if (count($cid) > 0) {
                for ($i = 0; $i < count($cid); $i++) {
                    $id = $cid[$i];
                    if (!OSPHelper::checkPermissionWithSpecificProperty($id)) {
                        throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
                    }
                }
            }

            if (count($cid) > 0) {
                $cids = implode(",", $cid);
                $db->setQuery("Select a.*,b.image from #__osrs_properties as a left join #__osrs_photos as b on b.pro_id = a.id where a.id in ($cids) group by a.id order by b.ordering");
                $rows = $db->loadObjectList();
            }
            //payment for checking
            if ($configClass['active_payment'] == 1) {
                $paymentMethod = OSPHelper::getStringRequest('payment_method', os_payments::getDefautPaymentMethod());
                if (!$paymentMethod) $paymentMethod = os_payments::getDefautPaymentMethod();
            }

            ###############Payment Methods parameters###############################
            //Creditcard payment parameters
            $x_card_num = $jinput->getString('x_card_num', '');
            $expMonth = $jinput->getString('exp_month', date('m'));
            $expYear = $jinput->getString('exp_year', date('Y'));
            $x_card_code = $jinput->getString('x_card_code', '');
            $cardHolderName = $jinput->getString('card_holder_name', '');
            $lists['exp_month'] = HTMLHelper::_('select.integerlist', 1, 12, 1, 'exp_month', ' id="exp_month" class="input-mini"  ', $expMonth, '%02d');
            $currentYear = date('Y');
            $lists['exp_year'] = HTMLHelper::_('select.integerlist', $currentYear, $currentYear + 10, 1, 'exp_year', ' id="exp_year" class="input-small" ', $expYear);


            $methods = os_payments::getPaymentMethods(true, false);

            $lists['x_card_num'] = $x_card_num;
            $lists['x_card_code'] = $x_card_code;
            $lists['cardHolderName'] = $cardHolderName;
            $lists['methods'] = $methods;
            $lists['idealEnabled'] = false;
            $lists['paymentMethod'] = $paymentMethod;
            //check to see if user has already get the coupon before

            if ($configClass['integrate_membership'] == 1) {
                if(OspropertyMembership::getUserFeaturedCredit() == 0){
                    if(HelperOspropertyCommon::isAgent()){
                        $usertype = 0;
                    }elseif(HelperOspropertyCommon::isCompanyAdmin()){
                        $usertype = 2;
                    }
                    $session = Factory::getSession();
                    $cidVar = "";
                    if(count($cid)){
                        $tempArr = [];
                        foreach($cid as $ci){
                            $tempArr[] = "&cid[]=".$ci;
                        }
                        $cidVar = implode("&",$tempArr);
                        $cidVar = "&".$cidVar;
                    }
                    $session->set('osm_return_url',Route::_('index.php?option=com_osproperty&task=property_upgrade&type=2'.$cidVar));
                    OspropertyMembership::generatePlanPurchasing($usertype,1,Text::_('OS_PLEASE_PURCHASE_SUBSCRIPTION_FIRST'),1);
                }else {
                    HTML_OspropertyListing::updateFormStep1WithMembership($option, $rows);
                }
            } else {
                HTML_OspropertyListing::updateFormStep1($rows, $lists);
            }
        }
	}
	
	/**
	 * Remove Upgrade property from the list
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function removeUpgrade($option,$cid){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDBO();
		$remove_value = $jinput->getInt('remove_value',0);
		$cids = [];
		$needs = [];
		$needs[] = "aeditdetails";
		$needs[] = "agent_default";
		$needs[] = "agent_editprofile";
		$itemid = OSPRoute::getItemid($needs);
		if(count($cid) > 0){
			$j = 0;
			for($i=0;$i<count($cid);$i++){
				if($cid[$i] != $remove_value){
					$cids[$j] = $cid[$i];
					$j++;
				}
			}
			$cid = $cids;
			//make the form and submit it
			if(count($cid) > 0){
				?>
				<form method="POST" action="index.php" name="ftForm">
					<input type="hidden" name="option" value="com_osproperty" />
					<input type="hidden" name="task" value="property_upgrade" />
					<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
					<select name="cid[]" multiple class="nodisplay">
					<?php
					for($i=0;$i<count($cid);$i++){
						?>
						<option value="<?php echo $cid[$i]?>" selected></option>
						<?php
					}
					?>
					</select>
				</form>
				<script type="text/javascript">
					document.ftForm.submit();
				</script>
				<?php
			}else{
				//change to agent's properties because there aren't any properties
				OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid));
			}
		}else{
			//change to agent's properties
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid));
		}
	}
	
	/**
	 * Confirm upgrade;
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function confirmUpgrade($option,$cid){
		global $bootstrapHelper, $mainframe,$configClass,$configs;
		$db = Factory::getDBO();
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE'));
		$user = Factory::getUser();
		//check to see if user is agent
		if(!HelperOspropertyCommon::isAgent()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}else{
			$agentId = HelperOspropertyCommon::getAgentID();
		}
		
		//
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				//check to see if current user is owner of the property
				$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$agentId'");
				$count = $db->loadResult();
				if($count == 0){
					OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));	
				}
			}
		}else{
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));	
		}
		
		if(count($cid) > 0){
			$cids = implode(",",$cid);
			$db->setQuery("Select a.*,b.image from #__osrs_properties as a left join #__osrs_photos as b on b.pro_id = a.id where a.id in ($cids) group by a.id order by b.ordering");
			$rows = $db->loadObjectList();
		}
		
		//check to see if we have the coupon
		$current_time = date("Y-m-d",time());
		$db->setQuery("Select * from #__osrs_coupon where start_time <= '$current_time' and end_time >= '$current_time' and published = '1'");
		$coupon = $db->loadObject();
		
		$coupon_id = $_COOKIE['coupon_code_awarded'];
		$awarded = 0;
		if($coupon_id != ""){
			if($coupon_id == $coupon->id){
				$awarded = 1;
				setcookie('coupon_id',$coupon_id,time() + 3600);
			}
		}
		if(($configClass['general_featured_upgrade_amount'] == 0) and ((int)$configClass['active_payment'] == 0)){
			//process upgrade
			OspropertyListing::upgradeProperties($cid);
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_editprofile"),Text::_('OS_PROPERTIES_HAVE_BEEN_UPGRADED'));
		}else{
			HTML_OspropertyListing::confirmUpgrade($option,$rows,$coupon,$awarded);
		}
	}
	
	static function confirmUpgradewithMembership($option,$cid){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDBO();
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE'));
		$user = Factory::getUser();
		//check to see if user is agent
		if(!HelperOspropertyCommon::isAgent()){
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
		}else{
			$agentId = HelperOspropertyCommon::getAgentID();
		}
		//
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				//check to see if current user is owner of the property
				$db->setQuery("Select count(id) from #__osrs_properties where agent_id = '$agentId'");
				$count = $db->loadResult();
				if($count == 0){
					OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));	
				}
			}
		}else{
			OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));	
		}
		
		if(count($cid) > 0){
			$cids = implode(",",$cid);
			$db->setQuery("Select a.*,b.image from #__osrs_properties as a left join #__osrs_photos as b on b.pro_id = a.id where a.id in ($cids) group by a.id order by b.ordering");
			$rows = $db->loadObjectList();
		}
		
		$membership_sub_id = $jinput->getInt('membership_sub_id',0);
		if($membership_sub_id == 0){
			$msg = Text::_('OS_PLEASE_SELECT_SUBSCRIPTION_PLAN');
			OSPHelper::redirect(Uri::root()."index.php?option=com_osproperty&task=property_upgrade&cid[]=".$rows[0]->id,$msg);
		}else{
			$db->setQuery("UPDATE #__osrs_properties SET isFeatured = '1' WHERE id = '".$rows[0]->id."'");
			$db->execute();
			HelperOspropertyCommon::setExpiredTime($rows[0]->id,'f',0);
			HelperOspropertyCommon::discountSubscription($membership_sub_id);

			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_editprofile"),Text::_('OS_PROPERTIES_HAVE_BEEN_UPGRADED'));
		}
	}

	static function membershipprocess($cid){
        global $mainframe,$configClass,$jinput;
        $db                     = Factory::getDBO();
        $type                   = $jinput->getInt('type',0);
        if( !HelperOspropertyCommon::isAgent() && !HelperOspropertyCommon::isCompanyAdmin()){
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }
        if(!count($cid)){
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }
        if(HelperOspropertyCommon::isAgent()){
            $usertype = 0;
        }elseif(HelperOspropertyCommon::isCompanyAdmin()){
            $usertype = 2;
        }
        if($configClass['integrate_membership'])
        {
            if (OspropertyMembership::checkExistingSubscribers())
            {
                $property_type = $jinput->getInt('property_type',-1);
                if($property_type == 0 && (OspropertyMembership::getUserStandardCredit() == 0 || OspropertyMembership::getUserStandardCredit() < count($cid))) {
                    $msg = Text::_('OS_YOU_DONOT_HAVE_ENOUGH_CREDIT_TO_IMPLEMENT_THIS_TASK');
                    $session = Factory::getSession();
                    $cidVar = "";
                    if (count($cid)) {
                        $tempArr = [];
                        foreach ($cid as $ci) {
                            $tempArr[] = "&cid[]=" . $ci;
                        }
                        $cidVar = implode("&", $tempArr);
                        $cidVar = "&" . $cidVar;
                    }
                    $session->set('osm_return_url', Route::_('index.php?option=com_osproperty&task=property_membershipprocess&type=' . $type . $cidVar));
                    OspropertyMembership::generatePlanPurchasing($usertype, 0, $msg, 1);

                }elseif($property_type == 1 && (OspropertyMembership::getUserFeaturedCredit() == 0 || OspropertyMembership::getUserFeaturedCredit() < count($cid))){
                    $msg = Text::_('OS_YOU_DONOT_HAVE_ENOUGH_CREDIT_TO_IMPLEMENT_THIS_TASK');
                    $session = Factory::getSession();
                    $cidVar = "";
                    if(count($cid)){
                        $tempArr = [];
                        foreach($cid as $ci){
                            $tempArr[] = "&cid[]=".$ci;
                        }
                        $cidVar = implode("&",$tempArr);
                        $cidVar = "&".$cidVar;
                    }
                    $session->set('osm_return_url',Route::_('index.php?option=com_osproperty&task=property_membershipprocess&type='.$type.$cidVar));
                    OspropertyMembership::generatePlanPurchasing($usertype,1,$msg, 1);
                }

                switch ($type) {
                    case "1":
                        if($property_type == 0){
                            for ($i = 0; $i < count($cid); $i++) {
                                $pid = $cid[$i];
                                HelperOspropertyCommon::setApproval("n", $pid);
                                //set Feature expired time
                                HelperOspropertyCommon::setExpiredTime($pid, "n", 0);
                            }
                            $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                        }
                        elseif($property_type == 1)
                        {
                            for ($i = 0; $i < count($cid); $i++) {
                                $pid = $cid[$i];
                                HelperOspropertyCommon::setApproval("f", $pid);
                                //set Feature expired time
                                HelperOspropertyCommon::setExpiredTime($pid, "f", 0);
                            }
                            $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                        }
                        OspropertyMembership::discountCredit($property_type,count($cid));
                        break;
                    case "2":
                        for ($i = 0; $i < count($cid); $i++) {
                            $pid = $cid[$i];
                            HelperOspropertyCommon::setApproval("f", $pid);
                            //set Feature expired time
                            HelperOspropertyCommon::setExpiredTime($pid, "f", 0);
                        }
                        $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');

                        OspropertyMembership::discountCredit(1,count($cid));
                        break;
                    case "3":
                        if($property_type == 0){
                            for ($i = 0; $i < count($cid); $i++) {
                                $pid = $cid[$i];
                                HelperOspropertyCommon::setApproval("n", $pid);
                                //set Feature expired time
                                HelperOspropertyCommon::setExpiredTime($pid, "n", 0);
                            }
                            $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                        }
                        elseif($property_type == 1)
                        {
                            for ($i = 0; $i < count($cid); $i++) {
                                $pid = $cid[$i];
                                HelperOspropertyCommon::setApproval("f", $pid);
                                //set Feature expired time
                                HelperOspropertyCommon::setExpiredTime($pid, "f", 0);
                            }
                            $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                        }
                        OspropertyMembership::discountCredit($property_type,count($cid));
                        break;
                }
                if (HelperOspropertyCommon::isAgent()) {
                    $needs[] = "aeditdetails";
                    $needs[] = "agent_default";
                    $needs[] = "agent_editprofile";
                    $itemid = OSPRoute::getItemid($needs);
                    if (count($msg) > 0) {
                        for ($i = 0; $i < count($msg); $i++) {
                            $msg[$i] = "<i class='osicon-ok'></i>&nbsp;" . $msg[$i];
                        }
                        $msg = implode("<Br />", $msg);
                    }
                    $url = Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=" . $itemid);
                } elseif (HelperOspropertyCommon::isCompanyAdmin()) {
                    $needs[] = "ccompanydetails";
                    $needs[] = "company_edit";
                    $itemid = OSPRoute::getItemid($needs);
                    if (count($msg) > 0) {
                        for ($i = 0; $i < count($msg); $i++) {
                            $msg[$i] = "<i class='osicon-ok'></i>&nbsp;" . $msg[$i];
                        }
                        $msg = implode("<Br />", $msg);
                    }
                    $url = Route::_("index.php?option=com_osproperty&task=company_properties&Itemid=" . $itemid);
                }
                $mainframe->enqueueMessage($msg);
                $mainframe->redirect($url);
            } else { //redirect to plans purchasing page
                $msg = Text::_('OS_PLEASE_PURCHASE_SUBSCRIPTION_FIRST');
                $session = Factory::getSession();
                $cidVar = "";
                if(count($cid)){
                    $tempArr = [];
                    foreach($cid as $ci){
                        $tempArr[] = "&cid[]=".$ci;
                    }
                    $cidVar = implode("&",$tempArr);
                    $cidVar = "&".$cidVar;
                }
                $session->set('osm_return_url',Route::_('index.php?option=com_osproperty&task=property_membershipprocess&type='.$type.$cidVar));
                OspropertyMembership::generatePlanPurchasing($usertype,'',$msg, 1);
            }
        }else{
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }
    }
	/**
	 * payment process
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function paymentprocess($option,$cid)
	{
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDBO();

        $type = $jinput->getInt('type',0);
        if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) 
		{ 
			//all permission at frontend
            //done
            $msg = [];
            switch ($type) 
			{
                case "1":
                    if (count($cid) > 0) 
					{
                        for ($i = 0; $i < count($cid); $i++) 
						{
                            $pid = $cid[$i];
                            OSPHelper::approvalPropertyNonPayment($pid);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                    break;
                case "2":
                    if (count($cid) > 0) 
					{
                        for ($i = 0; $i < count($cid); $i++) 
						{
                            $pid = $cid[$i];
                            HelperOspropertyCommon::setApproval("f",$pid);
                            //set Feature expired time
                            HelperOspropertyCommon::setExpiredTime($pid,"f",0);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                    break;
                case "3":
                    if (count($cid) > 0) {
                        for ($i = 0; $i < count($cid); $i++) 
						{
                            $pid = $cid[$i];
                            OSPHelper::approvalPropertyNonPayment($pid);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                    break;
            }
            $needs[] = "lmanageproperties";
            $itemid = OSPRoute::getItemid($needs);
            if (count($msg) > 0) 
			{
                for ($i = 0; $i < count($msg); $i++) 
				{
                    $msg[$i] = "<i class='osicon-ok'></i>&nbsp;" . $msg[$i];
                }
                $msg = implode("<Br />", $msg);
            }
            $url = Route::_("index.php?option=com_osproperty&view=lmanageproperties&Itemid=" . $itemid);

        }
		elseif( !HelperOspropertyCommon::isAgent() && !HelperOspropertyCommon::isCompanyAdmin())
		{
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }
        //check permission

        //end check permission
		if(count($cid) > 0)
		{
			for($i=0;$i<count($cid);$i++)
			{
				$id = $cid[$i];
				if(!OSPHelper::checkPermissionWithSpecificProperty($id))
				{
					throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
				}
			}
		}
		else
		{
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }
        $featured = 0;
        switch ($type){
            case "1": //active listing
                $isFeatured = $jinput->getInt('isFeatured',0);
                if($isFeatured == 0)
				{
                    $amount = $configClass['normal_cost'];
                    if($amount == 0)
					{
                        //done
                        if (count($cid) > 0) 
						{
                            for ($i = 0; $i < count($cid); $i++) 
							{
                                $pid = $cid[$i];
                                OSPHelper::approvalPropertyPaymentZero($pid);
                            }
                        }
                        $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                    }
                }
				elseif($isFeatured == 1)
				{
                    $featured = 1;
                    $amount = $configClass['general_featured_upgrade_amount'];
                    if($amount == 0)
					{
                        //done
                        if (count($cid) > 0) 
						{
                            for ($i = 0; $i < count($cid); $i++) 
							{
                                $pid = $cid[$i];
                                HelperOspropertyCommon::setApproval("f",$pid);
                                //set Feature expired time
                                HelperOspropertyCommon::setExpiredTime($pid,"f",0);
                            }
                        }
                        $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                    }
                }
                break;
            case "2":
                $featured = 1;
                $amount = $configClass['general_featured_upgrade_amount'];
                if($amount == 0)
				{
                    //done
                    if (count($cid) > 0) 
					{
                        for ($i = 0; $i < count($cid); $i++) 
						{
                            $pid = $cid[$i];
                            HelperOspropertyCommon::setApproval("f",$pid);
                            //set Feature expired time
                            HelperOspropertyCommon::setExpiredTime($pid,"f",0);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                }
                break;
            case "3":
                $isFeatured = $jinput->getInt('isFeatured',0);
                if($isFeatured == 0){
                    $amount = $configClass['normal_cost'];
                    if($amount == 0){
                        //done
                        if (count($cid) > 0) {
                            for ($i = 0; $i < count($cid); $i++) {
                                $pid = $cid[$i];
                                OSPHelper::approvalPropertyPaymentZero($pid);
                            }
                        }
                        $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                    }
                }elseif($isFeatured == 1){
                    $featured = 1;
                    $amount = $configClass['general_featured_upgrade_amount'];
                    if($amount == 0){
                        //done
                        if (count($cid) > 0) {
                            for ($i = 0; $i < count($cid); $i++) {
                                $pid = $cid[$i];
                                HelperOspropertyCommon::setApproval("f",$pid);
                                //set Feature expired time
                                HelperOspropertyCommon::setExpiredTime($pid,"f",0);
                            }
                        }
                        $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                    }
                }
                break;
        }


        if((floatVal($amount) > 0) && ($configClass['active_payment'] == 1))
		{
            $total = 0;
            if (count($cid) > 0) 
			{
                $cids = implode(",", $cid);
                $db->setQuery("Select * from #__osrs_properties where id in ($cids)");
                $rows = $db->loadObjectList();
                for ($i = 0; $i < count($rows); $i++) 
				{
                    $total = $total + $amount;
                }
            }

            //make the new order
            $row                    = Table::getInstance('Order', 'OspropertyTable');
            $row->id                = 0;
            $row->created_on        = date("Y-m-d H:i:s", time());
            $row->order_status      = "P";
            $row->transaction_id    = "";
            $row->total             = $total;

            $row->message           = "";
            if(HelperOspropertyCommon::isAgent())
			{
                $agent_id = HelperOspropertyCommon::getAgentID();
                $created_by = 0;
            }
			elseif(HelperOspropertyCommon::isCompanyAdmin())
			{
                $agent_id = HelperOspropertyCommon::getCompanyId();
                $created_by = 1;
            }
            $row->agent_id          = $agent_id;
            $row->created_by        = $created_by;
            $row->payment_method    = $jinput->getString('payment_method', '');
            if($row->payment_method == 'os_stripe')
			{
                $row->stripe_token  = $jinput->getString('stripeToken','');
            }
            elseif($row->payment_method == 'os_squareup')
            {
                $row->nonce         = $jinput->getString('nonce','');
            }
            $card_num               = $jinput->getString('x_card_num', '');
            $card_num               = base64_encode($card_num);
            $row->x_card_num        = $card_num;
            $row->x_card_code       = $jinput->getInt('x_card_code', '');
            $row->card_holder_name  = $jinput->getString('card_holder_name', '');
            $row->exp_year          = $jinput->getString('exp_year', '');
            $row->exp_month         = $jinput->getString('exp_month', '');
            $row->curr              = $configClass['general_currency_default'];
            $row->direction         = $type;
			$row->card_type			= (string)$row->card_type;

            if (!$row->store()) 
			{
				//JError::raiseError(500, $row->getError() );
				throw new Exception($row->getError(), 500);
			}

            //order id
            $order_id = $db->insertID();
            //add property into order details table
            for ($i = 0; $i < count($cid); $i++) 
			{
                $pid = $cid[$i];
                $db->setQuery("Insert into #__osrs_order_details (id, order_id, pid,`type`) values (NULL,'$order_id','$pid','$featured')");
                $db->execute();
            }
            OSPHelper::redirect(Uri::root() . "index.php?option=com_osproperty&task=payment_process&order_id=$order_id&Itemid=" . $jinput->getInt('Itemid', 0));
        }
		else
		{
            $msg = [];
            switch ($type) {
                case "1":
                    if (count($cid) > 0) {
                        for ($i = 0; $i < count($cid); $i++) {
                            $pid = $cid[$i];
                            OSPHelper::approvalPropertyNonPayment($pid);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                    break;
                case "2":
                    if (count($cid) > 0) {
                        for ($i = 0; $i < count($cid); $i++) {
                            $pid = $cid[$i];
                            HelperOspropertyCommon::setApproval("f",$pid);
                            //set Feature expired time
                            HelperOspropertyCommon::setExpiredTime($pid,"f",0);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTIES_HAS_BEEN_UPGRADED_TO_FEATURED');
                    break;
                case "3":
                    if (count($cid) > 0) {
                        for ($i = 0; $i < count($cid); $i++) {
                            $pid = $cid[$i];
                            OSPHelper::approvalPropertyNonPayment($pid);
                        }
                    }
                    $msg[] = Text::_('OS_PROPERTY_HAS_BEEN_APPROVED');
                    break;
            }
            if (HelperOspropertyCommon::isAgent()) {
                $needs[] = "aeditdetails";
                $needs[] = "agent_default";
                $needs[] = "agent_editprofile";
                $itemid = OSPRoute::getItemid($needs);
                if (count($msg) > 0) {
                    for ($i = 0; $i < count($msg); $i++) {
                        $msg[$i] = "<i class='osicon-ok'></i>&nbsp;" . $msg[$i];
                    }
                    $msg = implode("<Br />", $msg);
                }
                $url = Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=" . $itemid);
            } elseif (HelperOspropertyCommon::isCompanyAdmin()) {
                $needs[] = "ccompanydetails";
                $needs[] = "company_edit";
                $itemid = OSPRoute::getItemid($needs);
                if (count($msg) > 0) {
                    for ($i = 0; $i < count($msg); $i++) {
                        $msg[$i] = "<i class='osicon-ok'></i>&nbsp;" . $msg[$i];
                    }
                    $msg = implode("<Br />", $msg);
                }
                $url = Route::_("index.php?option=com_osproperty&task=company_properties&Itemid=" . $itemid);
            }
            $mainframe->enqueueMessage($msg);
            $mainframe->redirect($url);
        }
	}
	
	/**
	 * Load States
	 *
	 * @param unknown_type $option
	 */
	static function loadStates($option){
		global $bootstrapHelper, $mainframe,$lang_suffix,$jinput;
		$db = Factory::getDBO();
		$country_id = $jinput->getInt('country_id',0);
		$stateArr = [];
		$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
		$query  = "Select id as value,state_name$lang_suffix as text from #__osrs_states where 1=1 ";
		if($country_id > 0){
			$query .= " and country_id = '$country_id'";
		}
		$query .= " order by state_name";
		$db->setQuery($query);
		$states = $db->loadObjectList();
		$stateArr   = array_merge($stateArr,$states);
		echo HTMLHelper::_('select.genericlist',$stateArr,'state','class="input-small"','value','text');
		?>
		<!--<label><font class="small_text"><?php echo Text::_('OS_NEW_STATE')?>:</font></label> <input type="text" name="nstate" id="nstate" size="10" class="inputbox">-->
		<?php
		echo '<span class="required">(*)</span>';
	}
	
	
	static function propertyDetails($pid)
	{
		global $bootstrapHelper, $mainframe,$configClass,$lang_suffix,$option;
		$db = Factory::getDBO();

		//$db->setQuery("Select * from #__osrs_configuration");
		//$configs = $db->loadObjectList();

		$row = Table::getInstance('Property','OspropertyTable');
		if($pid > 0)
		{
			$row->load((int)$pid);
			$db->setQuery("Select amen_id from #__osrs_property_amenities where pro_id = '$row->id'");
			$amenitylists = $db->loadOBjectList();
			$amenitylists1 = [];
			if(count($amenitylists) > 0)
			{
				for($i=0;$i<count($amenitylists);$i++)
				{
					$amenitylists1[$i] = $amenitylists[$i]->amen_id;
				}
				$amenitylists = [];
				$amenitylists = $amenitylists1;
			}
			
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$pid' order by ordering");
			$photos = $db->loadobjectList();
			$row->photo = $photos;
		}
		else
		{
			$row->published = 1;
			$row->access = 0;
		}
		
		$lists['show_address'] = HTMLHelper::_('select.booleanlist', 'show_address', '', $row->show_address);
		$lists['price_call'] = HTMLHelper::_('select.booleanlist', 'price_call', '', $row->price_call);
		
		//agent
		$db->setQuery("SELECT * FROM #__osrs_agents WHERE `published` = '1' AND `id` = '$row->agent_id'");
		$agent = $db->loadObject();


		if($agent->id > 0)
		{
			//agent country;
			//$db->setQuery("select country_name from #__osrs_countries where id = '$agent->country'");
			$agent->country_name = OSPHelper::getCountryName($agent->country);
			$row->agent = $agent;
			
			//agent state;
			//$db->setQuery("select state_name$lang_suffix as state_name from #__osrs_states where id = '$agent->state'");
			$agent->state_name = OSPHelper::loadSateName($agent->state);//$db->loadResult();
		}
		
		//property types
		$db->setQuery("SELECT * FROM #__osrs_types WHERE `published` = '1' AND `id` = '$row->pro_type'");
		$rs = $db->loadObject();
		$lists['type'] = OSPHelper::getLanguageFieldValue($rs,'type_name');
		
		//categories
		$db->setQuery("SELECT * FROM #__osrs_categories WHERE `id` = '$row->category_id'");
		$rs = $db->loadObject();
		$lists['category'] = OSPHelper::getLanguageFieldValue($rs,'category_name');
		
		//country
		//$db->setQuery("SELECT country_name FROM #__osrs_countries WHERE `id` = '$row->country'");
		//$lists['country'] = $db->loadResult();
		
		//states
		//$db->setQuery("SELECT state_name FROM #__osrs_states WHERE `id` = '$row->state'");
		//$lists['states'] = $db->loadResult();
		
		// access
			$lists['access'][0] = Text::_('OS_PUBLIC');
			$lists['access'][1] = Text::_('OS_REGISTERED');
			$lists['access'][2] = Text::_('OS_SPECIAL');
		
		$db->setQuery("SELECT * FROM #__osrs_amenities WHERE published = '1' order by ordering");
		$amenities = $db->loadObjectList();
		
		$user = Factory::getUser();

        $access_sql = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
		$groups = $db->loadObjectList();
		if(count($groups) > 0){
			for($i=0;$i<count($groups);$i++){
				$group = $groups[$i];
                $access_sql = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
			}
		}
		
		$access_sql = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
		$extra_field_groups = [];
		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
		$fieldgroups = $db->loadObjectList();
		$j = 0;
		if(count($fieldgroups) > 0){
			for($i=0;$i<count($fieldgroups);$i++)
			{
				$fieldgroup = $fieldgroups[$i];

                $access_sql = ' and b.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
				$checkgroup = HelperOspropertyFields::checkFieldData($row->id, $fieldgroup->id);
				
				if($checkgroup == 1)
				{
					$tmp = new stdClass();	
					$tmp->group_name = OSPHelper::getLanguageFieldValue($fieldgroup,'group_name');
					$tmp->fields = HelperOspropertyFields::getFieldsData($row->id, $fieldgroup->id);
					$extra_field_groups[$j] = $tmp; 
					$j++;
				}
			}
		}
		
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/propertyprint.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('option',$option);
		$tpl->set('extra_field_groups',$extra_field_groups);
		$tpl->set('row',$row);
		$tpl->set('lists',$lists);
		$tpl->set('amenities',$amenities);	
		$tpl->set('amenitylists',$amenitylists);
		$tpl->set('groups',$groups);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$tpl->set('configClass',$configClass);
		$body = $tpl->fetch("propertyprint.php");
		echo $body;
	}
	
	/**
	 * Print property function
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function printProperty($option,$id){
		global $bootstrapHelper, $mainframe;
		$document = Factory::getDocument();
		$document->setMetaData( "robots", "noindex" );
		echo OspropertyListing::propertyDetails($id);
	}
	
	static  function showSharingForm($id)
	{
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$captcha_value = $jinput->getString('c','');
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$title = "";
		if($property->ref != "" && $configClass['show_ref'] == 1){
			$title .= $property->ref.", ";
		}
		$title .= OSPHelper::getLanguageFieldValue($property,'pro_name');

		$themename = OSPHelper::getThemeName();
		$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
		$themeobj = $db->loadObject();
		if(file_exists(JPATH_COMPONENT."/templates/".$themename."/sharing.html.tpl.php")){
			$tpl = new OspropertyTemplate();
			$tpl->set('id',$id);
			$tpl->set('title',$title);
			$tpl->set('captcha_value',$captcha_value);
			$tpl->set('bootstrapHelper',$bootstrapHelper);
			$body = $tpl->fetch("sharing.html.tpl.php");	
			echo $body;	
		}
	}
	/**
	 * Submit tell friend form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function submitTellfriend($option,$id){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDbo();
        $user = Factory::getUser();
		$itemid = OSPRoute::getPropertyItemid($id);
		
		if($configClass['property_mail_to_friends'] == 0){
			$msg = Text::_('OS_THIS_FUNCTIONALITY_DOES_NOT_BE_ACTIVATED');
			$url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
			OSPHelper::redirect($url,$msg);
		}

        $captcha = 0;
        if($configClass['captcha_in_tell_friend_form'] == 1){
            $captcha = 1;
            if($user->id > 0 && $configClass['pass_captcha_with_logged_user'] == 1){
                $captcha = 0;
            }
        }
		if($captcha == 1) 
		{
			$captchaPlugin = Factory::getApplication()->getParams()->get('captcha', Factory::getConfig()->get('captcha'));
			$plugin		   = PluginHelper::getPlugin('captcha', $captchaPlugin);
			if ($plugin)
			{

				try
				{
					$res   = Captcha::getInstance($captchaPlugin)->checkAnswer($jinput->post->get('recaptcha_response_field', '', 'string'));
					if (!$res)
					{
						OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=property_details&id=' . $id . '&Itemid=' . $itemid),Text::_('OS_SECURITY_CODE_IS_WRONG'));
					}
				}
				catch (Exception $e)
				{
					//do the same with case !$res
					OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=property_details&id=' . $id . '&Itemid=' . $itemid),Text::_('OS_SECURITY_CODE_IS_WRONG'));
				}
			}
        }

		

        $session = Factory::getSession();
        $pid = $session->get('pid',0);
        if(($pid == 0) or ($pid != $id)){
            $msg = Text::_('OS_SECURITY_CODE_IS_WRONG');
            $url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
            OSPHelper::redirect($url,$msg);
        }
		
		$friend_name 				 = OSPHelper::getStringRequest('friend_name','','');
		$friend_email 				 = OSPHelper::getStringRequest('friend_email','','');
		$your_name 					 = OSPHelper::getStringRequest('your_name','','');
		$your_email 			 	 = OSPHelper::getStringRequest('your_email','','');
		$message					 = $_POST['message'];
		
		$contact['friend_name']  	 = $friend_name;
		$contact['friend_email'] 	 = $friend_email;
		$contact['your_name']   	 = $your_name;
		$contact['your_email']  	 = $your_email;
		$contact['message'] 		 = $message;
		//$link 						 = Route::_(Uri::root()."index.php?option=com_osproperty&task=property_details&id=$id");
		$link						 = Route::_("index.php?option=com_osproperty&task=property_details&id=".$id."&Itemid=".$itemid);
		$link						 = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$link;
		$contact['link']			 = "<a href='".$link."'>".$link."</a>";
		OspropertyEmail::sendFriendEmail($option,$contact);
		$tmpl = $jinput->getString('tmpl','');

		if($tmpl == "component"){
			?>
			<div class="width90pc padding20">
				<h2>
					<?php echo Text::_('OS_EMAIL_HAS_BEEN_SENT_TO_YOUR_FRIEND');?>
				</h2>
			</div>
			<?php
		}else{
			$url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
			OSPHelper::redirect($url,Text::_('OS_EMAIL_HAS_BEEN_SENT_TO_YOUR_FRIEND'));
		}
	}
	
	/**
	 * Submit comment
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function submitComment($option,$id){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDbo();

		$itemid = OSPRoute::getPropertyItemid($id);
		
		if($configClass['comment_active_comment'] == 0){
			$msg = Text::_('OS_THIS_FUNCTIONALITY_DOES_NOT_BE_ACTIVATED');
			$url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
			OSPHelper::redirect($url,$msg);
		}

		if(!OSPHelper::checkJDCaptcha())
		{
			$msg = Text::_('OS_SECURITY_CODE_IS_WRONG');
			$url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
			OSPHelper::redirect($url,$msg);
		}
		
        $session = Factory::getSession();
        $pid = $session->get('pid',0);
        if(($pid == 0) or ($pid != $id)){
            $msg = Text::_('OS_SECURITY_CODE_IS_WRONG');
            $url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid);
            OSPHelper::redirect($url,$msg);
        }
		
		$comment_author 	= OSPHelper::getStringRequest('comment_author','','');
		$comment_title  	= OSPHelper::getStringRequest('comment_title','','');
		$comment_message	= $_POST['comment_message'];
		//$rating				=$jinput->getString('rating','');
		
		//check to see if user is already 
		//and only in the case comment_allow_register=1;
		if($configClass['registered_user_write_comment'] == 1){
			$user = Factory::getUser();
			$db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$id' and user_id = '$user->id'");
            $count = $db->loadResult();
			if(($count > 0) and ($configClass['only_one_review'] == 1)){
				OSPHelper::redirect(Route::_("index.php?opton=com_osproperty&task=property_details&id=$id&Itemid=".$itemid),Text::_('OS_YOU_HAVE_COMMENTED_FOR_THIS_PROPERTY_ALREADY'));
			}
		}
		
		$row = Table::getInstance('Comment','OspropertyTable');
		$post = $jinput->post->getArray();
		$row->bind($post);
		$rate1		  = $row->rate1;
		$rate2		  = $row->rate2;
		$rate3		  = $row->rate3;
		$rate4		  = $row->rate4;
		$rating		  = round(($rate1 + $rate2 + $rate3 + $rate4)/4);
		$row->id = 0;
		if($configClass['registered_user_write_comment'] == 1){
			$user = Factory::getUser();
			$row->user_id = $user->id;
		}else{
			$row->user_id = 0;
		}
		$row->content	= $comment_message;
		$row->title		= $comment_title;
		$row->name		= $comment_author;
		$row->pro_id	= $id;
		$row->rate		= $rating;
		$row->ip_address = OSPHelper::get_ip_address();
		$row->country	= (string) $row->country;
		$row->created_on = date("Y-m-d H:i:s",time());
		if($configClass['comment_auto_approved'] == 1){
			$row->published = 1;
			$row->alreadyPublished = 1;
		}else{
			$row->published = 0;
			$row->alreadyPublished = 0;
		}
		if (!$row->store()) 
		{
			//JError::raiseError(500, $row->getError() );
			throw new Exception($row->getError(), 500);
		}
		
		$cmt_id = $db->insertID();
		
		//if the comment is published automatically, update the rating and send email to property's onwer
		if($configClass['comment_auto_approved'] == 1){
			$db->setQuery("Select number_votes,total_points from #__osrs_properties where id = '$id'");
			$rating_details = $db->loadObject();
			$number_votes = $rating_details->number_votes;
			$total_points = $rating_details->total_points;
			$number_votes++;
			$total_points += $rating;	
			
			$db->setQuery("Update #__osrs_properties set number_votes = '$number_votes',total_points='$total_points' where id = '$id'");
			$db->execute();
			
			//send email to property's onwer
			$emailopt['author'] 	= $comment_author;
			$emailopt['message']	= $comment_message;
			$emailopt['title'] 		= $comment_title;
			$emailopt['rate'] 		= $rating."/5";
			
			$query = "Select a.name,a.email from #__osrs_agents as a inner join #__osrs_properties as b on b.agent_id = a.id where b.id = '$id'";
			$db->setQuery($query);
			$agent = $db->loadObject();
			$emailopt['agentname'] = $agent->name;
			$emailopt['agentemail'] = $agent->email;
			
			$link = OSPHelper::getSiteUrl().Route::_("index.php?option=com_osproperty&task=property_details&id=".$id);
			$emailopt['link'] 		= "<a href='$link'>".$link."</a>";
					
			OspropertyEmail::sendCommentEmail($option,$emailopt);
			
			$msg = Text::_('OS_COMMENT_HAS_BEEN_SUBMITTED');
		}else{
			//send email to administrator to inform there are new comment			
			//send email to property's onwer
			$db->setQuery("Select * from #__osrs_properties where id = '$id'");
			$property = $db->loadObject();
			$pro_name = OSPHelper::getLanguageFieldValue($property,'pro_name');
			
			$emailopt['pro_name']	= $pro_name;
			$emailopt['author'] 	= $comment_author;
			$emailopt['message']	= $comment_message;
			$emailopt['title'] 		= $comment_title;
			$emailopt['rate'] 		= $rating."/5";
			
			$link = Uri::root()."administrator/index.php?option=com_osproperty&task=comment_list";
			$emailopt['link'] 		= "<a href='$link'>".$link."</a>";
					
			OspropertyEmail::sendAdministratorCommentEmail($option,$emailopt);
			
			$msg = Text::_('OS_COMMENT_HAS_BEEN_SUBMITTED_ADMINISTRATOR_WILL_CHECK_AND_PUBLISH_THE_COMMENT_AS_SOON_AS_POSSIBLE');
		}
		$link = Route::_("index.php?option=com_osproperty&task=property_details&id=$id&Itemid=".$itemid);
		OSPHelper::redirect($link,$msg);
	}
	
	
	/**
	 * Export property in pdf layout
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function exportpdf($option,$pid)
	{
		global $bootstrapHelper, $mainframe,$configClass,$lang_suffix,$languages;
		$db = Factory::getDBO();

		if((int)$pid == 0)
		{
			return;
		}
		
		$pdf_root = JPATH_ROOT."/tmp";
		$filename = "property".$pid.".pdf";
		//update the content of the pdf file before download it
		//$db->setQuery("Select * from #__osrs_configuration");
		//$configs = $db->loadObjectList();
		
		$row = Table::getInstance('Property','OspropertyTable');
		if($pid > 0)
		{
			$row->load((int)$pid);
			$db->setQuery("Select amen_id from #__osrs_property_amenities where pro_id = '$row->id'");
			$amenitylists = $db->loadOBjectList();
			$amenitylists1 = [];
			if(count($amenitylists) > 0)
			{
				for($i=0;$i<count($amenitylists);$i++)
				{
					$amenitylists1[$i] = $amenitylists[$i]->amen_id;
				}
				$amenitylists = [];
				$amenitylists = $amenitylists1;
			}
			
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$pid' order by ordering");
			$photos = $db->loadobjectList();
			$row->photo = $photos;
		}
		else
		{
			$row->published = 1;
			$row->access = 0;
		}
		$lists['show_address'] = HTMLHelper::_('select.booleanlist', 'show_address', '', $row->show_address);
		$lists['price_call'] = HTMLHelper::_('select.booleanlist', 'price_call', '', $row->price_call);
		
		//agent
		$agent = new stdClass();
		//if($row->agent_id > 0)
		//{
			$db->setQuery("SELECT * FROM #__osrs_agents WHERE `published` = '1' AND `id` = '$row->agent_id'");
			$agent = $db->loadObject();
			//agent country;
			//$db->setQuery("select country_name from #__osrs_countries where id = '$agent->country'");
			//$agent->country_name = OSPHelper::getCountryName($agent->country);//$db->loadResult();
		//}
		$row->agent = $agent;
		
		//agent state;
		//$db->setQuery("select state_name$lang_suffix as state_name from #__osrs_states where id = '$agent->state'");
		//$agent->state_name = OSPHelper::loadSateName($agent->state);//$db->loadResult();
		
		//property types
		$db->setQuery("SELECT id,type_name$lang_suffix as type_name FROM #__osrs_types WHERE `published` = '1' AND `id` = '$row->pro_type'");
		$type = $db->loadObject();
		$lists['type'] = OSPHelper::getLanguageFieldValue($type,'type_name');

		//country
		///$db->setQuery("SELECT country_name FROM #__osrs_countries WHERE `id` = '$row->country'");
		$lists['country'] = OSPHelper::getCountryName($row->country);//$db->loadResult();
		
		//states
		//$db->setQuery("SELECT state_name$lang_suffix as state_name FROM #__osrs_states WHERE `id` = '$row->state'");
		$lists['states'] = OSPHelper::loadSateName($row->state);//$db->loadResult();
		
		$db->setQuery("SELECT * FROM #__osrs_amenities WHERE published = '1' order by ordering");
		$amenities = $db->loadObjectList();

        $access_sql = ' and `access` IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';

		$db->setQuery("Select * from #__osrs_fieldgroups where published = '1' $access_sql order by ordering");
		$groups = $db->loadObjectList();

		if(count($groups) > 0)
		{
			for($i=0;$i<count($groups);$i++)
			{
				$group = $groups[$i];
				$groups[$i]->fields = HelperOspropertyFields::getFieldsData($pid, $group->id);
			}
		}
		
		while (@ob_end_clean());
		ob_start();
		HTML_OspropertyListing::printPropertyPdf($option,$row,$lists,$amenities,$amenitylists,$groups);
		$body = ob_get_contents();
		ob_clean();
		jimport('joomla.filesystem.folder');
		require_once JPATH_ROOT . "/components/com_osproperty/helpers/tcpdf/tcpdf.php";
		//require_once JPATH_ROOT . "/components/com_osproperty/helpers/tcpdf/config/lang/eng.php";
		if(file_exists(JPATH_ROOT . "/components/com_osproperty/helpers/tcpdf/config/lang/eng.php"))
		{
			require_once JPATH_ROOT . "/components/com_osproperty/helpers/tcpdf/config/lang/eng.php";
		}
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($sitename);
		$pdf->SetTitle($row->pro_name);
		$pdf->SetSubject($row->pro_name);
		$pdf->SetKeywords($row->pro_name);
		$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		//set auto page breaks
		$pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		$font = empty($configClass['pdf_font']) ? 'times' : $configClass['pdf_font'];
		$pdf->SetFont($font, '', 8);
		$pdf->AddPage('P','A4');
		$v = $pdf->writeHTML($body, true, false, false, false, '');

		//Filename
		$pdf->Output($pdf_root.DS.$filename, 'F');
		HelperOspropertyCommon::downloadfile($filename);
		exit();
		//}
	}
	
	static function generateCaptcha($option){
		global $bootstrapHelper, $mainframe;
		while (@ob_end_clean());
		$ResultStr = OSPHelper::getStringRequest('ResultStr','','');
		$NewImage =imagecreatefromjpeg(JPATH_ROOT."/components/com_osproperty/captcha/img.jpg");//image create by existing image and as back ground 
		$LineColor = imagecolorallocate($NewImage,233,239,239);//line color 
		$TextColor = imagecolorallocate($NewImage, 58, 43, 149);//text color-white
		imageline($NewImage,1,1,40,40,$LineColor);//create line 1 on image 
		imageline($NewImage,1,100,60,0,$LineColor);//create line 2 on image 
		imagestring($NewImage, 5, 20, 10, $ResultStr, $TextColor);// Draw a random string horizontally 
		header("Content-type: image/jpeg");// out out the image 
		imagejpeg($NewImage);//Output image to browser 
		exit();
	}
	
	/**
	 * Approval details
	 *
	 * @param unknown_type $option
	 */
	static function approvalDetails($option,$id){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_properties where id = '$id'");
		$property = $db->loadObject();
		$db->setQuery("Select * from #__osrs_expired where id = '$id'");
		$expired = $db->loadObject();
		
		HTML_OspropertyListing::approvalDetails($option,$property,$expired);
	}
	
	/**
	 * Unfeatured
	 *
	 * @param unknown_type $id
	 */
	static function unFeatured($id){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Update #__osrs_properties set isFeatured = '0' where id = '$id'");
		$db->execute();
	}
	
	/**
	 * Un approved
	 *
	 * @param unknown_type $id
	 */
	static function unApproved($id){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		$db->setQuery("Update #__osrs_properties set approved = '0' where id = '$id'");
		$db->execute();
	}
	
	
	/**
	 * Request approval
	 * Confirm page
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function requestApproval($option,$cid){
		global $bootstrapHelper, $mainframe,$configClass;
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_REQUEST_APPROVAL'));
		if(count($cid) == 0){
			?>
			<script type="text/javascript">
			history.go(-1);
			</script>
			<?php
		}
		else
		{
			$rows = [];
			for($i=0;$i<count($cid);$i++)
			{
				$id = $cid[$i];
				$db->setQuery("Select * from #__osrs_properties where id = '$id'");
				$property = $db->loadObject();
				if($property->isNew == 0 && $property->approved == 0)
				{
					$tmp = new stdClass();
					$tmp->id = $property->id;
					$tmp->property = $property->pro_name;
					$rows[$i] = $tmp;
					//$rows[$i]->id = $property->id;
					//$rows[$i]->property = $property->pro_name;
				}
			}
			if(count($rows) == 0){
				?>
				<script type="text/javascript">
				history.go(-1);
				</script>
				<?php
			}
			HTML_OspropertyListing::confirmApproval($option,$rows);
		}
	}
	
	/**
	 * Process request approval
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function processRequestApproval($option,$cid){
		global $bootstrapHelper, $mainframe,$configClass;
		$user = Factory::getUser();
		$db = Factory::getDbo();
		$cids = implode(",",$cid);
        $emailOpt = [];
		if($configClass['general_approval'] == 1)
		{
			$db->setQuery("Update #__osrs_properties set approved = '1',published = '1' where id in ($cids)");
			$db->execute();
			for($i=0;$i<count($cid);$i++){
				OspropertyListing::setexpired($option,$cid[$i]);
			}
			$needs = [];
			$needs[] = "agent_editprofile";
			$needs[] = "agent_default";
			$needs[] = "lagents";
			$itemid = OSPRoute::getItemid($needs);
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid),Text::_('OS_PROPERTIES_HAVE_BEEN_APPROVED'));
		}
		else
		{
			$db->setQuery("Update #__osrs_properties set request_to_approval = '1' where id in ($cids)");
			$db->execute();
			
			//send email to admin to inform
			$db->setQuery("Select * from #__osrs_agents where user_id = '$user->id'");
			$agent = $db->loadObject();
			$tmp = new stdClass();
			$tmp->customer = $agent->name;
			$db->setQuery("Select id,pro_name from #__osrs_properties where id  in ($cids)");
			$rows = $db->loadObjectList();
			$proArr = [];
			for($i=0;$i<count($rows);$i++){
				$proArr[$i] = $rows[$i]->pro_name;
			}
			$pro_name = implode(",",$proArr);
			$tmp->property = $pro_name;
			$emailOpt[0] = $tmp;
			OspropertyEmail::sendPropertyApprovalRequest($option,$emailOpt);
			$needs[] = "agent_editprofile";
			$needs[] = "agent_default";
			$needs[] = "lagents";
			$itemid = OSPRoute::getItemid($needs);
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_default&Itemid=".$itemid),Text::_('OS_REQUEST_HAVE_BEEN_SENT_TO_ADMINISTRATOR'));
		}
		
	}
	
	
	/**
	 * Updade properties
	 *
	 * @param unknown_type $cid
	 */
	static function upgradeProperties($cid){
		global $bootstrapHelper, $mainframe;
		$configClass = OSPHelper::loadConfig();
		$db = Factory::getDbo();
		$time_amount = $configClass['general_time_in_days_featured'];//$configs[16]->fieldvalue;
		if(count($cid) > 0){
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				//update approved, published, approved
				$db->setQuery("Update #__osrs_properties set isFeatured = '1',approved = '1',published = '1' where id = '$id'");
				//echo $db->getQuery();die();
				$db->execute();
				//calculate the expired feature time
				$current_time = time();
				$expired_time = $current_time + $time_amount*3600*24;
				$expired_time = date("Y-m-d H:i:s",$expired_time);
				//update into expired table
				$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
				$count = $db->loadResult();
				if($count == 0){
					self::setexpiredInPayment($id);
				}
				
				$db->setQuery("select count(id) from #__osrs_expired where pid = '$id'");
				$countItem = $db->loadResult();
				if($countItem == 0){
					$db->setQuery("INSERT INTO #__osrs_expired (id,expired_feature_time,pid) values (NULL,'$expired_time','$id')");
					$db->execute();
				}else{
					$db->setQuery("Update #__osrs_expired set expired_feature_time = '$expired_time' where pid = '$id'");
					$db->execute();
				}
			}
		}
	}
	
	/**
	 * After customer complete the payment, the system must auto set expired the time. 
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	public static function setexpiredInPayment($id){
		global $bootstrapHelper, $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$current_time 	 = HelperOspropertyCommon::getRealTime();
		$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
		$count = $db->loadResult();
		$use_expired = $configClass['general_use_expiration_management'];
		if($use_expired == 1){
			if($count == 0){
				//$general_approval = $configs[12]->fieldvalue;
				//check and calculate the expired and clean db time
				$unpublish_time  = intval($configClass['general_time_in_days']);
				$remove_time	 = intval($configClass['general_unpublished_days']);
				$feature_time    = intval($configClass['general_time_in_days_featured']);
				$send_appro	     = $configClass['send_approximates'];
				$appro_days		 = $configClass['approximates_days'];
				
				
				$unpublish_time  = $current_time + $unpublish_time*24*3600;
				$feature_time    = $current_time + $feature_time*24*3600;
				$remove_time     = $unpublish_time + $remove_time*24*3600;
				//allow to send the approximates expired day
				if($send_appro == 1){
					$inform_time = $unpublish_time - $appro_days*24*3600;
					$inform_time = date("Y-m-d H:i:s",$inform_time);
				}else{
					$inform_time = "";
				}
				$unpublish_time	 = date("Y-m-d H:i:s",$unpublish_time);
				$remove_time	 = date("Y-m-d H:i:s",$remove_time);
				$feature_time	 = date("Y-m-d H:i:s",$feature_time);
				//insert into #__osrs_expired
				$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,expired_time,expired_feature_time,remove_from_database) values (NULL,$id,'$inform_time','$unpublish_time','$feature_time','$remove_time')");
				$db->execute();
				//update start publishing today
				OspropertyListing::updateStartPublishing($id);
			}else{
				//$general_approval = $configs[12]->fieldvalue;
				//check and calculate the expired and clean db time
				$db->setQuery("Select * from #__osrs_expired where pid = '$id'");
				$row = $db->loadObject();
				$expired_time    = $row->expired_time;
				$unpublish_time  = strtotime($expired_time);
				
				
				$remove_time	 = intval($configClass['general_unpublished_days']);
				$feature_time    = intval($configClass['general_time_in_days_featured']);
				$send_appro	     = $configClass['send_approximates'];
				$appro_days		 = $configClass['approximates_days'];
				
				$current_time 	 = time();
				$feature_time    = $current_time + $feature_time*24*3600;
				if($current_time > $unpublish_time){
					$new = 1;
					$unpublish_time = intval($configClass['general_time_in_days']);
					$unpublish_time  = $current_time + $unpublish_time*24*3600;
				}else{
					$new = 0;
					if($feature_time > $unpublish_time){
						$unpublish_time = $feature_time;
					}
				}
				$remove_time     = $unpublish_time + $remove_time*24*3600;
				$remove_time	 = date("Y-m-d H:i:s",$remove_time);
				//allow to send the approximates expired day
				if($send_appro == 1){
					$inform_time = $unpublish_time - $appro_days*24*3600;
					$inform_time = date("Y-m-d H:i:s",$inform_time);
				}else{
					$inform_time = "";
				}
				$unpublish_time	 = date("Y-m-d H:i:s",$unpublish_time);
				$feature_time	 = date("Y-m-d H:i:s",$feature_time);
				//insert into #__osrs_expired
				$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',expired_feature_time= '$feature_time',remove_from_database='$remove_time' WHERE pid = '$id'");
				$db->execute();
				
				//update start publishing today
				$db->setQuery("Select publish_up from #__osrs_properties where id = '$id'");
				$publish_up = $db->loadResult();
				if(($publish_up == "0000-00-00") or ($new == 1)){
					OspropertyListing::updateStartPublishing($id);
				}
			}
		}
	}
	
	/**
	 * Set expired time for properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function setexpired($option,$id){
		global $bootstrapHelper, $mainframe,$configs,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) from #__osrs_expired where pid = '$id'");
		$count = $db->loadResult();
		$current_time 	= HelperOspropertyCommon::getRealTime();
		$use_expired = $configClass['general_use_expiration_management'];
		if($use_expired == 1){
			if($count == 0){
				//check and calculate the expired and clean db time
				$unpublish_time = intval($configClass['general_time_in_days']);
				$remove_time	= intval($configClass['general_unpublished_days']);
				$send_appro		= $configClass['send_approximates'];
				$appro_days		= $configClass['approximates_days'];
				
				$unpublish_time = $current_time + $unpublish_time*24*3600;
				//calculate remove time
				$remove_time    = $unpublish_time + $remove_time*24*3600;
				//allow to send the approximates expired day
				if($send_appro == 1){
					$inform_time = $unpublish_time - $appro_days*24*3600;
					$inform_time = date("Y-m-d H:i:s",$inform_time);
				}else{
					$inform_time = "";
				}
				//change to time stamp
				$unpublish_time	= date("Y-m-d H:i:s",$unpublish_time);
				$remove_time	= date("Y-m-d H:i:s",$remove_time);
				//insert into #__osrs_expired
				$db->setQuery("Insert into #__osrs_expired (id,pid,inform_time,expired_time,remove_from_database) values (NULL,$id,'$inform_time','$unpublish_time','$remove_time')");
				$db->execute();
				//update start publishing today
				OspropertyListing::updateStartPublishing($id);
				
			}else{//in the case this property is already in the expired table
				//check and calculate the expired and clean db time
				$unpublish_time = intval($configClass['general_time_in_days']);
				$remove_time	= intval($configClass['general_unpublished_days']);
				$send_appro		= $configClass['send_approximates'];
				$appro_days		= $configClass['approximates_days'];
				
				$current_time 	= time();
				$unpublish_time = $current_time + $unpublish_time*24*3600;
				//calculate remove time
				$remove_time    = $unpublish_time + $remove_time*24*3600;
				//allow to send the approximates expired day
				if($send_appro == 1){
					$inform_time = $unpublish_time - $appro_days*24*3600;
					$inform_time = date("Y-m-d H:i:s",$inform_time);
				}else{
					$inform_time = "";
				}
				//change to time stamp
				$unpublish_time	= date("Y-m-d H:i:s",$unpublish_time);
				$remove_time	= date("Y-m-d H:i:s",$remove_time);
				//insert into #__osrs_expired
				$db->setQuery("UPDATE #__osrs_expired SET inform_time = '$inform_time',expired_time='$unpublish_time',remove_from_database='$remove_time' WHERE pid = '$id'");
				$db->execute();
				//update start publishing today
				OspropertyListing::updateStartPublishing($id);
			}
		}
	}
	
	/**
	 * Update start publishing
	 *
	 * @param unknown_type $id
	 */
	static function updateStartPublishing($id){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		$time = date("Y-m-d",time());
		$db->setQuery("Update #__osrs_properties set publish_up = '$time' where id = '$id'");
		$db->execute();
	}
	
	
	/**
	 * Search function for ajax search module.
	 *
	 * @param unknown_type $option
	 */
	static function search($option){
		global $bootstrapHelper, $mainframe,$configClass,$languages,$lang_suffix,$jinput;
		$db = Factory::getDBO();
        $ordering = $jinput->getString('ordering','type');
        $show_introtext = $jinput->getInt('show_introtext',1);
        $show_address = $jinput->getInt('show_address',1);
        $show_cost = $jinput->getInt('show_cost',1);
        $show_agent = $jinput->getInt('show_agent',1);
        $orderby = $jinput->getString('orderby','listdate');
        $ordertype = $jinput->getString('ordertype','');
        $search_name = $jinput->getInt('search_name',1);
        $search_address = $jinput->getInt('search_address',1);
        $search_agent = $jinput->getInt('search_agent',1);
        $search_desc = $jinput->getInt('search_desc',1);
        $keyword = OSPHelper::getStringRequest('search_exp','','');
		$keyword = $db->escape($keyword);
		$results = [];
		//if keyword is not null. Do search
		if ($keyword != ""){
			if(!function_exists('json_decode'))
			{
			    function json_decode($json)
			    {
			        $comment = false;
			        $out = '$x=';
			        for ($i=0; $i<strlen($json); $i++)
			        {
			            if (!$comment)
			            {
			                if (($json[$i] == '{') || ($json[$i] == '['))
			                    $out .= ' array(';
			                else if (($json[$i] == '}') || ($json[$i] == ']'))
			                    $out .= ')';
			                else if ($json[$i] == ':')
			                    $out .= '=>';
			                else
			                    $out .= $json[$i];
			            }
			            else
			                $out .= $json[$i];
			            if ($json[$i] == '"' && $json[($i-1)]!="\\")
			                $comment = !$comment;
			        }
			        eval($out . ';');
			        return $x;
			    }
			}
			if (!function_exists('json_encode'))
			{
				  function json_encode($a=false){
				  		global $bootstrapHelper, $mainframe;
					    if (is_null($a)) return 'null';
					    if ($a === false) return 'false';
					    if ($a === true) return 'true';
					    if (is_scalar($a)){
					        if (is_float($a)){
						        // Always use "." for floats.
						        return floatval(str_replace(",", ".", strval($a)));
					        }
					        if (is_string($a)){
					        	static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
					       		return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
					        }else{
					        	return $a;
					        }
					    }
					    $isList = true;
					    for ($i = 0, reset($a); $i < count($a); $i++, next($a)){
						      if (key($a) !== $i)
						      {
							        $isList = false;
							        break;
						      }
					    }
					    $result = [];
					    if ($isList){
						      foreach ($a as $v) $result[] = json_encode($v);
						      return '[' . join(',', $result) . ']';
					    }else{
						      foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
						      return '{' . join(',', $result) . '}';
					    }
				   }
			 }
		
			 ob_end_clean();
			 
			 //$db->setQuery("select * from #__osrs_configuration");
			// $configs = $db->loadObjectList();
			 
			 switch ($ordering){
			 	
			 	case "category": //category
			 		$db->setQuery("Select * from #__osrs_categories where published = '1' order by ordering");
			 		$categories = $db->loadObjectList();
			 		if(count($categories) > 0){
			 			for($i=0;$i<count($categories);$i++){
			 				$category = $categories[$i];
			 				$category_name = OSPHelper::getLanguageFieldValue($category,'category_name');
			 				$query = "Select a.*,c.name as agent_name,d.country_name,e.state_name from #__osrs_properties as a"
			 						." inner join #__osrs_agents as c on c.id = a.agent_id"
			 						." inner join #__osrs_countries as d on d.id = a.country"
			 						." inner join #__osrs_states as e on e.id = a.state"
									." inner join #__osrs_cities as g on g.id = a.city"
			 						." where a.approved = '1' and a.published = '1' and a.category_id = '$category->id' and (";
			 				//
			 				$query1 = [];
			 				if($search_name == 1){
			 					$query1[] = " a.pro_name$lang_suffix like '%$keyword%'";
			 					$query1[] = " a.ref like '%$keyword%'";
			 				}
			 				if($search_address == 1){
			 					$query1[] = " a.address like '%$keyword%' or a.region like '%$keyword%' or a.postcode like '%$keyword%' or d.country_name like '%$keyword%' or e.state_name like '%$keyword%' or g.city like '%$keyword%'";
			 				}
			 				
			 				if($search_desc == 1){
			 					$query1[] = " a.pro_small_desc$lang_suffix like '%$keyword%' or a.pro_full_desc$lang_suffix like '%$keyword%'";
			 				}
			 				
			 				if($search_agent == 1){
			 					$query1[] = " c.name like '%$keyword%'";
			 				}
			 				
			 				$query1a = implode(" or ",$query1);
			 				$query .= $query1a." )";
			 				
			 				switch ($orderby){
			 					case "pro_name":
			 						$query .= " order by a.pro_name$lang_suffix ".$ordertype;
			 					break;
			 					case "price":
			 						$query .= " order by a.price ".$ordertype;
			 					break;
			 					case "listdate":
			 						$query .= " order by a.created ".$ordertype;
			 					break;
			 				}
			 				
			 				$db->setQuery($query);
			 				
			 				$rows = $db->loadObjectList();
			 				
			 				if(count($rows) > 0){
			 					//echo $db->getQuery();
				  	  	  	   //add categories into array
				  	  	  	   for($j=0;$j<count($rows);$j++){
				  	  	  	   		
				  	  	  	   		$row = $rows[$j];
				  	  	  	   		$needs = [];
                                    $needs[] = "property_details";
                                    $needs[] = $row->id;
                                    $itemid = OSPRoute::getItemid($needs);

				  	  	  	   		$pro_name = OSPHelper::getLanguageFieldValue($row,'pro_name');
				  	  	  	   		$row->pro_name = $pro_name;
				  	  	  	   		
				  	  	  	   		$db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id'");
				  	  	  	   		$image = $db->loadResult();
				  	  	  	   		if($image != ""){
				  	  	  	   			//having thumbnail image
				  	  	  	   			if(file_exists(JPATH_ROOT."/images/osproperty/properties/".$row->id."/thumb/".$image)){
				  	  	  	   				$results[$category_name][$j]->pimage = "<img src='".Uri::root()."images/osproperty/properties/".$row->id."/thumb/".$image."' height='60' width='60' border='0'>"; 
				  	  	  	   			}else{
				  	  	  	   				$results[$category_name][$j]->pimage = "<img src='".Uri::root()."media/com_osproperty/assets/images/noimage64.png' height='60' width='60' border='0'>"; 
				  	  	  	   			}
				  	  	  	   		}else{
				  	  	  	   			$results[$category_name][$j]->pimage = "<img src='".Uri::root()."media/com_osproperty/assets/images/noimage64.png' height='60' width='60' border='0'>"; 
				  	  	  	   		}
				  	  	  	   		$title = "";
				  	  	  	   		if(($rows[$j]->ref  != "") and ($configClass['show_ref'] == 1)){
				  	  	  	   			$title .= $rows[$j]->ref.", ";
				  	  	  	   		}
				  	  	  	   		$title .= $rows[$j]->pro_name;
				  	  	  	   		$results[$category_name][$j]->title = $title;
				  	  	  	   		//text here
				  	  	  	   		$text = "";
				  	  	  	   		
				  	  	  	   		if($show_cost == 1){
				  	  	  	   			$text .= "(".Text::_('OS_COST')." : ";
				  	  	  	   			if($row->price_call == 1){
				  	  	  	   				$text.= Text::_('OS_CALL_FOR_PRICE');
				  	  	  	   			}else{
				  	  	  	   				$text.= HelperOspropertyCommon::loadCurrency($row->curr)." ".OSPHelper::showPrice($row->price);
				  	  	  	   				if($row->rent_time != ""){
				  	  	  	   					$text .= " / ".Text::_($row->rent_time);
				  	  	  	   				}
				  	  	  	   			}
				  	  	  	   			$text .= ")  ";
				  	  	  	   		}
				  	  	  	   		if($show_address == 1){
				  	  	  	   			$text .= OSPHelper::getLanguageFieldValue($row,'address').", ".HelperOspropertyCommon::loadCityName($row->city).", ";
				  	  	  	   			$text .= HelperOspropertyCommon::loadStateName($row->state).", ".$row->country_name;
				  	  	  	   		}
				  	  	  	   		if ($show_introtext == 1){
										$pro_small_desc = strip_tags(stripslashes(OSPHelper::getLanguageFieldValue($row,'pro_small_desc')));
				                        $text .= $pro_small_desc;
				                    }
				                    if ($show_agent == 1){
				                    	$text .= ". ".Text::_('OS_AGENT').": ".strip_tags(stripslashes($row->agent_name));
				                    }
				  	  	  	   		$results[$category_name][$j]->text  = $text;
				  	  	  	   		$results[$category_name][$j]->href  = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id&Itemid=".$itemid);
				  	  	  	   }
				  	  	    }
			 			}
			 		}
			 	break;
			 	case "type": //category
			 		$db->setQuery("Select * from #__osrs_types where published = '1'");
			 		$categories = $db->loadObjectList();
			 		if(count($categories) > 0){
			 			for($i=0;$i<count($categories);$i++){
			 				$category = $categories[$i];
			 				$type_name = OSPHelper::getLanguageFieldValue($category,'type_name');
			 				$query = "Select a.*,c.name as agent_name,d.country_name,e.state_name from #__osrs_properties as a"
			 						." inner join #__osrs_agents as c on c.id = a.agent_id"
			 						." inner join #__osrs_countries as d on d.id = a.country"
			 						." inner join #__osrs_states as e on e.id = a.state"
									." inner join #__osrs_cities as g on g.id = a.city"
			 						." where a.approved = '1' and a.published = '1' and a.pro_type = '$category->id' and (";
			 				//
			 				$query1 = [];
			 				if($search_name == 1){
			 					$query1[] = " a.pro_name$lang_suffix like '%$keyword%'";
			 					$query1[] = " a.ref like '%$keyword%'";
			 				}
			 				if($search_address == 1){
			 					$query1[] = " a.address like '%$keyword%' or a.region like '%$keyword%' or a.postcode like '%$keyword%' or d.country_name like '%$keyword%' or e.state_name like '%$keyword%' or g.city like '%$keyword%'";
			 				}
			 				
			 				if($search_desc == 1){
			 					$query1[] = " a.pro_small_desc$lang_suffix like '%$keyword%' or a.pro_full_desc$lang_suffix like '%$keyword%'";
			 				}
			 				
			 				if($search_agent == 1){
			 					$query1[] = " c.name like '%$keyword%'";
			 				}
			 				
			 				$query1a = implode(" or ",$query1);
			 				$query .= $query1a." )";
			 				
			 				switch ($orderby){
			 					case "pro_name":
			 						$query .= " order by a.pro_name ".$ordertype;
			 					break;
			 					case "price":
			 						$query .= " order by a.price ".$ordertype;
			 					break;
			 					case "listdate":
			 						$query .= " order by a.created ".$ordertype;
			 					break;
			 				}
			 				
			 				$db->setQuery($query);
			 				$rows = $db->loadObjectList();
			 				
			 				if(count($rows) > 0)
							{
			 					//echo $db->getQuery();
				  	  	  	   //add categories into array
				  	  	  	   for($j=0;$j<count($rows);$j++)
							   {
				  	  	  	   		$tmp = new stdClass();
				  	  	  	   		$row = $rows[$j];
                                    $needs = [];
                                    $needs[] = "property_details";
                                    $needs[] = $row->id;
                                    $itemid = OSPRoute::getItemid($needs);
				  	  	  	   		$pro_name = OSPHelper::getLanguageFieldValue($row,'pro_name');
				  	  	  	   		$row->pro_name = $pro_name;
				  	  	  	   		
				  	  	  	   		$db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id'");
				  	  	  	   		$image = $db->loadResult();
				  	  	  	   		if($image != "")
									{
				  	  	  	   			//having thumbnail image
				  	  	  	   			if(file_exists(JPATH_ROOT."/images/osproperty/properties/".$row->id."/thumb/".$image)){
				  	  	  	   				$tmp->pimage = "<img src='".Uri::root()."images/osproperty/properties/".$row->id."/thumb/".$image."' height='60' width='60' border='0' />"; 
				  	  	  	   			}else{
				  	  	  	   				$tmp->pimage = "<img src='".Uri::root()."media/com_osproperty/assets/images/noimage64.png' height='60' width='60' border='0' />"; 
				  	  	  	   			}
				  	  	  	   		}
									else
									{
				  	  	  	   			$tmp->pimage = "<img src='".Uri::root()."media/com_osproperty/assets/images/noimage64.png' height='60' width='60' border='0' />"; 
				  	  	  	   		}
				  	  	  	   		$title = "";
				  	  	  	   		if($rows[$j]->ref  != "" && $configClass['show_ref'] == 1)
									{
				  	  	  	   			$title .= $rows[$j]->ref.", ";
				  	  	  	   		}
				  	  	  	   		$title .= $rows[$j]->pro_name;
				  	  	  	   		$tmp->title = $title;
				  	  	  	   		//text here
				  	  	  	   		$text = "";
				  	  	  	   		
				  	  	  	   		if($show_cost == 1){
				  	  	  	   			$text .= "(".Text::_('OS_COST')." : ";
				  	  	  	   			if($row->price_call == 1){
				  	  	  	   				$text.= Text::_('OS_CALL_FOR_PRICE');
				  	  	  	   			}else{
				  	  	  	   				$text.= HelperOspropertyCommon::loadCurrency($row->curr)." ".OSPHelper::showPrice($row->price);
				  	  	  	   			}
				  	  	  	   			$text .= ")  ";
				  	  	  	   		}
				  	  	  	   		if($show_address == 1){
				  	  	  	   			$text .= OSPHelper::getLanguageFieldValue($row,'address').", ".HelperOspropertyCommon::loadCityName($row->city).", ";
				  	  	  	   			//$text .= "<BR />";
				  	  	  	   			$text .= HelperOspropertyCommon::loadStateName($row->state).", ".$row->country_name;
				  	  	  	   		}
				  	  	  	   		if ($show_introtext == 1){
				                        $pro_small_desc = strip_tags(stripslashes(OSPHelper::getLanguageFieldValue($row,'pro_small_desc')));
				                        $text .= $pro_small_desc;
				                    }
				                    if ($show_agent == 1){
				                    	$text .= ". ".Text::_('OS_AGENT').": ".strip_tags(stripslashes($row->agent_name));
				                    }
				  	  	  	   		$tmp->text  = $text;
				  	  	  	   		$tmp->href  = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id&Itemid=".$itemid);

									$results[$type_name][$j] = $tmp;
				  	  	  	   }
				  	  	    }
			 			}
			 		}
			 	break;
			 	case "country": //category
			 		$db->setQuery("Select * from #__osrs_countries");
			 		$categories = $db->loadObjectList();
			 		if(count($categories) > 0){
			 			for($i=0;$i<count($categories);$i++){
			 				$category = $categories[$i];
			 				$query = "Select a.*,c.name as agent_name,d.country_name,e.state_name from #__osrs_properties as a"
			 						." inner join #__osrs_agents as c on c.id = a.agent_id"
			 						." inner join #__osrs_countries as d on d.id = a.country"
			 						." inner join #__osrs_states as e on e.id = a.state"
									." inner join #__osrs_cities as g on g.id = a.city"
			 						." where a.approved = '1' and a.published = '1' and a.country = '$category->id' and (";
			 				//
			 				$query1 = [];
			 				if($search_name == 1){
			 					$query1[] = " a.pro_name$lang_suffix like '%$keyword%'";
			 					$query1[] = " a.ref like '%$keyword%'";
			 				}
			 				if($search_address == 1){
			 					$query1[] = " a.address like '%$keyword%' or a.region like '%$keyword%' or a.postcode like '%$keyword%' or d.country_name like '%$keyword%' or e.state_name like '%$keyword%' or g.city like '%$keyword%'";
			 				}
			 				
			 				if($search_desc == 1){
			 					$query1[] = " a.pro_small_desc$lang_suffix like '%$keyword%' or a.pro_full_desc$lang_suffix like '%$keyword%'";
			 				}
			 				
			 				if($search_agent == 1){
			 					$query1[] = " c.name like '%$keyword%'";
			 				}
			 				
			 				$query1a = implode(" or ",$query1);
			 				$query .= $query1a." )";
			 				
			 				switch ($orderby){
			 					case "pro_name":
			 						$query .= " order by a.pro_name ".$ordertype;
			 					break;
			 					case "price":
			 						$query .= " order by a.price ".$ordertype;
			 					break;
			 					case "listdate":
			 						$query .= " order by a.created ".$ordertype;
			 					break;
			 				}
			 				
			 				$db->setQuery($query);
			 				
			 				$rows = $db->loadObjectList();
			 				
			 				if(count($rows) > 0){
			 					//echo $db->getQuery();
				  	  	  	   //add categories into array
				  	  	  	   for($j=0;$j<count($rows);$j++){
				  	  	  	   		
				  	  	  	   		$row = $rows[$j];
                                    $needs = [];
                                    $needs[] = "property_details";
                                    $needs[] = $row->id;
                                    $itemid = OSPRoute::getItemid($needs);
				  	  	  	   		$pro_name = OSPHelper::getLanguageFieldValue($row,'pro_name');
				  	  	  	   		$row->pro_name = $pro_name;
				  	  	  	   		
				  	  	  	   		$db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id'");
				  	  	  	   		$image = $db->loadResult();
				  	  	  	   		if($image != ""){
				  	  	  	   			//having thumbnail image
				  	  	  	   			if(file_exists(JPATH_ROOT."/images/osproperty/properties/".$row->id."/thumb/".$image)){
				  	  	  	   				$results[$category->country_name][$j]->pimage = "<img src='".Uri::root()."images/osproperty/properties/".$row->id."/thumb/".$image."' height='60' width='60' border='0'>"; 
				  	  	  	   			}else{
				  	  	  	   				$results[$category->country_name][$j]->pimage = "<img src='".Uri::root()."media/com_osproperty/assets/images/noimage64.png' height='60' width='60' border='0'>"; 
				  	  	  	   			}
				  	  	  	   		}else{
				  	  	  	   			$results[$category->country_name][$j]->pimage = "<img src='".Uri::root()."media/com_osproperty/assets/images/noimage64.png' height='60' width='60' border='0'>"; 
				  	  	  	   		}
				  	  	  	   		$title = "";
				  	  	  	   		if(($rows[$j]->ref  != "") and ($configClass['show_ref'] == 1)){
				  	  	  	   			$title .= $rows[$j]->ref.", ";
				  	  	  	   		}
				  	  	  	   		$title .= $rows[$j]->pro_name;
				  	  	  	   		$results[$category->country_name][$j]->title = $title;
				  	  	  	   		//text here
				  	  	  	   		$text = "";
				  	  	  	   		
				  	  	  	   		if($show_cost == 1){
				  	  	  	   			$text .= "(".Text::_('OS_COST')." : ";
				  	  	  	   			if($row->price_call == 1){
				  	  	  	   				$text.= Text::_('OS_CALL_FOR_PRICE');
				  	  	  	   			}else{
				  	  	  	   				$text.= OSPHelper::showPrice($row->price)." ".$configClass['general_currency_default'];
				  	  	  	   			}
				  	  	  	   			$text .= ")  ";
				  	  	  	   		}
				  	  	  	   		if($show_address == 1){
				  	  	  	   			$text .= OSPHelper::getLanguageFieldValue($row,'address').", ".HelperOspropertyCommon::loadCityName($row->city).", ";
				  	  	  	   			$text .= HelperOspropertyCommon::loadStateName($row->state).", ".$row->country_name;
				  	  	  	   		}
				  	  	  	   		if ($show_introtext == 1){
				                        $pro_small_desc = strip_tags(stripslashes(OSPHelper::getLanguageFieldValue($row,'pro_small_desc')));
				                        $text .= $pro_small_desc;
				                    }
				                    if ($show_agent == 1){
				                    	$text .= ". ".Text::_('OS_AGENT').": ".strip_tags(stripslashes($row->agent_name));
				                    }
				  	  	  	   		$results[$category->country_name][$j]->text  = $text;
				  	  	  	   		$results[$category->country_name][$j]->href  = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id&Itemid=".$itemid);
				  	  	  	   }
				  	  	    }
			 			}
			 		}
			 	break;
			 }
		  	 print_r(json_encode($results));
			 exit(0);
		}
	}
	
	static function resetData(){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.folder');
		
		$db->setQuery("Select runtime from #__osrs_resetdemo");
		$runtime = $db->loadResult();
		$runtime = intval($runtime);
		
		if($runtime < time() - 4*3600){
			if(File::exists(JPATH_ROOT."/sample.sql")){
				$sql = File::read(JPATH_ROOT."/sample.sql") ;
				$queries = $db->splitSql($sql);
				if (count($queries)) {
					foreach ($queries as $query) {
					$query = trim($query);
					if ($query != '') {
							$db->setQuery($query);
							$db->execute();						
						}	
					}
				}
			}
			
			if(File::exists(JPATH_ROOT."/osproperty.zip")){
				Folder::delete(JPATH_ROOT."/images/osproperty");
				JArchive::extract(JPATH_ROOT."/osproperty.zip",JPATH_ROOT."/images");
			}
			$db->setQuery("Update #__osrs_resetdemo set runtime = '".time()."'");
			$db->execute();
		}
	}
	
	/**
	 * Delete properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */                      
	static function deleteProperties($option,$cid,$type=0){
		global $bootstrapHelper, $mainframe;
		$db = Factory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		if(count($cid) > 0){
			//only check if type = 1
			if($type == 0){
				foreach ($cid as $id){
					if(!HelperOspropertyCommon::isOwner($id)){
						throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
					}
				}
			}
			OSPHelper::removeProperties($cid);
		}
		if($type == 0){
			$needs = [];
			$needs[] = "agent_editprofile";
			$needs[] = "agent_default";
			$needs[] = "aeditdetails";
			$itemid = OSPRoute::getItemid($needs);
			OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=agent_editprofile&Itemid=".$itemid),Text::_('OS_PROPERTIES_HAVE_BEEN_REMOVED'));
		}else{
			$needs = [];
			$needs[] = "lmanageproperties";
			$needs[] = "property_manageallproperties";
			$itemid  = OSPRoute::getItemid($needs);
			OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=property_manageallproperties&Itemid='.$itemid),Text::_('OS_PROPERTY_STATUS_HAS_BEEN_CHANGED'));
		}
	}
	
	static function exportRSS(){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		jimport( 'joomla.document.feed.feed' );
		$db = Factory::getDbo();
		$category_id = $jinput->getInt('category_id',0);
		$db->setQuery("Select * from #__osrs_categories where id = '$category_id'");
		$category = $db->loadObject();
		$query = "Select a.*,c.name as agent_name,d.id as typeid,d.type_name from #__osrs_properties as a"
				//." LEFT JOIN #__osrs_categories as b on b.id = a.category_id"
				." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." WHERE 1=1";
		//$query .= " and a.`access` = '0' ";
		if($category_id > 0){
			$categoryArr = [];
			$categoryArr = HelperOspropertyCommon::getSubCategories($category_id,$categoryArr);
			$catids      = implode(",",$categoryArr);
			$query .= " AND a.id in(Select pid from #__osrs_property_categories where category_id in ($catids))";
		}
		$query .= " and a.published = '1' and a.approved = '1'";
		$query .= " order by a.id desc";
		$db->setQuery($query);
		//echo $db->getQuery();
		//die();
		$rows = $db->loadObjectList();
		$doc		= Factory::getDocument();
		$doc->setLink(Route::_('index.php?option=com_osproperty&task=category_details&id='.$category_id));
		$doc->setTitle($category->category_name);
		$doc->setDescription(strip_tags($category->category_description));
		$doc->setGenerator($configClass['general_bussiness_name']);
		for($i=0;$i<count($rows);$i++)
		{
			$row = $rows[$i];
			$db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id' order by ordering");
			$image = $db->loadResult();
			
			$title = $row->pro_name;
			$title = html_entity_decode($title, ENT_COMPAT, 'UTF-8');
			if(($row->ref != "") and ($configClass['show_ref'] == 1))	
			{
				$title = $row->ref.", ".$title;
			}
			
			$itemid = OSPRoute::getPropertyItemid($row->id);
			$link = Route::_('index.php?option=com_osproperty&task=property_details&id='.$row->id.'&Itemid='.$itemid);			
			// feed item description text
			@$created = ($row->created ? date('r', strtotime($row->created)) : '');
			// load individual item creator class
			$item = new FeedItem();
			$item->title 		= $title;
			$item->link 		= $link;
			$item->author		= $row->agent_name;
			ob_start();
			?>
			<div class="width100pc">
				<?php
				if($image != "")
				{
					if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/thumb/'.$image))
					{
						?>
						<div class="floatleft">
							<img src="<?php echo Uri::root().'images/osproperty/properties/'.$row->id.'/thumb/'.$image?>" />
						</div>
						<?php
					}
				}
				echo $row->pro_small_desc;
				?>
			</div>
			<?php
			$desc = ob_get_contents();
			ob_end_clean();
			$item->description 	= $desc;
			$item->date			= @$created;
			$item->category		= OSPHelper::getCategoryNamesOfProperty($row->id);//$row->category_name;						
			$doc->addItem( $item );
		}
	}
	
	/**
	 * Report form
	 *
	 */
	static function reportForm($id){
        global $bootstrapHelper, $jinput;
		$session = Factory::getSession();
        $session->set('item',$id);
		$item_type = $jinput->getInt('item_type',0);
		$reportArr = [];
		if($item_type == 0){
			$reportArr[] = HTMLHelper::_('select.option','',Text::_('OS_PLEASE_SELECT'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INVALID_DATA',Text::_('OS_INVALID_DATA'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INCORRECT_AGENT_CLAIM',Text::_('OS_INCORRECT_AGENT_CLAIM'));
			$reportArr[] = HTMLHelper::_('select.option','OS_PROPERTY_SOLD',Text::_('OS_PROPERTY_SOLD'));
			$reportArr[] = HTMLHelper::_('select.option','OS_PROPERTY_NOT_FOR_SALE',Text::_('OS_PROPERTY_NOT_FOR_SALE'));
			$reportArr[] = HTMLHelper::_('select.option','OS_PRICE_CHANGE',Text::_('OS_PRICE_CHANGE'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INCORRECT_PHOTO',Text::_('OS_INCORRECT_PHOTO'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INCORRECT_MAP_LOCATION',Text::_('OS_INCORRECT_MAP_LOCATION'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INCORRECT_SOLD_PRICE',Text::_('OS_INCORRECT_SOLD_PRICE'));
			$reportArr[] = HTMLHelper::_('select.option','OS_OFFENSIVE_CONTENT',Text::_('OS_OFFENSIVE_CONTENT'));
			$reportArr[] = HTMLHelper::_('select.option','OS_IRRELEVANT_CONTENT',Text::_('OS_IRRELEVANT_CONTENT'));
			$reportArr[] = HTMLHelper::_('select.option','OS_SPAM',Text::_('OS_SPAM'));
			$reportArr[] = HTMLHelper::_('select.option','OS_OTHER',Text::_('OS_OTHER'));
			
		}else{
			$reportArr[] = HTMLHelper::_('select.option','',Text::_('OS_PLEASE_SELECT'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INVALID_DATA',Text::_('OS_INVALID_DATA'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INCORRECT_AGENT_CLAIM',Text::_('OS_INCORRECT_AGENT_CLAIM'));
			$reportArr[] = HTMLHelper::_('select.option','OS_INCORRECT_PHOTO',Text::_('OS_INCORRECT_PHOTO'));
			$reportArr[] = HTMLHelper::_('select.option','OS_OFFENSIVE_CONTENT',Text::_('OS_OFFENSIVE_CONTENT'));
			$reportArr[] = HTMLHelper::_('select.option','OS_IRRELEVANT_CONTENT',Text::_('OS_IRRELEVANT_CONTENT'));
			$reportArr[] = HTMLHelper::_('select.option','OS_SPAM',Text::_('OS_SPAM'));
			$reportArr[] = HTMLHelper::_('select.option','OS_OTHER',Text::_('OS_OTHER'));
		}
		$lists['report_reason'] = HTMLHelper::_('select.genericlist',$reportArr,'report_reason','class="input-large form-select"','value','text');
		HTML_OspropertyListing::reportForm($lists,$id,$item_type);
	}

	/**
	 * Send report
	 *
	 * @param unknown_type $id
	 */
	static function doreportproperty($id){
		global $bootstrapHelper, $mainframe,$configClass,$jinput;
		$db = Factory::getDbo();
		if($configClass['enable_report']==0){
			throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
		}

        $security_code = $jinput->getString('security_code','');
        $captcha_str   = $jinput->getString('property_captcha_str','');
        if($security_code != $captcha_str){
            ?>
            <script type="text/javascript">
                window.history.go(-1);
            </script>
            <?php
        }

        $session = Factory::getSession();
        $item = $session->get('item',0);
        if(($item != $id) or ($item == 0)){
            ?>
            <script type="text/javascript">
                window.history.go(-1);
            </script>
            <?php
        }

		$report_reason = OSPHelper::getStringRequest('report_reason','');
		$report_details = $jinput->getString('report_details','');
		$your_email = OSPHelper::getStringRequest('your_email','','');
		$item_type = $jinput->getInt('item_type',0);
		$itemid = $jinput->getInt('Itemid',0);
		if($report_reason == "")
		{
			OspropertyListing::reportForm($id);
		}
		else
		{
			$row = Table::getInstance('Report','OspropertyTable');
			$row->id = 0;
			$row->item_type = $item_type;
			$row->report_ip = OSPHelper::get_ip_address();
			$row->report_reason = $report_reason;
			$row->report_details = $report_details;
			$row->report_email = $your_email;
			$row->item_id = $id;
			if($item_type == 0){
				$row->frontend_url = Uri::root().'index.php?option=com_osproperty&task=property_details&id='.$id.'&Itemid='.$itemid;
				$row->backend_url = Uri::root()."administrator/index.php?option=com_osproperty&task=properties_edit&cid[]=".$id;
			}elseif($item_type == 1){
				$row->frontend_url = Uri::root().'index.php?option=com_osproperty&task=agent_info&id='.$id.'&Itemid='.$itemid;
				$row->backend_url = Uri::root()."administrator/index.php?option=com_osproperty&task=agent_edit&cid[]=".$id;
			}elseif($item_type == 2){
				$row->frontend_url = Uri::root().'index.php?option=com_osproperty&task=company_edit&id='.$id.'&Itemid='.$itemid;
				$row->backend_url = Uri::root()."administrator/index.php?option=com_osproperty&task=companies_edit&cid[]=".$id;
			}
			$row->report_on = time();
			
			jimport('joomla.filesystem.file');
			if($row->store()){
				if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/reportcomplete.php')){
					$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
				}else{
					$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
				}
				$tpl->set('mainframe',$mainframe);
				$tpl->set('bootstrapHelper',$bootstrapHelper);
				$body = $tpl->fetch("reportcomplete.php");
			}else{
				if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/reportfailure.php')){
					$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
				}else{
					$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
				}
				$tpl->set('mainframe',$mainframe);
				$tpl->set('bootstrapHelper',$bootstrapHelper);
				$body = $tpl->fetch("reportfailure.php");
			}
			echo $body;
		}
	}
	
	/**
	 * This function is used to unfeatured property by agent
	 *
	 * @param unknown_type $id
	 */
	static function unfeaturedproperty($id){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDbo();
		if((!HelperOspropertyCommon::isAgent()) or ($id == 0)){
			throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
		}
		if($id > 0){
			if(!HelperOspropertyCommon::isOwner($id)){
				throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
			}
		}
		$db->setQuery("Update #__osrs_properties set isFeatured = '0' where id = '$id'");
		$db->execute();
		OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=agent_editprofile&Itemid='.$jinput->getInt('Itemid',0)),Text::_('OS_FEATURED').' '.Text::_('OS_STATUS_CHANGED'));
	}
	
	/**
	 * Get Json
	 *
	 */
	static function getjson(){
        global $bootstrapHelper, $jinput;
        $db = Factory::getDbo();
        $lang_suffix = OSPHelper::getFieldSuffix();
        $configClass = OSPHelper::loadConfig();
        $mainframe = Factory::getApplication();
        $db = Factory::getDbo();
        $map_layout = $jinput->getInt('map_layout','');
        $map_type = $jinput->getInt('map_type',0);
        if($map_type > 0){
            $typeSql = " and a.pro_type = '$map_type'";
        }
        $map_category = $jinput->getInt('map_category',0);
        if($map_category > 0){
            $catSql = " and a.id in (Select pid from #__osrs_property_categories where category_id = '$map_category')";
        }
        $map_country = $jinput->getInt('map_country',0);
        if($map_country > 0){
            $countrySql = " and a.country = '$map_country'";
        }
        $map_state = $jinput->getInt('map_state',0);
        if($map_state > 0){
            $stateSql = " and a.state = '$map_state'";
        }
        $map_city = $jinput->getInt('map_city',0);
        if($map_city > 0){
            $citySql = " and a.city = '$map_city'";
        }
        $priceSql = "";
        if($configClass['price_filter_type'] == 1){
            $price_from = $jinput->getFloat('price_from',0);
            if($price_from > 0){
                $priceSql .= " AND a.price >= '$price_from'";
            }
            $price_to = $jinput->getFloat('price_to',0);
            if($price_to > 0){
                $priceSql .= " AND a.price <= '$price_to'";
            }
        }else{
            $price = $jinput->getInt('price',0);
            if($price > 0){
                $db->setQuery("Select * from #__osrs_pricegroups where id = '$price'");
                $pricegroup = $db->loadObject();
                $price_from = $pricegroup->price_from;
                $price_to	= $pricegroup->price_to;
                if($price_from  > 0){
                    $priceSql .= " AND (a.price >= '$price_from')";
                }
                if($price_to > 0){
                    $priceSql .= " AND (a.price <= '$price_to')";
                }
            }
        }
        $count = $jinput->getInt('max_items',50);
		$access_sql = ' and a.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')';
        $query  = "Select a.*,b.type_name$lang_suffix as type_name,b.type_icon from #__osrs_properties as a"
            ." inner join #__osrs_types as b on b.id = a.pro_type"
            ." where a.published = '1' and a.approved = '1' and show_address = '1' and lat_add <> '' and long_add <> '' "
			.$access_sql
            .$typeSql
            .$catSql
            .$countrySql
            .$stateSql
            .$citySql
            .$priceSql
            ." order by id desc limit ".$count;
        ;
        $db->setQuery($query);
        //echo $db->getQuery();
        $properties = $db->loadObjectList();
        if(count($properties) > 0){
            foreach ($properties as $property){
                $needs = [];
                $needs[] = "property_details";
                $needs[] = $property->id;
                $itemid  = OSPRoute::getItemid($needs);
                $property->url = Route::_('index.php?option=com_osproperty&task=property_details&id='.$property->id.'&Itemid='.$itemid);

                $db->setQuery("Select image from #__osrs_photos where pro_id = '$property->id' order by ordering");
                $image = $db->loadResult();
                if($image != ""){
                    $property->photo = Uri::root()."images/osproperty/properties/".$property->id."/medium/".$image;
                }else{
                    $property->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
                }
            }

            $min = 0.9999997;
            $max = 1.00000000;
            $start_point = 1;
            for($i=0; $i<count($properties)-1; $i++)
			{
                $obj1 = $properties[$i];
                for($j = 1;$j<count($properties);$j++)
				{
                    $obj2 = $properties[$j];
                    if($obj1->lat_add == $obj2->lat_add && $obj1->long_add == $obj2->long_add && $properties[$j]->id != $properties[$i]->id)
					{
		  				$properties[$i]->lat_add  =  $obj2->lat_add * ($start_point * ($max - $min) + $min);
		  				$properties[$i]->long_add =  $obj2->long_add * ($start_point * ($max - $min) + $min);
		  				$start_point++;
		  			}
                }
            }
        }

        while (@ob_end_clean());
        $tempArr = [];
        $i = 0;
        if(count($properties) > 0)
		{
            foreach ($properties as $property)
			{
				$pro_name = $property->pro_name;
				$pro_name = str_replace("'","",$pro_name);
				$pro_name = str_replace("\"","",$pro_name);
				$tmp	  = new stdClass();

                $tmp->url 			= $property->url;
                $tmp->latitude 		= $property->lat_add;
                $tmp->longitude 	= $property->long_add;
                $tmp->title 		= $pro_name;
				$pro_name = OSPHelper::getLanguageFieldValue($property,'pro_name');
				$pro_name = str_replace("'","",$pro_name);
				$pro_name = str_replace("\"","",$pro_name);
                $tmp->pro_name		= $pro_name;//$property->pro_name;
                if($property->price_call == 1)
				{
                    $tmp->price		= Text::_("OS_CALL_FOR_PRICE");
                }
				else
				{
                    $tmp->price 	= OSPHelper::generatePrice($property->curr,$property->price);
                }
                $tmp->curr 			= $property->curr;
                $tmp->price_call	= $property->price_call;
                $tmp->photo			= $property->photo;
                $tmp->type_name		= $property->type_name;
                $type_icon							= $property->type_icon;
                if($type_icon == "")
				{
                    $type_icon = "1.png";
                }
                $tmp->bath_room		= OSPHelper::showBath($property->bath_room);
                $tmp->bed_room	= $property->bed_room;
                if($property->square_feet > 0)
				{
                    $tmp->square_feet= $property->square_feet."&nbsp;".OSPHelper::showSquareSymbol();
                }
                switch ($map_layout)
				{
                    case "2":
                        if(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/2/'.$type_icon)){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/2/".$type_icon;
                        }elseif(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/2/1.png')){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/2/1.png";
                        }else{
                            $tmp->icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
                        }
                        break;
                    case "3":
                        if(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/3/'.$type_icon)){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/3/".$type_icon;
                        }elseif(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/3/1.png')){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/3/1.png";
                        }else{
                            $tmp->icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
                        }
                        break;
                    case "4":
                        if(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/4/'.$type_icon)){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/4/".$type_icon;
                        }elseif(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/4/1.png')){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/4/1.png";
                        }else{
                            $tmp->icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
                        }
                        break;
                    case "5":
                        if(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/5/'.$type_icon)){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/5/".$type_icon;
                        }elseif(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/5/1.png')){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/5/1.png";
                        }else{
                            $tmp->icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
                        }
                        break;
                    case "6":
                        if(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/6/'.$type_icon)){
                            $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/6/".$type_icon;
                        }elseif(file_exists(JPATH_ROOT.'/modules/mod_osmapsearch/asset/images/6/1.png')){
                           $tmp->icon = Uri::root()."modules/mod_osmapsearch/asset/images/6/1.png";
                        }else{
                            $tmp->icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
                        }
                        break;
                    default:
                        $tmp->icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
                        break;
                }
                //$tempArr['markers'][$i]->icon		= Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;
				$tempArr['markers'][$i] = $tmp;
                $i++;
            }
        }
        print_r(json_encode($tempArr));
        exit();
	}


    /**
     * Edit Comment
     */
    public static function editComment(){
        global $bootstrapHelper, $configClass,$jinput;
        $user = Factory::getUser();
        if($user->id == 0){
            throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
        }else{
            $id = $jinput->getInt('id',0);
            if($id == 0){
                throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
            }else{
                $db = Factory::getDbo();
                $db->setQuery("Select * from #__osrs_comments where id = '$id'");
                $comment = $db->loadObject();
                if($user->id != $comment->user_id){
                    throw new Exception(Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'), 500);
                }else{
                    HTML_OspropertyListing::editCommentForm($comment);
                }
            }
        }
    }

    /**
     * Edit Comment
     */
    public static function submitEditComment()
	{
        global $bootstrapHelper, $configClass,$mainframe,$jinput;
        $id = $jinput->getInt('id',0);
        $db = Factory::getDbo();
        $user = Factory::getUser();
        $db->setQuery("Select * from #__osrs_comments where id = '$id'");
        $comment = $db->loadObject();
        $pro_id = $comment->pro_id;
        $needs = [];
        $needs[] = "property_details";
        $needs[] = $pro_id;
        $itemid  = OSPRoute::getItemid($needs);

        $comment_author 	= OSPHelper::getStringRequest('comment_author','','');
        $comment_title  	= OSPHelper::getStringRequest('comment_title','','');
        $comment_message	= $_POST['comment_message'];
        $rating				= $jinput->getString('rating');

        $row = Table::getInstance('Comment','OspropertyTable');
        $post = $jinput->post->getArray();
        $row->bind($post);
        $row->id = $id;
        $row->content = $comment_message;
        $row->title = $comment_title;
        $row->name = $comment_author;
        $row->pro_id = $pro_id;
        $row->rate = $rating;
        $row->store();

        //if the comment is published automatically, update the rating and send email to property's onwer
        if($configClass['comment_auto_approved'] == 1){
            $db->setQuery("Select number_votes,total_points from #__osrs_properties where id = '$pro_id'");
            $rating_details = $db->loadObject();
            $number_votes = $rating_details->number_votes;
            $total_points = $rating_details->total_points;
            $total_points = $total_points + (int)$rating - (int)$comment->rate;
            $db->setQuery("Update #__osrs_properties set total_points='$total_points' where id = '$pro_id'");
            $db->execute();
            $msg = Text::_('OS_COMMENT_HAS_BEEN_UPDATED');
        }
        $link = Uri::root()."index.php?option=com_osproperty&task=property_editcomment&tmpl=component&id=".$id;
        OSPHelper::redirect($link,$msg);
    }

    /**
     * This is function is used to manage all properties at front-end through layout: Manage Properties
     */
	static function manageAllProperties($option){
		global $bootstrapHelper, $configClass,$jinput, $languages;
		//check permission
		$translatable   = Multilanguage::isEnabled() && count($languages);
		$sef			= OSPHelper::getFieldSuffix();
        $db = Factory::getDbo();
        $document = Factory::getDocument();
        $document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_MANAGE_PROPERTIES'));
		OSPHelper::generateHeading(2,Text::_('OS_MANAGE_PROPERTIES'));
		$config         = new JConfig();
		$list_limit     = $config->list_limit;
        $limit			= $jinput->getInt('limit', $list_limit);

        $limitstart     = OSPHelper::getLimitStartPost();

        $category_id    = $jinput->getInt('category_id',0);
        $catIds 	    = [];
        $catIds[]	    = $category_id;
        $type_id        = $jinput->getInt('type_id',0);
        $status         = $jinput->getString('status','');
        $featured       = $jinput->getInt('featured_stt',-1);
        $approved       = $jinput->getInt('approved',-1);

		$country_id		= $jinput->getInt('country_id',HelperOspropertyCommon::getDefaultCountry());
		$city_id		= $jinput->getInt('city_id',0);
		$state_id		= $jinput->getInt('state_id',0);

        $keyword = OSPHelper::getStringRequest('filter_search','','post');
        $orderby = OSPHelper::getStringRequest('orderby','desc','post');
        $sortby = OSPHelper::getStringRequest('sortby','a.id','post');
		if($sortby == "")
		{
			$sortby = "a.id";
		}

        $query = "Select count(a.id) from #__osrs_properties as a"
            ." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
            ." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
            ." INNER JOIN #__osrs_countries as e on e.id = a.country"
            ." LEFT JOIN #__osrs_states as s on s.id = a.state"
            ." LEFT JOIN #__osrs_cities as c on c.id = a.city"
            ." LEFT join #__osrs_expired as ex on ex.pid = a.id"
            ." WHERE 1=1";
        if($keyword != "")
		{
            $query .= " AND (a.pro_name LIKE '%$keyword%'";
            $query .= " OR a.ref like '%$keyword%'";
            $query .= " OR g.name like '%$keyword%'";
            $query .= " OR d.type_name like '%$keyword%'";
            $query .= " OR s.state_name like '%$keyword%'";
            $query .= " OR c.city like '%$keyword%'";
            $query .= " OR g.name like '%$keyword%'";
            $query .= ")";
        }
        if($category_id > 0)
		{
            $query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id = '$category_id')";
        }
        if($type_id > 0)
		{
            $query .= " AND a.pro_type = '$type_id'";
        }
        if($status != "")
		{
            $query .= " AND a.published = '$status'";
        }
        if($featured > -1)
		{
            $query .= " AND a.isFeatured = '$featured'";
        }
        if($approved > -1){
            $query .= " AND a.approved = '$approved'";
        }
		if((int)$country_id > 0)
		{
			$query .= " AND a.country = '$country_id'";
		}
		if($state_id > 0)
		{
            $show_form = 1;
			$query .= " AND a.state = '$state_id'";
		}
		if($city_id > 0)
		{
		    $show_form = 1;
		    $query .= " AND a.city = '$city_id'";
        }
        $db->setQuery($query);
        $count = $db->loadResult();

        $pageNav = new OSPJPagination($count,$limitstart,$limit);

		if($translatable)
		{
			$query = "Select a.id, a.ref, a.pro_name".$sef.",a.posted_by,a.company_id, d.id as typeid,d.type_name".$sef." as type_name,g.name as agent_name,a.published,a.approved, a.isFeatured,a.curr,a.price,a.price_call,a.rent_time,a.show_address,a.hits,c.city".$sef." as city,s.state_name".$sef." as state_name,a.address, ex.expired_time,ex.expired_feature_time,a.total_request_info,a.total_points,a.number_votes,a.agent_id from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
				." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_states as s on s.id = a.state"
				." LEFT JOIN #__osrs_cities as c on c.id = a.city"
				." LEFT JOIN #__osrs_expired as ex on ex.pid = a.id"
				." WHERE 1=1";
		}
		else
		{
			$query = "Select a.id, a.ref, a.pro_name,a.posted_by,a.company_id, d.id as typeid,d.type_name as type_name,g.name as agent_name,a.published,a.approved, a.isFeatured,a.curr,a.price,a.price_call,a.rent_time,a.show_address,a.hits,c.city,s.state_name as state_name,a.address, ex.expired_time,ex.expired_feature_time,a.total_request_info,a.total_points,a.number_votes,a.agent_id from #__osrs_properties as a"
				." INNER JOIN #__osrs_agents as g on g.id = a.agent_id"
				." LEFT  JOIN #__osrs_types as d on d.id = a.pro_type"
				." INNER JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_states as s on s.id = a.state"
				." LEFT JOIN #__osrs_cities as c on c.id = a.city"
				." LEFT JOIN #__osrs_expired as ex on ex.pid = a.id"
				." WHERE 1=1";
		}
        if($keyword != ""){
            $query .= " AND (a.pro_name LIKE '%$keyword%'";
            $query .= " OR a.ref like '%$keyword%'";
            $query .= " OR g.name like '%$keyword%'";
            $query .= " OR d.type_name like '%$keyword%'";
            $query .= " OR s.state_name like '%$keyword%'";
            $query .= " OR c.city like '%$keyword%'";
			$query .= " OR g.name like '%$keyword%'";
            $query .= ")";
        }
        if($category_id > 0){
            $query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id = '$category_id')";
        }
        if($type_id > 0){
            $query .= " AND a.pro_type = '$type_id'";
        }
        if($status != ""){
            $query .= " AND a.published = '$status'";
        }
        if($featured > -1){
            $query .= " AND a.isFeatured = '$featured'";
        }
        if($approved > -1){
            $query .= " AND a.approved = '$approved'";
        }
		if((int)$country_id > 0)
		{
			$query .= " AND a.country = '$country_id'";
		}
		if($state_id > 0)
		{
            $show_form = 1;
			$query .= " AND a.state = '$state_id'";
		}
		if($city_id > 0)
		{
		    $show_form = 1;
		    $query .= " AND a.city = '$city_id'";
        }
        $query .= " ORDER BY $sortby $orderby";

        $db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
        $rows = $db->loadObjectList();
        if(count($rows) > 0){
            for($i=0;$i<count($rows);$i++){
                $row = $rows[$i];
                $db->setQuery("select count(id) from #__osrs_photos where pro_id = '$row->id'");
                $count = $db->loadResult();
                if($count > 0){
                    $row->count_photo = $count;
                    $db->setQuery("select image from #__osrs_photos where pro_id = '$row->id' order by ordering");
                    $photo = $db->loadResult();
                    if($photo != ""){
                        if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$row->id.'/thumb/'.$photo)){
                            $row->photo = Uri::root()."images/osproperty/properties/".$row->id."/thumb/".$photo;
                        }else{
                            $row->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
                        }
                    }else{
                        $row->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
                    }
                }else{
                    $row->count_photo = 0;
                    $row->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
                }//end photo
            }
        }

        $orderbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_ORDERBY'));
        $orderbyArr[] = HTMLHelper::_('select.option','asc',Text::_('OS_ASC'));
        $orderbyArr[] = HTMLHelper::_('select.option','desc',Text::_('OS_DESC'));
        $lists['orderby'] = HTMLHelper::_('select.genericlist',$orderbyArr,'orderby','class="input-medium form-select" onchange="javascript:document.ftForm.submit();"','value','text',$orderby);

        $sortbyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SORTBY'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.ref',Text::_('Ref #'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.pro_name',Text::_('OS_TITLE'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.address',Text::_('OS_ADDRESS'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.state',Text::_('OS_STATE'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.city',Text::_('OS_CITY'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.published',Text::_('OS_PUBLISHED'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.isFeatured',Text::_('OS_FEATURED'));
        $sortbyArr[] = HTMLHelper::_('select.option','a.id',Text::_('ID'));
		$sortbyArr[] = HTMLHelper::_('select.option','a.price',Text::_('OS_PRICE'));
        $lists['sortby'] = HTMLHelper::_('select.genericlist',$sortbyArr,'sortby','class="input-medium form-select" onchange="javascript:document.ftForm.submit();"','value','text',$sortby);

        $lists['category'] = OSPHelper::listCategories($category_id,'onChange="this.form.submit();"');

        //property types
        $typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_PROPERTY_TYPES'));
		if($translatable)
		{
			$db->setQuery("SELECT id as value,type_name".$sef." as text FROM #__osrs_types where published = '1' ORDER BY type_name");
		}
		else
		{
			$db->setQuery("SELECT id as value,type_name as text FROM #__osrs_types where published = '1' ORDER BY type_name");
		}
        $protypes = $db->loadObjectList();
        $typeArr   = array_merge($typeArr,$protypes);
        $lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'type_id','class="input-large form-select" onChange="this.form.submit();"','value','text',$type_id);

        $statusArr = [];
        $statusArr[] = HTMLHelper::_('select.option','',Text::_('OS_ALL_STATUS'));
        $statusArr[] = HTMLHelper::_('select.option',0,Text::_('OS_UNPUBLISHED'));
        $statusArr[] = HTMLHelper::_('select.option',1,Text::_('OS_PUBLISHED'));
        $lists['status'] = HTMLHelper::_('select.genericlist',$statusArr,'status','class="input-medium" onChange="this.form.submit();"','value','text',$status);

        $featuredArr = [];
        $featuredArr[] = HTMLHelper::_('select.option','-1',Text::_('OS_FEATURED_STATUS'));
        $featuredArr[] = HTMLHelper::_('select.option','0',Text::_('OS_NON_FEATURED_PROPERTIES'));
        $featuredArr[] = HTMLHelper::_('select.option','1',Text::_('OS_FEATURED_PROPERTIES'));
        $lists['featured'] = HTMLHelper::_('select.genericlist',$featuredArr,'featured_stt','class="input-medium" onChange="this.form.submit();"','value','text',$featured);

        $approvedArr = [];
        $approvedArr[] = HTMLHelper::_('select.option','-1',Text::_('OS_APPROVAL_STATUS'));
        $approvedArr[] = HTMLHelper::_('select.option','0',Text::_('OS_UNAPPROVED'));
        $approvedArr[] = HTMLHelper::_('select.option','1',Text::_('OS_APPROVED'));
        $lists['approved'] = HTMLHelper::_('select.genericlist',$approvedArr,'approved','class="input-medium" onChange="this.form.submit();"','value','text',$approved);

        $db->setQuery("select id as value, company_name as text from #__osrs_companies where published = '1' order by company_name");
        $companies 	  = $db->loadObjectList();
        $companyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COMPANY'));
        $companyArr   = array_merge($companyArr,$companies);
        $lists['company'] = HTMLHelper::_('select.genericlist',$companyArr,'company_id','class="input-large"','value','text',$agent->company_id);

		//country	
		$lists['country'] = HelperOspropertyCommon::makeCountryList($country_id,'country_id','onChange="javascript:this.form.submit();"',Text::_('OS_ALL_COUNTRIES'),'class="chosen input-medium"');
		//state
		$lists['states'] = HelperOspropertyCommon::makeStateList($country_id,$state_id,'state_id','onChange="javascript:this.form.submit();"',Text::_('OS_ALL_STATES'),'class="chosen input-medium"');

		$lists['cities'] = HelperOspropertyCommon::makeCityList($country_id,$state_id, $city_id, 'city_id','onChange="javascript:this.form.submit();"',Text::_('OS_ALL_CITIES'),'class="chosen input-medium"');


        HTML_OspropertyListing::manageAllProperties($rows,$lists,$pageNav);
	}

    static function changeStatus($option,$id,$type){
        global $bootstrapHelper, $mainframe,$jinput,$configClass;
        $value = $jinput->getInt('value',0);
        $db = Factory::getDbo();
        $db->setQuery("Update #__osrs_properties set $type = '$value' where id = '$id'");
        $db->execute();
		if(($type == "isFeatured") or ($type == "approved")){
			require_once(JPATH_ADMINISTRATOR.'/components/com_osproperty/classes/property.php');
			require_once(JPATH_ADMINISTRATOR.'/components/com_osproperty/classes/email.php');
			OspropertyProperties::updateStatus($option,$type,$value,$id);
		}
		$needs = [];
		$needs[] = "lmanageproperties";
		$needs[] = "property_manageallproperties";
		$itemid  = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=property_manageallproperties&Itemid='.$itemid),Text::_('OS_PROPERTY_STATUS_HAS_BEEN_CHANGED'));
    }

	static function changeStatuses($option,$cid,$type,$value){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
        $db = Factory::getDbo();
        $db->setQuery("Update #__osrs_properties set $type = '$value' where id in (".implode(",",$cid).")");
        $db->execute();
		$needs = [];
		$needs[] = "lmanageproperties";
		$needs[] = "property_manageallproperties";
		$itemid  = OSPRoute::getItemid($needs);
		OSPHelper::redirect(Route::_('index.php?option=com_osproperty&task=property_manageallproperties&Itemid='.$itemid),Text::_('OS_PROPERTY_STATUS_HAS_BEEN_CHANGED'));
	}

	public static function stasInformation(){
		global $bootstrapHelper, $configClass,$mainframe,$jinput,$lang_suffix;
        $db   = Factory::getDbo();
        $user = Factory::getUser();
        $document = Factory::getDocument();
        $id = $jinput->get("id",0);
        $document->addScript(Uri::root()."media/com_osproperty/assets/js/Chart.min.js");
        if($user->id==0){
            OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
        }
        $company=[];
        if($id > 0){
            if((!HelperOspropertyCommon::isOwner($id)) and (!HelperOspropertyCommon::isCompanyOwner($id)) and (!Factory::getUser()->authorise('frontendmanage', 'com_osproperty'))){
                OSPHelper::redirect(Uri::root(),Text::_('OS_YOU_DO_NOT_HAVE_PERMISION_TO_GO_TO_THIS_AREA'));
            }
        }
        if((HelperOspropertyCommon::isCompanyAdmin())){
            $db->setQuery("SELECT id FROM #__osrs_agents WHERE company_id=(SELECT id FROM #__osrs_companies WHERE user_id=$user->id)");
            $company=$db->loadColumn();
        }else{
            $db->setQuery("SELECT id FROM #__osrs_agents WHERE published=1 AND user_id=$user->id");
            $company[]=$user->id;
        }

        $company='('.implode(",",$company).')';
        $query = "SELECT a.*,c.type_name as pro_type, c.type_icon FROM #__osrs_properties as a LEFT JOIN #__osrs_types as c on c.id = a.pro_type LEFT JOIN #__osrs_countries as d on d.id = a.country  WHERE a.id = $id";
        $db->setQuery($query);

        $row = $db->loadObject();

		$type_icon = $row->type_icon;
		if($type_icon == ""){
			$type_icon = "1.png";
		}
		$row->type_icon = Uri::root()."media/com_osproperty/assets/images/googlemapicons/".$type_icon;

        $query ="SELECT image FROM #__osrs_photos WHERE pro_id=$id AND ordering=1";
        $db->setQuery($query);
        $row->photo=$db->loadObject();

		//saved
		$query = $db->getQuery(true);
		$query->select('count(id)')->from('#__osrs_favorites')->where('pro_id="'.$id.'"');
		$db->setQuery($query);
		$row->saved = (int)$db->loadResult();
		$query->clear();

		$document->setTitle($row->pro_name);

		$Order_by = " ORDER BY pr.hits desc ";
		$sql =   " SELECT pr.* "
				."\n, ci.city AS city_name"
				."\n, st.state_name" 
				."\n, co.country_name"
				."\n, ty.type_name$lang_suffix, ty.id as type_id"
				."\n FROM #__osrs_properties pr"
				."\n LEFT JOIN #__osrs_cities AS ci ON ci.id = pr.city "
				."\n LEFT JOIN #__osrs_states AS st ON st.id = pr.state"
				."\n LEFT JOIN #__osrs_countries AS co ON co.id = pr.country"
				."\n INNER JOIN #__osrs_types AS ty ON ty.id = pr.pro_type"
				."\n WHERE 	pr.approved  = '1' and pr.published = '1'" 
				."\n AND pr.id <> '$id' "
				."\n AND pr.access IN (" . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ")";
		$sql .=	$Order_by;
		$sql .= " LIMIT 10";
		$db->setQuery($sql);

		$relates = $db->loadObjectList();
		if (count($relates)){
			for($i=0;$i<count($relates);$i++){//for
				$relate = $relates[$i];
				$type_name = OSPHelper::getLanguageFieldValue($relate,'type_name');
				$relate->type_name = $type_name;
				$db->setQuery("select image from #__osrs_photos where pro_id = '$relate->id' order by ordering");
				$relate->photo = $db->loadResult();
				if ($relate->photo == ''){
					$relate->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
				}else {
					if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$relate->id.'/thumb/'.$relate->photo)){
						$relate->photo = Uri::root()."images/osproperty/properties/".$relate->id."/thumb/".$relate->photo;	
					}else{
						$relate->photo = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";	
					}
				}

				if($configClass['load_lazy']){
					$relate->photosrc = Uri::root()."media/com_osproperty/assets/images/loader.gif";
				}else{
					$relate->photosrc = $relate->photo;
				}

				$needs = [];
				$needs[] = "property_details";
				$needs[] = $relate->id;
				$item_id = OSPRoute::getItemid($needs);
				$relate->itemid = $item_id;
			}//for			
		}

		jimport('joomla.filesystem.file');
		
		ob_start();
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/relateproperties.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('mainframe',$mainframe);
		$tpl->set('relates',$relates);
		$tpl->set('configClass',$configClass);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		echo $tpl->fetch("relateproperties.php");
		$relate = ob_get_contents();
		ob_end_clean();
		$row->relate_properties = $relate;

        if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/propertystatistic.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
        $tpl->set('row',$row);
		$tpl->set('configClass',$configClass);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
        $body= $tpl->fetch("propertystatistic.php");
		echo $body;
	}

	/**
	 * @param $id
	 * This function is used to show Panorama picture of property
	 */
	static function showPanorama($id)
	{
		global $bootstrapHelper, $mainframe,$bootstrapHelper;
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('pro_name,panorama')->from('#__osrs_properties')->where('id="'.$id.'"');
		$db->setQuery($query);
		$property = $db->loadObject();
		$panorama = $property->panorama;
		if(file_exists(JPATH_ROOT.'/images/osproperty/properties/panorama/'.$panorama)) 
		{
			$rowFluidClass  = $bootstrapHelper->getClassMapping('row-fluid');
			$span12Class	= $bootstrapHelper->getClassMapping('span12');
			$panoramainfo	= getimagesize(JPATH_ROOT.'/images/osproperty/properties/panorama/'.$panorama);
			$width			= $panoramainfo[0];
			$height			= $panoramainfo[1];
			$new_width		= round(400*$width/$height);
			$document		= Factory::getDocument();
			$document->addCustomTag('<meta name="robots" content="nofollow" />');
            $document->setMetaData( "robots", "noindex" );
			$document->addStyleSheet(Uri::root(true).'/media/com_osproperty/assets/css/panorama.css');
			$document->addScript(Uri::root(true).'/media/com_osproperty/assets/js/three.js');
			$document->addScript(Uri::root(true).'/media/com_osproperty/assets/js/photosphere_plugins.js');
			$document->addScript(Uri::root(true).'/media/com_osproperty/assets/js/photo-sphere-viewer.js');
			/*
			?>
			<link href="<?php echo Uri::root(); ?>media/com_osproperty/assets/css/jquery.panorama.css" rel="stylesheet" />
			<script src="<?php echo Uri::root(); ?>media/com_osproperty/assets/js/jquery.panorama.js"></script>
			<img src="<?php echo Uri::root()?>images/osproperty/properties/panorama/<?php echo $panorama?>" class="panorama" height="400" width="<?php echo $new_width;?>"/>
			<?php
			*/
			?>
			<div class="<?php echo $rowFluidClass;?>">
				<div class="<?php echo $span12Class; ?>" style="text-align:center;">
					<div id="panorma_1" class="psvupwraper " style=""></div>
					<div id="panorama_error_msg"></div>
				</div>
			</div>
			<script type="text/javascript">

			var $jQ = jQuery.noConflict();

			$jQ(document).ready(function () {

				if(!window.WebGLRenderingContext) {
					// the browser doesn't even know what WebGL is
					$jQ("#panorama_error_msg").html("Your Browser Does Not Support WebGL");
				}
				else {
					var panorma_1 = new PhotoSphereViewer({
						panorama: '<?php echo Uri::base(true)?>/images/osproperty/properties/panorama/<?php echo $panorama; ?>',
						container: 'panorma_1',
										caption: '<?php echo $property->pro_name;?>',
										loading_img: '<?php echo Uri::base(true)?>/media/com_osproperty/assets/images/loading_large.gif',
						navbar: 'autorotate zoom download caption fullscreen',
						default_fov: 70,
						anim_speed: '1rpm',
						mousewheel: false,
						time_anim: true,                size: {
							height: 350                }
					});
				}
			});
		</script>
<?php
		}
	}

	static function gallery($id){
		global $mainframe,$bootstrapHelper,$jinput,$configClass;
		$db = Factory::getDbo();
		if((int)$id == 0){
			throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 404);
		}

		$db->setQuery("Select pro_name from #__osrs_properties where id = '$id'");
		$pro_name = $db->loadResult();
		$photo_id = $jinput->get('photo_id',0);
		if((int)$configClass['limit_upload_photos'] == 0){
			$limitphoto = "";
		}else{
			$limitphoto = " limit ".$configClass['limit_upload_photos'];
		}
		$db->setQuery("Select * from #__osrs_photos where pro_id = '$id' order by ordering ".$limitphoto);
		$photos = $db->loadObjectList();
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/propertygallery.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('photo_id',$photo_id);
		$tpl->set('id',$id);
		$tpl->set('pro_name',$pro_name);
		$tpl->set('mainframe',$mainframe);
		$tpl->set('photos',$photos);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		echo $tpl->fetch("propertygallery.php");
	}
}
?>
