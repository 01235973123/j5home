<?php
/*------------------------------------------------------------------------
# ajax.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2025 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Updater\Updater;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;



class OspropertyAjax
{
    /**
     * Ajax default function
     *
     * @param unknown_type $option
     * @param unknown_type $task
     */
    static function display($option, $task)
    {
        global $jinput, $mainframe;
		$jinput				= Factory::getApplication()->input;
        $db					= Factory::getDBO();
        $id					= $jinput->getInt('id', 0);
        $user				= Factory::getUser();
		$lang				= $jinput->getString('lang','');
		if(strpos($lang, "-") > 0)
		{
			$lang			= substr($lang, 0, 2);
		}
		if($lang == OSPHelper::getDefaultLanguageTag())
		{
			$lang			= "";
		}
        switch ($task) {
            case "ajax_addFavorites":
                self::addFavorites($id);
				exit();
                break;
            case "ajax_removeFavorites":
                self::removeFavorites($id);
				exit();
                break;
            case "ajax_removeCompare":
                self::removeCompare($id);
				exit();
            break;
            case "ajax_addCompare":
                self::addCompare($id);
				exit();
                break;
            case "ajax_checkcouponcode":
                OspropertyAjax::checkcouponcode($option);
                break;
            case "ajax_loadStateInListPage":
                OspropertyAjax::loadStateInListPage($option);
                break;
            case "ajax_loadStateBackend":
                OspropertyAjax::loadStateBackend($option);
                break;
            case "ajax_loadCityBackend":
                OspropertyAjax::loadCityBackend($option);
                break;
            case "ajax_agentsearch":
                OspropertyAjax::agentSearch($option);
                break;
            case "ajax_searchagentforaddtocompany":
                OspropertyAjax::searchAgentforaddtocompany($option);
                break;
            case "ajax_loadstatecity":
                $country_name		= OSPHelper::getStringRequest('country_name','','get');
                $country_id			= $jinput->getInt('country_id', '0');
                $state_name			= OSPHelper::getStringRequest('state_name','','get');
                $state_id			= $jinput->getInt('state_id', '0');
                $city_id			= $jinput->getInt('city_id');
				
                OspropertyAjax::loadStateCity($option, $country_name, $country_id, $state_id, $city_id, $state_name,'input-large form-select',$lang);
                break;
            case "ajax_loadstatecityBackend":
                $country_name = OSPHelper::getStringRequest('country_name','','get');
                $country_id = $jinput->getInt('country_id', '0');
                $state_name = OSPHelper::getStringRequest('state_name','','get');
                $state_id = $jinput->getInt('state_id', '0');
                $city_id = $jinput->getInt('city_id');
                OspropertyAjax::loadStateCityBackend($option, $country_name, $country_id, $state_id, $city_id, $state_name,'input-large form-control form-select');
                break;
            case "ajax_loadstatecityArr":
                $country_name = OSPHelper::getStringRequest('country_name','','get');
                $country_id = $jinput->getInt('country_id', '0');
                $state_name = OSPHelper::getStringRequest('state_name','','get');
                $state_id = $jinput->getInt('state_id', '0');
                $city_id = $jinput->getInt('city_id');
                OspropertyAjax::loadStateCity($option, $country_name, $country_id, $state_id, $city_id, $state_name);
                break;
            case "ajax_loadstatecitylocatorModule":
                $country_name		= OSPHelper::getStringRequest('country_name','','get');
                $country_id			= $jinput->getInt('country_id', '0');
                $state_name			= OSPHelper::getStringRequest('state_name','','get');
                $state_id			= $jinput->getInt('state_id', '0');
                $city_id			= $jinput->getInt('city_id');
                $random_id			= $jinput->getInt('random_id', '0');
				
				
                OspropertyAjax::loadStateCityLocatorModule($option, $country_name, $country_id, $state_id, $city_id, $state_name, $random_id, $lang);
                break;
            case "ajax_loadstatecitylocator":
                $country_name		= OSPHelper::getStringRequest('country_name','','get');
                $country_id			= $jinput->getInt('country_id', '0');
                $state_name			= OSPHelper::getStringRequest('state_name','','get');
                $state_id			= $jinput->getInt('state_id', '0');
                $city_id			= $jinput->getInt('city_id');
                $class				= OSPHelper::getStringRequest('class', 'input-medium form-select form-control');
                OspropertyAjax::loadStateCityLocator($option, $country_name, $country_id, $state_id, $city_id, $state_name, $class, $lang);
                break;
            case "ajax_loadcityModule":
                $state_name			= OSPHelper::getStringRequest('state_name','','get');
                $state_id			= $jinput->getInt('state_id', '0');
                $city_id			= $jinput->getInt('city_id');
                $random_id			= OSPHelper::getStringRequest('random_id', '','get');
				
                OspropertyAjax::loadCityModule($option, $state_id, $city_id, $state_name, $random_id, $lang);
                break;
            case "ajax_loadcity":
                $state_name			= OSPHelper::getStringRequest('state_name','','get');
                $state_id			= $jinput->getInt('state_id', '0');
                $city_id			= $jinput->getInt('city_id');
                $useConfig			= $jinput->getInt('useConfig', 1);
                $class				= OSPHelper::getStringRequest('class', 'input-medium form-select ilarge','get');
				
                $city_name			= OSPHelper::getStringRequest('city_name', 'city','get');
                OspropertyAjax::loadCity($option, $state_id, $city_id, $state_name, $useConfig, $class, $city_name,$lang);
                break;
            case "ajax_loadcityAddProperty":
                $state_name			= OSPHelper::getStringRequest('state_name','','get');
                $state_id			= $jinput->getInt('state_id', '0');
                $city_id			= $jinput->getInt('city_id');
				
				$city_name			= OSPHelper::getStringRequest('city_name', 'city','get');
                OspropertyAjax::loadCity($option, $state_id, $city_id, $state_name, 0, 'input-large form-select ilarge', $city_name, $lang);
                break;
			case "ajax_loadAgentDropDown":
				$company_id = $jinput->getInt('company_id',0);
				self::loadAgentDropdown($company_id);
				break;
            case "ajax_convertCurrency":
                OspropertyAjax::convertCurrency($option);
                break;
			case "ajax_updateCurrency":
				 OspropertyAjax::updateCurrency($option);
				break;
            case "ajax_loadPriceListOption":
                $property_type = $jinput->getInt('property_type', 0);
                echo HelperOspropertyCommon::generatePriceList($property_type, $price);
                exit();
                break;
            case "ajax_updatePrice":
                $type_id = $jinput->getInt('type_id',0);
                $option_id = $jinput->getInt('option_id',0);
                $min_price = $jinput->getFloat('min_price','');
                $max_price = $jinput->getFloat('max_price','');
                $module_id = $jinput->get('module_id','');
                OSPHelper::showPriceFilter($option_id,$min_price,$max_price,$type_id,'input-medium form-select form-control ilarge',$module_id);
				$mainframe->close();
                break;
            case "ajax_availabilitysearch":
                OspropertyAjax::ajaxsearch();
                break;
            case "ajax_loadLocationInformation":
                OspropertyAjax::loadLocationInformation();
                break;
            case "ajax_updateSendEmailStatus":
                OspropertyAjax::updateSendEmailStatus();
                break;
			case "ajax_checkingVersion":
				OspropertyAjax::checkingVersion();
				break;
            case "ajax_grabImagess":
                OspropertyAjax::grabImage();
                break;
            case "ajax_cancelgrabImagess":
                OspropertyAjax::cancelGrab();
                break;
			case "ajax_fbconnect":
				OspropertyAjax::fbConnect();
				break;
			case "ajax_newupload":
				$session = Factory::getSession();
				$sRandomText = $session->get('randomText','');
				$randomText = $jinput->getString('randomText','');
				if($randomText == ''){
					OSPHelper::redirect(Uri::root());
				}elseif($sRandomText != $randomText){
					OSPHelper::redirect(Uri::root());
				}else{
					DJUploadHelper::upload();
				}
			break;
			case "ajax_userdata":
				self::ajax_userdata();
			break;
        }
    }

	public static function ajax_userdata(){
		global $configClass,$jinput,$mainframe;
		$id = $jinput->getInt('user_id',0);
		$data = [];
		if($id > 0){
			$db = Factory::getDbo();
			$db->setQuery("Select name, email from #__users where id = '$id'");
			$user = $db->loadObject();
			if($user->name != ""){
				$data['name'] = $user->name;
			}
			if($user->email != ""){
				$data['email'] = $user->email;
			}
		}
		echo json_encode($data);
		$mainframe->close();
	}

    public static function removeCompare($id){
        global $jinput;
        $session = Factory::getSession();
        $comparelist = $session->get('comparelist');
        $comparelistArr = explode(",", $comparelist);
        $post = array_search($id,$comparelistArr);
        unset($comparelistArr[$post]);
        $comparelist = implode(",",$comparelistArr);
        $session->set('comparelist', $comparelist);
        ?>
		<div
			id="notice_message" class="alert alert-primary"><?php echo Text::_('OS_LISTING_HAS_BEEN_REMOVED_FROM_COMPARE_LIST')?>
			<b><a href="<?php echo Route::_('index.php?option=com_osproperty&task=compare_list')?>"
				  class="static"><?php echo Text::_('OS_HERE')?></a></b> <?php echo Text::_('OS_TO_VIEW_THE_COMPARISON')?>
		</div>
        <?php
        echo "@@@";
        $theme = $jinput->getString('theme','');
        $layout = $jinput->getString('layout','');
        if($theme != ""){
            $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_COMPARE_LIST');
            $msg = str_replace("'", "\'", $msg);
            switch($theme){
                case "default":
                    switch($layout) {
                        case "details":
                            ?>
                                <a class="inactivated" onclick="javascript:osConfirmExtend('<?php echo $msg;
                                ?>','ajax_addCompare','<?php echo $id?>','<?php echo Uri::root()?>','compare<?php echo $id?>','default','details')"
                                   href="javascript:void(0)"  title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>">
                                    <i class="edicon edicon-copy"></i>
                                    <?php echo Text::_('OS_COMPARISON')?>
                                </a>
                            <?php
                        break;
                        case "listing_list":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','default','listing_list')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>" class="compareLink">
								<span class="edicon edicon-copy"></span>
							</a>
                            <?php
                        break;
                        case "listing_grid":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','default','listing_grid')" href="javascript:void(0)" class="compareLink" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>">
                                <span class="edicon edicon-stats-bars"></span>
                            </a>
                            <?php
                            break;
                    }
                break;
                case "theme1":
                    switch($layout) {
                        case "details":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;
                            ?>','ajax_addCompare','<?php echo $id?>','<?php echo Uri::root()?>','compare<?php echo $id?>','theme1','details')"
                               href="javascript:void(0)">
                                <?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>
                            </a>
                            <?php
                            break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','theme1','listing')"
                               href="javascript:void(0)"
                               class="btn btn-warning btn-small">
                                <i class="osicon-bookmark osicon-white"></i> <?php echo Text::_('OS_ADD_TO_COMPARE_LIST'); ?>
                            </a>
                            <?php
                            break;
                    }
                    break;
				case "theme2":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','theme2','details')" href="javascript:void(0)" class="compareLink">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
								  <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
								</svg>
                            </a>
                            <?php
                            break;
                    }
                    break;
                case "wider":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','wider','details')" href="javascript:void(0)" class="compareLink">
                                <span class="edicon edicon-copy"></span>
                                <?php echo Text::_('OS_COMPARE');?>
                            </a>
                            <?php
                            break;
                    }
                    break;
				case "house":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','house','details')" href="javascript:void(0)" class="compareLink">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-diagram-2" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5zM8.5 5a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5zM3 11.5A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm4.5.5a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5zm1.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
</svg>
									</a>
                            <?php
                            break;
						case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','house','listing')"
                               href="javascript:void(0)"
                               class="btn btn-warning btn-small">
                                <i class="fa fa-plus"></i>
                            </a>
                            <?php
                            break;
                    }
                    break;
				case "theme3":
                    switch($layout) {
						case "details":
							$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_COMPARE_LIST');
							$msg = str_replace("'","\'",$msg);
							?>
							
							<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','details')" href="javascript:void(0)" class="link-icon" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>">
								<i class='edicon edicon-copy'></i>
							</a>
							<?php
						break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_addCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST');?>">
								<img title="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_ADD_TO_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24_gray.png" width="16"/>
							</a>
                            <?php
                            break;
                    }
                    break;
            }
        }
    }
    public static function addCompare($id)
	{
        global $jinput;
        $session = Factory::getSession();
        $comparelist = $session->get('comparelist');
        $comparelistArr = explode(",", $comparelist);

        if (in_array($id, $comparelistArr)) 
		{
            ?>
             <div id="notice_message" class="alert alert-primary"><?php echo Text::_('OS_THE_PROPERTY_HAS_BEEN_ADDED_TO_COMPARE')?>
				<strong><a href="<?php echo Route::_('index.php?option=com_osproperty&task=compare_list')?>" class="static"><?php echo Text::_('OS_HERE')?></a></strong> <?php echo Text::_('OS_TO_VIEW_THE_COMPARISON')?>
             </div>
                    
        <?php
        } 
		else 
		{
            if ($comparelist == "") 
			{
                $comparelist = $id;
            } 
			else 
			{
                $comparelist .= "," . $id;
            }
            $session->set('comparelist', $comparelist);
            ?>
			<div id="notice_message" class="alert alert-primary"><?php echo Text::_('OS_LISTING_HAS_BEEN_ADDED_TO_COMPARE_LIST') ?>
				<strong><a href="<?php echo Route::_('index.php?option=com_osproperty&task=compare_list') ?>"
					  class="static"><?php echo Text::_('OS_HERE') ?></a></strong> <?php echo Text::_('OS_TO_VIEW_THE_COMPARISON') ?>
			</div>
        <?php
        }

        echo "@@@";
        $theme = $jinput->getString('theme','');
        $layout = $jinput->getString('layout','');
        if($theme != "")
		{
            $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
            $msg = str_replace("'", "\'", $msg);
            switch($theme)
			{
                case "default":
                    switch($layout) 
					{
                        case "details":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;
                            ?>','ajax_removeCompare','<?php echo $id?>','<?php echo Uri::root()?>','compare<?php echo $id?>','default','details')"
                               href="javascript:void(0)"  title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
                                <i class="edicon edicon-copy"></i>
                                <?php echo Text::_('OS_COMPARISON')?>
                            </a>
                            <?php
                            break;
                        case "listing_list":
                            ?>
							<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','default','listing_grid')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>" class="compareLink activated">
								<span class="edicon edicon-copy"></span>
							</a>
                            <?php
                            break;
                        case "listing_grid":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','default','listing_grid')" href="javascript:void(0)" class="compareLinkActive" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
                                <span class="edicon edicon-stats-bars"></span>
                            </a>
                            <?php
                            break;
                    }
                    break;
                case "theme1":
                    switch($layout) {
                        case "details":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeCompare','<?php echo $id?>','<?php echo Uri::root()?>','compare<?php echo $id?>','theme1','details')"
                               href="javascript:void(0)">
                                <?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>
                            </a>
                            <?php
                        break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','theme1','listing')"
                               href="javascript:void(0)"
                               class="btn btn-warning btn-small">
                                <i class="osicon-bookmark osicon-white"></i> <?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST'); ?>
                            </a>
                            <?php
                        break;
                    }
                break;
				case "theme2":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','theme2','details')" href="javascript:void(0)" class="compareLink">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash-lg" viewBox="0 0 16 16">
								  <path fill-rule="evenodd" d="M2 8a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11A.5.5 0 0 1 2 8Z"/>
								</svg>
                            </a>
                            <?php
                            break;
                    }
                    break;
                case "wider":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','wider','details')" href="javascript:void(0)" class="compareLinkActive">
                                <span class="edicon edicon-copy"></span>
                                <?php echo Text::_('OS_REMOVE_COMPARE');?>
                            </a>
                            <?php
                            break;
                    }
                    break;
				case "house":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>" onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','house','details')" href="javascript:void(0)" class="compareLinkActive">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-diagram-2-fill" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M6 3.5A1.5 1.5 0 0 1 7.5 2h1A1.5 1.5 0 0 1 10 3.5v1A1.5 1.5 0 0 1 8.5 6v1H11a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-1 0V8h-5v.5a.5.5 0 0 1-1 0v-1A.5.5 0 0 1 5 7h2.5V6A1.5 1.5 0 0 1 6 4.5zm-3 8A1.5 1.5 0 0 1 4.5 10h1A1.5 1.5 0 0 1 7 11.5v1A1.5 1.5 0 0 1 5.5 14h-1A1.5 1.5 0 0 1 3 12.5zm6 0a1.5 1.5 0 0 1 1.5-1.5h1a1.5 1.5 0 0 1 1.5 1.5v1a1.5 1.5 0 0 1-1.5 1.5h-1A1.5 1.5 0 0 1 9 12.5z"/>
</svg>
                            <?php
                            break;
						case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','house','listing')"
                               href="javascript:void(0)" class="compareLink">
                                <i class="fa fa-plus" style="color:#000;"></i>
                            </a>
                            <?php
                        break;
                    }
                    break;
				case "theme3":
                    switch($layout) {
						case "details":
							$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_COMPARE_LIST');
							$msg = str_replace("'","\'",$msg);
							?>
							
							<a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $row->id;?>','theme3','details')" href="javascript:void(0)" class="link-icon" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>">
								<i class='edicon edicon-copy'></i>
							</a>
							<?php
						break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg; ?>','ajax_removeCompare','<?php echo $id ?>','<?php echo Uri::root() ?>','compare<?php echo $id;?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST');?>">
								<img title="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_FROM_COMPARE_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/compare24.png" width="16"/>
							</a>
                            <?php
                            break;
                    }
                    break;
            }
        }
    }

    /**
     * Add Favorite
     */
    public static function addFavorites($id)
	{
        global $jinput, $mainframe;
        $db = Factory::getDbo();
        $user = Factory::getUser();
        $db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$id'");
        $count = $db->loadResult();
        if ($count > 0) 
		{
            ?>
           
			<div id="notice_message" class="alert alert-primary"><?php echo Text::_('OS_ALREADY_ADD_FAVORITES_CLICK')?> <b><a
						href="<?php echo Route::_('index.php?option=com_osproperty&task=property_favorites')?>"
						class="static"><?php echo Text::_('OS_HERE')?></a></b> <?php echo Text::_('OS_TO_VIEW_FAVORITES_RESULTS')?>
			</div>
			<?php
        } 
		else 
		{
            $db->setQuery("INSERT INTO #__osrs_favorites (id,user_id,pro_id) VALUES (NULL,'$user->id','$id')");
            $db->execute();
            ?>
			<div id="notice_message" class="alert alert-primary"><?php echo Text::_('OS_ALREADY_ADD_FAVORITES_CLICK')?> <strong><a
						href="<?php echo Route::_('index.php?option=com_osproperty&task=property_favorites')?>"
						class="static"><?php echo Text::_('OS_HERE')?></a></strong> <?php echo Text::_('OS_TO_VIEW_FAVORITES_RESULTS')?>
			</div>
        <?php
        }

        echo "@@@";
        $theme = $jinput->getString('theme','');
        $layout = $jinput->getString('layout','');
        if($theme != ""){
            $msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
            $msg = str_replace("'", "\'", $msg);
            switch($theme){
                case "default":
                    switch($layout) {
                        case "details":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;
                            ?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id?>','default','details')"
                               href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_FAVORITES')?>">
                                <i class="edicon edicon-heart"></I>
                                <?php
                                echo Text::_('OS_FAVORITE');
                                ?>
                            </a>
                            <?php
                            break;
                        case "listing_list":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','default','listing_list')" href="javascript:void(0)" title="<?php echo $msg;?>">
								<i class="icon-heart favred"></i>
							</a>
                            <?php
                            break;
                        case "listing_grid":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','default','listing_grid')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST');?>" class="favLinkActive">
                                <span class="edicon edicon-heart"></span>
                            </a>
                            <?php
                            break;
                    }
                    break;
                case "theme1":
                    switch($layout) {
                        case "details":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme1','details')"
                               href="javascript:void(0)" class="_saveListingLink save has icon s_16">
                                <?php echo Text::_('OS_REMOVE_FAVORITES');?>
                            </a>
                            <?php
                        break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme1','listing')" href="javascript:void(0)" class="btn btn-success btn-small">
                                <i class="osicon-remove osicon-white"></i> <?php echo Text::_('OS_REMOVE_FAVORITES');?>
                            </a>
                            <?php
                        break;
                    }
                break;
				case "theme2":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme2','details')" href="javascript:void(0)" class="favLinkActive">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="orange" class="bi bi-heart-fill" viewBox="0 0 16 16">
								  <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
								</svg>
                            </a>
                            <?php
                        break;
                        case "listing":
                            ?>
                            <a title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme2','listing')" href="javascript:void(0)" class="favLinkActive">
                                <span class="edicon edicon-heart"></span>
                            </a>
                            <?php
                        break;
                    }
                    break;
				case "house":
                    switch($layout) {
                        case "details":
							$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
							$msg = str_replace("'","\'",$msg);
							?>
							<a title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','house','details')" href="javascript:void(0)" class="favLinkActive">
								<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart-fill" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314"/>
</svg>
							</a>
						<?php
                        break;
                        case "listing":
                            ?>
                            <a title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme2','listing')" href="javascript:void(0)" class="favLinkActive">
                                <span class="edicon edicon-heart"></span>
                            </a>
                            <?php
                        break;
                    }
                    break;
				case "theme3":
                    switch($layout) {
						case "details":
							$msg = Text::_('OS_DO_YOU_WANT_TO_REMOVE_PROPERTY_OUT_OF_YOUR_FAVORITE_LISTS');
							$msg = str_replace("'","\'",$msg);
							?>
							
							<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme3','details')" href="javascript:void(0)" class="link-icon" title="<?php echo Text::_('OS_REMOVE_FAVORITES');?>">
								<i class='edicon edicon-floppy-disk'></i>
							</a>
							<?php
						break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_removeFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST');?>">
								<img title="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" alt="<?php echo Text::_('OS_REMOVE_PROPERTY_OUT_OF_FAVORITES_LIST')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24.png" width="16"/>
							</a>
                            <?php
                            break;
                    }
                    break;
            }
        }
    }

    /**
     * Remove Favorites
     * @param $id
     */
    public static function removeFavorites($id)
	{
        global $jinput;
        $db = Factory::getDbo();
        $user = Factory::getUser();
        $db->setQuery("Delete from #__osrs_favorites where user_id = '$user->id' and pro_id = '$id'");
        $db->execute();
        ?>
        <div id="notice_message" class="alert alert-primary"><?php echo Text::_('OS_ADDED_TO_FAVORITES')?></div>
        <?php

        echo "@@@";
        $theme = $jinput->getString('theme','');
        $layout = $jinput->getString('layout','');
        if($theme != ""){
            $msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
            $msg = str_replace("'", "\'", $msg);
            switch($theme){
                case "default":
                    switch($layout) {
                        case "details":
                            ?>
                            <a class="inactivated" onclick="javascript:osConfirmExtend('<?php echo $msg;
                            ?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id?>','default','details')"
                               href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>">
                                <i class="edicon edicon-heart"></i>
                                <?php
                                echo Text::_('OS_FAVORITE');
                                ?>
                            </a>
                            <?php
                            break;
                        case "listing_list":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','default','listing_list')" href="javascript:void(0)" title="<?php echo $msg;?>">
								<i class="icon-heart white"></i>
							</a>
                            <?php
                            break;
                        case "listing_grid":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','default','listing_grid')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" class="favLink">
                                <span class="edicon edicon-heart"></span>
                            </a>
                            <?php
                            break;
                    }
                    break;
                case "theme1":

                    switch($layout) {
                        case "details": ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme1','details')"
                               href="javascript:void(0)" class="_saveListingLink save has icon s_16">
                                <?php echo Text::_('OS_ADD_TO_FAVORITES');?>
                            </a>
                            <?php
                        break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme1','listing')" href="javascript:void(0)" class="btn btn-success btn-small">
                                <i class="osicon-ok osicon-white"></i> <?php echo Text::_('OS_ADD_TO_FAVORITES');?>
                            </a>
                            <?php
                        break;
                    }
                break;
				case "theme2":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme2','details')" class="favLink">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
								  <path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z"/>
								</svg>
                            </a>
                            <?php
                        break;
                        case "listing":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme2','listing')" class="favLink">
                                <span class="edicon edicon-heart"></span>
                            </a>
                            <?php
                        break;
                    }
                    break;
				case "house":
                    switch($layout) {
                        case "details":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','house','details')" class="favLink">
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
<path d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143q.09.083.176.171a3 3 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15"/>
</svg>
									</a>
                            <?php
                        break;
                        case "listing":
                            ?>
                            <a title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>" onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme2','listing')" class="favLink">
                                <span class="edicon edicon-heart"></span>
                            </a>
                            <?php
                        break;
                    }
                    break;
				case "theme3":
                    switch($layout) {
						case "details":
							$msg = Text::_('OS_DO_YOU_WANT_TO_ADD_PROPERTY_TO_YOUR_FAVORITE_LISTS');
							$msg = str_replace("'","\'",$msg);
							?>
							<a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme3','details');" class="link-icon" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>">
								<i class='edicon edicon-floppy-disk'></i>
							</a>
							<?php
						break;
                        case "listing":
                            ?>
                            <a onclick="javascript:osConfirmExtend('<?php echo $msg;?>','ajax_addFavorites','<?php echo $id?>','<?php echo Uri::root()?>','fav<?php echo $id; ?>','theme3','listing')" href="javascript:void(0)" title="<?php echo Text::_('OS_ADD_TO_FAVORITES');?>">
								<img title="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" alt="<?php echo Text::_('OS_ADD_TO_FAVORITES')?>" src="<?php echo Uri::root()?>media/com_osproperty/assets/images/save24_gray.png" width="16"/>
							</a>
                            <?php
                            break;
                    }
                    break;
            }
        }
    }

    static function loadLocationInformation()
    {
        $db = Factory::getDbo();

		$langArr = OSPHelper::returnSupportedCountries();

        $countryArr = [];
        for ($i = 0; $i < count($langArr); $i++) {
            $countryArr[] = $langArr[$i]->country_id;
        }
        $countrySql = implode(",", $countryArr);

        $db->setQuery("Select * from #__osrs_countries where id in ($countrySql)");
        $countries = $db->loadObjectList();

        ?>
        <table width="100%" class="table table-striped">
            <thead>
            <tr>
				<?php
				if(!OSPHelper::isJoomla4())
				{
				?>
                <th width="5%" class="center">
                    <?php echo Text::_('OS_COUNTRY')?>
                </th>
				<?php
				}	
				?>
                <th width="20%" class="center">
                    <?php echo Text::_('OS_COUNTRY')?>
                </th>
				<?php
				if(!OSPHelper::isJoomla4())
				{
				?>
                <th width="15%" class="center">
                    <?php echo Text::_('OS_STATE')?>
                </th>
                <th width="20%" class="center">
                    <?php echo Text::_('OS_CITY')?>
                </th>
				<?php
				}	
				?>
                <th width="20%" class="center">
                    <?php echo Text::_('OS_UPDATE_LOCATION')?>
                </th>
                <th width="10%" class="center">
                    Enable
                </th>
                <th width="10%" class="center">
                    Disable
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < count($countries); $i++) 
			{
                $country = $countries[$i];
				if(!OSPHelper::isJoomla4())
				{
					$db->setQuery("Select count(id) from #__osrs_states where country_id = '$country->id'");
					$nStates = $db->loadResult();

					$db->setQuery("Select count(id) from #__osrs_states where country_id = '$country->id' and published = '1'");
					$pStates = $db->loadResult();

					$db->setQuery("Select count(id) from #__osrs_states where country_id = '$country->id' and published = '0'");
					$uStates = $db->loadResult();

					$db->setQuery("Select count(id) from #__osrs_cities where country_id = '$country->id'");
					$nCities = $db->loadResult();

					$db->setQuery("Select count(id) from #__osrs_cities where country_id = '$country->id' and published = '1'");
					$pCities = $db->loadResult();

					$db->setQuery("Select count(id) from #__osrs_cities where country_id = '$country->id' and published = '0'");
					$uCities = $db->loadResult();
				}
                ?>
                <tr>
					<?php
					if(!OSPHelper::isJoomla4())
					{
					?>
						<td class="center padding1">
							<?php
							for ($j = 0; $j < count($langArr); $j++) {
								if ($langArr[$j]->country_id == $country->id) {
									$flag_name = $langArr[$j]->file_name;
									$flag_name = str_replace(".txt", "", $flag_name);
									$flag_name = explode("_", $flag_name);
									$flag_code = $flag_name[0];
									$flag_name = $flag_name[1];
									$flag_name = "flag_" . $flag_name . ".png";
									if (file_exists(JPATH_COMPONENT_ADMINISTRATOR . '/images/flag/' . $flag_name)) {
										?>
										<img
											src="<?php echo Uri::base()?>administrator/components/com_osproperty/images/flag/<?php echo $flag_name?>"
											width="28">
									<?php
									} else {
										?>
										<img
											src="<?php echo Uri::root()?>media/com_osproperty/flags/<?php echo $flag_code?>.png"
											width="28">
									<?php
									}
								}
							}
							?>
						</td>
					<?php
					}	
					?>
                    <td align="left" class="padding5">
                        <?php echo $country->country_name;?>
                    </td>
					<?php
					if(!OSPHelper::isJoomla4())
					{
					?>
                    <td align="center" class="padding5">
                        <b><?php echo $nStates;?></b>
                        (<span color='Green'><?php echo $pStates?></span>/<span color='Red'><?php echo $uStates?></span>)
                    </td>
                    <td align="center" class="padding5">
                        <b><?php echo $nCities;?></b>
                        (<span color='Green'><?php echo $pCities?></span>/<span color='Red'><?php echo $uCities?></span>)
                    </td>
					<?php
					}	
					?>
                    <td align="center" class="padding5">
                        <a href="index.php?option=com_osproperty&task=properties_updatelocation&country_id=<?php echo $country->id?>"
                           title="<?php echo Text::_('OS_INSERT_LOCATION_DATABASE_FOR')?> <?php echo $country->country_name?>">
                            <?php echo Text::_('OS_UPDATE_LOCATION')?>
                        </a>
                    </td>
                    <td align="center" class="padding5">
                        <a href="index.php?option=com_osproperty&task=properties_changeLocation&s=1&country_id=<?php echo $country->id?>"
                           title="Enable location for <?php echo $country->country_name;?>">
                            <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/tick.png"
                                 border="0">
                        </a>
                    </td>
                    <td align="center" class="padding5">
                        <a href="index.php?option=com_osproperty&task=properties_changeLocation&s=0&country_id=<?php echo $country->id?>"
                           title="Disable location for <?php echo $country->country_name;?>">
                            <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/publish_x.png"
                                 border="0">
                        </a>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
        <?php
		echo "@@@";
		self::checkingVersion();
        exit();
    }

    static function loadStateCityLocatorModule($option, $country_name, $country_id, $state_id, $city_id, $state_name, $random_id, $lang='')
    {
        global $jinput, $mainframe, $configClass;

        @header('Content-Type: text/html; charset=utf-8');
		$translatable = OSPHelper::isJoomlaMultipleLanguages();
		$suffix = "";
		if($lang != "" && $lang != OSPHelper::getDefaultLanguage())
		{
			$suffix = "_".substr($lang, 0, 2);
		}
        elseif ($translatable) 
		{
            $suffix = OSPHelper::getFieldSuffix();
        }
        $db = Factory::getDbo();
        $availSql = "";
        $show_available_states_cities = $configClass['show_available_states_cities'];
        $option_state = [];
        $option_state[] = HTMLHelper::_('select.option', 0, Text::_('OS_ALL_STATES'));
        if ($country_id > 0) {
            if ($show_available_states_cities == 1) {
                $availSql = " and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
            }
            $db->setQuery("SELECT id AS value, state_name".$suffix." AS text, state_name FROM #__osrs_states WHERE published = '1' $availSql and `country_id` = '$country_id' ORDER BY state_name");
            $states = $db->loadObjectList();
			foreach($states as $state)
			{
				if($state->text == "")
				{
					$state->text = $state->state_name;
				}
			}
            if (count($states)) {
                $option_state = array_merge($option_state, $states);
            }
            $disable = '';
        } else {
            $disable = 'disabled="disabled"';
        }
        $random_id = ($random_id > 0 ? $random_id : '');
        echo HTMLHelper::_('select.genericlist', $option_state, 'mstate_id' . $random_id, 'onChange="javascript:change_stateModule' . $random_id . '(this.value,' . $city_id . ',\'' . $random_id . '\')" class="input-medium" ' . $disable, 'value', 'text', $state_id);
        echo "@@@";
        $availSql = "";
        if ($show_available_states_cities == 1) {
            $availSql = " and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
        }
        //check to see if the state is belong to this country
        $db->setQuery("Select count(id) from #__osrs_states where published = '1' and country_id = '$country_id' $availSql and id = '$state_id'");
        $count = $db->loadResult();
        $availSql = "";
        if ($count > 0) 
		{
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', '', ' - ' . Text::_('OS_ALL_CITIES') . ' - ');
            if ($show_available_states_cities == 1) 
			{
                $availSql = " and id in (Select city from #__osrs_properties where approved = '1' and published = '1')";
            }
            $db->setQuery("Select id as value, city".$suffix." as text, city from #__osrs_cities where published = '1' $availSql and state_id = '$state_id' order by city");
            $cities = $db->loadObjectList();
			foreach($cities as $city)
			{
				if($city->text == "")
				{
					$city->text = $city->city;
				}
			}
            $cityArr = array_merge($cityArr, $cities);
            echo HTMLHelper::_('select.genericlist', $cityArr, 'city' . $random_id, 'class="input-medium" ', 'value', 'text', $city_id);
        } 
		else 
		{
            $option_state = [];
            $option_state[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            echo HTMLHelper::_('select.genericlist', $option_state, 'city' . $random_id, 'class="input-medium" disabled', 'value', 'text');
        }

        if ($random_id != "") {
            ?>
            |*|<?php echo $random_id?>
        <?php
        }
        exit;
    }

    static function loadStateCityLocator($option, $country_name, $country_id, $state_id, $city_id, $state_name, $class = "input-medium form-select form-control", $lang = "")
    {
        global $jinput, $mainframe, $configClass;
        @header('Content-Type: text/html; charset=utf-8');
        $db = Factory::getDBO();

		$translatable = OSPHelper::isJoomlaMultipleLanguages();
		$suffix = "";
		if($lang != "")
		{
			$suffix = "_".substr($lang, 0, 2);
		}
        elseif ($translatable) 
		{
            $suffix = OSPHelper::getFieldSuffix();
        }

        $availSql = "";
        $show_available_states_cities = $configClass['show_available_states_cities'];
        $option_state = [];
        $option_state[] = HTMLHelper::_('select.option', 0, ' - ' . Text::_('OS_ALL_STATES') . ' - ');
        if ($country_id > 0) {
            if ($show_available_states_cities == 1) {
                $availSql = " and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
            }
            $db->setQuery("SELECT id AS value, state_name".$suffix." AS text, state_name FROM #__osrs_states WHERE published = '1' and `country_id` = '$country_id' $availSql ORDER BY state_name");
            $states = $db->loadObjectList();
			foreach($states as $state)
			{
				if($state->text == "")
				{
					$state->text = $state->state_name;
				}
			}
            if (count($states)) {
                $option_state = array_merge($option_state, $states);
            }
            $disable = '';
        } else {
            $disable = 'disabled="disabled"';
        }

        echo HTMLHelper::_('select.genericlist', $option_state, $state_name, 'onChange="javascript:change_state(this.value,\'' . $city_id . '\')" class="' . $class . '" ' . $disable, 'value', 'text', $state_id);
        echo "@@@";
        $availSql = "";
        if ($show_available_states_cities == 1) {
            $availSql = " and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
        }
        //check to see if the state is belong to this country
        $db->setQuery("Select count(id) from #__osrs_states where published = '1' and country_id = '$country_id' and id = '$state_id' $availSql");
        $count = $db->loadResult();
        if ($count > 0) 
		{
            $availSql = "";
            if ($show_available_states_cities == 1) 
			{
                $availSql = " and id in (Select city from #__osrs_properties where approved = '1' and published = '1')";
            }
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', 0, Text::_('OS_ALL_CITIES'));
            $db->setQuery("Select id as value, city".$suffix." as text, city from #__osrs_cities where published = '1' and state_id = '$state_id' $availSql order by city");
            $cities = $db->loadObjectList();
			foreach($cities as $city)
			{
				if($city->text == "")
				{
					$city->text = $city->city;
				}
			}
            $cityArr = array_merge($cityArr, $cities);
            echo HTMLHelper::_('select.genericlist', $cityArr, 'city', 'class="' . $class . '" ' , 'value', 'text', $city_id);
        } 
		else 
		{
            $option_state = [];
            $option_state[] = HTMLHelper::_('select.option', 0, Text::_('OS_ALL_CITIES'));
            echo HTMLHelper::_('select.genericlist', $option_state, 'city', 'class="' . $class . '" disabled', 'value', 'text');
        }
        exit;
    }


    /**
     * Load State and City
     *
     * @param unknown_type $option
     */
    static function loadStateCityBackend($option, $country_name, $country_id, $state_id, $city_id, $state_name, $class='input-medium form-select form-control')
    {
        global $jinput, $mainframe;
        @header('Content-Type: text/html; charset=utf-8');
        $db = Factory::getDBO();

        $lgs = OSPHelper::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
        $suffix = "";
        if ($translatable) {
            $suffix = OSPHelper::getFieldSuffix();
        }

        $option_state = [];
        $option_state[] = HTMLHelper::_('select.option', 0, ' - ' . Text::_('OS_SELECT_STATE') . ' - ');
        if ($country_id) {
            $db->setQuery("SELECT id AS value, state_name" . $suffix . " AS text FROM #__osrs_states WHERE published = '1' and  `country_id` = '$country_id' ORDER BY state_name");
            $states = $db->loadObjectList();
            if (count($states)) {
                $option_state = array_merge($option_state, $states);
            }
            $disable = '';
        } else {
            $disable = 'disabled="disabled"';
        }

        echo HTMLHelper::_('select.genericlist', $option_state, $state_name, 'onChange="javascript:loadCityBackend(this.value,\'' . $city_id . '\')" class="chosen '.$class.'" ' . $disable, 'value', 'text', $state_id);
        echo "@@@";

        //check to see if the state is belong to this country
        $db->setQuery("Select count(id) from #__osrs_states where published = '1' and  country_id = '$country_id' and id = '$state_id'");
        $count = $db->loadResult();
        if ($count > 0) {
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            $db->setQuery("Select id as value, city" . $suffix . " as text from #__osrs_cities where published = '1' and state_id = '$state_id' order by city");
            $cities = $db->loadObjectList();
            $cityArr = array_merge($cityArr, $cities);
            echo HTMLHelper::_('select.genericlist', $cityArr, 'city' , 'class="'.$class.' chosen" ', 'value', 'text', $city_id);
        } else {
            $option_state = [];
            $option_state[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            echo HTMLHelper::_('select.genericlist', $option_state, 'city' , 'class="'.$class.' chosen" disabled', 'value', 'text');
        }
        exit;
    }
    /**
     * Load State and City
     *
     * @param unknown_type $option
     */
    static function loadStateCity($option, $country_name, $country_id, $state_id, $city_id, $state_name , $class='input-medium form-select form-control', $lang='')
    {
        global $jinput, $mainframe;
        @header('Content-Type: text/html; charset=utf-8');
        $db = Factory::getDBO();

        $translatable = OSPHelper::isJoomlaMultipleLanguages();
		$suffix = "";
		if($lang != "")
		{
			$suffix = "_".substr($lang, 0, 2);
		}
        elseif ($translatable) 
		{
            $suffix = OSPHelper::getFieldSuffix();
        }

        $option_state = [];
        $option_state[] = HTMLHelper::_('select.option', 0, ' - ' . Text::_('OS_SELECT_STATE') . ' - ');
        if ($country_id) {
            $db->setQuery("SELECT id AS value, state_name" . $suffix . " AS text, state_name FROM #__osrs_states WHERE published = '1' and  `country_id` = '$country_id' ORDER BY state_name");
            $states = $db->loadObjectList();
			foreach($states as $state)
			{
				if($state->text == "")
				{
					$state->text = $state->state_name;
				}
			}
            if (count($states)) {
                $option_state = array_merge($option_state, $states);
            }
            $disable = '';
        } else {
            $disable = 'disabled="disabled"';
        }

        echo HTMLHelper::_('select.genericlist', $option_state, $state_name, 'onChange="javascript:loadCity(this.value,\'' . $city_id . '\')" class="'.$class.'" ' . $disable, 'value', 'text', $state_id);
        echo "@@@";

        //check to see if the state is belong to this country
        $db->setQuery("Select count(id) from #__osrs_states where published = '1' and  country_id = '$country_id' and id = '$state_id'");
        $count = $db->loadResult();
        if ($count > 0) {
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            $db->setQuery("Select id as value, city" . $suffix . " as text from #__osrs_cities where published = '1' and state_id = '$state_id' order by city");
            $cities = $db->loadObjectList();
            $cityArr = array_merge($cityArr, $cities);
            echo HTMLHelper::_('select.genericlist', $cityArr, 'city' , 'class="'.$class.'" ' , 'value', 'text', $city_id);
        } else {
            $option_state = [];
            $option_state[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            echo HTMLHelper::_('select.genericlist', $option_state, 'city', 'class="'.$class.'" disabled', 'value', 'text');
        }
        exit;
    }

    /**
     * Load City
     *
     * @param unknown_type $option
     * @param unknown_type $state_id
     * @param unknown_type $city_id
     * @param unknown_type $state_name
     */
    public static function loadCity($option, $state_id = 0, $city_id = '', $state_name = '' , $useConfig = 0, $class = "input-medium form-select ilarge", $city_name = "city", $lang = "")
    {
        global $jinput, $mainframe, $configClass, $bootstrapHelper;
        $db = Factory::getDBO();

        $lgs = OSPHelper::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
        $suffix = "";
		if($lang != "" && $lang != OSPHelper::getDefaultLanguage())
		{
			$suffix = "_".substr($lang, 0, 2);
		}
        elseif ($translatable) 
		{
            $suffix = OSPHelper::getFieldSuffix();
        }

		//$class = $bootstrapHelper->getClassMapping($class);

        @header('Content-Type: text/html; charset=utf-8');
        $availSql = "";
        $show_available_states_cities = $configClass['show_available_states_cities'];
		//echo $class;
        if ($state_id > 0) {
            $availSql = "";
            if (($show_available_states_cities == 1) && ($useConfig == 1)) {
                $availSql = " and id in (Select city from #__osrs_properties where approved = '1' and published = '1')";
            }
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            $db->setQuery("Select id as value, city" . $suffix . " as text, city from #__osrs_cities where published = '1' and state_id = '$state_id' $availSql order by city");
            $cities = $db->loadObjectList();
			foreach($cities as $city)
			{
				if($city->text == "")
				{
					$city->text = $city->city;
				}
			}
            $cityArr = array_merge($cityArr, $cities);
            echo HTMLHelper::_('select.genericlist', $cityArr, $city_name, 'class="' . $class . '" ' . $disabled, 'value', 'text', $city_id);
        } 
		else 
		{
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            echo HTMLHelper::_('select.genericlist', $cityArr, $city_name, 'class="' . $class . '" disabled', 'value', 'text');
        }
        exit;
    }

	static function loadAgentDropdown($company_id){
		global $jinput, $mainframe, $configClass;
		$db = Factory::getDbo();
		$query = "Select a.id as value, a.name as text from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where b.block = '0' and a.company_id = '$company_id' and a.published = '1'";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$optionArr = [];
		$optionArr[] = HTMLHelper::_('select.option',0,Text::_('OS_ALL_AGENTS'));
		$optionArr = array_merge($optionArr,$rows);
		echo HTMLHelper::_('select.genericlist', $optionArr, 'agent_id', 'class="input-medium chosen"', 'value', 'text');
		exit;
	}

    static function loadCityModule($option, $state_id, $city_id, $state_name, $random_id, $lang = '')
    {
        global $jinput, $mainframe, $configClass;
        @header('Content-Type: text/html; charset=utf-8');
        $random_id = ($random_id > 0 ? $random_id : '');
        $show_available_states_cities = $configClass['show_available_states_cities'];
        $db = Factory::getDBO();

        $translatable = OSPHelper::isJoomlaMultipleLanguages();
		$suffix = "";

		if($lang != "" && $lang != OSPHelper::getDefaultLanguage())
		{
			$suffix = "_".substr($lang, 0, 2);
		}
        elseif ($translatable) 
		{
            $suffix = OSPHelper::getFieldSuffix();
        }

        if ($state_id > 0) 
		{
            $cityArr = [];
            $availSql = "";
            if ($show_available_states_cities == 1) {
                $availSql = " and id in (Select city from #__osrs_properties where approved = '1' and published = '1')";
            }
            $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            $db->setQuery("Select id as value, city" . $suffix . " as text, city from #__osrs_cities where published = '1' and state_id = '$state_id' $availSql order by city" . $suffix );
            $cities = $db->loadObjectList();
			foreach($cities as $city)
			{
				if($city->text == "")
				{
					$city->text = $city->city;
				}
			}
            $cityArr = array_merge($cityArr, $cities);
            echo HTMLHelper::_('select.genericlist', $cityArr, 'city' . $random_id, 'class="input-medium" ' , 'value', 'text', $city_id);
        } 
		else 
		{
            $cityArr = [];
            $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_CITIES'));
            echo HTMLHelper::_('select.genericlist', $cityArr, 'city' . $random_id, 'class="input-medium" disabled', 'value', 'text');
        }
        if ($random_id != "") {
            ?>
            |*|<?php echo $random_id?>
        <?php
        }
        exit;
    }

    /**
     * Search agent, add agent to the company
     *
     * @param unknown_type $option
     */
    static function searchAgentforaddtocompany($option)
    {
        global $jinput, $mainframe;
        @header('Content-Type: text/html; charset=utf-8');
        $db = Factory::getDbo();
        $queryString = OSPHelper::getStringRequest('queryString', '');
        if ($queryString != "") {
            $query = "Select a.id, a.name, a.user_id, a.email, a.address, a.state,a.photo from #__osrs_agents as a"
                . " inner join #__users as b on b.id = a.user_id"
                . " where a.published = '1' and b.block = '0'"
                . " and a.id not in (Select agent_id from #__osrs_company_agents) and a.id in (Select id from #__osrs_agents where a.name like '%$queryString%' or a.address like '%$queryString%' or a.email like '%$queryString%')"
                . " order by a.name";
            $db->setQuery($query);
            $rows = $db->loadObjectList();
            if (count($rows) > 0) {
                for ($i = 0; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    $agent_id = $row->id;
                    $photo = $row->photo;

                    if ($photo != "") {
                        $photo_link = Uri::root() . "components/com_osproperty/images/agent/thumbnail/" . $photo;
                        $photo_real_link = JPATH_COMPONENT . DS . "images" . DS . "agent" . DS . "thumbnail" . DS . $photo;
                        if (file_exists($photo_real_link)) {
                            $photo_value = "<img src='$photo_link' width='60' class='border0' />";
                        } else {
                            $photo_value = "";
                        }
                    }

                    $db->setQuery("select state_name from #__osrs_states where id = '$row->state'");
                    $state_name = $db->loadResult();
                    ?>
                    <li onClick="fill(<?php echo $row->id?>,'<?php echo $row->name?>')">
                        <div class="searchAgentforaddtocompany">
                            <div class="searchAgentforaddtocompany1">
                                <?php
                                if ($photo_value != "") {
                                    echo $photo_value;
                                }
                                ?>
                            </div>
                            <b>
                                <?php echo $row->name?>
                            </b>
                            <BR>
                            <?php echo $row->email?>
                            <BR>
                            <?php echo $row->address?>, <?php echo $state_name;?>
                        </div>
                    </li>
                <?php
                }
            } else {
                ?>
                <li><?php echo Text::_('OS_NO_DATA_MATCH')?></li>
            <?php
            }
        }
        exit;
    }

    static function agentSearch($option)
    {
        global $jinput, $mainframe;
        $db = Factory::getDbo();
        @header('Content-Type: text/html; charset=utf-8');
        $queryString = OSPHelper::getStringRequest('queryString', '');
        if ($queryString != "") {
            $queryStringArr = explode(",", $queryString);
            //$address = trim($queryStringArr[0]);
            //$city = trim($queryString[1]);
            //$state = trim($queryString[2]);

            $returnArr = [];
            for ($i = 0; $i < count($queryStringArr); $i++) {
                $item = $queryStringArr[$i];

                $db->setQuery("Select a.id from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where a.address like '%$item%' and a.published = '1'");
                $addressArr = $db->loadObjectList();
                if (count($addressArr) > 0) {
                    for ($j = 0; $j < count($addressArr); $j++) {
                        if (!in_array($addressArr[$j]->id, $returnArr)) {
                            $returnArr[count($returnArr)] = $addressArr[$j]->id;
                        }
                    }
                }

                $db->setQuery("Select a.id from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where a.city like '%$item%' and a.published = '1'");
                $cityArr = $db->loadObjectList();
                if (count($cityArr) > 0) {
                    for ($j = 0; $j < count($cityArr); $j++) {
                        if (!in_array($cityArr[$j]->id, $returnArr)) {
                            $returnArr[count($returnArr)] = $cityArr[$j]->id;
                        }
                    }
                }

                $db->setQuery("Select id from #__osrs_states where (state_name like '%$item%' or state_code like '$item') and published = '1'");
                $states = $db->loadObjectList();
                if (count($states) > 0) {
                    $state_ids = "";
                    for ($i = 0; $i < count($states); $i++) {
                        $state_ids .= $states[$i]->id . ",";
                    }
                    $state_ids = substr($state_ids, 0, strlen($state_ids) - 1);
                    $db->setQuery("Select a.id from #__osrs_agents as a inner join #__users as b on b.id = a.user_id where a.state in ($state_ids) and a.published = '1'");
                    $stateArr = $db->loadObjectList();
                    if (count($stateArr) > 0) {
                        for ($j = 0; $j < count($stateArr); $j++) {
                            if (!in_array($stateArr[$j]->id, $returnArr)) {
                                $returnArr[count($returnArr)] = $stateArr[$j]->id;
                            }
                        }
                    }
                }
            }

            if (count($returnArr) > 0) {
                for ($i = 0; $i < count($returnArr); $i++) {
                    $id = $returnArr[$i];
                    $db->setQuery("Select * from #__osrs_agents where id = '$id' and published = '1'");
                    $row = $db->loadObject();
                    $db->setQuery("Select state_name from #__osrs_states where id = '$row->state'");
                    $state = $db->loadResult();
                    $value = $row->name;
                    $value .= " - " . $row->address;
                    if ($row->city != "") {
                        $value .= ", " . $row->city;
                    }
                    $value .= ", " . $state;
                    ?>
                    <li onClick="fill(<?php echo $id?>,'<?php echo $value?>')">
                        <?php echo $value?>
                    </li>
                <?php
                }
            } else {
                ?>
                <li><?php echo Text::_('OS_NO_DATA_MATCH')?></li>
            <?php
            }
        }
        exit;
    }


    static function loadStateBackend($option)
    {
        global $jinput, $mainframe;
        $db = Factory::getDbo();
        @header('Content-Type: text/html; charset=utf-8');
        $country = $jinput->getInt('country',0);
        $pid = $jinput->getInt('pid', 0);
        $db->setQuery("Select id as value, state_name as text from #__osrs_states where published = '1' and country_id = '$country'");
        $states = $db->loadObjectList();
        $stateArr = [];
        $stateArr[] = HTMLHelper::_('select.option', '', Text::_('OS_SELECT_STATE'));
        $stateArr = array_merge($stateArr, $states);
        echo HTMLHelper::_('select.genericlist', $stateArr, 'state' . $pid, 'onChange="javascript:changeStateValue(' . $pid . ');" class="input-medium form-select form-control ilarge"', 'value', 'text');
        exit;
    }

    static function loadCityBackend($option)
    {
        global $jinput, $mainframe;
        @header('Content-Type: text/html; charset=utf-8');
        $db = Factory::getDbo();
        $state = $jinput->getInt('state');
        $pid = $jinput->getInt('pid', 0);
        $db->setQuery("Select id as value, city as text from #__osrs_cities where published = '1' and state_id = '$state' order by city");
        $cities = $db->loadObjectList();
        $cityArr = [];
        $cityArr[] = HTMLHelper::_('select.option', '', Text::_('OS_SELECT_CITY'));
        $cityArr = array_merge($cityArr, $cities);
        echo HTMLHelper::_('select.genericlist', $cityArr, 'city' . $pid, ' class="input-medium form-select form-control ilarge"', 'value', 'text');
        exit;
    }


    static function loadStateInListPage($option)
    {
        global $jinput, $mainframe, $configClass;
        @header('Content-Type: text/html; charset=utf-8');
        $show_available_states_cities = $configClass['show_available_states_cities'];

        $db = Factory::getDbo();

        $lgs = OSPHelper::getLanguages();
        $translatable = Multilanguage::isEnabled() && count($lgs);
        $suffix = "";
        if ($translatable) {
            $suffix = OSPHelper::getFieldSuffix();
        }

        $country_id = $jinput->getInt('country_id', 0);
        $availSql = "";
        if (($country_id > 0) and ($show_available_states_cities == 1)) {
            $availSql = " and id in (Select state from #__osrs_properties where approved = '1' and published = '1')";
        }
        $db->setQuery("Select id as value, state_name" . $suffix . " as text from #__osrs_states where published = '1' and country_id = '$country_id' $availSql");
        $states = $db->loadObjectList();

        $stateArr = [];
        $stateArr[] = HTMLHelper::_('select.option', '', Text::_('OS_ALL_STATES'));
        $stateArr = array_merge($stateArr, $states);
        $lists['states'] = HTMLHelper::_('select.genericlist', $stateArr, 'state_id', 'onChange="javascript:changeCity(this.value,0);" class="input-medium" disable', 'value', 'text', '');
        echo $lists['states'];
        echo "@@@@";
        self::loadCity($option, '', '', 'state_id');
        exit;
    }


    /**
     * Check coupon code
     *
     * @param unknown_type $option
     */
    static function checkcouponcode($option)
    {
        global $jinput, $mainframe;
        $db = Factory::getDBO();
        $id = $jinput->getInt('id');
        $db->setQuery("Select * from #__osrs_coupon where id = '$id'");
        $coupon = $db->loadObject();
        $coupon_code = OSPHelper::getStringRequest('coupon_code', '');
        $user = Factory::getUser();
        $number_check = "";
        $number_check = $_COOKIE['u' . $user->id];
        if ($number_check == "") {
            $number_check = 0;
            setcookie('u' . $user->id, 1, time() + 3600);
        } else {
            $number_check++;
            setcookie('u' . $user->id, $number_check, time() + 3600);
        }
        $db->setQuery("Select count(id) from #__osrs_coupon where id = '$id' and coupon_code = '$coupon_code'");
        $count = $db->loadResult();
        if ($count > 0) {
            ?>
            <span class="checkcouponcode1">
                <?php
                printf('OS_CORRECT_COUPON_CODE', $coupon->discount . '%', $coupon->coupon_name);
                ?>
            </span>
            <?php
            @setcookie('coupon_code_awarded', $id, time() + 3600);
        } elseif ($number_check <= 4) {
            ?>
            <span class="checkcouponcode2">
                <?php
                echo Text::_('Wrong coupon code, please try again !!!');
                ?>
            </span>
            <BR>
            <?php
            echo Text::_('OS_IF_YOU_HAVE_COUPON_CODE');
            ?>
            <BR><BR>
            <input type="text" name="coupon_code" id="coupon_code" class="input-small" size="10">
            <input type="button" class="button" value="<?php echo Text::_('OS_CHECK_COUPON_CODE')?>"
                   onclick="javascript:checkCouponCode(<?php echo $coupon->id?>)">
        <?php
        } else {
            ?>
            <span class="checkcouponcode2">
                <?php
                echo Text::_('OS_WRONG_CODE');
                ?>
            </span>
        <?php
        }
        exit;
    }

    /**
     * Convert Currency
     *
     * @param unknown_type $option
     */
    static function convertCurrency($option)
    {
        global $jinput, $mainframe;
        @header('Content-Type: text/html; charset=utf-8');
        $db             = Factory::getDbo();
        $pid            = $jinput->getInt('pid',0);
        $show_label     = $jinput->getInt('show_label', 0);
		$theme          = $jinput->getString('theme','');
        $db->setQuery("Select price,curr from #__osrs_properties where id = '$pid'");
        $property       = $db->loadObject();
        $price          = $property->price;
        $ocurr          = $property->curr;
        $ncurr          = $jinput->getInt('curr', '');

        $db->setQuery("Select currency_code from #__osrs_currencies where id = '$ocurr'");
        $ocurr_code     = $db->loadResult();

        $db->setQuery("Select currency_code from #__osrs_currencies where id = '$ncurr'");
        $ncurr_code     = $db->loadResult();
        $exchange       = HelperOspropertyCommon::get_conversion($ocurr_code, $ncurr_code);
        $newprice       = $price * $exchange;

        //prepare the list
        $db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where published = '1' order by currency_code");
        $currencies     = $db->loadObjectList();
        $currenyArr[]   = HTMLHelper::_('select.option', '', Text::_('OS_SELECT'));
        $currenyArr     = array_merge($currenyArr, $currencies);
		if($theme == "defaultdetails") {
            $lists['curr'] = HTMLHelper::_('select.genericlist', $currenyArr, 'curr', 'onChange="javascript:convertCurrencyDefaultDetails(' . $pid . ',this.value,' . $show_label . ')" class="input-small"', 'value', 'text', $ncurr);
        }elseif($theme == "default"){
            $lists['curr'] = HTMLHelper::_('select.genericlist', $currenyArr, 'curr', 'onChange="javascript:convertCurrencyDefault(' . $pid . ',this.value,' . $show_label . ')" class="input-small"', 'value', 'text', $ncurr);
		}else{
            $lists['curr'] = HTMLHelper::_('select.genericlist', $currenyArr, 'curr', 'onChange="javascript:convertCurrency(' . $pid . ',this.value,' . $show_label . ')" class="input-small"', 'value', 'text', $ncurr);
		}

        if ($show_label == 1) {
            echo Text::_('OS_PRICE');
            echo ": ";
        }
        if ($ncurr == "") {
            echo OSPHelper::generatePrice($ocurr, $price);
        } else {
            echo OSPHelper::generatePrice($ncurr, $newprice);
        }

        $db->setQuery("Select rent_time from #__osrs_properties where id = '$pid'");
        $rent_time = $db->loadResult();
        if ($rent_time != "") {
            echo " /" . Text::_($rent_time);
        }
		if($theme != "default" && $theme != "defaultdetails")
		{
        ?>
            <BR/>
            <span class="spansmall">
            <?php echo Text::_('OS_CONVERT_CURRENCY')?>:
		<?php
		}
		?>
		<?php echo $lists['curr']?>
		<?php if($theme != "default"){ ?>
		</span>
        <?php
		}
        exit();
    }

	static function updateCurrency($option){
		global $jinput, $mainframe;
        @header('Content-Type: text/html; charset=utf-8');
        $db             = Factory::getDbo();
        $pid            = $jinput->getInt('pid',0);
		$item           = $jinput->getInt('item',0);
        $show_label     = $jinput->getInt('show_label', 0);
		$theme          = $jinput->getString('theme','');
        $db->setQuery("Select price,curr from #__osrs_properties where id = '$pid'");
        $property       = $db->loadObject();
        $price          = $property->price;
        $ocurr          = $property->curr;

        $ncurr          = $jinput->getInt('curr', '');

        $db->setQuery("Select currency_code from #__osrs_currencies where id = '$ocurr'");
        $ocurr_code     = $db->loadResult();

        $db->setQuery("Select currency_code from #__osrs_currencies where id = '$ncurr'");
        $ncurr_code     = $db->loadResult();
        $exchange       = HelperOspropertyCommon::get_conversion($ocurr_code, $ncurr_code);
        $newprice       = $price * $exchange;

        //prepare the list
        $db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where published = '1' order by currency_code");
        $currencies     = $db->loadObjectList();
        $currenyArr[]   = HTMLHelper::_('select.option', '', 'Select');
        $currenyArr     = array_merge($currenyArr, $currencies);
	    $lists['curr']  = HTMLHelper::_('select.genericlist', $currenyArr, 'curr', 'onChange="javascript:updateCurrency(' . $item . ','.$pid.',' . $show_label . ')" class="input-small"', 'value', 'text', $ncurr);
        if ($show_label == 1) {
            echo Text::_('OS_PRICE');
            echo ": ";
        }
        if ($ncurr == "") {
            echo OSPHelper::generatePrice($ocurr, $price);
        } else {
            echo OSPHelper::generatePrice($ncurr, $newprice);
        }

        $db->setQuery("Select rent_time from #__osrs_properties where id = '$pid'");
        $rent_time      = $db->loadResult();
        if ($rent_time != "") {
            echo " /" . Text::_($rent_time);
        }
		if($theme != "default"){
        ?>

        <BR/>
        <span class="spansmall">
		<?php echo Text::_('OS_CONVERT_CURRENCY')?>: 
		<?php } ?>
		<?php echo $lists['curr']?>
		<?php if($theme != "default"){ ?>
		</span>
        <?php
		}
        exit();
	}

    /**
     * Ajax search
     *
     */
    static function ajaxsearch()
    {
        global $jinput, $mainframe, $configClass, $lang_suffix;
        $keyword = OSPHelper::getStringRequest('input', '');
        $db = Factory::getDbo();

        $answer = [];

        $db->setQuery("Select id, pro_name$lang_suffix as pro_name,address from #__osrs_properties where published = '1' and approved = '1' and pro_name$lang_suffix like '%$keyword%'");
        $rows = $db->loadObjectList();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $count = count($answer);
                $answer[$count]->id = $row->id;
                $answer[$count]->value = $row->pro_name;
                $answer[$count]->info = OSPHelper::generateAddress($row);
            }
        }

        $db->setQuery("Select a.id, a.pro_name$lang_suffix as pro_name,a.address,a.state,a.city from #__osrs_properties as a inner join #__osrs_states as b on b.id = a.state inner join #__osrs_cities as c on c.id = a.city inner join #__osrs_countries as d on d.id = a.country where a.published = '1' and a.approved = '1' and a.show_address = '1' and (a.ref like '%$keyword%' or a.pro_name$lang_suffix like '%$keyword%' or a.address like '%$keyword%' or b.state_name like '%$keyword%' or c.city like '%$keyword%' or d.country_name like '%$keyword%') group by a.id");
        $rows = $db->loadObjectList();
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $count = count($answer);
                $answer[$count]->id = $row->id;
                $answer[$count]->value = OSPHelper::generateAddress($row);
                $answer[$count]->info = $row->pro_name;
            }
        }
        //print_r($answer);
        header("Expires: Mon, 26 Jul 2010 05:00:00 GMT"); // Date in the past
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Pragma: no-cache"); // HTTP/1.0

        sleep(2);

        if (isset($_REQUEST['json'])) {
            header("Content-Type: application/json");

            echo "{\"results\": [";
            $arr = [];
            if (count($answer) > 0) {
                foreach ($answer as $as) {
                    $arr[] = "{\"id\": \"1\", \"value\": \"" . $as->value . "\", \"info\":\"" . $as->info . "\"}";
                }
            }

            echo implode(", ", $arr);
            echo "]}";
        }
        exit();
    }

    static function updateSendEmailStatus(){
        global $jinput, $mainframe;
        $db = Factory::getDbo();
        $list_id = $jinput->getInt('list_id',0);
        $send_status = $jinput->getInt('send_status',0);
        $user = Factory::getUser();
        $db->setQuery("Select user_id from #__osrs_user_list where id = '$list_id'");
        $list_user_id = $db->loadResult();
        if($user->id == $list_user_id){
            $db->setQuery("Update #__osrs_user_list set receive_email = '$send_status' where id = '$list_id'");
            $db->execute();
        }
        $db->setQuery("Select receive_email from #__osrs_user_list where id = '$list_id'");
        $receive_email = $db->loadResult();
        if($receive_email == 0){
            ?>
            <a href="javascript:updateSendEmailStatus(<?php echo $list_id?>,1);" title="<?php echo Text::_('OS_CLICK_HERE_TO_RECEIVE_ALERT_EMAIL_WHEN_NEW_PROPERTIES_ARE_ADDED');?>">
                <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/publish_x.png"/>
            </a>
        <?php
        }else{
            ?>
            <a href="javascript:updateSendEmailStatus(<?php echo $list_id;?>,0);" title="<?php echo Text::_('OS_IF_YOU_DONT_WANT_TO_RECEIVE_ALERT_EMAIL_PLEASE_CLICK_HERE');?>">
                <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/tick.png"/>
            </a>
        <?php
        }
        exit();
    }

	public static function checkingVersion()
	{
		global $jinput, $mainframe;
		if(file_exists(JPATH_ROOT.DS."components/com_osproperty/version.txt"))
		{														
			$fh = fopen(JPATH_ROOT.DS."components/com_osproperty/version.txt","r");
			$current_version = fread($fh,filesize(JPATH_ROOT.DS."components/com_osproperty/version.txt"));
			@fclose($fh);
		}


		// Get the caching duration.
		$component     = ComponentHelper::getComponent('com_installer');
		$params        = $component->params;
		$cache_timeout = $params->get('cachetimeout', 6, 'int');
		$cache_timeout = 3600 * $cache_timeout;

		// Get the minimum stability.
		$minimum_stability = (int) $params->get('minimum_stability', Updater::STABILITY_STABLE);

		if (OSPHelper::isJoomla4())
		{
			/* @var \Joomla\Component\Joomlaupdate\Administrator\Model\UpdateModel $model */
			$model = $mainframe->bootComponent('com_installer')->getMVCFactory()
				->createModel('Update', 'Administrator', ['ignore_request' => true]);
		}
		else
		{
			BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');

			/** @var InstallerModelUpdate $model */
			$model = BaseDatabaseModel::getInstance('Update', 'InstallerModel');
		}

		$model->purge();

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('extension_id')
			->from('#__extensions')
			->where('`type` = "package"')
			->where('`element` = "pkg_osproperty"');
		$db->setQuery($query);
		$eid = (int) $db->loadResult();

		$result['status'] = 0;

		if ($eid)
		{
			$ret = Updater::getInstance()->findUpdates($eid, $cache_timeout, $minimum_stability);

			if ($ret)
			{

				$model->setState('list.start', 0);
				$model->setState('list.limit', 0);
				$model->setState('filter.extension_id', $eid);
				$updates          = $model->getItems();
				$result['status'] = 2;
				if (count($updates))
				{
					?>
					<img src="<?php echo Uri::root();?>media/com_osproperty/assets/images/noupdated.png" />
					<?php
					echo '<span class="version-checking-alert">'.Text::sprintf('OS_UPDATE_CHECKING_UPDATE_FOUND', $updates[0]->version).'</span>';
				}
				else
				{
					?>
					<img src="<?php echo Uri::root();?>media/com_osproperty/assets/images/noupdated.png" />
					<?php
					echo '<span class="version-checking-alert">'.Text::sprintf('OS_UPDATE_CHECKING_UPDATE_FOUND', null).'</span>';
				}
			}
			else
			{
				?>
				<img src="<?php echo Uri::root();?>media/com_osproperty/assets/images/updated.png" />
				<?php
				echo '<span class="version-checking-noalert">'.Text::_('OS_UPDATE_CHECKING_UP_TO_DATE').'</span>';
			}
		}

		$mainframe->close();

	}

	static function grabImage()
	{
        global $mainframe,$jinput,$bootstrapHelper;
        $url = $jinput->getString('url','');
        if($url != ""){
            $parse = parse_url($url);
            $domain = $parse['host'];
            $protocol = strtolower(substr($url,0,5));
            if($protocol == "https"){
                $domain1 = "https://".$domain;
            }else{
                $domain1 = "http://".$domain;
            }
            require_once JPATH_ROOT.'/components/com_osproperty/helpers/simple_html_dom.php';
            $html = file_get_html($url);
			if($html == ""){
				$html = str_get_html(file_get_contents($url));
			}
			?>
            <div class="row-fluid">
                <?php
                $i = 0;
                foreach($html->find('img') as $e){
                    $image = $e->src;
                    $image = explode("?",$image);
                    $image = $image[0];
                    if((strpos($image,'http') === false) && (strpos($image,'https') === false)){
                        $image = $domain1.$image;
                    }
                    $extension = strtolower(substr($image,strlen($image)-3));
                    if(($extension == "jpg") && (strpos($image,"logo") === false)){
                        $i++;
                        ?>
                        <div class="span3 center paddingbottom10">
                            <img src="<?php echo $image?>" width="200" height="150"/>
                            <br />
                            <input type="checkbox" name="grabImages[]" value="<?php echo $image?>"/> <?php echo Text::_('OS_GET_THIS_PHOTO');?>
                        </div>
                        <?php
                        if($i == 4){
                            ?>
                            </div><div class="row-fluid">
                            <?php
                            $i = 0;
                        }
                    }
                }
                ?>
            </div>
            <?php
        }
        ?>
        <input type="button" value="<?php echo Text::_('OS_CANCEL');?>" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>" onClick="javascript:cancelGrab('<?php echo Uri::root();?>');"/>
        <?php
        exit();
    }

	static public function get_web_page( $url )
    {
        $user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(

            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        return $content;
    }

    static function cancelGrab(){
        global $mainframe,$bootstrapHelper;
        ?>
        URL&nbsp;
        <span class="input-append">
        <input type="text" name="graburl" class="input-xxlarge" id="graburl" />
        <input type="button" value="<?php echo Text::_('OS_GRAB_IMAGES');?>" class="<?php echo $bootstrapHelper->getClassMapping('btn');?>" onClick="javascript:doGrabImage();"/></span>
        <?php
        exit();
    }
}
?>
