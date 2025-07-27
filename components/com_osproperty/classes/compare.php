<?php
/*------------------------------------------------------------------------
# compare.php - Ossolution Property
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
class OspropertyCompare{
	/**
	 * Comparision
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $jinput, $mainframe,$configClass;
		$p = $jinput->getInt('p',0);
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/default/style/style.css");
		//echo HelperOspropertyCommon::buildToolbar('property');
		switch ($task){
			case "compare_remove":
				OspropertyCompare::remove($option);
			break;
			default:
				$show_top_menus_in = $configClass['show_top_menus_in'];
				$show_top_menus_in = explode("|",$show_top_menus_in);
				if((in_array('compare',$show_top_menus_in)) and ($p == 0)){
					echo HelperOspropertyCommon::buildToolbar('compare');
				}
				OspropertyCompare::compare($option,$task);
			break;
			
		}
	}
	
	
	/**
	 * Compare function
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	
	static function compare($option,$task){
		global $jinput, $mainframe,$configs,$configClass,$lang_suffix;
		$db = Factory::getDBO();
		$document = Factory::getDocument();
		//Factory::getApplication()->setHeader('Pragma: ', 'public',true);
		//Factory::getApplication()->setHeader('Cache-Control: ','public',true);
		//Factory::getApplication()->setHeader('Expires: ', gmdate('D, d M Y H:i:s', time()+(5)) . ' GMT',true);
		$document->setTitle($configClass['general_bussiness_name']." - ".Text::_('OS_COMPARE_PROPERTIES'));
		$session = Factory::getSession();
		$comparelist_ids = $session->get('comparelist');
		$comparelist = explode(",",trim($comparelist_ids));
		
		if(trim($comparelist_ids) == ""){ //no property for comparision
			?>
			<h1 class="componentheading">
				<h1 class="border0">
					<?php echo Text::_('OS_NO_ITEMS_TO_COMPARE');?>
				</h1>
				<?php
				printf(Text::_('CLICK_HERE_TO_GO_BACK'),"<a href='javascript:history.go(-1)'>","</a>");
				?>
			</h1>
			<?php
		}
		else
		{
			$comparisionArr = array();
			$j = 0;
			$fields = HelperOspropertyCommon::getExtrafieldInList();
			for($i=0;$i<count($comparelist);$i++)
			{
				$pid = $comparelist[$i];
				if($pid > 0)
				{
					$db->setQuery("Select * from #__osrs_properties where id = '$pid'");
					$property = $db->loadObject();
					$temp = new stdClass();
					$temp->property = $property;
					$temp->show_address = $property->show_address;
					$db->setQuery("Select * from #__osrs_photos where pro_id = '$pid' order by ordering");
					$photo = $db->loadObject();
					$temp->photo = $photo;
					$query = "Select b.* from #__osrs_property_amenities as a"
							." inner join #__osrs_amenities as b on b.id = a.amen_id"
							." where a.pro_id = '$pid' order by b.amenities";
					$db->setQuery($query);
					$amenities = $db->loadObjectList();
					$temp->amenities = $amenities;
					//$db->setQuery("Select id,category_name$lang_suffix as category_name from #__osrs_categories where id = '$property->category_id'");
					//$rs = $db->loadObject();
					$temp->category_name = OSPHelper::getCategoryNamesOfProperty($property->id);//$rs->category_name;
					
					$db->setQuery("Select id,type_name$lang_suffix as type_name from #__osrs_types where id = '$property->pro_type'");
					$rs = $db->loadObject();
					$temp->property_type = $rs->type_name;
					
					$db->setQuery("Select state_name from #__osrs_states where id = '$property->state'");
					$temp->state = $db->loadResult();
					
					$db->setQuery("Select country_name from #__osrs_countries where id = '$property->country'");
					$temp->country = $db->loadResult();
					
					$fieldArr = array();
					if(count($fields) > 0){
						
						$k 		  = 0;
						for($l=0;$l<count($fields);$l++)
						{
							$field = $fields[$l];
							$temp1 = new stdClass();
							$value = "";
							$value = HelperOspropertyFieldsPrint::showField($field,$property->id);
							//if($value != ""){
							$temp1->fieldvalue = $value;
							$fieldArr[$l] = $temp1;
							//}
							//print_r($fieldArr);
						}
						$temp->fieldarr = $fieldArr;
					}
					$comparisionArr[$j] = $temp;
					$j++;
					
				}
			}
			$isPrint = $jinput->getInt('p',0);
			HTML_OspropertyCompare::showCompareForm($option,$comparisionArr,$configs,$isPrint,$fields);
		}
	}
	
	/**
	 * Remove property out of the compare list
	 *
	 * @param unknown_type $option
	 */
	static function remove($option){
		global $jinput, $mainframe;
		$session = Factory::getSession();
		$needs = array();
		$needs[] = "rcompare";
		$needs[] = "compare_properties";
		$itemid = OSPRoute::getItemid($needs);
		//$itemid = OSPRoute::confirmItemid($itemid,'compare_properties');
		$pid = $jinput->getInt('pid',0);
		$comparelist = $session->get('comparelist');
		$comparelist = explode(",",$comparelist);
		$newcomparelist = array();
		$j = 0 ;
		for($i=0;$i<count($comparelist);$i++){
			if($comparelist[$i] != $pid){
				$newcomparelist[$j] = $comparelist[$i];
				$j++;
			}
		}
		$comparelistStr = "";
		if(count($newcomparelist))
		{
			$comparelistStr = implode(",", $newcomparelist);
		}
		$session->set('comparelist',$comparelistStr);
		
		OSPHelper::redirect(Route::_("index.php?option=com_osproperty&task=compare_properties&Itemid=".$itemid));
		
	}
}
?>