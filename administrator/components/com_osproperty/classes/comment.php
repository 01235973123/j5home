<?php
/*------------------------------------------------------------------------
# comment.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class OspropertyComment{
	/**
	 * Default static function
	 *
	 * @param unknown_type $option
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get( 'cid', array(),'ARRAY');
		$db = Factory::getDbo();
		$db->setQuery("Update #__osrs_properties set total_points = '0' where total_points < 0");
		$db->execute();
		$db->setQuery("Update #__osrs_properties set number_votes = '0' where number_votes < 0");
		$db->execute();
		switch ($task){
			case "comment_list":
				OspropertyComment::comment_list($option);
			break;
			case "comment_unpublish":
				OspropertyComment::comment_change_publish($option,$cid,0);	
			break;
			case "comment_publish":
				OspropertyComment::comment_change_publish($option,$cid,1);
			break;
			case "comment_remove":
				OspropertyComment::comment_remove($option,$cid);
			break;
			case "comment_add":
				OspropertyComment::comment_edit($option,0);
			break;
			case "comment_edit":
				OspropertyComment::comment_edit($option,$cid[0]);
			break;
			case 'comment_cancel':
				$mainframe->redirect("index.php?option=$option&task=comment_list");
			break;	
			case "comment_save":
				OspropertyComment::comment_save($option,1);
			break;
			case "comment_new":
				OspropertyComment::comment_save($option,2);
			break;
			case "comment_apply":
				OspropertyComment::comment_save($option,0);
			break;	
		}
	}
	
	/**
	 * comment list
	 *
	 * @param unknown_type $option
	 */
	static function comment_list($option){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$lists = array();
		$condition = '';
		
		$filter_order = $jinput->getString('filter_order','c.created_on');
		$filter_order_Dir = $jinput->getString('filter_order_Dir','desc');
		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);
		$keyword = $jinput->getString('keyword','');
		if($keyword != ""){
			$condition .= " AND (c.name LIKE '%$keyword%' OR c.title LIKE '%$keyword%' OR c.content LIKE '%$keyword%' OR p.pro_name like '%$keyword%')";
		}
		
		$count = "SELECT count(c.id) FROM #__osrs_comments AS c"
				."\n INNER JOIN #__osrs_properties AS p ON p.id = c.pro_id "
				."\n WHERE 1=1 "
				;
		$count .= $condition;
		$db->setQuery($count);
		$total = $db->loadResult();
		jimport('joomla.html.pagination');
		$pageNav = new Pagination($total,$limitstart,$limit);
		
		$list  = " SELECT c.*, p.pro_name FROM #__osrs_comments AS c "
				."\n INNER JOIN #__osrs_properties AS p ON p.id = c.pro_id "
				."\n WHERE 1=1 "
				;
		$list .= $condition;
		$list .= " ORDER BY $filter_order $filter_order_Dir";
		$db->setQuery($list,$pageNav->limitstart,$pageNav->limit);
		
		$rows = $db->loadObjectList();
		
		HTML_OspropertyComment::comment_list($option,$rows,$pageNav,$lists);
	}
	
	/**
	 * publish or unpublish comment
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	static function comment_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("UPDATE #__osrs_comments SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
			if($state == 1){
				for($i=0;$i<count($cid);$i++){
					$id = $cid[$i];
					$db->setQuery("Select * from #__osrs_comments where id = '$id'");
					$comment = $db->loadObject();
					$alreadyPublished = $comment->alreadyPublished;
					
					//update rate into #__osrs_properties table
					$db->setQuery("Select pro_id from #__osrs_comments where id = '$id'");
					$pro_id = $db->loadResult();
					
					self::updateComment($pro_id);
					/*
					$db->setQuery("Select number_votes,total_points from #__osrs_properties where id = '$pro_id'");
					$rating_details = $db->loadObject();
					$number_votes = $rating_details->number_votes;
					$total_points = $rating_details->total_points;
					$number_votes++;
					$total_points += $comment->rate;	
					
					$db->setQuery("Update #__osrs_properties set number_votes = '$number_votes',total_points='$total_points' where id = '$pro_id'");
					$db->execute();
					*/
					
					if($alreadyPublished == 0){ //the first time publish the comment, send information email
						//require_once(JPATH_ROOT.DS."components".DS."com_osproperty".DS."classes".DS."email.php");
						//send email to property's onwer
						$emailopt['author'] 	= $comment->name;
						$emailopt['message']	= $comment->content;
						$emailopt['title'] 		= $comment->title;
						$emailopt['rate'] 		= $comment->rate."/5";
						
						$query = "SELECT a.name,a.email FROM #__osrs_agents AS a INNER JOIN #__osrs_properties AS b ON b.agent_id = a.id WHERE b.id = '$pro_id'";
						$db->setQuery($query);
						$agent = $db->loadObject();
						$emailopt['agentname'] = $agent->name;
						$emailopt['agentemail'] = $agent->email;
						
						$link = Uri::root()."index.php?option=com_osproperty&task=property_details&id=$pro_id";
						$emailopt['link'] 		= "<a href='$link'>".$link."</a>";
						OspropertyComment::sendCommentEmail($option,$emailopt);
						
						//after send email. update already published
						$db->setQuery("UPDATE #__osrs_comments SET alreadyPublished = '1' WHERE id = '$id'");
						$db->execute();
					}
				}
			}else{
				for($i=0;$i<count($cid);$i++){
					$id = $cid[$i];
					//update rate into #__osrs_properties table
					$db->setQuery("Select pro_id from #__osrs_comments where id = '$id'");
					$pro_id = $db->loadResult();
					
					//$db->setQuery("Select * from #__osrs_comments where id = '$id'");
					//$comment = $db->loadObject();

					self::updateComment($pro_id);
					/*
					$db->setQuery("Select number_votes,total_points from #__osrs_properties WHERE id = '$pro_id'");
					$rating_details = $db->loadObject();
					$number_votes = $rating_details->number_votes;
					$total_points = $rating_details->total_points;
					$number_votes--;
					$total_points -= $comment->rate;	
					
					$db->setQuery("Update #__osrs_properties set number_votes = '$number_votes',total_points='$total_points' WHERE id = '$pro_id'");
					$db->execute();
					*/
				}
			}//end state 0/1
		}
		$mainframe->enqueueMessage(Text::_('Comment(s) have been updated.'));
		$mainframe->redirect("index.php?option=$option&task=comment_list");
	}
	
	
	/**
	 * Send comment email /update email.php from frontend
	 *
	 * @param unknown_type $option
	 * @param unknown_type $emailopt
	 */
	static function sendCommentEmail($option,$emailopt){
		global $jinput, $mainframe;
		
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_configuration");
		$configs = $db->loadObjectList();
		$emailfrom = $configs[3]->fieldvalue;
		$sitename  = $configs[0]->fieldvalue;
		
		$db->setQuery("Select * from #__osrs_emails where id = '5'");
		$email = $db->loadObject();
		$subject = $email->email_title;
		$message = $email->email_content;
		
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
	
	/**
	 * remove comment
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	static function comment_remove($option,$cid){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$properties = array();
			for($i=0;$i<count($cid);$i++){
				$id = $cid[$i];
				$db->setQuery("Select pro_id from #__osrs_comments where id = '$id'");
				$pro_id = $db->loadColumn(0);
				$properties[] = $pro_id[0];
			}
			
			$db->setQuery("DELETE FROM #__osrs_comments WHERE id IN ($cids)");
			$db->execute();

			if(count($properties) > 0){
				foreach($properties as $pid){
					self::updateComment($pid);
				}
			}
		}
		$mainframe->redirect("index.php?option=$option&task=comment_list");
	}
	
	
	/**
	 * comment Detail
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	static function comment_edit($option,$id){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		$row = Table::getInstance('Comment','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
		$lists['published']   = OSPHelper::getBooleanInput('published',$row->published);
		
		$db->setQuery("SELECT `pro_name` FROM #__osrs_properties WHERE `id` = '$row->pro_id'");
		$row->pro_name = $db->loadResult();
		
		$rateOption = array();
		for($i=1;$i<=5;$i++){
			$rateOption[] = HTMLHelper::_('select.option',$i,$i);
		}

		$lists['rate']  = HTMLHelper::_('select.genericlist',$rateOption,'rate' ,'class="input-mini form-select smallSizeBox"','value','text',$row->rate);
		$lists['rate1'] = HTMLHelper::_('select.genericlist',$rateOption,'rate1','class="input-mini form-select smallSizeBox"','value','text',$row->rate1);
		$lists['rate2'] = HTMLHelper::_('select.genericlist',$rateOption,'rate2','class="input-mini form-select smallSizeBox"','value','text',$row->rate2);
		$lists['rate3'] = HTMLHelper::_('select.genericlist',$rateOption,'rate3','class="input-mini form-select smallSizeBox"','value','text',$row->rate3);
		$lists['rate4'] = HTMLHelper::_('select.genericlist',$rateOption,'rate4','class="input-mini form-select smallSizeBox"','value','text',$row->rate4);

		if(($row->created_on != "") && ($row->created_on != "0000-00-00 00:00:00")){
			$created_on = explode(" ",$row->created_on);
			$created_on = explode(":",$created_on[1]);
			$hour = (int)$created_on[0];
			$min = (int)$created_on[1];
		}else{
			$hour = 1;
			$min = 1;
		}

		$hourArr = array();
		for($i=1;$i<=24;$i++){
			$hourArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['hours'] = HTMLHelper::_('select.genericlist',$hourArr,'hour','class="input-mini form-select smallSizeBox"','value','text',$hour);

		$minArr = array();
		for($i=1;$i<=60;$i++){
			$minArr[] = HTMLHelper::_('select.option',$i,$i);
		}
		$lists['min'] = HTMLHelper::_('select.genericlist',$minArr,'min','class="input-mini form-select smallSizeBox"','value','text',$min);
		
		HTML_OspropertyComment::editHTML($option,$row,$lists);
	}
	
	/**
	 * save comment
	 *
	 * @param unknown_type $option
	 */
	static function comment_save($option,$save){
		global $jinput, $mainframe;
		$id			= $jinput->getInt('id',0);
		$db			= Factory::getDBO();
		$post		= $jinput->post->getArray();
		$row		= Table::getInstance('Comment','OspropertyTable');
		$row->bind($post);
		$created_on = $jinput->getString('created_on','');
		if($created_on != "")
		{
			if($id == 0)
			{
				$row->created_on = $created_on." ".date("H:i:s",time());
			}
		}
		else
		{
			$row->created_on = date("Y-m-d H:i:s",time());
		}
		$row->check();
		$msg = Text::_('OS_ITEM_SAVED'); 
	 	if (!$row->store())
		{
		 	throw new Exception($row->getError(), 500);			 	
		}
		$rate1		  = $row->rate1;
		$rate2		  = $row->rate2;
		$rate3		  = $row->rate3;
		$rate4		  = $row->rate4;
		$rate		  = round(($rate1 + $rate2 + $rate3 + $rate4)/4);
		$id = $jinput->getInt('id',0);
		if($id == 0){
			$id = $db->insertid();
			//update into osrs_properties
			$db->setQuery("Select number_votes ,total_points from #__osrs_properties where id = '$row->pro_id'");
			$vote = $db->loadObject();
			$number_votes = $vote->number_votes;
			$total_points = $vote->total_points;
			$number_votes++;
			
			$total_points += $rate;

			$db->setQuery("Update #__osrs_properties set number_votes = '$number_votes',total_points='$total_points' where id = '$row->pro_id'");
			$db->execute();
		}

		$db->setQuery("Update #__osrs_comments set rate = '$rate' where id = '$id'");
		$db->execute();
		
		if(($row->ip_address != "") and ($row->country == "")){
			$country = self::countryFromIP($row->ip_address);
			$db->setQuery("Select country_code from #__osrs_countries where country_name like '$country'");
			$country_code = $db->loadResult();
			$db->setQuery("Update #__osrs_comments set country = '".strtolower($country_code)."' where id = '$row->id'");
			$db->execute();
		}
		
		self::updateComment($row->pro_id);
		$mainframe->enqueueMessage($msg);
		if($save == 1) {
			$mainframe->redirect("index.php?option=$option&task=comment_list");
		}elseif($save == 2) {
			$mainframe->redirect("index.php?option=$option&task=comment_add");
		}else{
			$mainframe->redirect("index.php?option=$option&task=comment_edit&cid[]=".$row->id);
		}
	}
	
	public static function countryFromIP($ipAddr){
   	    ip2long($ipAddr)== -1 || ip2long($ipAddr) === false ? trigger_error("Invalid IP", E_USER_ERROR) : "";$ipDetail=array();
	    $xml = file_get_contents("http://api.hostip.info/?ip=".$ipAddr);
	    preg_match("@<Hostip>(\s)*<gml:name>(.*?)</gml:name>@si",$xml,$match);
	    preg_match("@<countryName>(.*?)</countryName>@si",$xml,$matches);
	    $country = $matches[1];
	    preg_match("@<countryAbbrev> (.*?)</countryAbbrev>@si",$xml,$cc_match);$ipDetail['country_code']=$cc_match[1];
   	   return $country;
    }
	
	static function getPropertyInput($pro_id)
    {
        if (version_compare(JVERSION, '3.5', 'le'))
        {
            // Initialize variables.
            $html = array();
            //$groups = $this->getGroups();
            //$excluded = $this->getExcluded();
            $link = 'index.php?option=com_osproperty&amp;task=properties_list&amp;&amp;tmpl=component&amp;field=pro_id';

            // Initialize some field attributes.
            $attr = ' class="input-large"';

            // Load the modal behavior script.
            HTMLHelper::_('behavior.modal');
            HTMLHelper::_('behavior.modal', 'a.modal_pro_id');

            // Build the script.
            $script = array();
            $script[] = '	static function jSelectUser_pro_id(id, title) {';
            $script[] = '		var old_id = document.getElementById("pro_id").value;';
            $script[] = '		if (old_id != id) {';
            $script[] = '			document.getElementById("pro_id").value = id;';
            $script[] = '			document.getElementById("pro_name").value = title;';
            $script[] = '			' . $onchange;
            $script[] = '		}';
            $script[] = '		SqueezeBox.close();';
            $script[] = '	}';

            // Add the script to the document head.
            Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

            // Load the current username if available.
            $table = Table::getInstance('Property', 'OspropertyTable');

            if ($pro_id) {
                $table->load($pro_id);
            } else {
                $table->pro_name = Text::_('OS_SELECT_PROPERTY');
            }

            // Create a dummy text field with the user name.
            $html[] = '<span class="input-append">';
            $html[] = '<input type="text" class="input-large" id="pro_name" value="' . htmlspecialchars($table->pro_name, ENT_COMPAT, 'UTF-8') . '" disabled="disabled" size="35" /><a class="modal btn" title="' . Text::_('OS_SELECT_PROPERTY') . '"  href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> ' . Text::_('OS_SELECT_PROPERTY') . '</a>';
            $html[] = '</span>';

            // Create the real field, hidden, that stored the user id.
            $html[] = '<input type="hidden" id="pro_id" name="pro_id" value="' . $pro_id . '" />';

            return implode("\n", $html);
        }
        else
        {
            HTMLHelper::_('jquery.framework');
            FormHelper::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_osproperty/fields');

            if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
            {
                FormHelper::addFieldPrefix('Joomla\Component\Osproperty\Administrator\Field');

            }
            $field = FormHelper::loadFieldType('Modal_Property');
            $element = new SimpleXMLElement('<field />');
            $element->addAttribute('name', $pro_id);
            $element->addAttribute('select', 'true');
            $element->addAttribute('clear', 'true');
            $element->addAttribute('class', 'readonly');
            $field->setup($element, 0);

            return $field->input;
        }
	}

	public static function updateComment($pid){
		$db = Factory::getDbo();
		$db->setQuery("Select count(id) as number_votes, sum(rate) as total_points from #__osrs_comments where pro_id = '$pid' and published = '1'");
		$vote = $db->loadObject();
		$number_votes = $vote->number_votes;
		$total_points = $vote->total_points;
		$db->setQuery("Update #__osrs_properties set total_points = '$total_points',number_votes = '$number_votes' where id = '$pid'");
		$db->execute();
	}
}
?>
