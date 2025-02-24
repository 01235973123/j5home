<?php
use Joomla\CMS\Utility\Utility;
/*------------------------------------------------------------------------
# cron.php - Ossolution Property
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
class OspropertyCron{
	/**
	 * Default function
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $mainframe;
		switch ($task){
			case "cron_checklist":
				OspropertyCron::checklist();
			break;
			case "cron_test":
				OspropertyCron::test($option);
			break;
		}
	}
	
	/**
	 * Check and process the expired properties
	 * check all the properties have 
	 * inform_time < current_time and pid not in the queue, add into the queue
	 * expired_time < current_time and pid not in the queue, add into the queue, if the property is approved => unapproved the property
	 * feature_time < current_time, feature -> unfeatured
	 *
	 * @param unknown_type $option
	 */
	static function checklist(){
		global $mainframe,$configClass;
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/helper.php");
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = Factory::getDbo();
		$current_time = time();
		$current_date = date("Y-m-d H:i:s",$current_time);
		
		$query = "Select a.* from #__osrs_expired as a"
				." inner join #__osrs_properties as b on b.id = a.pid"
				." where a.inform_time < '$current_date'"
				." and a.expired_time > '$current_date'"
				." and a.send_inform = '0'"
				." and b.approved = '1'"
				." and a.pid not in (Select pid from #__osrs_queue where `emailtype` = 1)";
		$db->setQuery($query);
	
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0){
			//insert into queue
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				$db->setQuery("Insert into #__osrs_queue (id,pid,emailtype) values (NULL,'$row->pid','1')");
				$db->execute();
			}
		}
		
		//check approved
		$query = "Select a.* from #__osrs_expired as a"
				." inner join #__osrs_properties as b on b.id = a.pid"
				." where a.expired_time < '$current_date'"
				." and b.approved = '1'"
				." and a.send_expired = '0'"
				." and a.pid not in (Select pid from #__osrs_queue where `emailtype` = 2)";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0){
			//insert into queue
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				//unapproved 
				$db->setQuery("Update #__osrs_properties set approved = '0' where id = '$row->pid'");
				$db->execute();
				$db->setQuery("Insert into #__osrs_queue (id,pid,emailtype) values (NULL,'$row->pid','2')");
				$db->execute();
			}
		}
		
		
		//check featured
		$query = "Select a.* from #__osrs_expired as a"
				." inner join #__osrs_properties as b on b.id = a.pid"
				." where a.expired_feature_time < '$current_date'"
				." and b.isFeatured = '1'"
				." and a.send_featured = '0'"
				." and a.pid not in (Select pid from #__osrs_queue where `emailtype` = 3)";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			//insert into queue
			for($i=0;$i<count($rows);$i++){
				$row = $rows[$i];
				//unapproved 
				$db->setQuery("Update #__osrs_properties set isFeatured = '0' where id = '$row->pid'");
				$db->execute();
				$db->setQuery("Insert into #__osrs_queue (id,pid,emailtype) values (NULL,'$row->pid','3')");
				$db->execute();
			}
		}
		
		//remove from database
		$query = "Select a.* from #__osrs_expired as a"
				." inner join #__osrs_properties as b on b.id = a.pid"
				." where a.remove_from_database  < '$current_date'"
				." and a.pid not in (Select pid from #__osrs_queue where `emailtype` = 3)";
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		if(count($rows) > 0)
		{
			for($i=0;$i<count($rows);$i++)
			{
				$row = $rows[$i];
				$pid = $row->pid;
				$db->setQuery("Delete from #__osrs_properties where id = '$row->pid'");
				$db->execute();
				$db->setQuery("Delete from #__osrs_expired where pid ='$row->pid'");
				$db->execute();
				$db->setQuery("Delete from #__osrs_queue where pid = '$row->pid'");
				$db->execute();
				$db->setQuery("Delete from #__osrs_property_field_value where pro_id = '$row->pid'");
				$db->execute();
				$db->setQuery("Delete from #__osrs_property_amenities where pro_id = '$row->pid'");
				$db->execute();
				$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->pid'");
				$photos = $db->loadObjectList();
				if(count($photos) > 0)
				{
					for($j=0;$j<count($photos);$j++)
					{
						$photo = $photos[$j];
						if($pid > 0)
						{
							$image_path = JPATH_ROOT."/images/osproperty/properties/".$pid;
							@unlink($image_path."/".$photo->image);
							@unlink($image_path."/medium/".$photo->image);
							@unlink($image_path."/thumb/".$photo->image);
						}
					}
				}
				Folder::delete(JPATH_ROOT."/images/osproperty/properties/".$pid);
				$db->setQuery("Delete from #__osrs_photos where pro_id = '$row->pid'");
				$db->execute();
			}
		}
		
		
		//now, send email 
		$number_email = $configClass['number_email_by_hour'];
		if($number_email == ""){
			$number_email = 10;
		}
		
		$db->setQuery("Select runtime from #__osrs_lastcron");
		$runtime = $db->loadResult();
		$runtime = intval($runtime);
		
		if($runtime < time()-3600)
		{
			$db->setQuery("select * from #__osrs_queue order by id limit $number_email");
			$rows = $db->loadObjectList();
			if(count($rows) > 0)
			{
				for($i=0;$i<count($rows);$i++)
				{
					$row = $rows[$i];
					$pid = $row->pid;
					$emailtype = $row->emailtype;
					
					switch ($emailtype)
					{
						case "1"://approximates expired
							OspropertyEmail::sendApproximatesEmail($option,$pid,$row->id);
						break;
						case "2": //expired
							OspropertyEmail::sendExpiredEmail($option,$pid,$row->id);
						break;
						case "3": //featured
							OspropertyEmail::sendExpiredFeatureEmail($option,$pid,$row->id);
						break;
					}
					//remove from the queue table
					$db->setQuery("Delete from #__osrs_queue where id = '$row->id'");
					$db->execute();
				}
			}
		}
		//update cron
		$db->setQuery("Update #__osrs_lastcron set runtime = '".time()."'");
		$db->execute();
	}
	
	/**
	 * Test cron function
	 *
	 * @param unknown_type $option
	 */
	static function test($option){
		global $mainframe;
		Utility::sendMail('damdt@joomservices.com','Dang Thuc Dam','damdt@joomservices.com','test','test1');
	}
}
?>
