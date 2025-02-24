<?php

/*------------------------------------------------------------------------
# csvform.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2024 joomdonation.com. All Rights Reserved.
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
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Archive\Archive;
//use League\Csv\Reader;

class OspropertyCsvform{
	/**
	 * Default form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	public static function display($option,$task){
		global $jinput, $mainframe;
		$cid = $jinput->get( 'cid', array(), 'ARRAY');
		$id  = $jinput->getInt('id',0);
		switch ($task){
			case "form_default":
				self::defaultList($option);
				break;
			case "form_add":
				self::editForm($option,0);
				break;
			case "form_edit":
				self::editForm($option,$cid[0]);
				break;
			case "form_cancel":
				$mainframe->redirect("index.php?option=com_osproperty&task=form_default");
				break;
			case "form_save":
				self::save($option,1);
				break;
			case "form_apply":
				self::save($option,0);
				break;
			case "form_remove":
				self::removeForms($option,$cid);
				break;
			case "form_unpublish":
				self::form_change_publish($option,$cid,0);
				break;
			case "form_publish":
				self::form_change_publish($option,$cid,1);
				break;
			case "form_downloadcsv":
				self::downloadCsv($option,$cid[0]);
				break;
			case "form_importcsv":
				self::importCsvForm($option,$cid[0]);
				break;
			case "form_doimportcsv":
				self::doImportCSV($option);
				break;
			case "form_importphotoform":
				self::photoForm($option);
				break;
			case "form_doimportphoto":
				self::doimportPhoto($option,$id);
				break;
			case "form_updateOtherInfor":
				self::updateOtherInfor($option,$id);
				break;
			case "form_saveotherinformation":
				self::saveotherinformation($option);
				break;
			case "form_completeimport":
				self::completeImportCsv($option);
				break;
		}
	}

	/**
	 * Complete import CSV
	 *
	 * @param unknown_type $option
	 */
	public static function completeImportCsv($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$id = $jinput->getInt('id',0);
		$db->setQuery("SELECT * FROM #__osrs_importlog WHERE form_id = '$id'");
		$log = $db->loadObject();
		HTML_OspropertyCsvform::completeImportCsv($option,$log);
	}

	/**
	 * Photo form
	 *
	 * @param unknown_type $option
	 */
	public static function photoForm($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$id = $jinput->getInt('id',0);
		HTML_OspropertyCsvform::importPhotoForm($option,$id);
	}


	/**
	 * Do import photo
	 *
	 * @param unknown_type $option
	 */
	public static function doimportPhoto($option,$id){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_importlog_properties where form_id = '$id'");
		$properties = $db->loadObjectList();

		jimport('joomla.filesystem.archive');
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$db = Factory::getDbo();
		if(is_uploaded_file($_FILES['photopack']['tmp_name']))
		{
			if(!HelperOspropertyCommon::checkIsArchiveFileUploaded('photopack'))
			{
				//return to previous page
				?>
                <script type="text/javascript">
                    window.history(-1);
                </script>
			<?php
			}
			else
			{
				$filename = time().$_FILES['photopack']['name'];
				move_uploaded_file($_FILES['photopack']['tmp_name'],JPATH_ROOT."/tmp/".$filename);
				//extract file
				$folder = time();
				//JArchive::extract(JPATH_ROOT."/tmp/".$filename,JPATH_ROOT."/tmp/".$folder);
				$archive = new Archive(array('tmp_path' => Factory::getConfig()->get('tmp_path')));
				$result  = $archive->extract(JPATH_ROOT."/tmp/".$filename,JPATH_ROOT."/tmp/".$folder);
				self::photoImportProcessing($properties,$folder);
			}//end is upload
		}
		else
		{ //check if import package file from directory
			$photodirectory = $jinput->getString('photodirectory','');
			if($photodirectory != "")
			{
				$photodirectoryArr = explode(".",$photodirectory);
				$ext = strtolower($photodirectoryArr[count($photodirectoryArr)-1]);
				if($ext != "zip")
				{
					$id = $jinput->getInt('id',0);
					$msg = Text::_('OS_PLEASE_ENTER_ZIP_FILE');
					$mainframe->enqueueMessage($msg);
					$mainframe->redirect("index.php?option=com_osproperty&task=form_importphotoform&id=$id");
				}
				else
				{
					$folder = time();
					//JArchive::extract($photodirectory,JPATH_ROOT."/tmp/".$folder);

					$archive = new Archive(array('tmp_path' => Factory::getConfig()->get('tmp_path')));
					$result  = $archive->extract($photodirectory,JPATH_ROOT."/tmp/".$folder);
					self::photoImportProcessing($properties,$folder);
				}
			}
		}
		$id = $jinput->getInt('id',0);
		$msg = Text::_('OS_IMPORT_COMPLETE');
		$mainframe->enqueueMessage($msg);
		$mainframe->redirect("index.php?option=com_osproperty&task=form_completeimport&id=$id");
	}

	/**
	 * Import photos
	 *
	 */
	public static function photoImportProcessing($properties,$folder){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		for($i=0;$i<count($properties);$i++)
		{
			$property = $properties[$i];
			$pid 	  = $property->pid;
			if(!Folder::exists(JPATH_ROOT.'/images/osproperty/properties/'.$pid))
			{
				Folder::create(JPATH_ROOT.'/images/osproperty/properties/'.$pid);
				Folder::create(JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/medium');
				Folder::create(JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/thumb');
				File::copy(JPATH_ROOT.'/images/osproperty/properties/index.html',JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/index.html');
				File::copy(JPATH_ROOT.'/images/osproperty/properties/index.html',JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/medium/index.html');
				File::copy(JPATH_ROOT.'/images/osproperty/properties/index.html',JPATH_ROOT.'/images/osproperty/properties/'.$pid.'/thumb/index.html');
			}
			$db->setQuery("Select * from #__osrs_photos where pro_id = '$property->pid'");
			//echo $db->getQuery();
			$photos = $db->loadObjectList();
			$pid = $property->pid;
			if(count($photos) > 0)
			{
				for($j=0;$j<count($photos);$j++)
				{
					$photo = $photos[$j];
					$entry = $photo->image;
					$photo_id = $photo->id;
					$newentry = time().rand(1000,9999).$photo->image;
					$property_tmp_link = JPATH_ROOT."/tmp/".$folder."/".$photo->image;
					//echo $property_tmp_link;
					//die();
					if(File::exists($property_tmp_link))
					{

						$property_image_link = JPATH_ROOT."/images/osproperty/properties/".$pid."/".$newentry;
						$property_medium_link = JPATH_ROOT."/images/osproperty/properties/".$pid."/medium/".$newentry;
						$property_thumb_link = JPATH_ROOT."/images/osproperty/properties/".$pid."/thumb/".$newentry;
						File::copy($property_tmp_link,$property_image_link);
						File::copy($property_image_link,$property_medium_link);
						File::copy($property_image_link,$property_thumb_link);

						//thumb
						$thumb_width = $configClass['images_thumbnail_width'];
						$thumb_height = $configClass['images_thumbnail_height'];

						OSPHelper::resizePhoto($property_thumb_link,$thumb_width,$thumb_height);

						//medium
						$medium_width = $configClass['images_large_width'];
						$medium_height = $configClass['images_large_height'];

						OSPHelper::resizePhoto($property_medium_link,$medium_width,$medium_height);

						//Update the photo after rename
						$db->setQuery("UPDATE #__osrs_photos SET image = '$newentry' WHERE id = '$photo_id'");
						$db->execute();
					}
				}
			}
			//update watermark for property
			OSPHelper::generateWaterMark($property->pid);
		}//end for
	}

	/**
	 * Update Other Information
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	public static function updateOtherInfor($option,$id){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$db->setQuery("Select a.* from #__osrs_properties as a inner join #__osrs_importlog_properties as b on b.pid = a.id where b.form_id = '$id'");
		$properties = $db->loadObjectList();
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_csv_forms where id = '$id'");
		$row = $db->loadObject();
		$db->setQuery("Select id as value, name as text from #__osrs_agents where published = '1' order by name");
		$agents = $db->loadObjectList();
		$agentArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_AGENT'));
		$agentArr   = array_merge($agentArr,$agents);
		$lists['agentArr'] = $agentArr;

		$countryArr = [];
		$stateArr = [];
		$cityArr = [];
		if($configClass['show_country_id'] == ""){
			//country
			$countryArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COUNTRY'));
			$db->setQuery("Select id as value, country_name as text from #__osrs_countries order by country_name");
			$countries = $db->loadObjectList();
			$countryArr = array_merge($countryArr,$countries);
			$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
		}else{

			if(HelperOspropertyCommon::checkCountry()){
				$stateArr = [];
				$countryArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_COUNTRY'));
				$db->setQuery("Select id as value, country_name as text from #__osrs_countries where id in (".$configClass['show_country_id'].") order by country_name");
				$countries = $db->loadObjectList();
				$countryArr = array_merge($countryArr,$countries);
				$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
				$query  = "Select id as value,state_name as text from #__osrs_states where 1=1 ";
				$query .= " order by state_name";
				$db->setQuery($query);
				$states = $db->loadObjectList();
				$stateArr   = array_merge($stateArr,$states);
			}else{
				$stateArr = [];
				$stateArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_STATE'));
				$query  = "Select id as value,state_name as text from #__osrs_states where 1=1 ";
				$query .= " and country_id = ".$configClass['show_country_id'];
				$query .= " order by state_name";
				$db->setQuery($query);
				$states = $db->loadObjectList();
				$stateArr   = array_merge($stateArr,$states);
			}
		}

		$lists['country'] = $countryArr;
		$lists['state']	= $stateArr;
		$cityArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_CITY'));
		$lists['city'] = $cityArr;

		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_PROPERTY_TYPE'));
		$db->setQuery("Select id as value,type_name as text from #__osrs_types where published = '1' order by type_name");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = $typeArr;

		HTML_OspropertyCsvform::updateOtherInformationForm($option,$properties,$row,$lists);
	}

	/**
	 * Save Other Information
	 *
	 * @param unknown_type $option
	 */
	public static function saveotherinformation($option){
		global $jinput, $mainframe;
		include_once(JPATH_ROOT."/components/com_osproperty/helpers/googlemap.lib.php");
		$db = Factory::getDbo();
		$properties_str = $jinput->getString('property_str','');
		$propertiesArr = explode(",",$properties_str);
		if(count($propertiesArr) > 0){
			for($i=0;$i<count($propertiesArr);$i++){
				$property_id = $propertiesArr[$i];
				$agent_id_name = "agent_id".$property_id;
				$agent_id	   = $jinput->getString($agent_id_name,'');

				$agent_id_value_name = "agent_id_value".$property_id;
				$agent_id_value		 = $jinput->getString($agent_id_value_name,'');

				if($agent_id == ""){
					$agent_id = $agent_id_value;
				}

				$country = $jinput->getInt('country'.$property_id,'');
				$state = $jinput->getInt('state'.$property_id,'');
				$city = $jinput->getInt('city'.$property_id,'');
				$category_id = $jinput->getInt('category_id'.$property_id,'');
				$pro_type = $jinput->getInt('pro_type'.$property_id,'');

				$db->setQuery("UPDATE #__osrs_properties SET category_id = '$category_id',pro_type='$pro_type',agent_id = '$agent_id',country = '$country',state='$state',city = '$city' where id = '$property_id'");
				$db->execute();

				//find lat long address
				$db->setQuery("Select * from #__osrs_properties where id = '$property_id'");
				$property = $db->loadObject();
				$address = $property->address;
				if($property->postcode != ""){
					$address .= ", ".$property->postcode;
				}
				$db->setQuery("select city from #__osrs_cities where id = '$city'");
				$city = $db->loadResult();
				if($city != ""){
					$address .= ", ".$city;
				}

				$db->setQuery("select state_name from #__osrs_states where id = '$state'");
				$state = $db->loadResult();
				if($state != ""){
					$address .= ", ".$state;
				}

				$db->setQuery("select country_name from #__osrs_countries where id = '$country'");
				$country = $db->loadResult();
				if($country != ""){
					$address .= ", ".$country;
				}

				if($address != ""){
					$return = HelperOspropertyGoogleMap::findAddress($option,'',$address,1);
					if($return[2] == "OK"){
						$db->setQuery("UPDATE #__osrs_properties SET lat_add = '".$return[0]."', long_add = '".$return[1]."'  WHERE id = '$property_id'");
						$db->execute();
					}
				}

			}
			$db->setQuery("DELETE FROM #__osrs_importlog_properties WHERE pid IN ($properties_str)");
			$db->execute();
		}
		$mainframe->redirect("index.php?option=com_osproperty&task=form_completeimport&id=".$jinput->getInt('id',0));
	}

	/**
	 * Import CSV Form
	 *
	 * @param unknown_type $option
	 */
	public static function importCsvForm($option,$id){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$row = Table::getInstance('Csvform','OspropertyTable');
		$row->load((int)$id);

		$db->setQuery("DELETE FROM #__osrs_importlog_properties WHERE form_id = '$id'");
		$db->execute();

		$db->setQuery("Select id as value, name as text from #__osrs_agents where published = '1' order by name");
		$agents = $db->loadObjectList();
		$agentArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_DEFAULT_AGENT'));
		$agentArr   = array_merge($agentArr,$agents);
		$lists['agent'] = HTMLHelper::_('select.genericlist',$agentArr,'agent_id','class="input-small"','value','text');

		$photoArr[] = HTMLHelper::_('select.option','0',Text::_('OS_PHOTO_FROM_DIFFERENT_HOST'));
		$photoArr[] = HTMLHelper::_('select.option','1',Text::_('OS_PHOTO_FROM_YOUR_COMPUTER'));
		$lists['photo'] = HTMLHelper::_('select.genericlist',$photoArr,'photofrom','class="inputbox"','value','text');

		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option','0',Text::_('OS_NO'));
		$optionArr[] = HTMLHelper::_('select.option','1',Text::_('OS_YES'));
		$lists['utf'] = HTMLHelper::_('select.genericlist',$optionArr,'import_utf','class="input-small"','value','text');

		$lists['removeproperties'] = HTMLHelper::_('select.genericlist',$optionArr,'removeproperties','class="input-small"','value','text');
		HTML_OspropertyCsvform::importCsvForm($option,$row,$lists);
	}

	/**
	 * Do import
	 *
	 * @param unknown_type $option
	 */
	public static function doImportCSV($option){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		$isImport = [];
		$log1 = [];
		$log2 = [];
		$log1_str = "";
		$log2_str = "";
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$id = $jinput->getInt('id',0);
		$form_id = $id;
		$row = Table::getInstance('Csvform','OspropertyTable');
		$row->load((int)$id);
		$max_file_size = $row->max_file_size;
		$max_file_size_in_bytes = $max_file_size*1024*1024;

		$csv_folder = JPATH_ROOT."/tmp/csv";
		if(!Folder::exists($csv_folder)){
			Folder::create($csv_folder);
		}

		if(!HelperOspropertyCommon::checkIsCsvFileUploaded('csv_file')){
			//return to previous page
			?>
            <script type="text/javascript">
                window.history(-1);
            </script>
<?php
		}else{
			if(is_uploaded_file($_FILES['csv_file']['tmp_name'])){
				$filesize = filesize($_FILES['csv_file']['tmp_name']);
				if($filesize > $max_file_size_in_bytes){
					$mainframe->enqueueMessage(Text::_('OS_YOUR_FILE_IS_LARGER_THAN_LIMIT'));
					$mainframe->redirect("index.php?option=com_osproperty&task=form_importcsv&cid[]=$id");
				}else{
					//remove all properties
					$removeproperties = $jinput->getInt('removeproperties',0);
					if($removeproperties == 1){
						$db->setQuery("Select id from #__osrs_properties");
						$cid = $db->loadColumn(0);

						if($cid)
						{
							$cids = implode(",",$cid);
							//remove from properties table
							$db->setQuery("Delete from #__osrs_properties where id in ($cids)");
							$db->execute();
							//remove from amenities table
							$db->setQuery("Delete from #__osrs_property_amenities where pro_id in ($cids)");
							$db->execute();
							//remove from extra field table
							$db->setQuery("Delete from #__osrs_property_field_value where pro_id in ($cids)");
							$db->execute();
							//remove from expired table
							$db->setQuery("Delete from #__osrs_expired where pid in ($cids)");
							$db->execute();
							//remove from queue table
							$db->setQuery("Delete from #__osrs_queue where pid in ($cids)");
							$db->execute();

							//remove images
							$db->setQuery("Select * from #__osrs_photos where pro_id in ($cids)");
							$photos = $db->loadObjectList();
							if(count($photos) > 0){
								for($i=0;$i<count($photos);$i++){
									$photo = $photos[$i];
									$image = $photo->image;
									$image_link = JPATH_ROOT."/images/osproperty/properties/".$photo->pro_id;
									File::delete($image_link.DS.$image);
									File::delete($image_link."/thumb/".$image);
									File::delete($image_link."/medium/".$image);
								}
							}
							$db->setQuery("Delete from #__osrs_photos where pro_id in ($cids)");
							$db->execute();
							foreach ($cid as $id){
								Folder::delete(JPATH_ROOT."/images/osproperty/properties/".$id);
							}
						}
					}

					$filename = time().str_replace(" ","_",$_FILES['csv_file']['name']);
					move_uploaded_file($_FILES['csv_file']['tmp_name'],$csv_folder.DS.$filename);

					//$reader = Reader::createFromPath($csv_folder.DS.$filename, 'r');
					//$reader->setHeaderOffset(0);
					//$records = $reader->getRecords();
					
					//do import data
					include_once(JPATH_ROOT."/components/com_osproperty/classes/listing.php");
					include(JPATH_ROOT."/components/com_osproperty/helpers/csv/FileReader.php");
					include(JPATH_ROOT."/components/com_osproperty/helpers/csv/CSVReader.php");
					$reader = new CSVReader( new FileReader($csv_folder.DS.$filename));
					$reader->setSeparator( $configClass['csv_seperator'] );
					$rs = 0;
					$j = 0;
					$import_utf = $jinput->getInt('import_utf',0);
					while( false != ( $cell = $reader->next() ) ){
						if($rs > 0){
							$isImport = self::importCell($row,$cell,$import_utf);
							if($isImport[0]->isInsert == 0){
								$log1[] = $isImport[0]->error;
							}else{
								$log2[] = $isImport[0]->error;
							}
						}
						$rs++;
					}
				}
			}
		}
		//update into form
		$current_time = date("Y-m-d H:i:s",time());
		$db->setQuery("UPDATE #__osrs_csv_forms SET last_import = '$current_time' WHERE id = '$form_id'");
		$db->execute();

		//insert into log table
		//if(count($isImport) > 0){
		if(count($log1) > 0){
			$log1_str = implode("<BR>",$log1);
		}
		if(count($log2) > 0){
			$log2_str = implode("<BR>",$log2);
		}
		$db->setQuery("DELETE FROM #__osrs_importlog WHERE form_id = '$form_id'");
		$db->execute();
		$log1_str = mb_convert_encoding($log1_str, 'UTF-8', 'UTF-8');
		$log2_str = mb_convert_encoding($log2_str, 'UTF-8', 'UTF-8');
		$db->setQuery("INSERT INTO #__osrs_importlog (id,form_id,log1,log2) values (NULL,'$form_id',".$db->quote($log1_str).",".$db->quote($log2_str).")");
		$db->execute();

		//empty sef table
		$db->setQuery("Delete from #__osrs_urls");
		$db->execute();

		$db->setQuery("Select count(id) from #__osrs_importlog_properties where form_id = '$form_id'");
		$count = $db->loadResult();
		if(($count > 0) and ($row->image_type == 0)){
			$msg = Text::_('OS_IMPORTPHOTO');
			$mainframe->enqueueMessage($msg);
			$mainframe->redirect("index.php?option=com_osproperty&task=form_importphotoform&id=$form_id");
		}else{
			$mainframe->redirect("index.php?option=com_osproperty&task=form_completeimport&id=$form_id");
		}
	}

	/**
	 * Import on Cell of CSV file
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $cell
	 */
	public static function importCell($row,$cell,$import_utf){
		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		// Select ALL Fields one query
		$db->setQuery("Select * from #__osrs_form_fields where form_id = '$row->id'");
		$allFields = $db->loadObjectList('field'); // Access with !empty($allFields['ref']) ?  $allFields['ref']->column_number : '';

		$col_ref = !empty($allFields['ref']) ?  $allFields['ref']->column_number : '';
		$col_ref--;
		$property_ref = $cell[$col_ref];

		//check property name
		$col_property_name = !empty($allFields['pro_name']) ?  $allFields['pro_name']->column_number : '';
		$col_property_name--;
		$property_name = $cell[$col_property_name];

		$col_alias = !empty($allFields['pro_alias']) ?  $allFields['pro_alias']->column_number : '';
		$col_alias--;
		$alias = $cell[$col_alias];

		//category
		$col_category = !empty($allFields['category_id']) ?  $allFields['category_id']->column_number : '';
		$col_category--;
		$category = trim($cell[$col_category]);
		//in case category == 0
		$catIds = [];
		if($category != ""){
			$categoryArr = explode(",",$category);
			foreach ($categoryArr as $category){
				$db->setQuery('Select id from #__osrs_categories where category_name like '.$db->quote($category));
				$catid = $db->loadResult();
				//echo $catid;
				if(intval($catid) == 0){//can't find the category
					if($row->fcategory == 0){
						$catInstance = & Table::getInstance('Category','OspropertyTable');
						$catInstance->id = 0;
						if($import_utf == 1){
							$catInstance->category_name = utf8_encode($category);
						}else{
							$catInstance->category_name = $category;
						}
						$catInstance->parent_id = 0;
						$catInstance->access = 1;
						$db->setQuery("Select ordering from #__osrs_categories order by ordering");
						$ordering = $db->loadResult();
						$ordering++;
						$catInstance->ordering = $ordering;
						$catInstance->published = 1;
						$catInstance->store();
						$catid = $db->insertID();
						$catIds[] = $catid;
					}else{
						$catid = $row->category_id;
						$catIds[] = $catid;
					}
				}else{
					$catIds[] = $catid;
				}
			}
		}else{
			if($row->fcategory == 1){
				$catid = $row->category_id;
				$catIds[] = $catid;
			}
		}

		$category = $catIds;

		//property type
		$col_type = !empty($allFields['pro_type']) ?  $allFields['pro_type']->column_number : '';
		$col_type--;
		$property_type = trim($cell[$col_type]);

		//in case type == 0
		if($property_type != ""){
			$db->setQuery('Select id from #__osrs_types where type_name like ' . $db->quote($property_type));
			$typeid = $db->loadResult();
			if(intval($typeid) == 0){//can't find the type
				if($row->ftype == 0){
					$typeInstance = & Table::getInstance('Type','OspropertyTable');
					$typeInstance->id = 0;
					//$typeInstance->type_name = utf8_encode($property_type);
					if($import_utf == 1){
						$typeInstance->type_name = utf8_encode($property_type);
					}else{
						$typeInstance->type_name = $property_type;
					}
					$typeInstance->published = 1;
					$typeInstance->store();
					$typeid = $db->insertID();
				}else{
					$typeid = $row->type_id;
				}
			}
		}else{
			if($row->ftype == 1){
				$typeid = $row->type_id;
			}
		}
		$property_type = $typeid;
		//agent
		$col_agent = !empty($allFields['agent_id']) ?  $allFields['agent_id']->column_number : '';
		$col_agent--;
		$agent = trim($cell[$col_agent]);
		//in case agent == 0
		if($agent != ""){
			$db->setQuery("Select a.id from #__osrs_agents as a inner join #__users as b on a.user_id = b.id where a.name like '$agent' or b.username like '$agent' or b.email like '%$agent%'");
			$agentid = $db->loadResult();
			if(intval($agentid) == 0){//can't find the type
				$agentid = $row->agent_id;
			}
		}else{
			$agentid = $row->agent_id;
		}
		$agent = $agentid;
		//country
		$col_country = !empty($allFields['country']) ?  $allFields['country']->column_number : '';
		$col_country--;
		$country = trim($cell[$col_country]);
		//in case country == 0
		if($country != ""){
			$db->setQuery("Select id from #__osrs_countries where `country_name` like ".$db->quote($country)." or country_code like ".$db->quote($country));
			$countryid = $db->loadResult();
			if(intval($countryid) == 0){//can't find the type
				$countryid = $row->country;
			}
		}else{
			$countryid = $row->country;
		}
		$country = $countryid;
		//state
		$db->setQuery("Select column_number from #__osrs_form_fields where form_id = '$row->id' and `field` like 'state'");
		$col_state = !empty($allFields['state']) ?  $allFields['state']->column_number : '';
		$col_state--;
		$state = trim($cell[$col_state]);
		//in case state == 0
		if($state != ""){
			$db->setQuery("Select id from #__osrs_states where (state_name like ".$db->quote($state)." or state_code like ".$db->quote($state).") and `country_id` = '$country'");
			$stateid = $db->loadResult();
			if(intval($stateid) == 0){//can't find the type
				if($row->fstate == 0){
					$stateInstance = & Table::getInstance('State','OspropertyTable');
					$stateInstance->id = 0;
					$stateInstance->country_id = $country;
					$stateInstance->state_name = $state;
					$stateInstance->state_code = $state;
					$stateInstance->published = 1;
					$stateInstance->store();
					$stateid = $db->insertID();
				}else{
					$stateid = $row->state;
				}
			}
		}else{
			if($row->fstate == 1){
				$stateid = $row->state;
			}
		}
		$state = $stateid;
		//city
		$db->setQuery("Select column_number from #__osrs_form_fields where form_id = '$row->id' and `field` like 'city'");
		$col_city = !empty($allFields['city']) ?  $allFields['city']->column_number : '';
		$col_city--;
		$city = trim($cell[$col_city]);
		//in case state == 0
		$cityid = 0;
		if($city != "")
		{
			$db->setQuery("Select id from #__osrs_cities where `state_id` = '$state' and `country_id` = '$country' and `city` like ".$db->quote($city));
			$cityid = $db->loadResult();
			if(intval($cityid) == 0)
			{	//can't find the type
				if($row->fcity == 0)
				{
					Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osproperty/tables');
					$cityInstance = Table::getInstance('City','OspropertyTable');
					$cityInstance->id = 0;
					$cityInstance->country_id = $country;
					$cityInstance->state_id = $state;
					$cityInstance->city = $city;
					$cityInstance->published = 1;
					$cityInstance->store();
					$cityid = $db->insertID();
				}
				else
				{
					$cityid = $row->city;
				}
			}
		}
		else
		{
			if($row->fcity == 1)
			{
				$cityid = $row->city;
			}
		}
		$city = $cityid;

		//price
		$col_price = !empty($allFields['price']) ?  $allFields['price']->column_number : '';
		$col_price--;
		$price = trim($cell[$col_price]);

		//small description
		$col_small_desc = !empty($allFields['pro_small_desc']) ?  $allFields['pro_small_desc']->column_number : '';
		$col_small_desc--;
		$small_desc = $cell[$col_small_desc];

		//full desc
		$col_full_desc = !empty($allFields['pro_full_desc']) ?  $allFields['pro_full_desc']->column_number : '';
		$col_full_desc--;
		$full_desc = $cell[$col_full_desc];

		//price_original
		$col_price_original = !empty($allFields['price_original']) ?  $allFields['price_original']->column_number : '';
		$price_original = '';
		if (!empty($col_price_original)) {
			$col_price_original--;
			$price_original = trim($cell[$col_price_original]);
		}

		//currency
		$col_curr = !empty($allFields['curr']) ?  $allFields['curr']->column_number : '';
		$curr     = '';
		if (!empty($col_curr)) {
			$col_curr--;
			$curr   = trim($cell[$col_curr]);

			$db->setQuery("Select id from #__osrs_currencies where currency_name like '$curr' or currency_code like '$curr' or currency_symbol like '$curr'");
			$curr = $db->loadResult();
		} else {
			$curr = $configClass['general_currency_default'];
		}

		//note
		$col_note = !empty($allFields['note']) ?  $allFields['note']->column_number : '';
		$note = '';
		if (!empty($col_note)) {
			$col_note--;
			$note = $cell[$col_note];
		}

		//price_call
		$col_price_call = !empty($allFields['price_call']) ?  $allFields['price_call']->column_number : '';

		if (!empty($col_price_call)) {
			$col_price_call--;
		}

		$price_call = 0;
		if((int) $col_price_call >= 0)
		{
			$price_call = $cell[$col_price_call];
			if($price_call != "")
			{
				if(trim(strtolower($price_call)) == trim(strtolower($row->yes_value)))
				{
					$price_call = 1;
				}
                elseif(trim(strtolower($price_call)) == trim(strtolower($row->no_value)))
				{
					$price_call = 0;
				}
			}
		}


		//address
		$col_address = !empty($allFields['address']) ?  $allFields['address']->column_number : '';
		$col_address--;
		$address = $cell[$col_address];
		//region
		$col_region = !empty($allFields['region']) ?  $allFields['region']->column_number : '';
		$col_region--;
		$region = $cell[$col_region];
		//postcode
		$col_postcode = !empty($allFields['postcode']) ?  $allFields['postcode']->column_number : '';
		$col_postcode--;
		$postcode = $cell[$col_postcode];

		//price_call
		$col_show_address = !empty($allFields['show_address']) ?  $allFields['show_address']->column_number : '';
		$col_show_address--;
		$show_address = 0;

		if((int)$col_show_address >= 0)
		{
			$show_address = trim($cell[$col_show_address]);
			if($show_address != "")
			{
				if(trim(strtolower($show_address)) == trim(strtolower($row->yes_value)))
				{
					$show_address = 1;
				}
                elseif(trim(strtolower($show_address)) == trim(strtolower($row->no_value)))
				{
					$show_address = 0;
				}
			}
		}

		// price text
		$col_price_text = !empty($allFields['price_text']) ?  $allFields['price_text']->column_number : '';
		$col_price_text--;
		$price_text = $cell[$col_price_text];

		// Created
		$col_created = !empty($allFields['created']) ?  $allFields['created']->column_number : '';
		$col_created--;
		$created = $cell[$col_created];

		// Sold
		$col_sold = !empty($allFields['isSold']) ?  $allFields['isSold']->column_number : '';
		$isSold   = 0;
		if (!empty($col_sold)) {
			$col_sold--;
			$isSold = trim($cell[$col_sold]);

			if(trim(strtolower($isSold)) == trim(strtolower($row->yes_value))){
				$isSold = 1;
			}elseif(trim(strtolower($isSold)) == trim(strtolower($row->no_value))){
				$isSold = 0;
			}
		}

		// Sold on
		$col_metadesc = !empty($allFields['metadesc']) ?  $allFields['metadesc']->column_number : '';
		$metadesc = '';
		if(!empty($col_metadesc)) {
			$col_metadesc--;
			$metadesc = $cell[$col_metadesc];
		}

		// bed_room
		$col_bed_room = !empty($allFields['bed_room']) ?  $allFields['bed_room']->column_number : '';
		$bed_room     = '';
		if (!empty($col_bed_room)) {
			$col_bed_room--;
			$bed_room = $cell[$col_bed_room];
		}

		//bath_room
		$col_bath_room = !empty($allFields['bath_room']) ?  $allFields['bath_room']->column_number : '';
		$bath_room = '';
		if (!empty($col_bath_room)) {
			$col_bath_room--;
			$bath_room = $cell[$col_bath_room];
		}

		//rooms
		$col_rooms = !empty($allFields['rooms']) ?  $allFields['rooms']->column_number : '';
		$rooms = '';
		if (!empty($col_rooms)) {
			$col_rooms--;
			$rooms = $cell[$col_rooms];
		}

		//floors
		$col_number_of_floors = !empty($allFields['number_of_floors']) ?  $allFields['number_of_floors']->column_number : '';
		$number_of_floors     = 0;
		if(!empty($col_number_of_floors)) {
			$col_number_of_floors--;
			$number_of_floors = $cell[$col_number_of_floors];
		}

		//square_feet
		$col_square_feet = !empty($allFields['square_feet']) ?  $allFields['square_feet']->column_number : '';
		$square_feet     = '';
		if (!empty($col_square_feet)) {
			$col_square_feet--;
			$square_feet = $cell[$col_square_feet];
		}

		//photo
		$col_photo = !empty($allFields['photo']) ?  $allFields['photo']->column_number : '';
		$photo     = '';
		if (!empty($col_photo)) {
			$col_photo--;
			$photo = $cell[$col_photo];
		}

		//convenience
		$col_convenience = !empty($allFields['convenience']) ?  $allFields['convenience']->column_number : '';
		$convenience = '';
		if (!empty($col_convenience)) {
			$col_convenience--;
			$convenience = $cell[$col_convenience];
		}

		$col_lat = !empty($allFields['lat_add']) ?  $allFields['lat_add']->column_number : '';
		$lat_add = '';
		if (!empty($col_lat)) {
			$col_lat--;
			$lat_add = $cell[$col_lat];
		}

		$col_long   = !empty($allFields['long_add']) ?  $allFields['long_add']->column_number : '';
		$lang_add   = '';
		if (!empty($col_long)) {
			$col_long--;
			$long_add = $cell[$col_long];
		}

		$col_renttime = !empty($allFields['rent_time']) ?  $allFields['rent_time']->column_number : '';
		$rent_time = '';
		if (!empty($col_renttime)) {
			$col_renttime--;
			$rent_time = $cell[$col_renttime];
		}

		$col_lotsize = !empty($allFields['lot_size']) ?  $allFields['lot_size']->column_number : '';
		$lot_size    = '';
		if (!empty($col_lotsize)) {
			$col_lotsize--;
			$lot_size = $cell[$col_lotsize];
		}

		$col_parking = !empty($allFields['parking']) ?  $allFields['parking']->column_number : '';
		$parking     = '';
		if (!empty($col_parking)) {
			$col_parking--;
			$parking = $cell[$col_parking];
		}

		$col_published = !empty($allFields['published']) ?  $allFields['published']->column_number : '';
		$published     = '';
		if(!empty($col_published)) {
			$col_published--;
			$published = $cell[$col_published];
		}

		$col_energy = !empty($allFields['energy']) ?  $allFields['energy']->column_number : '';
		$energy     = '';
		if (!empty($col_energy)) {
			$col_energy--;
			$energy = $cell[$col_energy];
		}

		$col_climate = !empty($allFields['climate']) ?  $allFields['climate']->column_number : '';
		$climate     = '';
		if(!empty($col_climate)) {
			$col_climate--;
			$climate = $cell[$col_climate];
		}

		$col_pro_video = !empty($allFields['pro_video']) ?  $allFields['pro_video']->column_number : '';
		$pro_video     = '';
		if (!empty($col_pro_video)) {
			$col_pro_video--;
			$pro_video = $cell[$col_pro_video];
		}

		$col_pro_pdf = !empty($allFields['pro_pdf']) ?  $allFields['pro_pdf']->column_number : '';
		$pro_pdf  = '';
		if (!empty($col_pro_pdf)) {
			$col_pro_pdf--;
			$pro_pdf = $cell[$col_pro_pdf];
		}

		$col_featured = !empty($allFields['isFeatured']) ?  $allFields['isFeatured']->column_number : '';
		$featured     = 0;
		if(!empty($col_featured)) {
			$col_featured--;
			$featured = $cell[$col_featured];
		}

		$propertyId = 0;
		//create the data for property first
		$property = Table::getInstance('Property','OspropertyTable');
		$query = $db->getQuery(true);
		if($row->update_type == 0) {
			$query->select('count(id)')->from('#__osrs_properties')->where('ref like "'.$property_ref.'" and pro_name like "'.$property_name.'"');
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count > 0){
				$query->clear();
				$query->select('id')->from('#__osrs_properties')->where('ref like "'.$property_ref.'" and pro_name like "'.$property_name.'"');
				$db->setQuery($query);
				$propertyId = $db->loadResult();
			}
		}else{
			$query->select('count(id)')->from('#__osrs_properties')->where('ref like "'.$property_ref.'"');
			$db->setQuery($query);
			$count = $db->loadResult();
			if($count > 0){
				$query->clear();
				$query->select('id')->from('#__osrs_properties')->where('ref like "'.$property_ref.'"');
				$db->setQuery($query);
				$propertyId = $db->loadResult();
			}
		}
		$property->id = $propertyId;
		$property->ref = $property_ref;
		if($import_utf == 1){
			$property->pro_name = utf8_encode($property_name);
			$property->pro_small_desc = utf8_encode($small_desc);
			$property->pro_full_desc = utf8_encode($full_desc);
			$property->address = utf8_encode($address);
		}else{
			$property->pro_name = $property_name;
			$property->pro_small_desc = $small_desc;
			$property->pro_full_desc = $full_desc;
			$property->address = $address;
		}
		$property->pro_alias = $property->pro_name;
		$property->agent_id = $agent;
		$property->category_id = $category;
		$property->pro_type = $property_type;
		$property->price = (float)$price;
		$property->price_original = (float)$price_original;
		$property->price_text = $price_text;
		$property->rent_time = $rent_time;

		$property->city = $city;
		$property->show_address = $show_address;
		$property->price_call = $price_call;
		$property->region = $region;
		//$property->province= $province;
		$property->postcode = $postcode;
		if($created ==""){
			$property->created = date("Y-m-d",time());
		}else{
			$property->created = $created;
		}

		$property->approved = 1;
		$property->published = (int)$published;
		$property->bed_room = (int)$bed_room;
		$property->bath_room = (float)$bath_room;
		$property->rooms = (int)$rooms;
		$property->number_of_floors = (int)$number_of_floors;
		$property->parking = $parking;
		$property->square_feet = (float)$square_feet;
		$property->lot_size = (float) $lot_size;
		$property->request_to_approval = 0;
		$property->request_featured = 0;
		$property->note = $note;
		$property->state = (int)$state;
		$property->country = (int)$country;
		$property->isFeatured = (int)$featured;
		$property->hits = 0; // Todo we shouldn't be resetting hits on every import.
		$property->access = 1;
		$property->curr = (int)$curr;
		$property->lat_add = $lat_add;
		$property->long_add = $long_add;
		$property->energy = (float)$energy;
		$property->climate = (float)$climate;
		$property->pro_video = $pro_video;
		$property->pro_pdf = $pro_pdf;
		$admin = Factory::getUser();
		$property->created_by = $admin->id;
		$property->isSold = $isSold;
		$property->metadesc = $metadesc;
		$property->lot_size = (float) $property->lot_size;
		$property->number_of_floors = (int)$property->number_of_floors;
		for($i1 = 1; $i1<10; $i1++)
		{
			$property->{'pro_pdf_file'.$i1} = (string)$property->{'pro_pdf_file'.$i1};
		}
		$newfields = array('garage_description','house_style','house_construction','exterior_finish','roof','flooring','floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total','basement_foundation','basement_size','percent_finished','subdivision','land_holding_type','total_acres','lot_dimensions','frontpage','depth','takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities','fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');
		foreach($newfields as $field){
			$db->setQuery("Select column_number from #__osrs_form_fields where form_id = '$row->id' and `field` like '".$field."'");
			if(!empty($allFields[$field])) {
				$col_field = $allFields[$field]->column_number;
				$col_field--;
				$property->{$field} = (string)$cell[$col_field];
			}
		}
		$newfields = array('built_on','remodeled_on','living_areas');
		foreach($newfields as $field){
			$db->setQuery("Select column_number from #__osrs_form_fields where form_id = '$row->id' and `field` like '".$field."'");
			if(!empty($allFields[$field])) {
				$col_field = $allFields[$field]->column_number;
				$col_field--;
				$property->{$field} = (int)$cell[$col_field];
			}
		}

		if(!$property->store())
		{
			$error = Text::_('OS_ERROR_IMPORT');
			$error .= " - ".$property_name." - ".$property->getError();
			$isInsert = 0;
		}
		else
		{
			if($propertyId > 0){
				$pid = $propertyId;
				$db->setQuery("Delete from #__osrs_property_categories where pid = '$pid'");
				$db->execute();

				$db->setQuery("Delete from #__osrs_photos where pro_id = '$pid'");
				$db->execute();
			}
			else
			{
				$pid = $db->insertid();
			}
			$pid = (int) $pid;
			//update alias
			$alias = OSPHelper::generateAlias('property',$pid,$alias);
			$db->setQuery("Update #__osrs_properties set pro_alias = '$alias' where id = '$pid'");
			$db->execute();

			$error = "<strong><span color=\"#665A0C\">".$property_name."</span> - ID: <span color=\"red\">".$pid."</span></strong><BR>";
			$error .= "<strong>".Text::_('OS_ADDRESS')."</strong>: ".$property->address;
			$isInsert = 1;
			if(count($catIds) > 0){
				foreach ($catIds as $catid){
					$db->setQuery("Insert into #__osrs_property_categories (id,pid,category_id) values (NULL,'$pid','$catid')");
					$db->execute();
				}
			}

			//photo
			$photofrom = $jinput->getInt('photofrom',0);
			self::importPhoto($photo,$pid,$row);
			//convenience
			if($convenience != ""){
				self::importConvenience($convenience,$pid);
			}

			//extra fields
			$db->setQuery("Select * from #__osrs_extra_fields where published = '1'");
			$fields = $db->loadObjectList();
			if(count($fields) > 0){
				$array1 = array('text','textarea','date');
				$array2 = array('singleselect','radio');
				$array3 = array('multipleselect','checkbox');
				for($i=0;$i<count($fields);$i++){
					$field = $fields[$i];
					$field_id	= $field->id;
					//check field type
					$field_type = $field->field_type;
					$field_name = $field->field_name;
					if(empty($allFields[$field_name])) continue; // No field so skip
					$column_number = $allFields[$field_name]->column_number;
					$column_number = $column_number - 1;
					$field_value = $cell[$column_number];
					if($field_value != ""){
						if(in_array($field_type,$array1)){
							switch($field->value_type){
								case "0":
									$value_name = "value";
									break;
								case "1":
									$value_name = "value_integer";
									break;
								case "2":
									$value_name = "value_decimal";
									break;
							}

							if($field_type == "date"){
								$value_name = "value_date";
							}elseif($field_type == "textarea"){
								$value_name = "value";
							}

							$db->setQuery("Delete from #__osrs_property_field_value where pro_id = '$pid' and field_id = '$field_id'");
							$db->execute();

							if($import_utf == 1)
							{
								$db->setQuery("INSERT INTO #__osrs_property_field_value (id,pro_id,field_id,`".$value_name."`) VALUES (NULL,'$pid','$field_id','".utf8_encode(addslashes($field_value))."')");
							}
							else
							{
								$db->setQuery("INSERT INTO #__osrs_property_field_value (id,pro_id,field_id,`".$value_name."`) VALUES (NULL,'$pid','$field_id','".addslashes($field_value)."')");
							}
							$db->execute();
						}
						elseif(in_array($field_type,$array2))
						{
							$db->setQuery("Select `id`  from #__osrs_extra_field_options where field_id = '$field_id' and field_option like '%$field_value%'");
							$option_id = $db->loadResult();
							//update into database
							if($option_id > 0){
								$db->setQuery("INSERT INTO #__osrs_property_field_opt_value (id,pid,fid,oid) values (NULL,'$pid','$field_id','$option_id')");
								$db->execute();
							}
						}elseif(in_array($field_type,$array3)){
							$field_value_array = explode("|",$field_value);
							for($j=0;$j<count($field_value_array);$j++){
								$field_value = $field_value_array[$j];
								$db->setQuery("Select `id`  from #__osrs_extra_field_options where field_id = '$field_id' and field_option like '%$field_value%'");
								$option_id = $db->loadResult();
								//update into database
								if($option_id > 0){
									$db->setQuery("INSERT INTO #__osrs_property_field_opt_value (id,pid,fid,oid) values (NULL,'$pid','$field_id','$option_id')");
									$db->execute();
								}
							}
						}
					}
				}
			}
			//add into expired table
			if($configClass['general_use_expiration_management']==1){
				HelperOspropertyCommon::setExpiredTime($pid,'n',1);
			}

			//insert into import log property table
			$db->setQuery("INSERT INTO #__osrs_importlog_properties (id,form_id,pid) VALUES (NULL,'$row->id','".(int)$pid."')");
			$db->execute();
		}

		$return = [];
		$return[0] = new \stdClass();
		$return[0]->error = $error;
		$return[0]->isInsert = $isInsert;

		return $return;
	}

	/**
	 * Import convenience
	 *
	 * @param unknown_type $convenience
	 * @param unknown_type $pid
	 */
	public static function importConvenience($convenience,$pid){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		if($convenience != ""){
			$convenienceArr = explode("|",$convenience);
			if(count($convenienceArr) > 0){
				for($i=0;$i<count($convenienceArr);$i++){
					$con = $convenienceArr[$i];
					$con = trim($con);
					if($con != ""){
						$db->setQuery("SELECT COUNT(id) FROM #__osrs_amenities WHERE amenities LIKE '".$con."'");
						$count_convenience = $db->loadResult();
						if(intval($count_convenience) == 0){
							$db->setQuery("INSERT INTO #__osrs_amenities (id, amenities, published ) VALUES (NULL,'$con','1')");
							$db->execute();
							$amen_id = $db->insertid();
						}else{
							$db->setQuery("SELECT id FROM #__osrs_amenities WHERE amenities LIKE '$con'");
							$amen_id = $db->loadResult();
						}
						//insert into #__osrs_property_amenities
						$db->setQuery("Select count(id) from #__osrs_property_amenities where pro_id = '$pid' and amen_id = '$amen_id'");
						$count = $db->loadResult();
						if($count == 0)
						{
							$db->setQuery("INSERT INTO #__osrs_property_amenities (id,pro_id,amen_id) VALUES (NULL,'$pid','$amen_id')");
							$db->execute();
						}
					}
				}
			}
		}
	}

	/**
	 * Import photo
	 *
	 * @param unknown_type $photo
	 */
	public static function importPhoto($photo,$pid,$row)
	{
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		if($photo != "")
		{
			$photoArr = explode("|",$photo);
			if(count($photoArr) > 0)		{
				for($i=0;$i<count($photoArr);$i++){
					$photo = trim($photoArr[$i]);
					if($row->image_type == 0) {
						//insert into photos table
						$db->setQuery("INSERT INTO #__osrs_photos (id,pro_id,image,ordering) VALUES (NULL,'$pid','$photo','$i')");
						$db->execute();
					}else{
						self::importPhotoFromOtherServer($photo,$pid, $i);
					}
				}
			}
		}
		if($row->image_type == 1) {
			OSPHelper::generateWaterMark($pid);
		}
	}


	static function importPhotoFromOtherServer($photo_url,$pid, $i)
	{
		global $configClass;
		if (!Folder::exists(JPATH_ROOT ."/images/osproperty/properties/". $pid)) {
			Folder::create(JPATH_ROOT ."/images/osproperty/properties/". $pid);
			Folder::create(JPATH_ROOT ."/images/osproperty/properties/". $pid ."/thumb");
			Folder::create(JPATH_ROOT ."/images/osproperty/properties/". $pid ."/medium");
			File::copy(JPATH_ADMINISTRATOR . '/components/com_osproperty/index.html', JPATH_ROOT ."/images/osproperty/properties/". $pid . "/index.html");
			File::copy(JPATH_ADMINISTRATOR . '/components/com_osproperty/index.html', JPATH_ROOT ."/images/osproperty/properties/". $pid ."/thumb/index.html");
			File::copy(JPATH_ADMINISTRATOR . '/components/com_osproperty/index.html', JPATH_ROOT ."/images/osproperty/properties/". $pid ."/medium/index.html");
		}
		$real_path_picture = JPATH_ROOT ."/images/osproperty/properties/". $pid . "/";
		//get file
		$photo_name = trim(pathinfo($photo_url, PATHINFO_BASENAME));
		$image_available = 0;
		$lfile = fopen($real_path_picture . $photo_name, "w");

		if (is_callable('curl_init')) {
			$picObj = OSPHelper::getImageFromUrl($photo_url);
			fwrite($lfile, $picObj);
			fclose($lfile);
		} else {
			$content = file_get_contents($photo_url);
			$fp = fopen($lfile, "w");
			fwrite($fp, $content);
			fclose($fp);
		}

		File::copy($real_path_picture . $photo_name, $real_path_picture . 'medium/' . $photo_name);
		File::copy($real_path_picture . $photo_name, $real_path_picture . 'thumb/' . $photo_name);
		//resize pictures
		$medium_width = $configClass['images_large_width'];
		$medium_height = $configClass['images_large_height'];
		// copy($original_image_link.DS.$photo['image'],$medium_image_link.DS.$photo['image']);
		OSPHelper::resizePhoto($real_path_picture . 'medium/' . $photo_name, $medium_width, $medium_height);
		$thumb_width = $configClass['images_thumbnail_width'];
		$thumb_height = $configClass['images_thumbnail_height'];
		//copy($original_image_link.DS.$photo['image'],$thumb_image_link.DS.$photo['image']);
		OSPHelper::resizePhoto($real_path_picture . 'thumb/' . $photo_name, $thumb_width, $thumb_height);

		$photorecord				= Table::getInstance('Photo', 'OspropertyTable');
		$photorecord->id			= 0;
		$photorecord->pro_id		= $pid;
		$photorecord->image			= $photo_name;
		$photorecord->ordering		= (int) $i;
		$photorecord->image_desc	= '';
		$photorecord->store();
		//$photo_id = $db->insertID();
		//Update watermark
		//OSPHelper::generateWaterMark($pid);
	}

	/**
	 * Save photo
	 *
	 * @param unknown_type $photolink
	 */
	public static function savePhoto($photolink){
		global $jinput, $configClass;
		if(self::_iscurlinstalled()){
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $photolink);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
			$fileContents = curl_exec($ch);
			curl_close($ch);
		}else{
			$fileContents = file_get_contents($photolink);
		}
		$newImg = imagecreatefromstring($fileContents);
		$photolinkArr = explode("/",$photolink);
		$photoname = $photolinkArr[count($photolinkArr)-1];
		@imagejpeg($newImg,JPATH_ROOT."/components/com_osproperty/images/properties/".$photoname);
		//resize image
		$original_image_link = JPATH_ROOT."/components/com_osproperty/images/properties/".$photoname;
		//copy and resize
		//thumb
		$thumb_width = $configClass['images_thumbnail_width'];
		$thumb_height = $configClass['images_thumbnail_height'];

		$thumb_image_link = JPATH_ROOT."/components/com_osproperty/images/properties/thumb/".$photoname;
		@copy($original_image_link,$thumb_image_link);

		$thumb_image = getimagesize($thumb_image_link);
		$original_thumb_height = $thumb_image[1];
		$original_thumb_width = $thumb_image[0];

		if(($original_thumb_width > $thumb_width) and ($original_thumb_height > $thumb_height)){
			$resize_width = $thumb_width;
			$resize_height = $thumb_height;
		}else if(($original_thumb_width > $thumb_width) and ($original_thumb_height < $thumb_height)){
			$resize_width = $thumb_width;
			$resize_height = $original_thumb_height;
		}else if(($original_thumb_width < $thumb_width) and ($original_thumb_height > $thumb_height)){
			$resize_width = $original_thumb_width;
			$resize_height = $thumb_height;
		}else{
			$resize_width = $original_thumb_width;
			$resize_height = $original_thumb_height;
		}

		$image = new SimpleImage();
		$image->load($thumb_image_link);
		$image->resize($resize_width,$resize_height);
		$image->save($thumb_image_link);

		//medium
		$medium_width = $configClass['images_large_width'];
		$medium_height = $configClass['images_large_height'];

		$medium_image_link = JPATH_ROOT."/components/com_osproperty/images/properties/medium/".$photoname;
		@copy($original_image_link,$medium_image_link);
		$medium_image = getimagesize($medium_image_link);
		$original_medium_width = $medium_image[0];
		$original_medium_height = $medium_image[1];

		if(($original_medium_width > $medium_width) and ($original_medium_height > $medium_height)){
			$resize_width = $medium_width;
			$resize_height = $medium_height;
		}else if(($original_medium_width > $medium_width) and ($original_medium_height < $medium_height)){
			$resize_width = $medium_width;
			$resize_height = $original_medium_height;
		}else if(($original_medium_width < $medium_width) and ($original_medium_height > $medium_height)){
			$resize_width = $original_medium_width;
			$resize_height = $medium_height;
		}else{
			$resize_width = $original_medium_width;
			$resize_height = $original_medium_height;
		}

		$image = new SimpleImage();
		$image->load($medium_image_link);
		$image->resize($resize_width,$resize_height);
		$image->save($medium_image_link);


		return $photoname;
	}

	/**
	 * Check curl existing
	 *
	 * @return unknown
	 */
	public static function _iscurlinstalled() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else{
			return false;
		}
	}

	/**
	 * Download CSV
	 *
	 * @param unknown_type $option
	 */
	public static function downloadCsv($option,$id){
		global $jinput, $mainframe;
		jimport('joomla.filesystem.file');
		$db = Factory::getDbo();
		$csv_absoluted_link = JPATH_ROOT."/images/osproperty/csv".$id.".csv";

		if(File::exists($csv_absoluted_link)){
			HelperOspropertyCommon::downloadfile2($csv_absoluted_link,$id);
		}
	}

	/**
	 * Default CSV form lists
	 *
	 * @param unknown_type $option
	 */
	public static function defaultList($option){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$limit = $jinput->getInt('limit',20);
		$limitstart = $jinput->getInt('limitstart',0);

		$query = "Select count(id) from #__osrs_csv_forms";
		$db->setQuery($query);
		$count = $db->loadResult();

		jimport('joomla.html.pagination');
		$pageNav = new Pagination($count,$limitstart,$limit);

		$query = "Select * from #__osrs_csv_forms";
		$db->setQuery($query,$pageNav->limitstart,$pageNav->limit);
		$rows = $db->loadObjectList();
		HTML_OspropertyCsvform::defaultList($option,$rows,$pageNav);
	}

	/**
	 * Edit form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 */
	public static function editForm($option,$id){
		global $jinput, $mainframe;
		$db = Factory::getDbo();
		$row = Table::getInstance('Csvform','OspropertyTable');
		if($id > 0){
			$row->load((int)$id);
		}else{
			$row->published = 1;
		}
		// creat published
		$lists['published']   = OSPHelper::getBooleanInput('published',$row->published);

		$lists['active_cron_import']   = OSPHelper::getBooleanInput('active_cron_import',$row->active_cron_import);


		$fields = array('ref','pro_name','pro_alias','agent_id','pro_type','category_id','country','state','city','region','price','price_original','price_text','rent_time','curr','price_call','show_address','pro_small_desc','pro_full_desc','note','address','postcode','lat_add','long_add','bed_room','bath_room','rooms','square_feet','number_of_floors','photo','convenience','parking','lot_size','published','energy','climate','pro_video','pro_pdf','created','isSold','metadesc','isFeatured');

		$labels = [];
		$labels[] = Text::_('Ref #');
		$labels[] = Text::_('OS_PROPERTY_NAME');
		$labels[] = Text::_('OS_ALIAS');
		$labels[] = Text::_('OS_USER');
		$labels[] = Text::_('OS_PROPERTY_TYPE');
		$labels[] = Text::_('OS_CATEGORY');
		$labels[] = Text::_('OS_COUNTRY');
		$labels[] = Text::_('OS_STATE');
		$labels[] = Text::_('OS_CITY');
		$labels[] = Text::_('OS_REGION');
		$labels[] = Text::_('OS_MARKET_PRICE');
		$labels[] = Text::_('OS_ORIGINAL_PRICE');
		$labels[] = Text::_('OS_PRICE_TEXT');
		$labels[] = Text::_('OS_RENT_TIME_FRAME');
		$labels[] = Text::_('OS_CURRENCY');
		$labels[] = Text::_('OS_CALL_FOR_PRICE');
		$labels[] = Text::_('OS_SHOW_ADDRESS');
		$labels[] = Text::_('OS_SMALL_DESCRIPTION');
		$labels[] = Text::_('OS_FULL_DESCRIPTION');
		$labels[] = Text::_('OS_AGENT_NOTE');
		$labels[] = Text::_('OS_ADDRESS');
		$labels[] = Text::_('OS_POSTCODE');
		$labels[] = Text::_('OS_LATITUDE');
		$labels[] = Text::_('OS_LONGTITUDE');
		$labels[] = Text::_('OS_NUMBER_BEDROOMS');
		$labels[] = Text::_('OS_NUMBER_BATHROOMS');
		$labels[] = Text::_('OS_NUMBER_ROOMS');
		$labels[] = Text::_('OS_SQUARE_FEET');
		$labels[] = Text::_('OS_NUMBER_OF_FLOORS');
		$labels[] = Text::_('OS_PHOTOS');
		$labels[] = Text::_('OS_CONVENIENCE');
		$labels[] = Text::_('OS_PARKING');
		$labels[] = Text::_('OS_LOT_SIZE');
		$labels[] = Text::_('OS_PUBLISHED');
		$labels[] = Text::_('OS_ENERGY');
		$labels[] = Text::_('OS_CLIMATE');
		$labels[] = Text::_('OS_VIDEO_EMBED_CODE');
		$labels[] = Text::_('OS_DOCUMENT_LINK');
		$labels[] = Text::_('OS_CREATED');
		$labels[] = Text::_('OS_MARKET_STATUS');
		$labels[] = Text::_('OS_META_DESCRIPTION');
		$labels[] = Text::_('OS_ISFEATURED');

		$newfields = array('living_areas','garage_description','built_on','remodeled_on','house_style','house_construction','exterior_finish','roof','flooring','floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total','basement_foundation','basement_size','percent_finished','subdivision','land_holding_type','total_acres','lot_dimensions','frontpage','depth','takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities','fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');

		foreach($newfields as $newfield){
			$fields[] = $newfield;
			$labels[] = Text::_('OS_'.strtoupper($newfield));
		}

		$db->setQuery("Select a.field_name, a.field_label from #__osrs_extra_fields as a inner join #__osrs_fieldgroups as b on b.id = a.group_id where a.published = '1' and b.published = 1 order by a.group_id, a.ordering");
		$rows = $db->loadObjectList();
		if(count($rows) > 0){
			for($i=0;$i<count($rows);$i++){
				$rs = $rows[$i];
				$fields[] = $rs->field_name;
				$labels[] = $rs->field_label;
			}
		}
		$requirefieldArr = [];
		$requirelabelArr = [];
		$temp = [];

		$required_fields = array(1,2,3,4,5,6,7);

		for($i=0;$i<count($required_fields);$i++){
			$requirefieldArr[] = $fields[$required_fields[$i]];
			$requirelabelArr[] = $labels[$required_fields[$i]];
		}

		$lists['requirefields'] = $requirefieldArr;
		$lists['requirelabels'] = $requirelabelArr;
		$lists['requireid']     = $required_fields;
		$lists['fields'] 		= $fields;
		$lists['labels']		= $labels;

		$typeArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_PROPERTY_TYPE'));
		$db->setQuery("Select id as value,type_name as text from #__osrs_types where published = '1' order by type_name");
		$protypes = $db->loadObjectList();
		$typeArr   = array_merge($typeArr,$protypes);
		$lists['type'] = HTMLHelper::_('select.genericlist',$typeArr,'type_id','class="input-large form-select" style="width:250px;"','value','text',$row->type_id);
		//categories
		$lists['category'] = OspropertyProperties::listCategories($row->category_id,'');
		//agent
		$agentArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT_AGENT'));
		$query  = "Select a.id as value,a.name as text from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where published = '1' ";
		$query .= " order by a.name";
		$db->setQuery($query);
		$agents = $db->loadObjectList();
		$agentArr   = array_merge($agentArr,$agents);
		$lists['agent'] = HTMLHelper::_('select.genericlist',$agentArr,'agent_id','class="input-large form-select" style="width:250px;"','value','text',$row->agent_id);

		$lists['country'] = HelperOspropertyCommon::makeCountryList($row->country,'country','','','');
		HTML_OspropertyCsvform::editHTML($option,$row,$lists);
	}

	/**
	 * Get state input
	 *
	 * @param unknown_type $state
	 * @return unknown
	 */
	public static function getStateInput($state){
		// Initialize variables.
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/jquery.php';
		$html = [];
		//$groups = $this->getGroups();
		//$excluded = $this->getExcluded();
		$link = 'index.php?option=com_osproperty&amp;task=state_list&amp;tmpl=component&amp;modal=1';

		// Initialize some field attributes.
		$attr = ' class="input-small form-control"';

		// Initialize JavaScript field attributes.
		//$onchange = (string) $this->element['onchange'];

		// Load the modal behavior script.
		if(!OSPHelper::isJoomla4())
		{
			HTMLHelper::_('behavior.modal','a.modalState');
		
			// Build the script.
			$script = [];
			$script[] = '	function jSelectState_state(id, title) {';
			$script[] = '		var old_id = document.getElementById("state").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("state").value = id;';
			$script[] = '			document.getElementById("state_name").value = title;';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';
			
		}
		else
		{
			OSPHelperJquery::colorbox('modalState');
			// Build the script.
			$script   = [];
			$script[] = '	function jSelectState_state(id, title) {';
			$script[] = '		var old_id = document.getElementById("state").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("state").value = id;';
			$script[] = '			document.getElementById("state_name").value = title;';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		parent.jQuery.colorbox.close(); return false;';
			$script[] = '	}';
		}

		

		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		$table = Table::getInstance('State','OspropertyTable');

		if ($state)
		{
			$table->load($state);
		}
		else
		{
			$table->username = Text::_('OS_SELECT_STATE');
		}

		// Create a dummy text field with the user name.
		$html[] = '<span class="input-append input-group">';
		$html[] = '<input type="text" class="input-small form-control" id="state_name" value="'.htmlspecialchars($table->state_name, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modalState btn btn-secondary" title="'.Text::_('OS_CHANGE_STATE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.Text::_('OS_CHANGE_STATE').'</a>';
		$html[] = '</span>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="state" name="state" value="'.$state.'" />';

		return implode("\n", $html);
	}

	/**
	 * Get state input
	 *
	 * @param unknown_type $city
	 * @return unknown
	 */
	public static function getCityInput($city)
	{
		// Initialize variables.
		$html = [];
		//$groups = $this->getGroups();
		//$excluded = $this->getExcluded();
		$link = 'index.php?option=com_osproperty&amp;task=city_list&amp;tmpl=component&amp;modal=1';

		// Initialize some field attributes.
		$attr = ' class="input-small"';

		// Initialize JavaScript field attributes.
		//$onchange = (string) $this->element['onchange'];

		// Load the modal behavior script.
		if(!OSPHelper::isJoomla4())
		{
			HTMLHelper::_('behavior.modal','a.modalCity');
		
			// Build the script.
			$script = [];
			$script[] = '	function jSelectCity_city(id, title) {';
			$script[] = '		var old_id = document.getElementById("city").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("city").value = id;';
			$script[] = '			document.getElementById("city_name").value = title;';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		SqueezeBox.close();';
			$script[] = '	}';
			
		}
		else
		{
			OSPHelperJquery::colorbox('modalCity');
			// Build the script.
			$script = [];
			$script[] = '	function jSelectCity_city(id, title) { ';
			$script[] = '		var old_id = document.getElementById("city").value;';
			$script[] = '		if (old_id != id) {';
			$script[] = '			document.getElementById("city").value = id;';
			$script[] = '			document.getElementById("city_name").value = title;';
			$script[] = '			' . $onchange;
			$script[] = '		}';
			$script[] = '		parent.jQuery.colorbox.close(); return false;';
			$script[] = '	}';
		}
		// Add the script to the document head.
		Factory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current username if available.
		$table = Table::getInstance('City','OspropertyTable');

		if ($city)
		{
			$table->load($city);
		}
		else
		{
			$table->username = Text::_('OS_SELECT_CITY');
		}

		// Create a dummy text field with the user name.
		$html[] = '<span class="input-append input-group">';
		$html[] = '<input type="text" class="input-small form-control" id="city_name" value="'.htmlspecialchars($table->city, ENT_COMPAT, 'UTF-8') .'" disabled="disabled" size="35" /><a class="modalCity btn btn-secondary" title="'.Text::_('OS_CHANGE_CITY').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 800, y: 450}}"><i class="icon-file"></i> '.Text::_('OS_CHANGE_CITY').'</a>';
		$html[] = '</span>';

		// Create the real field, hidden, that stored the user id.
		$html[] = '<input type="hidden" id="city" name="city" value="'.$city.'" />';

		return implode("\n", $html);
	}

	/**
	 * Save form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $save
	 */
	public static function save($option,$save){

		global $jinput, $mainframe,$configClass;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.file');
		$fields = array('ref','pro_name','pro_alias','agent_id','pro_type','category_id','country','state','city','region','price','price_original','price_text','rent_time','curr','price_call','show_address','pro_small_desc','pro_full_desc','note','address','postcode','lat_add','long_add','bed_room','bath_room','rooms','square_feet','number_of_floors','photo','convenience','parking','lot_size','published','energy','climate','pro_video','pro_pdf','created','isSold','metadesc','isFeatured');

		$labels = [];
		$labels[] = Text::_('Ref #');
		$labels[] = Text::_('OS_PROPERTY_NAME');
		$labels[] = Text::_('OS_ALIAS');
		$labels[] = Text::_('OS_USER');
		$labels[] = Text::_('OS_PROPERTY_TYPE');
		$labels[] = Text::_('OS_CATEGORY');
		$labels[] = Text::_('OS_COUNTRY');
		$labels[] = Text::_('OS_STATE');
		$labels[] = Text::_('OS_CITY');
		$labels[] = Text::_('OS_REGION');
		$labels[] = Text::_('OS_MARKET_PRICE');
		$labels[] = Text::_('OS_ORIGINAL_PRICE');
		$labels[] = Text::_('OS_PRICE_TEXT');
		$labels[] = Text::_('OS_RENT_TIME_FRAME');
		$labels[] = Text::_('OS_CURRENCY');
		$labels[] = Text::_('OS_CALL_FOR_PRICE');
		$labels[] = Text::_('OS_SHOW_ADDRESS');
		$labels[] = Text::_('OS_SMALL_DESCRIPTION');
		$labels[] = Text::_('OS_FULL_DESCRIPTION');
		$labels[] = Text::_('OS_AGENT_NOTE');
		$labels[] = Text::_('OS_ADDRESS');
		$labels[] = Text::_('OS_POSTCODE');
		$labels[] = Text::_('OS_LATITUDE');
		$labels[] = Text::_('OS_LONGTITUDE');
		$labels[] = Text::_('OS_NUMBER_BEDROOMS');
		$labels[] = Text::_('OS_NUMBER_BATHROOMS');
		$labels[] = Text::_('OS_NUMBER_ROOMS');
		$labels[] = Text::_('OS_SQUARE_FEET');
		$labels[] = Text::_('OS_NUMBER_OF_FLOORS');
		$labels[] = Text::_('OS_PHOTOS');
		$labels[] = Text::_('OS_CONVENIENCE');
		$labels[] = Text::_('OS_PARKING');
		$labels[] = Text::_('OS_LOT_SIZE');
		$labels[] = Text::_('OS_PUBLISHED');
		$labels[] = Text::_('OS_ENERGY');
		$labels[] = Text::_('OS_CLIMATE');
		$labels[] = Text::_('OS_VIDEO_EMBED_CODE');
		$labels[] = Text::_('OS_DOCUMENT_LINK');
		$labels[] = Text::_('OS_CREATED');
		$labels[] = Text::_('OS_MARKET_STATUS');
		$labels[] = Text::_('OS_META_DESCRIPTION');
		$labels[] = Text::_('OS_ISFEATURED');


		$newfields = array('living_areas','garage_description','built_on','remodeled_on','house_style','house_construction','exterior_finish','roof','flooring','floor_area_lower','floor_area_main_level','floor_area_upper','floor_area_total','basement_foundation','basement_size','percent_finished','subdivision','land_holding_type','total_acres','lot_dimensions','frontpage','depth','takings','returns','net_profit','business_type','stock','fixtures','fittings','percent_office','percent_warehouse','loading_facilities','fencing','rainfall','soil_type','grazing','cropping','irrigation','water_resources','carrying_capacity','storage');

		foreach($newfields as $newfield){
			$fields[] = $newfield;
			$labels[] = Text::_('OS_'.strtoupper($newfield));
		}

		$row = Table::getInstance('Csvform','OspropertyTable');
		$id = $jinput->getInt('id',0);

		$post = $jinput->post->getArray();
		$row->bind($post);
		if($id == 0)
		{
			$row->created_on = date("Y-m-d H:i:s",time());
		}
		$row->max_file_size = (int) $row->max_file_size;

		$row->type_id  = (int) $row->type_id ;
		$row->fcategory  = (int) $row->fcategory ;
		$row->category_id  = (int) $row->category_id ;
		$row->agent_id  = (int) $row->agent_id ;
		$row->country  = (int) $row->country ;
		$row->fstate  = (int) $row->fstate ;
		$row->state  = (int) $row->state ;
		$row->fcity  = (int) $row->fcity ;
		$row->ftype  = (int) $row->ftype ;
		$row->city  = (int) $row->city ;
		$row->image_type  = (int) $row->image_type ;

		if (!$row->store()) 
		{
			throw new Exception($row->getError(), 500);
		}
		if($id == 0)
		{
			$id = $db->insertID();
		}
		$db->setQuery("Delete from #__osrs_form_fields where form_id = '$id'");
		$db->execute();

		$csv_content = '';
		for($i=1;$i<=50;$i++){
			$fieldname = "fields".$i;
			$fieldvalue = $jinput->getString($fieldname,'');
			$headername = "header".$i;
			$headervalue = $jinput->getString($headername,'');
			if($headervalue != ""){
				$field_type = "header";
				$csv_content .= '"'.$headervalue.'"'.$configClass['csv_seperator'];
			}elseif($fieldvalue != ""){
				if(in_array($fieldvalue,$fields)){
					$field_type = "property";
					$key = array_search($fieldvalue,$fields);
					$csv_content .= '"'.$labels[$key].'"'.$configClass['csv_seperator'];
				}else{
					$field_type = "extra";
					$db->setQuery("Select field_label from #__osrs_extra_fields where field_name like '$fieldvalue'");
					$field_label = $db->loadResult();
					$csv_content .= '"'.$field_label.'"'.$configClass['csv_seperator'];
				}
			}
			if(($fieldvalue != "") or ($headervalue != "")){
				$db->setQuery("INSERT INTO #__osrs_form_fields (id,form_id,column_number,`field`,header_text,field_type) VALUES (NULL,'$id','$i','$fieldvalue','$headervalue','$field_type')");
				$db->execute();
			}
		}
		if($csv_content != ""){
			$csv_content = substr($csv_content,0,strlen($csv_content)-1);
		}
		$csv_content .= '';
		//echo $csv_content;
		//create the csv file
		$csv_absoluted_link = JPATH_ROOT."/images/osproperty/csv".$id.".csv";
		//create the content of csv
		$csvf = fopen($csv_absoluted_link,'w');
		@fwrite($csvf,$csv_content);
		@fclose($csvf);
		$mainframe->enqueueMessage(Text::_('OS_ITEM_SAVED'));
		if($save == 1)
		{
			$mainframe->redirect("index.php?option=com_osproperty&task=form_default");
		}
		else
		{
			$mainframe->redirect("index.php?option=com_osproperty&task=form_default&task=form_edit&cid[]=$id");
		}
	}

	/**
	 * Remove forms
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 */
	public static function removeForms($option,$cid){
		global $jinput, $mainframe;
		jimport('joomla.filesystem.file');
		$db = Factory::getDbo();
		$cids = implode(",",$cid);
		$db->setQuery("DELETE FROM #__osrs_csv_forms WHERE id IN ($cids)");
		$db->execute();
		$db->setQuery("DELETE FROM #__osrs_form_fields WHERE form_id IN ($cids)");
		$db->execute();

		for($i=0;$i<count($cid);$i++){
			$csv_absoluted_link = JPATH_ROOT."/images/osproperty/csv".$cid[$i].".csv";
			File::delete($csv_absoluted_link);
		}
		$mainframe->enqueueMessage(Text::_('OS_ITEM_HAS_BEEN_DELETED'));
		$mainframe->redirect("index.php?option=com_osproperty&task=form_default");
	}


	/**
	 * Change state
	 *
	 * @param unknown_type $option
	 * @param unknown_type $cid
	 * @param unknown_type $state
	 */
	public static function form_change_publish($option,$cid,$state){
		global $jinput, $mainframe;
		$db = Factory::getDBO();
		if(count($cid)>0)	{
			$cids = implode(",",$cid);
			$db->setQuery("UPDATE #__osrs_csv_forms SET `published` = '$state' WHERE id IN ($cids)");
			$db->execute();
		}
		$mainframe->enqueueMessage(Text::_('OS_ITEM_STATUS_HAS_BEEN_CHANGED'));
		$mainframe->redirect("index.php?option=$option&task=form_default");
	}


	public static function checkCsvValue($value){
		return str_replace("#","",$value);
	}
}

?>
