<?php
/*------------------------------------------------------------------------
# listing.html.php - Ossolution Property
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
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Plugin\PluginHelper;


class HTML_OspropertyListing{
	/**
	 * Show search list
	 *
	 * @param unknown_type $option
	 * @param unknown_type $lists
	 */
	static function searchList($option,$lists){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$db = Factory::getDbo();
		$adv_type_ids = $configClass['adv_type_ids'];
		if($adv_type_ids != "0")
		{
			$adv_type_ids = explode("|",$adv_type_ids);
		}
		$add_adv_type  = 0;
		$adv_type_ids = (array)$adv_type_ids;
		if(count($adv_type_ids) > 0){
			//add adv_type
			$add_adv_type = 1;
		}
		?>
        <form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty" name="searchlistform" id="searchlistform">
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                    <h1 class="componentheading">
                        <?php
                            echo Text::_('OS_YOUR_SEARCH_LIST');
                        ?>
                    </h1>
                    <div class="clearfix"></div>
                    <div class="btn-toolbar">
                        <div class="btn-group">
                            <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_DELETE');?>" onclick="javascript:removeList('property_removesearchlist');">
                                <i class="osicon-remove"></i> <?php echo Text::_('OS_DELETE');?>
                            </button>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <table class="adminlist table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    #
                                </th>
                                <th width="5%" >
                                </th>
                                <th width="45%" >
                                    <?php echo Text::_('OS_LIST_NAME')?>
                                </th>
                                <?php
                                if($configClass['active_alertemail'] == 1){
                                ?>
                                <th width="25%" >
                                    <?php echo Text::_('OS_RECEIVE_EMAIL_WITH_NEW_PROPERTIES')?>
                                </th>
                                <?php } ?>
                                <th width="25%" >
                                    <?php echo Text::_('OS_CREATED_ON')?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $select = "";
                        $k = 0;
                        for($i=0;$i<count($lists);$i++){
                            $list = $lists[$i];
                            if($i % 2 == 0){
                                $bgcolor = "white";
                            }else{
                                $bgcolor = "#efefef";
                            }
                            $suffixLink  = "";
                            if($add_adv_type ==1){
                                $db->setQuery("Select a.search_param from #__osrs_user_list_details as a inner join #__osrs_user_list as b on b.id = a.list_id where a.field_id like 'type' and b.id = '$list->id'");
                                $adv_type = $db->loadResult();
                                $suffixLink = "&adv_type=".$adv_type;
                            }
                            $select.= "<option value='$list->id'>".$list->id."</option>";
                            ?>
                            <tr class="row<?php echo $k;?>">
                                <td width="5%" class="center padding3" style="background-color:<?php echo $bgcolor?>;">
                                    <?php echo $i + 1?>
                                </td>
                                <td width="5%" class="center padding3" style="background-color:<?php echo $bgcolor?>;">
                                    <input type="checkbox" name="chkid" class="inputbox" onclick="javascript:updateSelectList('<?php echo $list->id?>')" />
                                </td>
                                <td width="45%" class="center padding3 paddingleft20 relative" style="background-color:<?php echo $bgcolor?>;">
                                    <div id="div_list_<?php echo $i?>">
                                        <a href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_advsearch&list=1&list_id=<?php echo $list->id?><?php echo $suffixLink?>&Itemid=<?php echo $jinput->getInt('Itemid',0)?>" title="<?php echo Text::_('OS_GET_SEARCH_RESULT')?>">
                                            <?php echo $list->list_name?>
                                        </a>
                                        <a href="javascript:showInputbox('<?php echo $i?>')">
                                            <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/edit.png" />
                                        </a>
                                        <div id="div_input_<?php echo $i?>" style="display:none;">
                                            <input type="text" name="list_name_<?php echo $i?>" id="list_name_<?php echo $i?>" class="inputbox" value="<?php echo htmlspecialchars($list->list_name);?>" size="40" />
                                            <a href="javascript:saveListName('<?php echo $i?>','<?php echo $list->id?>')" title="<?php echo Text::_('OS_SAVE')?>">
                                                <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/savefavorites.png" />
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <?php
                                if($configClass['active_alertemail'] == 1){
                                ?>
                                <td width="25%" style="background-color:<?php echo $bgcolor?>;" class="center padding3 paddingleft20">
                                    <div id="div_send_status_<?php echo $list->id; ?>">
                                        <?php
                                        if($list->receive_email == 0){
                                            ?>
                                            <a href="javascript:updateSendEmailStatus(<?php echo $list->id?>,1);" title="<?php echo Text::_('OS_CLICK_HERE_TO_RECEIVE_ALERT_EMAIL_WHEN_NEW_PROPERTIES_ARE_ADDED');?>">
                                                <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/publish_x.png"/>
                                            </a>
                                            <?php
                                        }else{
                                            ?>
                                            <a href="javascript:updateSendEmailStatus(<?php echo $list->id;?>,0);" title="<?php echo Text::_('OS_IF_YOU_DONT_WANT_TO_RECEIVE_ALERT_EMAIL_PLEASE_CLICK_HERE');?>">
                                                <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/tick.png"/>
                                            </a>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </td>
                                <?php } ?>
                                <td width="30%" class="center padding3" style="background-color:<?php echo $bgcolor?>;">
                                    <?php echo $list->created_on?>
                                </td>
                            </tr>
                            <?php
                            $k = 1 - $k;
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <select name="cid[]" id="cidchkid" multiple class="nodisplay">
            <?php echo $select?>
            </select>
            <input type="hidden" name="current_item" id="current_item" value="" />
            <input type="hidden" name="option" value="com_osproperty" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
            <input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root(); ?>" />
            <input type="hidden" name="select_item" id="select_item" value=""/>

		</form>
		<script type="text/javascript">
            function updateSendEmailStatus(list_id,send_status){
                var select_item = document.getElementById('select_item');
                select_item.value = list_id;
                updateSendEmailStatusAjax(list_id,send_status,'<?php echo Uri::root();?>');
            }

		function showInputbox(id){
			var div_input = document.getElementById('div_input_' + id);
			if(div_input != null){
				if(div_input.style.display == "inline"){
					div_input.style.display = "none";
				}else{
					div_input.style.display = "inline";
				}
			}
		}
		
		function saveListName(id,list_id){
			var list_name = document.getElementById('list_name_' + id);
			var current_item = document.getElementById('current_item');
			current_item.value = id;
			if(list_name != null){
				saveListNameAjax(id,list_id,list_name.value,'<?php echo Uri::root();?>','<?php echo $jinput->getInt('Itemid',0)?>');
			}
		}
		
		function removeList(){
			var form = document.searchlistform;
			check = 0;
			var cid = document.getElementById('cidchkid');
			var length = cid.options.length;
			for(i=0;i<length;i++){
				if(cid.options[i].selected == true){
					check = 1;
				}
			}
			if(check == 0){
				alert("<?php echo Text::_('OS_PLEASE_SELECT_ITEM_TO_REMOVE')?>");
			}else{
				var answer = confirm("<?php echo Text::_('DO_YOU_WANT_TO_REMOVE_SEARCH_LISTS')?>");
				if(answer == 1){
					form.task.value = "property_removesearchlist";
					form.submit();
				}
			}
		}
		
		function updateSelectList(list_id){
			var cidchkid = document.getElementById('cidchkid');
			var length = cidchkid.options.length;
			for(i=0;i<length;i++){
				if(cidchkid.options[i].value == list_id){
					if(cidchkid.options[i].selected == true){
						cidchkid.options[i].selected = false;
					}else{
						cidchkid.options[i].selected = true;
					}
				}
			}
		}
		</script>
		<?php
	}
	/**
	 * Advance search function
	 *
	 * @param unknown_type $option
	 */
	static function advSearchForm($option,$groups,$lists,$rows,$pageNav,$param,$adv_type,$dosearch){
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$ismobile;
		$db = Factory::getDbo();
		OSPHelper::generateHeading(2,Text::_('OS_ADVSEARCH'));
		?>
		<div class="clearfix"></div>
		<form method="GET" action="<?php echo Route::_("index.php?option=com_osproperty&view=ladvsearch&Itemid=".$jinput->getInt('Itemid',0));?>" name="ftForm" id="ftForm">
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> osp-container" id="advsearchformdiv">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
			<?php
			$show_advancesearchform = $jinput->getInt('show_advancesearchform',1);
			if($show_advancesearchform == 0){
				?>
				<div class="nodisplay">
				<?php
				}
				//echo $configClass['adv_type_ids'];
				if(($configClass['adv_type_ids'] == "0") or ($configClass['adv_type_ids'] == ""))
				{
					//HelperOspropertyCommon::advsearchForm($groups,$lists,0);
					HelperOspropertyCommon::advsearchForm($groups,$lists,0);
				}
				else
				{
					$adv_type_ids = $configClass['adv_type_ids'];
					$adv_type_idsArr = explode("|",$adv_type_ids);
					?>
					<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
						<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
							<ul class="nav nav-tabs">
								<?php
								for($i=0;$i<count($adv_type_idsArr);$i++)
								{
									$tid = $adv_type_idsArr[$i];
									$db->setQuery("Select * from #__osrs_types where id = '$tid'");
									$ptype = $db->loadObject();
									$type_name = OSPHelper::getLanguageFieldValue($ptype,'type_name');
									if($adv_type > 0)
									{
										if($tid == $adv_type)
										{
											$active = "class='active'";
										}
										else
										{
											$active = "";
										}
									}
									else
									{
										if($i ==0)
										{
											$active = "class='active'";
											$adv_type = $adv_type_idsArr[0];
										}
										else
										{
											$active = "";
										}
									}
									?>
									<li <?php echo $active;?>><a href="<?php echo Route::_('index.php?option=com_osproperty&view=ladvsearch&adv_type='.$tid.'&Itemid='.$jinput->getInt('Itemid',0))?>"><?php echo $type_name;?></a></li>
									<?php
								}
								?>
							</ul>
						</div>
						<div class="tab-content <?php echo $bootstrapHelper->getClassMapping('span12'); ?>" id="searchform<?php echo $adv_type;?>">
							<div class="tab-pane active overflowhidden" id="<?php echo strtolower(str_replace(" ","_",$type_name))?>">
								<?php
								HelperOspropertyCommon::advsearchForm($groups,$lists,$adv_type);
								?>
							</div>
						</div>
					</div>
					<?php
				}
				if($show_advancesearchform == 0)
				{
				?>
				    </div>
				<?php
				}
				?>
			</div>
			<div id="search_list_div"></div>
			<div class="clearfix"></div>
			<?php
			if($dosearch  == 0)
			{
				?>
				<div>
					<strong><?php echo Text::_('OS_PLEASE_SELECT_AT_LEAST_ONE_CRITERIA')?></strong>
				</div>
				<?php
			}
			else
			{
				if(count($rows) > 0)
				{
					for($i=0;$i<count($rows);$i++)
					{
						$row = $rows[$i];
						$otherInforArr = [];
						//$rows[$i]->city = HelperOspropertyCommon::loadCityName($rows[$i]->city);
						if(!$ismobile)
						{
							if($configClass['listing_show_view'] == 1)
							{
								 $otherInforArr[count($otherInforArr)] = Text::_('OS_TOTAL_VIEWING').": <strong>".$row->hits."</strong>";
							}
						}
						if(($configClass['listing_show_rating'] == 1) and ($configClass['show_rating'] == 1))
						{
							$otherInforArr[count($otherInforArr)] = Text::_('OS_RATE').": <strong>".$row->rating."</strong>";
						}
						if($configClass['listing_show_ncomments'] == 1)
						{
							$otherInforArr[count($otherInforArr)] = Text::_('OS_COMMENTS').": <strong>".$row->comment."</strong>";
						}
						$row->other_information = implode(", ",$otherInforArr);
					}
				?>
				<div class="clearfix"></div>
				<div id="listings">
					<div class="block_caption">
                        <?php
                        echo Text::_('OS_RESULTS');
                        echo " ";
                        echo $pageNav->limitstart." - ";
                        if($pageNav->total < $pageNav->limit)
                        {
                            echo $pageNav->total." ";
                        }
                        elseif($pageNav->limitstart + $pageNav->limit > $pageNav->total)
                        {
                            echo $pageNav->total." ";
                        }
                        else
                        {
                            echo $pageNav->limitstart + $pageNav->limit." ";
                        }
                        echo Text::_('OS_OF');
                        echo " ".$pageNav->total;
                        ?>
					</div>
					<?php
					$document = Factory::getDocument();
					$tpl = new OspropertyTemplate();
					$tpl->set('jinput',$jinput);
					$tpl->set('rows',$rows);
					$tpl->set('pageNav',$pageNav);
					$tpl->set('temp_path_img',Uri::root()."components/com_osproperty/templates/default/img");
					$tpl->set('configClass',$configClass);
					$tpl->set('bootstrapHelper',$bootstrapHelper);
					//$tpl->set('symbol',$symbol);
					$tpl->set('ismobile',$ismobile);
					
					$db->setQuery("Select * from #__osrs_themes where published = '1'");
					$theme = $db->loadObject();
					$themename = ($theme->name!= "")? $theme->name:"default";

					$tpl->set('themename',$themename);
					$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
					$themeobj = $db->loadObject();
					$params = $themeobj->params;
					$params = new Registry($params) ;
					$listview = 1;
					if(($themename == "default") or ($theme->default_duplicate == 1))
					{
						$default_list_view = $params->get('default_layout',1);
						switch ($default_list_view)
                        {
							case "1":
								$listview = "1";
							break;
							case "2":
								$listview = "3";
							break;
							case "3":
								$listview = "2";
							break;
						}
					}
					
					$view_type_cookie = $jinput->getInt('listviewtype',$listview);
					if($_COOKIE['viewtypecookie'] == 0)
					{
						$_COOKIE['viewtypecookie'] = $listview;
					}
					if($view_type_cookie == 0)
					{
						$view_type_cookie = $_COOKIE['viewtypecookie'];	
					}
					
					$tpl->set('params',$params);
					
					$tpl->set('path',JPATH_COMPONENT.'/templates/'.$themename);
					if($view_type_cookie == 1)
					{
						$body = $tpl->fetch("results.html.tpl.php");
					}
					elseif($view_type_cookie == 3)
                    {
						if(file_exists(JPATH_COMPONENT."/templates/".$themename."/results.grid.html.tpl.php"))
						{
							$body = $tpl->fetch("results.grid.html.tpl.php");
						}
						else
						{
							$body = $tpl->fetch("results.html.tpl.php");	
						}
					}
					elseif($view_type_cookie == 2)
                    {
						if(file_exists(JPATH_COMPONENT."/templates/".$themename."/results.map.html.tpl.php"))
						{
							$body = $tpl->fetch("results.map.html.tpl.php");
						}
						else
						{
							$body = $tpl->fetch("results.html.tpl.php");	
						}
					}
					echo $body;
					?>
				</div>
				<?php
				}
				else
				{
					?>
					<div class="clearfix"></div>
					<div>
						<strong><?php echo Text::_('OS_NO_RESULT')?></strong>
					</div>
					<?php
				}
			}
			?>
			</div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_advsearch" >
		<input type="hidden" name="show_more_div" id="show_more_div" value="0" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		<input type="hidden" name="search_param" id="search_param" value="<?php echo implode("_",$param);?>" />
		<input type="hidden" name="list_id" id="list_id" value="<?php echo $jinput->getInt('list_id',0)?>" />
		<input type="hidden" name="adv_type" id="adv_type" value="<?php echo $adv_type?>" />
		<input type="hidden" name="show_advancesearchform" value="<?php echo $show_advancesearchform;?>" />
		<input type="hidden" name="city_name" id="city_name" value="city" />
		<?php 
		$db->setQuery("Select id from #__osrs_types");
        $types = $db->loadObjectList();
        if(count($types) > 0)
        {
        	foreach ($types as $type)
        	{
        		$db->setQuery("Select fid from #__osrs_extra_field_types where type_id = '$type->id' and fid in (Select id from #__osrs_extra_fields where published = '1' and searchable = '1')");
        		$type->fields = $db->loadColumn(0);
        		?>
        		<input type="hidden" name="advtype_id_<?php echo $type->id?>" id="advtype_id_<?php echo $type->id?>" value="<?php echo implode(",",$type->fields);?>"/>
        		<?php
        	}
        }
		?>
		</form>

		<script type="text/javascript">
		var more_option_span = document.getElementById('more_option_span');
		jQuery('#more_option_span').click(function() {
		  var show_more_div = document.getElementById('show_more_div');
		  show_more_div_value = show_more_div.value;
		  if(show_more_div_value == 0){
		  	more_option_span.innerHTML = "<?php echo Text::_('OS_LESS_OPTION')?>&nbsp; <i class='osicon-chevron-up'></i>";
		  	show_more_div.value = 1;
			jQuery("#more_option_div").removeClass('nodisplay');
		  	jQuery('#more_option_div').show('slow');
		  }else{
		  	more_option_span.innerHTML = "<?php echo Text::_('OS_MORE_OPTION')?>&nbsp; <i class='osicon-chevron-down'></i>";
		  	show_more_div.value = 0;
			jQuery("#more_option_div").addClass('nodisplay');
		  	jQuery('#more_option_div').hide('slow');  
		  }
		});
		function change_country_company(country_id,state_id,city_id)
		{
			var live_site = '<?php echo Uri::root()?>';
			<?php
			if(OSPHelper::isJoomlaMultipleLanguages())
			{
				$lang = OSPHelper::getCurrentLanguage();
			}
			?>
			loadLocationInfoStateCityLocator(country_id,state_id,city_id,'country','state_id',live_site,'<?php echo $lang?>');
		}
		function change_state(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			<?php
			if(OSPHelper::isJoomlaMultipleLanguages())
			{
				$lang = OSPHelper::getCurrentLanguage();
			}
			?>
			loadLocationInfoCity(state_id,city_id,'state_id',live_site,'<?php echo $lang?>');
		}
		function saveSearchList(){
			//var live_site = '<?php echo Uri::root()?>';
			//saveSearchListAjax(document.advForm.search_param.value,live_site,'<?php echo $jinput->getInt('Itemid',0)?>');
			document.ftForm.task.value = "property_saveSearchList";
			document.ftForm.submit();
		}
		
		function updateSearchList(){
			document.ftForm.task.value = "property_updateSearchList";
			document.ftForm.submit();
		}
		</script>
		
		<?php
	}
	
	static function showPropertyCityListing($option,$type_id,$category_id,$show_filter_agent,$show_filter_state,$show_filter_keyword,$show_filter_bed,$show_filter_bath,$show_filter_price,$show_filter_room,$menu,$city_id,$state_id,$country_id){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$filterParams = [];
		//show cat
		$filterParams[0] = 0;
		//agent
		$filterParams[1] = 0;
		//keyword
		$filterParams[2] = $show_filter_keyword;
		//bed
		$filterParams[3] = 0;
		//bath
		$filterParams[4] = 0;
		//rooms
		$filterParams[5] = 0;
		//price
		$filterParams[6] = $show_filter_state;
		//property type 
		$filterParams[7] = 0;
		//state
		$filterParams[8] = $show_filter_state;
		$property_type	= $jinput->getInt('property_type',0);
		$keyword		= $jinput->getString('keyword','');
		$nbed			= $jinput->getInt('nbed',0);
		$nbath			= $jinput->getInt('nbath',0);
		$isfeatured		= $jinput->getInt('isfeatured',0);
		$nrooms			= $jinput->getInt('nrooms',0);
		$orderby		= $jinput->getString('orderby',$configClass['default_sort_properties_by']);
		$ordertype		= $jinput->getString('ordertype',$configClass['default_sort_properties_type']);
		$limitstart		= $jinput->getInt('limitstart',0);
		$limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
		$favorites		= $jinput->getInt('favorites',0);
		$price			= $jinput->getInt('price',0);
		$agent_id		= $jinput->getInt('agent_id',0);

		?>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=property_city&id='.$city_id.'&Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
		<?php
		OspropertyListing::listProperties($option,'',$category_id,$agent_id,$type_id,$keyword,$nbed,$nbath,$isfeatured,0,$nrooms,$orderby,$ordertype,$limitstart,$limit,$favorites,$price,$filterParams,$city_id,$state_id,$country_id,0,1,-1);
		?>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="property_city" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		<input type="hidden" name="id" value="<?php echo $city_id?>" />
		<input type="hidden" name="view" id="view" value="<?php echo $jinput->getString('view','');?>" />
		</form>
		<?php
	}
	/**
	 * List all the properties of one property type
	 *
	 * @param unknown_type $option
	 * @param unknown_type $type_id
	 */
	static function showPropertyTypeListing($option,$type,$category,$show_filter_agent,$show_filter_state,$show_filter_keyword,$show_filter_bed,$show_filter_bath,$show_filter_price,$show_filter_room,$menu,$city_id,$state_id,$country_id,$isFeatured,$isSold,$orderby,$ordertype,$company_id,$max_properties,$show_filterform,$show_categoryfilter,$show_propertytypefilter,$show_locationfilter,$show_keywordfilter,$show_pricefilter,$show_agenttypefilter,$agenttype,$show_marketstatusfilter,$min_price,$max_price,$price){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$db = Factory::getDbo();
		$filterParams = [];
		//show cat
		$filterParams[0] = 0;
		//agent
		$filterParams[1]  = $show_filter_agent;
		//keyword
		$filterParams[2]  = $show_filter_keyword;
		//bed
		$filterParams[3]  = $show_filter_bed;
		//bath
		$filterParams[4]  = $show_filter_bath;
		//rooms
		$filterParams[5]  = 0;
		//price
		$filterParams[6]  = $show_filter_state;
		//property type 
		$filterParams[7]  = 0;
		//state
		$filterParams[8]  = $show_filter_state;
		
		$filterParams[9]  = $show_categoryfilter;
		
		$filterParams[10] = $show_propertytypefilter;
		
		$filterParams[11] = $show_locationfilter;
		
		$filterParams[12] = $show_keywordfilter;
		
		$filterParams[13] = $show_pricefilter;
		
		$filterParams[14] = $show_agenttypefilter;

		$filterParams[15] = $show_marketstatusfilter;
		
		//$category_id 	= $category->id;
		//$property_type	= $jinput->getInt('property_type',0);
		//if($property_type == 0){
			//$property_type = $jinput->getInt('type_id',0);
		//}
		$property_type  = (int)$type->id;
		$keyword		= OSPHelper::getStringRequest('keyword','','');

		$nbed			= $jinput->getInt('nbed',0);
		$nbath			= $jinput->getInt('nbath',0);
		$nrooms			= $jinput->getInt('nrooms',0);

		$limitstart		= $jinput->getInt('limitstart',0);
		$limit			= $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
		$favorites		= $jinput->getInt('favorites',0);
		//$price			= $jinput->getInt('price',0);
		$agent_id		= $jinput->getInt('agent_id',0);
		
		OSPHelper::generateHeading(1,'');
		OSPHelper::generateHeading(2,'');
		
		$app		= Factory::getApplication();
		$menus		= $app->getMenu('site');
		$itemid     = $jinput->getInt('Itemid');
		$type_url = "";
		$cat_url = "";
		if($itemid > 0){
			$active = $menus->getActive();
			$url = $active->link;
			$type_id_menu = $active->query['type_id'];
			if(($property_type > 0) and (intval($type_id_menu) > 0) and ($property_type == $type_id_menu)){
				$type_url = "&type_id=".$property_type;
			}
			$catIds_menu = (array)$active->query['catIds'];
			if((count($catIds_menu) > 0) && count($category) > 0 && OSPHelper::array_equal($catIds_menu,$category))
			{
				//$cat_url = "&type_id=".$property_type;
				$cat_url = "";
				foreach ($catIds_menu as $catid)
				{
					$cat_url .= "&catIds[]=".$catid;
				}
			}
			
			$country_id_menu = $active->query['country_id'];
			if(($country_id > 0) and (intval($country_id_menu) > 0) and ($country_id == $country_id_menu)){
				$country_url = "&country_id=".$country_id_menu;
			}
			
			$company_id_menu = $active->query['company_id'];
			if(($company_id > 0) and (intval($company_id_menu) > 0) and ($company_id == $company_id_menu)){
				$company_url = "&company_id=".$company_id;
			}
			
		}else{
			if($property_type > 0){
				$type_url = "&type_id=".$property_type;
			}
		}

		$db->setQuery("Select count(id) from #__osrs_property_listing_layout where id = '$itemid'");
		$count = $db->loadResult();
		if($count == 0)
		{
            $state_id = (int) $state_id;
            $agenttype = (int) $agenttype;
			$db->setQuery("Insert into #__osrs_property_listing_layout (id,itemid,category_id,type_id,country_id,company_id, featured,sold,state_id,agenttype) values (NULL,'$itemid','0','$property_type','$country_id','$company_id','$isFeatured','$isSold','$state_id','$agenttype')");
			$db->execute();
		}
		
		?>
		<div class="clearfix"></div>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&view=ltype'.$cat_url.$type_url.$country_url.$company_url.'&Itemid='.$itemid)?>" name="ftForm" id="ftForm">
		<?php
		OspropertyListing::listProperties($option,$company_id,$category,$agent_id,$property_type,$keyword,$nbed,$nbath,$isFeatured,$isSold,$nrooms,$orderby,$ordertype,$limitstart,$limit,$favorites,$price,$filterParams,$city_id,$state_id,$country_id,$max_properties,$show_filterform,$agenttype,0,$min_price,$max_price);
		?>
		<input type="hidden" name="option" value="<?php echo $option?>" />
		<input type="hidden" name="task" value="property_type" />
		<input type="hidden" name="Itemid" id="Itemid" value="<?php echo $jinput->getInt('Itemid',0);?>" />
		<input type="hidden" name="view" id="view" value="<?php echo OSPHelper::getStringRequest('view','','');?>" />
		<input type="hidden" name="max_properties" id="max_properties" value="<?php echo $max_properties;?>" />
		<input type="hidden" name="show_filterform" id="show_filterform" value="<?php echo $show_filterform;?>" />
		<input type="hidden" name="show_categoryfilter" id="show_categoryfilter" value="<?php echo $show_categoryfilter;?>" />
		<input type="hidden" name="show_propertytypefilter" id="show_propertytypefilter" value="<?php echo $show_propertytypefilter;?>" />
		<input type="hidden" name="show_locationfilter" id="show_locationfilter" value="<?php echo $show_locationfilter;?>" />
		<input type="hidden" name="show_pricefilter" id="show_pricefilter" value="<?php echo $show_pricefilter;?>" />
		<input type="hidden" name="show_keywordfilter" id="show_keywordfilter" value="<?php echo $show_keywordfilter;?>" />
		<input type="hidden" name="show_agenttypefilter" id="show_agenttypefilter" value="<?php echo $show_agenttypefilter;?>" />
		<input type="hidden" name="show_marketstatusfilter" id="show_marketstatusfilter" value="<?php echo $show_marketstatusfilter;?>" />
		<input type="hidden" name="city_name" id="city_name" value="city_id" />
		</form>
		<?php
		
	}
	/**
	 * List Properties
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $pageNav
	 */
	static function listProperties($option,$rows,$pageNav,$lists,$filterParams){
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$symbol;
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		$user = Factory::getUser();

		$tpl = new OspropertyTemplate();
		for($i=0;$i<count($rows);$i++){
			$row = $rows[$i];
			$otherInforArr = [];

            if($configClass['listing_show_view'] == 1){
                 $otherInforArr[count($otherInforArr)] = Text::_('OS_TOTAL_VIEWING').": <strong>".$row->hits."</strong>";
            }

			if(($configClass['listing_show_rating'] == 1) and ($configClass['show_rating'] == 1)){
				$otherInforArr[count($otherInforArr)] = Text::_('OS_RATE').": <strong>".$row->rating."</strong>";
			}
			
			if($configClass['listing_show_ncomments'] == 1){
				$otherInforArr[count($otherInforArr)] = Text::_('OS_COMMENTS').": <strong>".$row->comment."</strong>";
			}
			
			$row->other_information = implode(", ",$otherInforArr);
			$inFav = 0;
			if($user->id > 0){
				$db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
				$count = $db->loadResult();
				if($count > 0){
					$inFav = 1;
				}else{
					$inFav = 0;
				}
			}
			$row->inFav = $inFav;
		}
		$tpl->set('rows',$rows);
		$tpl->set('pageNav',$pageNav);
		$tpl->set('task',$jinput->getString('task',''));
		$tpl->set('temp_path_img',OspropertyTemplate::livePath()."/img");
		
		//$tpl->set('filterParams',$filterParams);
		$showcat = $filterParams[0];
		$tpl->set('showcat',$showcat);
		$showagent = $filterParams[1];
		$tpl->set('showagent',$showagent);
		$showkeyword = $filterParams[2];
		$tpl->set('showkeyword',$showkeyword);
		$showbed = $filterParams[3];
		$tpl->set('showbed',$showbed);
		$showbath = $filterParams[4];
		$tpl->set('showbath',$showbath);
		$showrooms = $filterParams[5];
		$tpl->set('showrooms',$showrooms);
		$showprice = $filterParams[6];
		$showtype = $filterParams[7];
		$tpl->set('showtype',$showtype);
		$showstate = $filterParams[8];
		$tpl->set('showstate',$showstate);
		$show_categoryfilter				= $filterParams[9];
		$tpl->set('show_categoryfilter',$show_categoryfilter);
		$show_propertytypefilter			= $filterParams[10];
		$tpl->set('show_propertytypefilter',$show_propertytypefilter);
		$show_locationfilter				= $filterParams[11];
		$tpl->set('show_locationfilter',$show_locationfilter);
		$show_keywordfilter					= $filterParams[12];
		$tpl->set('show_keywordfilter',$show_keywordfilter);
		$show_pricefilter					= $filterParams[13];
		$tpl->set('show_pricefilter',$show_pricefilter);
		$show_agenttypefilter				= $filterParams[14];
		$tpl->set('show_agenttypefilter',$show_agenttypefilter);
		$show_marketstatusfilter			= $filterParams[15];
		$tpl->set('show_marketstatusfilter',$show_marketstatusfilter);
		
		$lists['show_categoryfilter'] 		= $show_categoryfilter;
		$lists['show_propertytypefilter'] 	= $show_propertytypefilter;
		$lists['show_keywordfilter'] 		= $show_keywordfilter;
		$lists['show_pricefilter'] 			= $show_pricefilter;
		$lists['show_locationfilter'] 		= $show_locationfilter;
		$lists['show_agenttypefilter'] 		= $show_agenttypefilter;
		$lists['show_marketstatusfilter'] 	= $show_marketstatusfilter;

		
		$tpl->set('lists',$lists);
		$tpl->set('showprice',$showprice);
		$tpl->set('configClass',$configClass);
		$tpl->set('symbol',$symbol);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$tpl->set('jinput',$jinput);

        $themename = OSPHelper::getThemeName();
		$tpl->set('themename',$themename);
		//echo $themename;
		$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
		$themeobj = $db->loadObject();
		$params = $themeobj->params;
		$params = new Registry($params) ;
		$listview = 1;

		if(($themename == "default") or ($themeobj->default_duplicate == 1)){
			$default_list_view = $params->get('default_layout',1);
			switch ($default_list_view){
				case "1":
					$listview = "1";
				break;
				case "2":
					$listview = "3";
				break;
				case "3":
					$listview = "2";
				break;
			}
		}
		
		$view_type_cookie = $jinput->getInt('listviewtype',$listview);
		//echo $view_type_cookie;
		if($_COOKIE['viewtypecookie'] == 0){
			$_COOKIE['viewtypecookie'] = $listview;
		}
		if($view_type_cookie == 0){
			$view_type_cookie = $_COOKIE['viewtypecookie'];	
		}

		$tpl->set('params',$params);
		if($view_type_cookie == 1)
		{
			$tpl->set("path", JPATH_COMPONENT."/templates/".$themename);
			$body = $tpl->fetch("listing.html.tpl.php");
		}
		elseif($view_type_cookie == 3)
		{
			if(file_exists(JPATH_COMPONENT."/templates/".$themename."/grid.html.tpl.php"))
			{
				$tpl->set("path", JPATH_COMPONENT."/templates/".$themename);
				$body = $tpl->fetch("grid.html.tpl.php");
			}
			else
			{
				$tpl->set("path", JPATH_COMPONENT."/templates/".$themename);
				$body = $tpl->fetch("listing.html.tpl.php");	
			}
		}
		elseif($view_type_cookie == 2)
		{
			if(file_exists(JPATH_COMPONENT."/templates/".$themename."/map.html.tpl.php"))
			{
				$tpl->set("path", JPATH_COMPONENT."/templates/".$themename);
				$body = $tpl->fetch("map.html.tpl.php");
			}
			else
			{
				$tpl->set("path", JPATH_COMPONENT."/templates/".$themename);
				$body = $tpl->fetch("listing.html.tpl.php");	
			}
		}
		echo $body;
	}
	
	public static function _getSchoolData($values) 
    {
		$key	 = $values['key'];
		$radius	 = $values['radius'];
		$min	 = $values['min'];
		$lat	 = $values['latitude'];
		$lon	 = $values['longitude'];
		$zip	 = $values['zip'];
        $city    = urlencode($values['city']);
        $state   = $values['state'];

		$query_string = "";
		$query_string .= "key=" . $key;
		$query_string .= "&v=3";
		$query_string .= "&f=system.multiCall";
		$query_string .= "&resf=php";

		// do the school search
		$query_string .= "&methods[0][f]=schoolSearch";
		$query_string .= "&methods[0][sn]=sf";
		$query_string .= "&methods[0][key]=" . $key;
		if($lat != 0 && $lon != 0) {
			$query_string .= "&methods[0][latitude]=" . $lat;
			$query_string .= "&methods[0][longitude]=" . $lon;
			$query_string .= "&methods[0][distance]=" . $radius;
		} elseif (($lat = 0 && $lon = 0) && $zip != 0) {
			$query_string .= "&methods[0][zip]=" . $zip;
		}
		$query_string .= "&methods[0][minResult]=" . $min;
		$query_string .= "&methods[0][fid]=F1";

		// do the branding search
		$query_string .= "&methods[1][f]=gbd";
                $query_string .= "&methods[1][city]=" . $city;
                if($state) $query_string .= "&methods[1][state]=" . $state;
        $query_string .= "&methods[1][sn]=sf";
		$query_string .= "&methods[1][key]=" . $key;
		$query_string .= "&methods[1][fid]=F2";

		$result = self::_curlContents($query_string);

		$schoolinfo = unserialize($result);

		return $schoolinfo;
	}
	
	public static function _curlContents($u)
    {
    	$url = "http://api.education.com/service/service.php?" . $u;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
    }
	
	/**
	 * Property details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $property
	 */
	static function propertyDetails($option,$row,$configs,$owner){
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$ismobile,$lang_suffix,$languages;
		$db = Factory::getDbo();
		$document = Factory::getDocument();
		//$document->addScript(Uri::root()."media/com_osproperty/assets/js/ajax.js","text/javascript",true);
        $themename = OSPHelper::getThemeName();
		$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
		$themeobj = $db->loadObject();
		?>
		<script type="text/javascript">
		function submitForm(form_id){
			var form = document.getElementById(form_id);
			var temp1,temp2;
			var cansubmit = 1;
			var require_field = form.require_field;
			require_field = require_field.value;
			var require_label = form.require_label;
			require_label = require_label.value;
			var require_fieldArr = require_field.split(",");
			var require_labelArr = require_label.split(",");
			for(i=0;i<require_fieldArr.length;i++){
				temp1 = require_fieldArr[i];
				temp2 = document.getElementById(temp1);
				
				if(temp2 != null){
					if(temp2.value == ""){
						alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY_FIELD')?>");
						temp2.focus();
						cansubmit = 0;
						return false;
					}else if(temp1 == "requestyour_email"){
						if(!validateEmail(temp2.value)){
							alert(" <?php echo Text::_('OS_PLEASE_ENTER_VALID_EMAIL')?>");
							temp2.focus();
							cansubmit = 0;
							return false;
						}
					}else if(temp1 == "comment_security_code"){
						var captcha_str = form.captcha_str;
						captcha_str = captcha_str.value;
						if(captcha_str != temp2.value){
							alert(" <?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
							temp2.focus();
							cansubmit = 0;
							return false;
						}
					}else if(temp1 == "request_security_code"){
						var captcha_str = form.captcha_str;
						captcha_str = captcha_str.value;
						if(captcha_str != temp2.value){
							alert("<?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
							temp2.focus();
							cansubmit = 0;
							return false;
						}
						<?php
						if($configClass['request_term_condition'] == 1){
							?>
							if(form.termcondition.checked == false){
								alert(" <?php echo Text::_('OS_PLEASE_AGREE_WITH_OUT_TERM_AND_CONDITION')?>");
								document.getElementById('termcondition').focus();
								cansubmit = 0;
								return false;
							}
							<?php
						}
						?>
					}else if(temp1 == "sharing_security_code"){
						var captcha_str = form.captcha_str;
						captcha_str = captcha_str.value;
						if(captcha_str != temp2.value){
							alert("<?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
							temp2.focus();
							cansubmit = 0;
							return false;
						}
					}else if(temp1 == "agree_privacy_policy"){
						var agree_privacy_policy = form.agree_privacy_policy;
						if(agree_privacy_policy.checked == false){
							alert("<?php echo Text::_('OS_AGREE_PRIVACY_POLICY_ERROR');?>");
							temp2.focus();
							cansubmit = 0;
							return false;
						}
					}
				}
			}
			if(cansubmit == 1){
				form.submit();
			}
		}
		</script>
        <?php
        $session = Factory::getSession();
        $url = $session->get('advurl');
        ?>
        <form method="GET" action="<?php echo $url; ?>" id="subform" name="subform">
            <input type="hidden" name="option" value="com_osproperty" />
        </form>
		<?php
		echo HelperOspropertyCommon::buildToolbar('property');
		//location
		$location = "";
		if($row->show_address == 1){
			ob_start();
			?>
			<table  width="100%">
				<tr>
					<td class="left_details_col">
						<?php echo Text::_('OS_ADDRESS')?>
					</td>
				<?php
					if($ismobile){
						echo "</tr><tr>";
					}
				?>
					<td class="right_details_col" >
						<?php
						echo OSPHelper::generateAddress($row);
						?>
					</td>
				</tr>
			</table>
			<?php
			$location = ob_get_contents();
			ob_end_clean();
		}
		$row->location = $location;
		//end location
		
		//property information
		ob_start();
		?>
		<table  width="100%">
			<?php
			if(($row->ref != "") and ($configClass['show_ref'] == 1)){
				?>
				<tr>
					<td class="left_details_col" >
						<?php echo Text::_('Ref #')?>
					</td>
					<td class="right_details_col" >
						<strong><?php echo $row->ref;?></strong>
					</td>
				</tr>
				<?php
			}
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_TITLE')?>
				</td>
				<td class="right_details_col" >
					<strong><?php echo OSPHelper::getLanguageFieldValue($row,'pro_name');?></strong>
				</td>
			</tr>
			<tr>
				<td class="left_details_col">
					<?php echo Text::_('OS_CATEGORY')?>
				</td>
				<td class="right_details_col">
					<a href="<?php echo Route::_('index.php?option=com_osproperty&task=category_details&id='.$row->category_id)?>" title="<?php echo OSPHelper::getLanguageFieldValue($row,'category_name');?>">
						<?php echo OSPHelper::getLanguageFieldValue($row,'category_name');?>
					</a>
				</td>
			</tr>
			<?php
			if($row->type_name != ""){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_PROPERTY_TYPE')?>
				</td>
				<td class="right_details_col" >
					<a href="<?php echo Route::_("index.php?option=com_osproperty&task=property_type&type_id=".$row->pro_type);?>">
						<?php echo OSPHelper::getLanguageFieldValue($row,'type_name');?>
					</a>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td class="left_details_col">
					<?php echo Text::_('OS_FEATURED')?>
			<?php
			if(!$ismobile){
			?>
				</td>
				<td class="right_details_col">
			<?php }  ?>
					<span class="fontnormal">
					<?php
					if($row->isFeatured == 1){
						echo Text::_('OS_YES');
					}else{
						echo Text::_('OS_NO');
					}
					?>
					</span>
				</td>
			</tr>
			<?php
			
			if($row->rent_time != ""){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_RENT_TIME_FRAME')?>
					<span class="fontnormal">
					<?php echo Text::_($row->rent_time);?>
					</span>
				</td>
			</tr>
			<?php
			}
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_DESCRIPTION')?>
				</td>
				<td class="right_details_col" >
					<?php echo OSPHelper::getLanguageFieldValue($row,'pro_small_desc');?>
					<BR />
					<?php 
						$row->pro_full_desc =  HTMLHelper::_('content.prepare', $row->pro_full_desc);
						echo stripslashes(OSPHelper::getLanguageFieldValue($row,'pro_full_desc'));
					?>
				</td>
			</tr>
			<?php
			if($configClass['energy'] == 1){
				if(($row->energy > 0) or ($row->climate > 0)){
					if($row->energy == "0.00"){
						$row->energy = "null";
					}
					if($row->climate == "0.00"){
						$row->climate = "null";
					}
					?>
					<tr>
						<td class="left_details_col" >
							<?php echo Text::_('OS_DPE')?>
						</td>
						<td class="right_details_col" >
							<?php
							echo HelperOspropertyCommon::drawGraph($row->energy, $row->climate, $row->e_class, $row->c_classs);
							?>
						</td>
					</tr>
					<?php
				}
			}
			?>
			<?php
			if(($row->note != "") and (HelperOspropertyCommon::isOwner($row->id))){
			?>
			<tr>
				<td class="left_details_col">
					<?php echo Text::_('OS_NOTE')?>
				</td>
				<td class="right_details_col">
					<?php echo $row->note;?>
				</td>
			</tr>
			<?php
			}
			if(($row->pro_pdf != "") or ($row->pro_pdf_file != "")){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>
				</td>
			<?php
			if($ismobile){
				echo "</tr><tr>";
			}
			?>
				<td class="right_details_col" >
				
					<?php
					if($row->pro_pdf != ""){
						?>
						<a href="<?php echo $row->pro_pdf?>" title="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" alt="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" target="_blank">
							<img src="<?php echo Uri::root()."components/com_osproperty/images/assets"; ?>/pdf.png" />
						</a>
						&nbsp;&nbsp;
						<?php
					}
					?>
					<?php
					if($row->pro_pdf_file != ""){
						?>
						<a href="<?php echo Uri::root()."components/com_osproperty/document/";?><?php echo $row->pro_pdf_file?>" title="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" alt="<?php echo Text::_('OS_PROPERTY_DOCUMENT')?>" target="_blank">
							<img src="<?php echo Uri::root()."components/com_osproperty/images/assets"; ?>/pdf.png" />
						</a>
						<?php
					}
					?>
				</td>
			</tr>
			<?php } 
			if($configClass['listing_show_view'] == 1){
			?>
			<tr>
				<td class="left_details_col">
					<?php echo Text::_('OS_TOTAL_VIEWING')?>
				
				</td>
				<td class="right_details_col">
				
					<span class="fontnormal"><?php echo $row->hits;?></span>
				</td>
			</tr>
			<?php
			}
			if($configClass['listing_show_rating'] == 1){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_RATE')?>
				
				</td>
				<td class="right_details_col" >
					<?php
					if($row->number_votes > 0){
						$points = round($row->total_points/$row->number_votes);
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star-<?php echo $points;?>.jpg" />
						<?php
					}else{
						?>
						<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/star-0.png" />
						<?php
					}
					?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
		$info = ob_get_contents();
		ob_end_clean();
		$row->info = $info;
		//property information END
		
		ob_start();
		if($row->number_votes > 0){
			$points = round($row->total_points/$row->number_votes);
			?>
			<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-<?php echo $points;?>.png" />	
			<?php
		}else{
			?>
			<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-0.png" />	
			<?php
		}
		$ratingvalue = ob_get_contents();
		ob_end_clean();
		$row->ratingvalue = $ratingvalue;
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
		//price
		$db = Factory::getDbo();
		$db->setQuery("Select id as value, currency_code as text from #__osrs_currencies where id <> '$row->curr' and published = '1' order by currency_code");
		$currencies   = $db->loadObjectList();
		$currenyArr[] = HTMLHelper::_('select.option','',Text::_('OS_SELECT'));
		$currenyArr   = array_merge($currenyArr,$currencies);
		if(($themename == "default") || ($themeobj->default_duplicate == 1))
		{
			$show_price_text = 1;
		}
		else
		{
			$show_price_text = 0;
		}
		if(count($currencies) > 0)
		{
			$lists['curr'] = HTMLHelper::_('select.genericlist',$currenyArr,'curr','onChange="javascript:convertCurrency('.$row->id.',this.value,'.$show_price_text.')" class="input-small"','value','text',$row->curr);
			$lists['curr_default'] = HTMLHelper::_('select.genericlist',$currenyArr,'curr','onChange="javascript:convertCurrencyDefaultDetails('.$row->id.',this.value,'.$show_price_text.')" class="input-small"','value','text',$row->curr);
		}
		else
		{
			$lists['curr'] = "";
			$lists['curr_default'] = "";

		}
		//featured
		ob_start();
		?>
		<table  width="100%">
			<?php
			if($configClass['use_rooms'] == 1){
			?>
			<tr>
				<td class="left_details_col width50pc">
					<?php echo Text::_('OS_NUMBER_ROOMS')?>
				</td>
				<td class="right_details_col width50pc">
					<strong><?php echo $row->rooms;?></strong>
				</td>
			</tr>
			<?php
			}
			?>
			<?php
			if($configClass['use_bedrooms'] == 1){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_BEDROOM')?>
				</td>
				<td class="right_details_col" >
					<?php echo $row->bed_room;?>
				</td>
			</tr>
			<?php
			}
			?>
			<?php
			if($configClass['use_bathrooms'] == 1){
			?>
			<tr>
				<td class="left_details_col">
					<?php echo Text::_('OS_BATHROOM')?>
				</td>
				<td class="right_details_col">
					<?php echo OSPHelper::showBath($row->bath_room);?>
				</td>
			</tr>
			<?php
			}
			?>
			<?php
			if($configClass['use_parking'] == 1){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_PARKING')?>
				</td>
				<td class="right_details_col" >
					<?php echo $row->parking;?>
				</td>
			</tr>
			<?php
			}
			?>
			<?php
			if($configClass['use_squarefeet'] == 1){
			?>
			<tr>
				<td class="left_details_col">
					<?php echo OSPHelper::showSquareLabels(); // Text::_('OS_SQUARE_FEET')?>
				</td>
				<td class="right_details_col">
					<?php echo OSPHelper::showSquare($row->square_feet);?>
				</td>
			</tr>
<?php
			}
			?>
			<?php
			if($configClass['use_nfloors'] == 1){
			?>
			<tr>
				<td class="left_details_col" >
					<?php echo Text::_('OS_NUMBER_OF_FLOORS')?>
				</td>
				<td class="right_details_col" >
					<?php echo $row->number_of_floors;?>
				</td>
			</tr>
			<?php
			}
			?>
		</table>
		<?php
		$featured = ob_get_contents();
		ob_end_clean();
		$row->featured = $featured;
		//end featured
		
		//collect agent information
		//photo
		$allowedExt = array('jpg','jpeg','gif','png','webp');
		if($configClass['show_agent_image'] == 1){
			ob_start();
			$agent_photo = $row->agent->photo;
			$agent_photo_array = explode(".",$agent_photo);
			$ext = $agent_photo_array[count($agent_photo_array)-1];
			if(($agent_photo != "") and (in_array(strtolower($ext),$allowedExt))){
				?>
				<img src="<?php echo Uri::root()?>images/osproperty/agent/<?php echo $row->agent->photo?>" class="border1 width60" />
				<?php
			}else{
				?>
				
				<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/user.jpg" class="border1 width60"/>
				<?php
			}
			$photo = ob_get_contents();
			ob_end_clean();
			$row->agentphoto = $photo;
		}
		
		$row->agent_name = $row->agent->name;
		$row->agent_phone = $row->agent->phone;
		$row->agent_mobile = $row->agent->mobile;
		$row->agentdetails = $row->agent;
		//agent
		ob_start();
		?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <h3><?php echo $row->agent->name;?></h3>
            </div>
        </div>
        <?php
        if(OSPHelper::allowShowingProfile($row->agent->optin))
		{
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
                <table width="100%" class="border0">
                    <?php
                    if($configClass['show_agent_image'] == 1)
					{
                    ?>
                    <tr>
                        <td class="left_details_col width40pc" valign="top">
                            <?php
							echo OSPHelper::loadAgentType($row->agent->agent_type);
                            echo " ".Text::_('OS_PHOTO');

                            ?>
                        </td>
                        <td class="right_details_col width60pc">
                            <?php
                            $agent_photo = $row->agent->photo;
                            $agent_photo_array = explode(".",$agent_photo);
                            $ext = $agent_photo_array[count($agent_photo_array)-1];
                            if($agent_photo != "" && in_array(strtolower($ext),$allowedExt))
							{
                                ?>
                                <img src="<?php echo Uri::root()?>images/osproperty/agent/<?php echo $row->agent->photo?>" width="100" />
                                <?php
                            }
							else
							{
                                ?>

                                <img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/user.jpg" class="border1" width="100" />
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    }
                    if($configClass['show_agent_address'] == 1)
					{
                    ?>
                    <tr>
                        <td class="left_details_col">
                            <?php echo Text::_('OS_ADDRESS')?>
                        </td>
                        <td class="right_details_col">
                            <?php echo $row->agent->address;?>
                        </td>
                    </tr>

                    <tr>
                        <td class="left_details_col" >
                            <?php echo Text::_('OS_STATE')?>
                        </td>
                        <td class="right_details_col" >
                            <?php echo $row->agent->state_name;?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left_details_col">
                            <?php echo Text::_('OS_COUNTRY')?>
                        </td>
                        <td class="right_details_col">
                            <?php echo $row->agent->country_name;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if($configClass['show_license'] == 1){
                    ?>
                    <tr>
                        <td class="left_details_col" >
                            <?php echo Text::_('OS_LICENSE')?>
                        </td>
                        <td class="right_details_col" >
                            <?php echo $row->agent->license;?>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
			</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
                <table  width="100%" class="border0">
                    <?php
                    if(($row->agent->phone != "") and ($configClass['show_agent_phone'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col" >
                            <div class="agent_phone width100px">
                                <strong><?php echo Text::_('OS_PHONE')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col" >
                            <?php echo $row->agent->phone;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->mobile != "") and ($configClass['show_agent_mobile'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col">
                            <div class="agent_mobile width100px">
                                <strong><?php echo Text::_('OS_MOBILE')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col">
                            <?php echo $row->agent->mobile;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->fax != "")and ($configClass['show_agent_fax'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col" >
                            <div class="agent_fax width100px">
                                <strong><?php echo Text::_('OS_FAX')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col" >
                            <?php echo $row->agent->fax;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->gtalk != "")and ($configClass['show_agent_gplus'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col" >
                            <div class="agent_gtalk width100px">
                                <strong><?php echo Text::_('OS_GPLUS')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col" >
                            <?php echo $row->agent->gtalk;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->skype != "")and ($configClass['show_agent_skype'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col">
                            <div class="agent_skype width100px">
                                <strong><?php echo Text::_('OS_SKYPE')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col">
                            <?php echo $row->agent->skype;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->msn != "")and ($configClass['show_agent_msn'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col" >
                            <div class="agent_msn width100px">
                                <strong><?php echo Text::_('OS_LINE_MESSAGES')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col" >
                            <?php echo $row->agent->msn;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->yahoo != "")and ($configClass['show_agent_linkin'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col">
                            <div class="agent_linkedin width100px">
                                <strong><?php echo Text::_('OS_LINKEDIN')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col">
                            <?php echo $row->agent->yahoo;?>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->facebook != "")and ($configClass['show_agent_facebook'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col">
                            <div class="agent_facebook width100px">
                                <strong><?php echo Text::_('OS_FACEBOOK')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col">
                            <a href="<?php echo $row->agent->facebook;?>" target="_blank"><?php echo $row->agent->facebook;?></a>
                        </td>
                    </tr>
                    <?php
                    }
                    if(($row->agent->aim != "")and ($configClass['show_agent_twitter'] == 1)){
                    ?>
                    <tr>
                        <td class="left_details_col">
                            <div class="agent_twitter width100px">
                                <strong><?php echo Text::_('Twitter')?>:</strong>
                            </div>
                        </td>
                        <td class="right_details_col">
                            <a href="<?php echo $row->agent->aim;?>" target="_blank"><?php echo $row->agent->aim;?></a>
                        </td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
			</div>
		</div>
        <?php } ?>
		<div class="clearfix"></div>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php
				$link = Route::_("index.php?option=com_osproperty&task=agent_info&id=".$row->agent_id."&Itemid=".OSPRoute::getAgentItemid($row->agent_id));
				?>
				<a href="<?php echo $link?>">
					<?php echo Text::_('OS_LISTING')?> (<?php echo $row->agent->countlisting?>)
				</a>
				
				&nbsp;|&nbsp;
				<a href="<?php echo $link?>">
					<?php
					if($row->agent->agent_type == 0){
					?>
					<?php echo Text::_('OS_AGENT_INFO')?>
					<?php
					}else{
						echo Text::_('OS_OWNER_INFO');
					}
					?>
				</a>
				<?php
				if($configClass['show_agent_contact'] == 1){
				?>
				&nbsp;|&nbsp;
				<a href="<?php echo $link?>">
					<?php
					if($row->agent->agent_type == 0){
					?>
					<?php echo Text::_('OS_CONTACT_AGENT');?>
					<?php
					}else{
						echo Text::_('OS_CONTACT_OWNER');
					}
					?>
				</a>
				<?php
				}
				?>
			</div>
		</div>
		<?php
		$agent = ob_get_contents();
		ob_end_clean();
		$row->agenttype = $row->agent->agent_type;
		$row->agent = $agent;
		
		//end featured
		$comments = (array) $row->comments;
		$row->ncomments = count($comments);
		ob_start();
		if(count($comments) > 0){
			$comments = $comments;
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
					<table  width="100%" class="border0">
						<tr>
							<td width="100%" c;ass="padding0">
								<div class="block_caption">
									<strong><?php echo Text::_('OS_COMMENTS')?></strong>
								</div>
							</td>
						</tr>
						<?php
						for($i=0;$i<count($comments);$i++){
                            $comment = $comments[$i];
                            ?>
                            <tr>
                                <td width="100%" align="left" valign="top" class="padding0 paddingbottom20" style="background:url(<?php echo Uri::root()?>media/com_osproperty/assets/images/bg_content.gif);background-repeat:repeat-x;">
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
											<?php
											if($configClass['show_rating'] == 1){
											?>
												<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/stars-<?php echo $comment->rate; ?>.png" />
												&nbsp;
											<?php
											}
											?>
                                            <strong><?php echo $comment->title?></strong>
                                            <?php
                                            if((Factory::getUser()->id == $comment->user_id) and ($configClass['allow_edit_comment'] == 1)){
                                                ?>
                                                <a href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_editcomment&id=<?php echo $comment->id; ?>&tmpl=component" class="osmodal" rel="{handler: 'iframe', size: {x: 500, y: 500}}" title="<?php echo Text::_('OS_EDIT_YOUR_COMMENT');?>"><i class="osicon-edit"></i></a>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> sub-score-item">
												<div class="progress-bar" data-value="<?php echo $comment->rate1; ?>">
													<?php
													$percent1 = ($comment->rate1*100/5);
													?>
													<span style="width: <?php echo $percent1;?>%;"></span>
												</div>
												<span class="item-text"><?php echo Text::_('OS_RATE_OPTION1');?></span>
												<span class="item-value" data-cleanliness-score="<?php echo $comment->rate1; ?>"><?php echo $comment->rate1; ?></span>
											</div>
                                        </div>
										<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> sub-score-item">
												<div class="progress-bar" data-value="<?php echo $comment->rate2; ?>">
													<?php
													$percent2 = ($comment->rate2*100/5);
													?>
													<span style="width: <?php echo $percent2;?>%;"></span>
												</div>
												<span class="item-text"><?php echo Text::_('OS_RATE_OPTION2');?></span>
												<span class="item-value" data-cleanliness-score="<?php echo $comment->rate2; ?>"><?php echo $comment->rate2; ?></span>
											</div>
                                        </div>
                                    </div>
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> sub-score-item">
												<div class="progress-bar" data-value="<?php echo $comment->rate3; ?>">
													<?php
													$percent3= ($comment->rate3*100/5);
													?>
													<span style="width: <?php echo $percent3;?>%;"></span>
												</div>
												<span class="item-text"><?php echo Text::_('OS_RATE_OPTION3');?></span>
												<span class="item-value" data-cleanliness-score="<?php echo $comment->rate3; ?>"><?php echo $comment->rate3; ?></span>
											</div>
                                        </div>
										<div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?>">
											<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> sub-score-item">
												<div class="progress-bar" data-value="<?php echo $comment->rate4; ?>">
													<?php
													$percent4 = ($comment->rate4*100/5);
													?>
													<span style="width: <?php echo $percent4;?>%;"></span>
												</div>
												<span class="item-text"><?php echo Text::_('OS_RATE_OPTION4');?></span>
												<span class="item-value" data-cleanliness-score="<?php echo $comment->rate4; ?>"><?php echo $comment->rate4; ?></span>
											</div>
                                        </div>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                            <?php
                                            echo nl2br($comment->content);
                                            ?>
                                        </div>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                                            <?php echo Text::_('OS_AUTHOR')?>: <strong><?php echo $comment->name?></strong>
                                            <?php
                                            if(file_exists(JPATH_ROOT.'/media/com_osproperty/flags/'.$comment->country.'.png')){
                                                ?>
                                                <img src="<?php echo Uri::root()?>media/com_osproperty/flags/<?php echo $comment->country?>.png"/>
                                            <?php
                                            }
                                            ?>
                                            &nbsp;|
                                            &nbsp;
                                            <?php echo Text::_("OS_POST_DATE")?>: <strong><?php echo HelperOspropertyCommon::loadTime($comment->created_on,$configClass['general_date_format']);?></strong>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
						}
						?>
					</table>
				</div>
			</div>
			<?php
		}else{
			?>
			<div class="center padding5">
				<?php echo Text::_('OS_THERE_ARE_NO_COMMENT_THERE');?>
			</div>
			<?php
		}
		$comments = ob_get_contents();
		ob_end_clean();
		$row->comments = $comments;
		
		$socialUrl = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id");
		$firstChar = substr($url,0,1);
		if($firstChar == "/"){
			$socialUrl = substr($socialUrl,1);
		}
		$socialUrl = Uri::root().$socialUrl;
		
		ob_start();
		if ($configClass['social_sharing'] == 1) {
		?>
			<div id="itp-social-buttons-box">
				<div id="eb_share_button">
					<?php
					$title = "";
					if(($row->ref != "") and ($configClass['show_ref'] == 1)){
						$title = $row->ref.", ";
					}
					
					$title.= OSPHelper::getLanguageFieldValue($row,'pro_name');
					$html  = HelperOspropertyCommon::getDeliciousButton( $title, $socialUrl );
	        		$html .= HelperOspropertyCommon::getDiggButton( $title, $socialUrl );
			        $html .= HelperOspropertyCommon::getFacebookButton( $title, $socialUrl );
			        $html .= HelperOspropertyCommon::getGoogleButton( $title, $socialUrl );
			        $html .= HelperOspropertyCommon::getStumbleuponButton( $title, $socialUrl );
			        $html .= HelperOspropertyCommon::getTechnoratiButton( $title, $socialUrl );
			        $html .= HelperOspropertyCommon::getTwitterButton( $title, $socialUrl );
			        echo $html ;
					?>
				</div>
				<div class="clearfix">&nbsp;</div>
			</div>		
			<div class="clearfix">&nbsp;</div>
		<!-- End social sharing -->
		<?php	
		}
		$share = ob_get_contents();
		ob_end_clean();
		$row->share = $share;
		
		$db = Factory::getDbo();
		$query = "Select count(a.id)from #__osrs_neighborhood as a"
				." inner join #__osrs_neighborhoodname as b on b.id = a.neighbor_id"
				." where a.pid = '$row->id'";
		$db->setQuery($query);
		$count_neighborhood = $db->loadResult();
		if($count_neighborhood > 0)
		{
			ob_start();
			HelperOspropertyCommon::loadNeighborHood($row->id);
			$neighborhood = ob_get_contents();
			ob_end_clean();
			$row->neighborhood = $neighborhood;
			
			ob_start();
			HelperOspropertyCommon::loadNeighborHood1($row->id);
			$neighborhood1 = ob_get_contents();
			ob_end_clean();
			$row->neighborhood1 = $neighborhood1;

			ob_start();
			HelperOspropertyCommon::loadNeighborHood2($row->id);
			$neighborhood2 = ob_get_contents();
			ob_end_clean();
			$row->neighborhood2 = $neighborhood2;
		}
		else
		{
			$row->neighborhood = "";
		}
		//Google map
		ob_start();
		$geocode = [];

		$tmp		= new \stdClass();
		$tmp->lat = $row->lat_add;
		$tmp->long = $row->long_add;
		$tmp->text = OSPHelper::getLanguageFieldValue($row,'pro_name');
		$geocode[0]	= $tmp;

		HelperOspropertyGoogleMap::loadGoogleMap($geocode,"map","");
		$google_header_js = ob_get_contents();
		ob_end_clean();
		$row->google_header_js = $google_header_js;

		//Random string
		$RandomStr = md5(microtime());// md5 to generate the random string
		$ResultStr = substr($RandomStr,0,5);//trim 5 digit 
		$row->ResultStr = $ResultStr;
		
		$row->relate = "";
		jimport('joomla.filesystem.file');
		
		if($configClass['relate_properties'] == 1 && count((array)$row->relate_properties) > 0)
		{
			ob_start();
			$relates = $row->relate_properties;
			if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/relateproperties.php')){
				$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
			}else{
				$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
			}
			$tpl->set('mainframe',$mainframe);
			$tpl->set('relates',$relates);
			$tpl->set('configClass',$configClass);
			$tpl->set('title',Text::_('OS_RELATE_PROPERTIES'));
			$tpl->set('bootstrapHelper',$bootstrapHelper);
			echo $tpl->fetch("relateproperties.php");
			$relate = ob_get_contents();
			ob_end_clean();
			$row->relate = $relate;
		}
		if(($configClass['relate_property_type'] == 1) and (count((array)$row->relate_type_properties) > 0)){
			ob_start();
			$relates = $row->relate_type_properties;
			if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/relateproperties.php')){
				$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
			}else{
				$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
			}
			$tpl->set('mainframe',$mainframe);
			$tpl->set('relates',$relates);
			$tpl->set('configClass',$configClass);
			$tpl->set('title',Text::_('OS_PROPERTIES_SAME_TYPES'));
			$tpl->set('bootstrapHelper',$bootstrapHelper);
			echo $tpl->fetch("relateproperties.php");
			$relate = ob_get_contents();
			ob_end_clean();
			$row->relate .= $relate;
		}
		
		if($configClass['show_walkscore'] == 1)
		{
			if($configClass['ws_id'] != "")
			{
		        ob_start();
		        
		        $address	= $row->address;
				$address   .= " ".HelperOspropertyCommon::loadCityName($row->city);
				if($row->postcode != ""){
					$address .= " ".$row->postcode;
				}
				$address   .= " ".$row->state_name;
				$address   .= " ".$row->country_name;
				
				$latitude	= $row->lat_add;
				$longitude	= $row->long_add;
		        ?>
		        <script type='text/javascript'>
				var ws_wsid    = '<?php echo $configClass['ws_id'];?>';
				var ws_address = '<?php echo $address; ?>';
				var ws_lat     = '<?php echo $latitude ; ?>';
				var ws_lon     = '<?php echo $longitude ; ?>';
				var ws_height  = '<?php echo $configClass['ws_height'] ; ?>';
				<?php
				if($ismobile){
				?>
				var ws_width   = '230';
				var ws_layout  = 'vertical';
				<?php
				}else{
				?>
				var ws_width   = '<?php echo $configClass['ws_width'] ; ?>';
				var ws_layout  = 'horizontal';
				<?php
				}
				?>
				var ws_distance_units = '<?php echo $configClass['ws_unit'] ; ?>';
				</script>
				
				<div id='ws-walkscore-tile'>
				<div id='ws-footer'>
				<form id='ws-form'>
				<a id='ws-a' href='#' style='display:none;'></a>
				<input type='text' id='ws-street' style='position:absolute;top:0px;left:225px;width:331px' />
				<input type='image' id='ws-go' src='https://www.walkscore.com/images/tile/go-button.gif' height='15' width='22' border='0' alt='get my Walk Score' />
				</form>
				</div>
				</div>
				<script type='text/javascript' src='//www.walkscore.com/tile/show-walkscore-tile.php'></script>
		        <?php
		        $walked_score = ob_get_contents();
		        ob_end_clean();
		        $row->ws = $walked_score;
			}
		}
		
		$db = Factory::getDbo();
		if((int)$configClass['limit_upload_photos'] == 0){
			$limitphoto = "";
		}else{
			$limitphoto = " limit ".$configClass['limit_upload_photos'];
		}
		$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id' and image <> '' order by ordering ".$limitphoto);
		$photos = $db->loadObjectList();
		
		$user = Factory::getUser();
		$can_add_cmt = 0;

        if($configClass['comment_active_comment'] == 1){
            $can_add_cmt = 1;
        }
        if($configClass['registered_user_write_comment'] == 1) {
            if ($user->id > 0) {
                $db->setQuery("Select count(id) from #__osrs_comments where pro_id = '$row->id' and user_id = '$user->id'");
                $already_add_comment = $db->loadResult();
                if ($already_add_comment > 0) {
                    if($configClass['only_one_review'] == 1){
                        $can_add_cmt = 0;
                    }else{
                        $can_add_cmt = 1;
                    }
                } else {
                    $can_add_cmt = 1;
                }
            }else{
				 $can_add_cmt = 0;
			}
        }
		//add google map
		$type_icon = OSPHelper::getTypeIcon($row->id);
		$map_house_icon     = '/media/com_osproperty/assets/images/googlemapicons/'.$type_icon;
		
		if($configClass['goole_aip_key'] != ""){
			$key = "&key=".$configClass['goole_aip_key'];
		}else{
			$key = "";
		}

		$document  = Factory::getDocument();
		$gscript = '
            var map;
            jQuery(document).ready( function(){
                ';
            for($i=0;$i<count($photos);$i++){
                $gscript .=	' 
                jQuery("#thumb'.$i.'").click(function(e){
                    ';
                for($j=0;$j<count($photos);$j++){
                    if($j != $i){
                        $gscript .=	'jQuery("#img'.$j.'").hide();';
                    }
                }
                $gscript .=	'jQuery("#img'.$i.'").show();';
                $gscript .= '
                });';

            }
		$google_map_overlay = $configClass['goole_map_overlay'];
		if($google_map_overlay == ""){
			$google_map_overlay = "ROADMAP";
		}
		$google_map_resolution = $configClass['goole_map_resolution'];
		if($google_map_resolution == 0){
			$google_map_resolution = 15;
			$population = 150;
		}elseif(($google_map_resolution > 0) and ($google_map_resolution <= 5)){
			$population = 400000;
		}elseif(($google_map_resolution > 5) and ($google_map_resolution <= 10)){
			$population = 2000;
		}elseif(($google_map_resolution > 10) and ($google_map_resolution <= 15)){
			$population = 150;
		}else{
			$population = 100;
		}

		if($configClass['map_type'] == 0) {

            $gscript .= ' 
                var zoom = ' . $google_map_resolution . ';
                var ipbaseurl = "' . Uri::root(true) . '";
                var coord = new google.maps.LatLng(' . $row->lat_add . ', ' . $row->long_add . ');
                var citymap = {};
                citymap["chicago"] = {
                   center: new google.maps.LatLng(' . $row->lat_add . ', ' . $row->long_add . '),population: ' . $population . '
                };
                var icon_url = ipbaseurl+"' . $map_house_icon . '";
                var streetview = new google.maps.StreetViewService();

                var mapoptions = {
                                    zoom: zoom,
                                    center: coord,
                                    //draggable: false,
                                    mapTypeControl: true,
                                    navigationControl: true,
                                    streetViewControl: false,
                                    mapTypeId: google.maps.MapTypeId.' . $google_map_overlay . ',
                                    maxZoom: 21
                                }

                map = new google.maps.Map(document.getElementById("map_canvas"), mapoptions);
                ';
            if ($row->show_address == 1) {
                $gscript .= ' 
                    var marker  = new google.maps.Marker({
                        position: coord,
                        visible: true,
                        flat: true,
                        clickable: false,
                        map: map,
                        icon: icon_url
                    });
                    ';
            } else {
                $gscript .= ' 
                    for (var city in citymap) {
                        var populationOptions = {
                          strokeColor: "#FF0000",
                          strokeOpacity: 0.8,
                          strokeWeight: 2,
                          fillColor: "#FF0000",
                          fillOpacity: 0.35,
                          map: map,
                          center: citymap[city].center,
                          radius: Math.sqrt(citymap[city].population) * 100
                        };
                        // Add the circle for this city to the map.
                        cityCircle = new google.maps.Circle(populationOptions);
                    }
            ';
            }

            $gscript .= '
					const googletab = document.querySelector("a[aria-controls=tabgoogle]");
					if(googletab)
					{
						googletab.addEventListener("click", function () {
							setTimeout(function() {
								google.maps.event.trigger(map, "resize");
								map.setZoom( map.getZoom() );
								map.setCenter(coord);
							}, (10));
						});	
					}
                    ';

            if ($configClass['show_streetview'] == 1 && $row->show_address == 1 && $configClass['map_type'] == 0) {
                $gscript .= '
                        var panoramaElement = document.getElementById("pano");
							const streetviewtab = document.querySelector("a[aria-controls=tabstreet]");
                            streetview.getPanoramaByLocation(coord, 25, function(data, status){
                                switch(status){
                                    case google.maps.StreetViewStatus.OK:

										if (streetviewtab)
										{
											streetviewtab.addEventListener("click", function () {
												setTimeout(function() {
                                                var panorama = new google.maps.StreetViewPanorama(panoramaElement, {
                                                    position: coord
                                                });
                                                google.maps.event.trigger(panorama, "resize");
                                            }, (10));
											});
										}
                                        break;
                                    case google.maps.StreetViewStatus.ZERO_RESULTS:
										
                                        streetviewtab.style.display = "none !important";
                                        break;
                                    default:
                                        streetviewtab.style.display = "none !important";
                                }
                            }); ';
            }
            $gscript .= '
                });';
        }
        else
        {
            $document = Factory::getDocument()
                ->addScript(Uri::root() . 'media/com_osproperty/assets/js/leaflet/leaflet.js')
                ->addStyleSheet(Uri::root() . 'media/com_osproperty/assets/js/leaflet/leaflet.css');

            $gscript .= ' 
                var zoom = ' . $google_map_resolution . ';
                var ipbaseurl = "' . Uri::root(true) . '";
                var citymap = {};                
                var icon_url = ipbaseurl + "' . $map_house_icon . '";
                
                var mymap = L.map("map_canvas").setView(['.$row->lat_add.', '.$row->long_add.'], zoom);
                L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                    attribution: "",
                    maxZoom: 21,
                    id: "mapbox.streets",
                    zoom: zoom,
                }).addTo(mymap);
                ';
            if ($row->show_address == 1)
            {
                $gscript .= ' 
                    var propertyIcon = L.icon({iconUrl: icon_url, iconSize: [33, 44]});
                    var marker = L.marker([' . $row->lat_add . ', ' . $row->long_add . '],{icon: propertyIcon}, {draggable: false}).addTo(mymap);
                    ';
            } else {
                $gscript .= ' 
                    var circle = L.circle([' . $row->lat_add . ', ' . $row->long_add . '], {
                        color: "#1D86A0",
                        fillColor: "#1D86A0",
                        fillOpacity: 0.5,
                        radius: 500
                    }).addTo(mymap);
                ';
            }
            $gscript .= '
				jQuery("a[aria-controls=\'tabgoogle\']").click( function(e){
					setTimeout(function() {
                        mymap.invalidateSize();
                    }, (10));
				});

                jQuery("#agooglemap").click( function(e){
                    setTimeout(function() {
                        mymap.invalidateSize();
                    }, (10));
                    jQuery("#liaphoto").removeClass("active");
                    jQuery("#liagooglemap").removeClass("active");
                    jQuery("#liastreetview").removeClass("active");
                    jQuery("#liavideo").removeClass("active");
                    jQuery("#liagooglemap").addClass("active");

					jQuery("#tabphoto").removeClass("active");
                    jQuery("#tabgoogle").removeClass("active");
					jQuery("#tabstreet").removeClass("active");
                    jQuery("#tabvideo").removeClass("active");
					jQuery("#tabgoogle").addClass("active");
                });
                    
                jQuery("#aphoto").click(function(e){
                    jQuery("#liaphoto").removeClass("active");
                    jQuery("#liagooglemap").removeClass("active");
                    jQuery("#liastreetview").removeClass("active");
                    jQuery("#liavideo").removeClass("active");
                    jQuery("#liaphoto").addClass("active");

					jQuery("#tabphoto").removeClass("active");
                    jQuery("#tabgoogle").removeClass("active");
					jQuery("#tabstreet").removeClass("active");
                    jQuery("#tabvideo").removeClass("active");
					jQuery("#tabphoto").addClass("active");
                }); 
                
                jQuery("#avideo").click( function(e){
                    jQuery("#liaphoto").removeClass("active");
                    jQuery("#liagooglemap").removeClass("active");
                    jQuery("#liastreetview").removeClass("active");
                    jQuery("#liavideo").removeClass("active");
                    jQuery("#liavideo").addClass("active");

					jQuery("#tabphoto").removeClass("active");
                    jQuery("#tabgoogle").removeClass("active");
					jQuery("#tabstreet").removeClass("active");
                    jQuery("#tabvideo").removeClass("active");
					jQuery("#tabvideo").addClass("active");
                }); 
                
                jQuery("#astreetview").click(function(e){
                    jQuery("#liaphoto").removeClass("active");
                    jQuery("#liagooglemap").removeClass("active");
                    jQuery("#liastreetview").removeClass("active");
                    jQuery("#liavideo").removeClass("active");
                    jQuery("#liastreetview").addClass("active");

					jQuery("#tabphoto").removeClass("active");
                    jQuery("#tabgoogle").removeClass("active");
					jQuery("#tabstreet").removeClass("active");
                    jQuery("#tabvideo").removeClass("active");
					jQuery("#tabstreet").addClass("active");
                });   
                
                ';
            $gscript .= '
                });';
        }

		if(($themename == "default" || $themeobj->default_duplicate == 1) && ($row->lat_add != "" && $row->long_add != "") && $configClass['goole_use_map'] == 1)
		{
			$document->addScriptDeclaration( $gscript );
		}
		
		if($configClass['show_gallery_tab'] == 1){
			ob_start();
			HelperOspropertyCommon::slimboxGallery($row->id,$photos);
			$gallery = ob_get_contents();
			ob_end_clean();
		}else{
			$gallery =  "";
		}
		
		$row->gallery = $gallery;
		
		
		//get subtitle
		if($row->show_address == 1)
		{
			$row->subtitle = OSPHelper::generateAddress($row);
		}
		else
		{
			$addInfo = [];
			$addInfo1 = [];
			if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
			{
				$addInfo1[] = OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
			}
			elseif($row->price > 0 && $row->price_call == 0)
			{
				$addInfo1[] = OSPHelper::generatePrice($row->curr,$row->price);
			}
			if(($row->bed_room > 0) and ($configClass['use_bedrooms'] == 1)){
				$addInfo[] = $row->bed_room." ".Text::_('OS_BEDROOMS');
			}
			if(($row->bath_room > 0) and ($configClass['use_bathrooms'] == 1)){
				$addInfo[] = OSPHelper::showBath($row->bath_room)." ".Text::_('OS_BATHROOMS');
			}
			if(($row->rooms > 0) and ($configClass['use_rooms'] == 1)){
				$addInfo[] = $row->rooms." ".Text::_('OS_ROOMS');
			}
			if(($row->square_feet > 0) and ($configClass['use_squarefeet'] == 1)){
				$addInfo[] = $row->square_feet." ".OSPHelper::showSquareSymbol();
			}
			$addInfo1[] = implode(", ",$addInfo);
			$row->subtitle = implode(" | ",$addInfo1);
		}
		
		ob_start();
		if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
		{
			echo OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
		}
		elseif($row->price_call == 0)
		{
			if($row->price > 0)
			{
				echo OSPHelper::generatePrice($row->curr,$row->price);
				if($row->rent_time != "")
				{
					echo "/".Text::_($row->rent_time);
				}
			}
		}
		elseif($row->price_call == 1)
		{
			echo Text::_('OS_CALL_FOR_DETAILS_PRICE');
		}
		$price = ob_get_contents();
		ob_end_clean();
		$row->price_raw = $price;
		
		//price pure
		ob_start();
		if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
		{
			echo " <span class='pricetext'>".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'))."</span>";
		}
		elseif($row->price_call == 0)
		{
			if($row->price > 0)
			{
				?>
				<div id="currency_div" class="padding0">
					<?php
					if(file_exists(JPATH_ROOT."/components/com_oscalendar/oscalendar.php"))
					{
						if($configClass['integrate_oscalendar'] == 1)
						{
							echo Text::_('OS_FROM')." ";
						}
					}
					echo OSPHelper::generatePrice($row->curr,$row->price);
					if($row->rent_time != ""){
						?>
						/ <span id="mthpayment"><?php echo Text::_($row->rent_time);?></span>
						<?php
					}
					if($configClass['show_convert_currency'] == 1 && count($currencies) > 0){
					?>
						<BR />
						<span class="fontsmall">
						<?php echo Text::_('OS_CONVERT_CURRENCY')?>: <?php echo $lists['curr']?>
						</span>
					<?php
					}
					?>
				</div>
				<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
				<input type="hidden" name="currency_item" id="currency_item" value="" />
				<?php
			}
		}elseif($row->price_call == 1){
			echo " <span class='pricetext'>".Text::_('OS_CALL_FOR_DETAILS_PRICE')."</span>";
		}
		$price = ob_get_contents();
		ob_end_clean();
		$row->price1 = $price;
		//end price
		
		//price
		ob_start();
		echo "<span class='pricetext'>";
		if(OSPHelper::getLanguageFieldValue($row,'price_text') != "")
		{
			echo " ".OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($row,'price_text'));
		}
		elseif($row->price_call == 0)
		{
			if($row->price > 0)
			{
				?>
				<div id="currency_div" class="padding0">
					<?php
					echo OSPHelper::generatePrice($row->curr,$row->price);
					
					if($row->rent_time != ""){
						echo "/".Text::_($row->rent_time);
					}
					if($configClass['show_convert_currency'] == 1 && count($currencies) > 0)
					{
						echo  "&nbsp;".$lists['curr_default'];
					}
					?>
				</div>
				<input type="hidden" name="live_site" id="live_site" value="<?php echo Uri::root()?>" />
				<input type="hidden" name="currency_item" id="currency_item" value="" />
				<?php
			}
		}elseif($row->price_call == 1){
			echo " ".Text::_('OS_CALL_FOR_DETAILS_PRICE');
		}
		echo "</span>";
		$price = ob_get_contents();
		ob_end_clean();
		$row->price = $price;
		//end price
		
		$needs = [];
		$needs[] = "property_details";
		$need = $row->id;
		$itemid = OSPRoute::getItemid($needs);
		
		if($configClass['integrate_education'] == 1){
			if(!$row->postcode && ( !$row->latitude || !$row->longitude )) {
				//do nothing
			}else{
				$values = [];
	
		        $values['zip']			= $row->postcode;
		        $values['latitude']		= $row->lat_add;
		        $values['longitude']    = $row->long_add;
		        $values['key']			= 'dad04b84073a265e5244ba6db8892348';
		        $values['radius']		= $configClass['education_radius'];
		        $values['min']			= $configClass['education_min'];
		        $values['city']         = HelperOspropertyCommon::loadCityName($row->city);
		        $db->setQuery("Select state_name$lang_suffix as state_name from #__osrs_states where id = '$row->state'");
		        $values['state']        = $db->loadResult();
		        $max				    = $configClass['education_max'];
		        $debug				    = 0;
		
		        $i = 1;
		        $result = self::_getSchoolData($values);
		        
		        if(isset($result[0]['methodResponse']['faultString']) && $debug == 0) {
		        	//do nothing
		        }else{
		        	ob_start();
		        	?>
		        	<table class="table table-striped">
		                <thead>
		                    <tr>
		                        <th width="25%"><?php echo Text::_('OS_SCHOOL_NAME');?></th>
		                        <th width="25%" class="hidden-phone"><?php echo Text::_('OS_GRADE_LEVEL');?></th>
		                        <th width="25%"><?php echo Text::_('OS_DISTANCE_FROM_LISTING');?></th>
		                        <th width="25%" class="hidden-phone"><?php echo Text::_('OS_ENROLLMENT');?></th>
		                    </tr>
		                </thead>
		                <tbody>
		                    <?php
		                    if(isset($result[0]['methodResponse']['faultString'])) {
		                        echo '<tr><td colspan="4" align="center"><b>Education.com Error:</b> '.$result[0]['methodResponse']['faultString'].'</td></tr>';
		                        $no_results = true;
		                    } elseif( count($result[0]['methodResponse']) < 1 ){
		                        echo '<tr><td colspan="4" align="center"><b>'.Text::_('OS_NO_RESULTS_FOUND').'.</b></td></tr>';
		                        $no_results = true;
		                    } else {
		                        $k = 0;
		                        foreach ($result[0]['methodResponse'] as $school){
		                            echo '
		                            <tr>
		                                <td><a href="'.$school['school']['url'].'" target="_blank">'.$school['school']['schoolname'].'</a></td>
		                                <td class="hidden-phone">'.$school['school']['gradelevel'].'</td>
		                                <td>'.round($school['school']['distance'], 2).' miles</td>
		                                <td class="hidden-phone">'.$school['school']['enrollment'].'</td>
		                            </tr>';
		
		                            if ($i >= $max) break;
		                            $i++;
		                            $k = 1 - $k;
		                            $no_results = false;
		                        }
		                    }
		                    ?>
		                </tbody>
		                <tfoot>
		                    <tr>
		                        <td colspan="4" class="small center">
		                            Schools provided by: <a href="http://www.education.com/schoolfinder/" target="_blank"><img src ="<?php echo $result[1]['methodResponse']['logosrc']; ?>" alt="" /></a><br />
		                            <?php if(!$no_results) echo '<a href="'.$result[1]['methodResponse']['lsc'].'" target="_blank">See more information on '.$property->city.' schools from Education.com</a><br />'; ?>
		                            <?php echo $result[1]['methodResponse']['disclaimer']; ?>
		                        </td>
		                    </tr>  
		                </tfoot>
		            </table> 
		        	<?php
		        	$education = ob_get_contents();
		        	ob_end_clean();
		        	$row->education = $education;
		        }
			}
		}
		$db->setQuery("Select * from #__osrs_themes where name like '$themename'");
		$themeobj = $db->loadObject();
		
		$params = $themeobj->params;
		$params = new Registry($params) ;
		$report_image = "";
		$translatable = Multilanguage::isEnabled() && count($languages);
		
		if($translatable){
			$language = Factory::getLanguage();
			$language = $language->getTag();
			$language = explode("-",$language);
			$langfolder = $language[0];
			if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/".$langfolder."/isfeatured.png")){
				$feature_image = Uri::root()."media/com_osproperty/assets/images/".$langfolder."/isfeatured.png";
			}else{
				$feature_image = Uri::root()."media/com_osproperty/assets/images/isfeatured.png";
			}
			if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/".$langfolder."/justadd.png")){
				$justadd_image = Uri::root()."media/com_osproperty/assets/images/".$langfolder."/justadd.png";
			}else{
				$justadd_image = Uri::root()."media/com_osproperty/assets/images/justadd.png";
			}
			if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/".$langfolder."/justupdate.png")){
				$justupdate_image = Uri::root()."media/com_osproperty/assets/images/".$langfolder."/justupdate.png";
			}else{
				$justupdate_image = Uri::root()."media/com_osproperty/assets/images/justupdate.png";
			}
			if($configClass['enable_report'] == 1){
				if(file_exists(JPATH_ROOT."/media/com_osproperty/assets/images/".$langfolder."/report.png")){
					$report_image = Uri::root()."media/com_osproperty/assets/images/".$langfolder."/report.png";
				}else{
					$report_image = Uri::root()."media/com_osproperty/assets/images/report.png";
				}
			}
		}else{
			$feature_image = Uri::root()."media/com_osproperty/assets/images/isfeatured.png";
			$justadd_image = Uri::root()."media/com_osproperty/assets/images/justadd.png";
			$justupdate_image = Uri::root()."media/com_osproperty/assets/images/justupdate.png";
			$report_image = Uri::root()."media/com_osproperty/assets/images/report.png";
		}
		
		$fb = "";
		$translatable = Multilanguage::isEnabled() && count($languages);
		//if($translatable){
			$language = Factory::getLanguage();
			$ltag = $language->getTag();
			$ltag = str_replace("-","_",$ltag);
			$ltag = "&amp;locale=".$ltag;
		//}else{
			//$ltag = "";
		//}
		$facebook_like = $configClass['show_fb_like'];
		$url = Route::_("index.php?option=com_osproperty&task=property_details&id=$row->id");
		$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$url;
		$url = urlencode($url);
		if($facebook_like == 1){
			$facebook_api = $configClass['facebook_api'];
			$facebook_height = $configClass['facebook_height'];
			if($facebook_api == ""){
				$facebook_height = "10150130831010177";
			}
			if($facebook_height == ""){
				$facebook_height = "80";
			}
			ob_start();
			?>
			<div id="facebook" class="floatleft paddingleft5">
				<iframe src="//www.facebook.com/plugins/like.php?href=<?php echo $url;?><?php echo $ltag;?>&amp;width&amp;layout=standard&amp;action=like&amp;show_faces=true&amp;share=true&amp;height=60&amp;appId=<?php echo $facebook_api;?>" scrolling="no" frameborder="0" class="border0 overflowhidden" style=" height:<?php echo $facebook_height;?>px;" allowTransparency="true"></iframe>
			</div>
			<?php
			$fb = ob_get_contents();
			ob_end_clean();
		}
		$document = Factory::getDocument();
		if($configClass['show_twitter'] == 1){
			$tweet_via = $configClass['twitter_via'];
			$tweet_hash = $configClass['twitter_hash'];
			$title = "Tweet";
			$language = Factory::getLanguage();
			$activate_lang = $language->getTag();
			$activate_lang = explode("-",$activate_lang);
			$activate_lang = $activate_lang[0];
			// make sure the hashtags are trimmed properly
	        $tags = array_map('trim', explode(",", $tweet_hash));
	        $tags = implode(',', $tags);
	        $tweetscript = '!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");';
	        $document->addScriptDeclaration( $tweetscript );
	        $tweet_div = '<div class= "relative floatleft paddingtop10 paddingright10">            
			                   <a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$url.'" data-via="'.$tweet_via.'" data-lang="'.$activate_lang.'" data-hashtags="'.$tags.'">'.$title.'</a>
			               </div>';
	        $row->tweet_div = $tweet_div;	
		}
		
		if($configClass['google_plus'] == 1){
			$document->addScript("https://apis.google.com/js/plusone.js");
	        $gplus_div = '<div class="relative paddingtop10 paddingright10 floatleft">
		                    <g:plusone></g:plusone>
		                </div>';
	        $row->gplus_div = $gplus_div;
		}
		
		if($configClass['pinterest'] == 1){
			$description = urlencode(OSPHelper::getLanguageFieldValue($row,'pro_name'));
	        // make sure we have good path for image
	        $db->setQuery("Select image from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
	        $default_image = $db->loadResult();
	        if($default_image != ""){
	        	$pin_image = Uri::root()."images/osproperty/properties/".$row->id."/thumb/".$default_image;
	        }else{
	        	$pin_image = Uri::root()."media/com_osproperty/assets/images/nopropertyphoto.png";
	        }
	        $pin_image = urlencode($pin_image);
	        $pinpath = "http://pinterest.com/pin/create/button/?url=".$url."&media=".$pin_image."&description=".$description;
	        // create javascript for pinterest request
	        $document->addScript( "//assets.pinterest.com/js/pinit.js" );
	        $pinterest =  '
                <div classs="floatleft paddingtop10 paddingright10 relative" style="width: 43px; height: 21px;">
                    <a href="'.$pinpath.'" class="pin-it-button" count-layout="horizontal" target="_blank">
                        <img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" alt="Pin It" />
                    </a>
                </div>';
	        $row->pinterest = $pinterest;
		}
		
		$db->setQuery("Select count(extension_id) from #__extensions where `type` like 'plugin' and `element` like 'jcomments' and `folder` like 'osproperty' and enabled = '1'");
		$countPlugin = $db->loadResult();
		
		jimport('joomla.filesystem.folder');
		if((Folder::exists(JPATH_ROOT.'/components/com_jcomments')) and ($countPlugin > 0)){
			$integrateJComments = 1;
		}else{
			$integrateJComments = 0; 
		}
		//load plugin
		PluginHelper::importPlugin('osproperty');
		//$dispatcher 	= JDispatcher::getInstance();
		$topPlugin 		= $mainframe->triggerEvent('onTopPropertyDetails', array($row));
		$middlePlugin 	= $mainframe->triggerEvent('onMiddlePropertyDetails', array($row));
		$bottomPlugin 	= $mainframe->triggerEvent('onBottomPropertyDetails', array($row));
		
		$db->setQuery("Select a.id,a.keyword$lang_suffix as keyword from #__osrs_tags as a inner join #__osrs_tag_xref as b on b.tag_id = a.id where a.published = '1' and b.pid = '$row->id'");
		$tags = $db->loadObjectList();
		if(count($tags) > 0){
			$tagArr = [];
			for($i=0;$i<count($tags);$i++){
				$tag = $tags[$i];
				$link = Route::_('index.php?option=com_osproperty&task=property_tag&tag_id='.$tag->id.'&Itemid='.$jinput->getInt('Itemid',0));
				$tagArr[] = "<a href='$link'><span class='label label-important tagkeyword'>".$tag->keyword."</span></a>";
			}
		}
		
		if($configClass['use_property_history'] == 1){
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_price_history")->where("pid = '$row->id'")->order("`date` desc");
			$db->setQuery($query);
			$prices = $db->loadObjectList();
			
			if(count($prices) > 0){
				ob_start();
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> leftmargin10">
						<h4>
							<?php echo Text::_('OS_PROPERTY_HISTORY');?>
						</h4>
					</div>
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
						<table class="table">
							<thead>
								<tr>
									<th>
										<?php echo Text::_('OS_DATE');?>
									</th>
									<th>
										<?php echo Text::_('OS_EVENT');?>
									</th>
									<th>
										<?php echo Text::_('OS_PRICE');?>
									</th>
									<th>
										<?php echo Text::_('OS_SOURCE');?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach ($prices as $price){
									?>
									<tr>
										<td>
											<?php echo $price->date;?>
										</td>
										<td>
											<?php echo $price->event;?>
										</td>
										<td>
											<?php echo OSPHelper::generatePrice('',$price->price);?>
										</td>
										<td>
											<?php echo $price->source;?>
										</td>
									</tr>
									<?php 
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?php 
				$price_history = ob_get_contents();
				ob_end_clean();
			}
			$row->price_history = $price_history;
			
			$query = $db->getQuery(true);
			$query->select("*")->from("#__osrs_property_history_tax")->where("pid = '$row->id'")->order("`tax_year` desc");
			$db->setQuery($query);
			$taxes = $db->loadObjectList();
			if(count($taxes) > 0){
				ob_start();
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
						<h4>
							<?php echo Text::_('OS_PROPERTY_TAX');?>
						</h4>
					</div>
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
						<table class="table">
							<thead>
								<tr>
									<th>
										<?php echo Text::_('OS_YEAR');?>
									</th>
									<th>
										<?php echo Text::_('OS_TAX');?>
									</th>
									<th>
										<?php echo Text::_('OS_CHANGE');?>
									</th>
									<th>
										<?php echo Text::_('OS_TAX_ASSESSMENT');?>
									</th>
									<th>
										<?php echo Text::_('OS_TAX_ASSESSMENT_CHANGE');?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								foreach ($taxes as $tax){
									?>
									<tr>
										<td>
											<?php echo $tax->tax_year;?>
										</td>
										<td>
											<?php echo OSPHelper::generatePrice('',$tax->property_tax);?>
										</td>
										<td>
											<?php 
											if($tax->tax_change != ""){
											?>
												<?php echo $tax->tax_change;?> %
											<?php }else { ?>
												--
											<?php } ?>
										</td>
										<td>
											<?php echo OSPHelper::generatePrice('',$tax->tax_assessment);?>
										</td>
										<td>
											<?php 
											if($tax->tax_assessment_change != ""){
											?>
												<?php echo $tax->tax_assessment_change;?> %
											<?php }else { ?>
												--
											<?php } ?>
										</td>
									</tr>
									<?php 
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
				<?php 
				$tax = ob_get_contents();
				ob_end_clean();
			}
			$row->tax = $tax;
		}
		if($configClass['use_open_house'] == 1)
		{
			$config = Factory::getConfig();
			$query = $db->getQuery(true);
			$date   = Factory::getDate('now', $config->get('offset'));
			$current_date = $date->format("Y-m-d H:i:s");
			$query->select("*")->from("#__osrs_property_open")->where("pid = '$row->id' and start_from >= '$current_date' ")->order("start_from desc");
			$db->setQuery($query);
			$opens = $db->loadObjectList();
			ob_start();
			?>
	   		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> <?php echo $bootstrapHelper->getClassMapping('img-polaroid'); ?> inspectiontimes">
		   		<strong><?php echo Text::_('OS_OPEN_FOR_INSPECTION_TIMES')?></strong>
		   		<div class="clearfix"></div>
		   		<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin fontsmall">
		    		<?php 
		    		if(count($opens) > 0){
		    			foreach ($opens as $info){
		     			?>
		     			<?php echo Text::_('OS_FROM')?>: <?php //echo date($configClass['general_date_format'],strtotime($info->start_from));
						echo date($configClass['general_date_format'],strtotime($info->start_from));
						?>
		     			-
		     			<?php echo Text::_('OS_TO')?>: <?php //echo date($configClass['general_date_format'],strtotime($info->end_to));
						//echo HTMLHelper::_('date', strtotime($info->end_to) , $configClass['general_date_format']);
						echo date($configClass['general_date_format'],strtotime($info->end_to));
						?>
		     			<div class="clearfix"></div>
		     			<?php
		    			} 
		    		}else{
		    			echo Text::_('OS_NO_INSPECTIONS_ARE_CURRENTLY_SCHEDULED');
		    		}
		    		?>
		   		</div>
	   		</div>
			<?php
			$open_hours = ob_get_contents();
			ob_end_clean();
			
		}
		$row->open_hours = $open_hours;
		
		if($user->id > 0){
			$db->setQuery("Select count(id) from #__osrs_favorites where user_id = '$user->id' and pro_id = '$row->id'");
			$count = $db->loadResult();
			if($count > 0){
				$inFav = 1;
			}else{
				$inFav = 0;
			}
		}

		if($configClass['social_sharing'] == 1){
			$row->social_sharing = OSPHelper::socialsharing($row->id);
		}
        //echo $themename;
        $db->setQuery("Select * from #__osrs_themes where name like '$themename'");
        $themeobj = $db->loadObject();
        $params = $themeobj->params;
        $params = new Registry($params);
		$tpl = new OspropertyTemplate();
		$tpl->set('lists',$lists);
		$tpl->set('integrateJComments',$integrateJComments);
		$tpl->set('facebook_like',$fb);
		$tpl->set('feature_image',$feature_image);
		$tpl->set('justadd_image',$justadd_image);
		$tpl->set('justupdate_image',$justupdate_image);
		$tpl->set('report_image',$report_image);
		$tpl->set('params',$params);
		$tpl->set('row',$row);
		$tpl->set('itemid',$itemid);
		$tpl->set('configs',$configs);	
		$tpl->set('socialUrl',$socialUrl);
		$tpl->set('configClass',$configClass);
		$tpl->set('owner',$owner);
		$tpl->set('can_add_cmt',$can_add_cmt);
		$tpl->set('photos',$photos);
		$tpl->set('ismobile',$ismobile);
		$tpl->set('topPlugin',$topPlugin);
		$tpl->set('middlePlugin',$middlePlugin);
		$tpl->set('bottomPlugin',$bottomPlugin);
		$tpl->set('tagArr',$tagArr);
		$tpl->set('themename',$themename);
        $tpl->set('params',$params);
		$tpl->set('inFav',$inFav);
		$tpl->set('jinput',$jinput);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$tpl->set('temp_path_img',Uri::root()."components/com_osproperty/templates/$themename/img");
		$tpl->set('path',JPATH_ROOT.'/components/com_osproperty/templates/'.$themename);
		//HTMLHelper::_('behavior.modal','a.osmodal');
		$body = $tpl->fetch("details.html.tpl.php");	
		echo $body;	
	}
	
	/**
	 * Listing photos
	 *
	 * @param unknown_type $option
	 * @param unknown_type $property_id
	 * @param unknown_type $photos
	 */
	static function listingPhotos($option,$property_id,$photos){
		global $bootstrapHelper, $mainframe;
		$document = Factory::getDocument();
		$tpl = new OspropertyTemplate();
		$tpl->set('property_id',$property_id);
		$tpl->set('photos',$photos);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$tpl->set('temp_path_img',Uri::root()."components/com_osproperty/templates/default/img");
		$body = $tpl->fetch("photos.html.tpl.php");
		return $body;
	}

	/**
	 * Add/Edit Property
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 */
	static function editListing($option,$row,$lists,$amenities,$amenitylists,$groups,$configs,$neighborhoods,$translatable,$isNew)
	{
		global $bootstrapHelper, $mainframe,$jinput,$configs,$configClass,$languages;
		$db = Factory::getDBO();
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/default/style/style.css");
		//$document->addScript(Uri::root()."media/com_osproperty/assets/js/ajax.js");
		OSPHelper::loadTooltip();
		
		if($row->id > 0){
			$edit = Text::_('OS_EDIT');
		}else{
			$edit = Text::_('OS_ADDNEW');
		}
		
		//jimport('joomla.html.pane');
		//$pane = &JPane::getInstance('sliders',array('startOffset'=>0,'useCookie' => 0));
		?>
		<script type="text/javascript">
		function check_file(id)
		{
            str=document.getElementById(id).value.toUpperCase();
			var elementspan = document.getElementById(id + 'div');
			//suffix=".JPG";
			var blnValid = false;
			var _validFileExtensions = [".jpg", ".jpeg", ".png", ".gif",".webp"];
			for (var j = 0; j < _validFileExtensions.length; j++) 
			{
                var sCurExtension = _validFileExtensions[j];
                if (str.substr(str.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) 
				{
                    blnValid = true;
                    break;
                }
            }
			if(!blnValid)
			{
				alert(Joomla.JText._('<?php echo Text::plural("OS_ONLY_SUPPORT_JPG_IMAGES", 1, array("script"=>true));?>'));
				document.getElementById(id).value='';
				if(elementspan != null){
					elementspan.innerHTML = elementspan.innerHTML;
				}
	        }
			else
			{
	        	//clientWidth,clientHeight;
	        	clientWidth = document.getElementById(id).clientWidth;
	        	clientHeight = document.getElementById(id).clientHeight;
	        	<?php
	        	if((intval($max_width) > 0) and (intval($max_height) > 0)){
	        		?>
	        		var max_width = <?php echo $max_width?>;
	        		max_width = parseInt(max_width);
	        		var max_height = <?php echo $max_height?>;
	        		max_height = parseInt(max_height);
	        		if((clientWidth > max_width) || (clientHeight > max_height)){
						alert(Joomla.JText._('<?php echo Text::plural("OS_YOUR_PHOTO_IS_OVER_LIMIT_SIZE", 1, array("script"=>true));?>'));
	        			document.getElementById(id).value='';
	        			if(elementspan != null){
				        	elementspan.innerHTML = elementspan.innerHTML;
				        }
	        		}
	        		<?php
	        	}
	        	?>
	        	
	        }
	    }
		function loadState(country_id,state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			<?php
			if(OSPHelper::isJoomlaMultipleLanguages())
			{
				$lang = OSPHelper::getCurrentLanguage();
			}
			?>
			loadLocationInfoStateCityAddProperty(country_id,state_id,city_id,'country','state',live_site,'<?php echo $lang;?>');
		}
		function loadCity(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			<?php
			if(OSPHelper::isJoomlaMultipleLanguages())
			{
				$lang = OSPHelper::getCurrentLanguage();
			}
			?>
			loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site,'<?php echo $lang;?>');
		}
		function addPhoto(){
			var current_number_photo = document.getElementById('current_number_photo');
			current_number = parseInt(current_number_photo.value);
			current_number++;
			var temp = document.getElementById('div_' + current_number);
			if(temp != null){
				jQuery("#div_" + current_number).removeClass('nodisplay').addClass('displayblock');
			}
			current_number_photo.value = current_number;
		}
		function submitftForm(save) {
			var form = document.ftForm1;
			var temp1,temp2;
			var cansubmit = 1;
			var require_field = document.getElementById('require_field');
			require_field = require_field.value;
			var require_label = document.getElementById('require_label');
			require_label = require_label.value;
			var require_fieldArr = require_field.split(",");
			var require_labelArr = require_label.split(",");
			for(i=0;i<require_fieldArr.length;i++){
				temp1 = require_fieldArr[i];
				pos   = temp1.indexOf("@");
				if(pos > 0){
					temp1a = temp1.substring(0,pos);
					temp1b = temp1.substring(pos+1);
					temp2  = document.getElementById(temp1a);
					temp3  = document.getElementById(temp1b);
					if((temp2!= null) && (temp3!= null)){
						if((temp2.value == "") && (temp3.value == "") && (cansubmit == 1)){
							showAlertMandatory(require_labelArr[i]);
							temp2.focus();
							cansubmit = 0;
						}
					}
				}else{
					if((temp1 == "category_id") && (cansubmit == 1)){
						if (jQuery('#categoryIds option:selected').length == 0){
							showAlertMandatory(require_labelArr[i]);
							jQuery('#categoryIds').focus();
							cansubmit = 0;
							return false;
						}
					}else{
						temp2 = document.getElementById(temp1);
					}
					if(temp2 != null){
						if((temp2.value == "") && (cansubmit == 1)){
							showAlertMandatory(require_labelArr[i]);
							temp2.focus();
							cansubmit = 0;
						}
					}else{
						temp2 = document.getElementsByName(temp1);
						if(temp2.length > 0){
							if(cansubmit == 1){
								cansubmit1 = 0;
								for(var j=0; j < temp2.length; j++) {
									if(temp2[j].checked == true){
										cansubmit1 = 1;
									}
								}
								if(cansubmit1 == 0){
									showAlertMandatory(require_labelArr[i]);
									temp2.focus();
									cansubmit = 0;
									//return false;
								}
							}
						}else{
							temp2 = document.getElementsByName(temp1 + "[]");
							if(temp2.length > 0){
								if(cansubmit == 1){
									cansubmit1 = 0;
									for(var j=0; j < temp2.length; j++) {
										if(temp2[j].checked == true){
											cansubmit1 = 1;
										}
									}
									if(cansubmit1 == 0){
										showAlertMandatory(require_labelArr[i]);
										temp2.focus();
										cansubmit = 0;
									}
								}
							}
						}
					}
				}
			}

			if(jQuery("#price_call").val() == "0")
            {
                if(jQuery("#price").val() == "" && jQuery("#price_text").val() == "")
                {
                    alert("<?php echo Text::_('OS_YOU_SHOULD_ENTER_PRICE_FOR_PROPERTY');?>");
                    jQuery("#price").focus();
                    return;
                }
            }

			if(cansubmit == 1)
			{
				var pro_type = document.getElementById('pro_type').value;
				if(pro_type != "")
				{
					var require_field = document.getElementById('type_id_' + pro_type + '_required_name');
					require_field = require_field.value;
					var require_label = document.getElementById('type_id_' + pro_type + '_required_title');
					require_label = require_label.value;
					if(require_field != "")
					{
						var require_fieldArr = require_field.split(",");
						var require_labelArr = require_label.split(",");
						for(i=0;i<require_fieldArr.length;i++)
						{
							temp1 = require_fieldArr[i];
							temp2 = document.getElementById(temp1);
							if(temp2 != null)
							{
								if(temp2.value == "")
								{
									showAlertMandatory(require_labelArr[i]);
									temp2.focus();
									cansubmit = 0;
									return false;
								}
							}
							else
							{
								temp2 = document.getElementsByName(temp1);
								if(temp2.length > 0)
								{
									cansubmit = 0;
									for(var j=0; j < temp2.length; j++)
									{
										if(temp2[j].checked == true)
										{
											cansubmit = 1;
										}
									}
									if(cansubmit == 0)
									{
										showAlertMandatory(require_labelArr[i]);
										temp2.focus();
										cansubmit = 0;
										return false;
									}
								}
								else
								{
									temp2 = document.getElementsByName(temp1 + "[]");
									if(temp2.length > 0)
									{
										cansubmit = 0;
										for(var j=0; j < temp2.length; j++)
										{
											if(temp2[j].checked == true)
											{
												cansubmit = 1;
											}
										}
										if(cansubmit == 0)
										{
											showAlertMandatory(require_labelArr[i]);
											temp2.focus();
											cansubmit = 0;
											return false;
										}
									}
								}
							}
						}
					}
				}
			}
			//cansubmit = 1;
			/*
			if(cansubmit == 1){
				<?php
				if(($configClass['active_payment'] == 1) && ($configClass['integrate_membership'] == 0)){
					?>
					var methodpass = 1;
					var paymentMethod 	= "";
					var x_card_num = "";
					var x_card_code = "";
					var card_holder_name = "";
					var exp_month = "";
					var exp_year = "";
					var card_type = "";

					var isFeatured = document.ftForm1.isFeatured;
					if(isFeatured != null){
						if(isFeatured.value == 0){
							<?php
								if(floatVal($configClass['normal_cost']) > 0){
							?>
									cansubmit = checkPaymentMethod();	
							<?php
								}
							?>
						}else{
							<?php
								if(floatVal($configClass['general_featured_upgrade_amount']) > 0){
							?>
									cansubmit = checkPaymentMethod();
							<?php
								}
							?>
						}
					}
					<?php
				}
				?>
			}
			*/
			if(cansubmit == 1)
			{
				var task = form.task;
				if(save == 1)
				{
					task.value = "property_save";
				}
				else if(save == 0)
				{
					task.value = "property_apply";
				}
				else if(save == 2)
				{
				    task.value = "property_saveandactive";
                }
				form.submit();
			}
		}

		function checkPaymentMethod(){
			var methodpass = 1;
			var paymentMethod 	= "";
			var x_card_num = "";
			var x_card_code = "";
			var card_holder_name = "";
			var exp_month = "";
			var exp_year = "";
			var card_type = "";
			var check = 1;
			<?php
			$methods = (array)$lists['methods'];
			if (count($methods) > 0) {
				if (count($methods) > 1) {
				?>
					var paymentValid = false;
					var nmethods = document.getElementById('nmethods');
					var methodtemp;
					for (var i = 0 ; i < nmethods.value; i++) {
						methodtemp = document.getElementById('pmt' + i);
						if(methodtemp.checked == true){
							paymentValid = true;
							paymentMethod = methodtemp.value;
							break;
						}
					}
					
					if (!paymentValid) {
						//alert("<?php echo Text::_('OS_REQUIRE_PAYMENT_OPTION'); ?>");
						alert(Joomla.JText._('<?php echo Text::plural("OS_REQUIRE_PAYMENT_OPTION", 1, array("script"=>true));?>'));
						methodpass = 0;
					}		
				<?php	
				} else {
				?>
					paymentMethod = "<?php echo $methods[0]->getName(); ?>";
				<?php	
				}				
				?>
				//var discount_100 = document.getElementById('discount_100');
				method = methods.Find(paymentMethod);	
				if ((method.getCreditCard()) && (check == 1)) {
					var x_card_nume = document.getElementById('x_card_num');
					if (x_card_nume.value == "") {
						//alert("<?php echo  Text::_('OS_ENTER_CARD_NUMBER'); ?>");
						alert(Joomla.JText._('<?php echo Text::plural("OS_ENTER_CARD_NUMBER", 1, array("script"=>true));?>'));
						x_card_nume.focus();
						methodpass = 0;
						return 0;
					}else{
						x_card_num = x_card_nume.value;
					}
					
					var x_card_codee = document.getElementById('x_card_code');
					if (x_card_codee.value == "") {
						//alert("<?php echo Text::_('OS_ENTER_CARD_CODE'); ?>");
						alert(Joomla.JText._('<?php echo Text::plural("OS_ENTER_CARD_CODE", 1, array("script"=>true));?>'));
						x_card_codee.focus();
						methodpass = 0;
						return 0;
					}else{
						x_card_code = x_card_codee.value;
					}
				}
				
				if (method.getCardHolderName()) {
					card_holder_namee = document.getElementById('card_holder_name');
					if (card_holder_namee.value == '') {
						alert(Joomla.JText._('<?php echo Text::plural("OS_ENTER_CARD_HOLDER_NAME", 1, array("script"=>true));?>'));
						card_holder_namee.focus();
						methodpass = 0;
						return 0;
					}else{
						card_holder_name = card_holder_namee.value;
					}
				}

				var exp_yeare = document.getElementById('exp_year');
				exp_year = exp_yeare.value;
				var exp_monthe = document.getElementById('exp_month');
				exp_month = exp_monthe.value;
				var card_typee =  document.getElementById('card_type');
				card_type = card_typee.value;

				return 1;
				<?php
			}
			?>
		}

		function showAlertMandatory(field){
			alert(field + " " + Joomla.JText._('<?php echo Text::plural("OS_IS_MANDATORY_FIELD", 1, array("script"=>true));?>'));
		}

		function showPriceFields(){
			var price_call = document.getElementById('price_call');
			var pricediv   = document.getElementById('pricediv');
			if(price_call.value == 0){
				pricediv.style.display = "block";
			}else{
				pricediv.style.display = "none";
			}
		}
		function gotoDefaultPage(){
			<?php
			if (Factory::getUser()->authorise('frontendmanage', 'com_osproperty')) {
				$needs = [];
				$needs[] = "lmanageproperties";
				$needs[] = "property_manageallproperties";
				$itemid = OSPRoute::getItemid($needs);
				?>
				location.href = "<?php echo Route::_('index.php?option=com_osproperty&view=lmanageproperties&Itemid='.$itemid)?>";
				<?php
			}else{
				if(HelperOspropertyCommon::isAgent()){
					$need = [];
					$needs = [];
					$needs[] = "agent_editprofile";
					$needs[] = "agent_default";
					$needs[] = "aeditdetails";
					$itemid = OSPRoute::getItemid($needs);
					?>
					location.href = "<?php echo Route::_('index.php?option=com_osproperty&task=agent_editprofile&Itemid='.$itemid)?>";
					<?php
				}elseif(HelperOspropertyCommon::isCompanyAdmin()){
					$needs = [];
					$needs[] = "company_properties";
					$itemid = OSPRoute::getItemid($needs);
					?>
					location.href = "<?php echo Route::_('index.php?option=com_osproperty&task=company_properties&Itemid='.$itemid)?>";
					<?php 
				}
			}
			?>
		}
		</script>
		<?php
		//$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		jimport('joomla.filesystem.file');
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/propertyedit.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$itemid = $jinput->getInt('Itemid',0);
		$tpl->set('itemid',$itemid);
		$extend = $jinput->getInt('extend',0);
		$tpl->set('extend',$extend);
		$tpl->set('mainframe',$mainframe);
		$tpl->set('lists',$lists);
		$tpl->set('neighborhoods',$neighborhoods);
		$tpl->set('configClass',$configClass);
		$tpl->set('row',$row);
		$tpl->set('edit',$edit);
		$tpl->set('lists',$lists);
		$tpl->set('amenities',$amenities);	
		$tpl->set('amenitylists',$amenitylists);
		$tpl->set('groups',$groups);
		$tpl->set('configs',$configs);
		$tpl->set('configClass',$configClass);
		$tpl->set('languages',$languages);
		$tpl->set('translatable',$translatable);
		$tpl->set('isNew',$isNew);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$body = $tpl->fetch("propertyedit.php");
		echo $body;
	}
	
	
	/**
	 * Update Form step 1
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $coupon
	 */
	static function updateFormStep1($rows,$lists){
		global $bootstrapHelper, $mainframe,$jinput,$configs,$configClass;
		$db = Factory::getDBO();
		$user = Factory::getUser();
		//HTMLHelper::_('behavior.modal','a.osmodal');
		OSPHelperJquery::colorbox('a.osmodal');
		$amount = $configClass['general_featured_upgrade_amount'];
		?>
		<script type="text/javascript">
		function removeItem(id){
			var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_REMOVE_THE_PROPERTY')?>");
			if(answer == 1){
				document.ftForm1.task.value = "property_removeUpgrade";
				document.ftForm1.remove_value.value = id;
				document.ftForm1.submit();
			}
		}
		function checkCouponCode(){
			var coupon_code = document.getElementById('coupon_code');
			checkCouponcode('<?php echo $coupon->id?>',coupon_code.value,'<?php echo Uri::root()?>');
			
		}
		</script>
        <?php
        jimport('joomla.filesystem.file');
        if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/upgrade_step1.php')){
            $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
        }else{
            $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
        }
		$itemid = $jinput->getInt('Itemid',0);
		$tpl->set('itemid',$itemid);
        $tpl->set('rows',$rows);
        $tpl->set('configClass',$configClass);
        $tpl->set('lists',$lists);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
        $body = $tpl->fetch("upgrade_step1.php");
        echo $body;
	}
	
	
	/**
	 * Upgrade normal properties to featured properties in case you are integrating OS Property with OS Membership
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $coupon
	 */
	static function updateFormStep1WithMembership($option,$rows){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$db = Factory::getDBO();
        if(HelperOspropertyCommon::isAgent()){
            $usertype = 0;
        }elseif(HelperOspropertyCommon::isCompanyAdmin()){
            $usertype = 1;
        }
		OSPHelperJquery::colorbox('a.osmodal');
		//OSPHelper::generateHeading(2,Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE'));
		$amount = $configClass['general_featured_upgrade_amount'];
		?>
		<script type="text/javascript">
		function removeItem(id){
			var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_REMOVE_THE_PROPERTY')?>");
			if(answer == 1){
				document.ftForm.task.value = "property_removeUpgrade";
				document.ftForm.remove_value.value = id;
				document.ftForm.submit();
			}
		}
		</script>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ">
             <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> ">
			 	<h1 class="componentheading">
					<?php
						echo Text::_('OS_UPGRADE_PROPERTIES_TO_FEATURE');
					?>
				</h1>
			 </div>
		</div>
        <?php
        if(OspropertyMembership::getUserFeaturedCredit() < count($rows)){
            $canSubmit = 0;
            ?>
            <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> ">
                <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> ">
                    <div class="error">
                        <?php
                        echo Text::_('OS_YOU_DONOT_HAVE_SUITABLE_SUBSCRIPTION_TO_IMPLEMENT_THIS_TASK');
                        ?>
                        .<?php
                        echo Text::_('OS_YOU_CAN').": ";
                        ?>
                        <Br />
                        <ul>
                            <li>
                                1. <?php echo Text::_('OS_REMOVE_PROPERTIES_IN_LIST');?>
                            </li>
                            <li>
                                <?php
                                $session = Factory::getSession();
                                $cidVar = "";
                                if(count($rows)){
                                    $tempArr = [];
                                    foreach($rows as $row){
                                        $tempArr[] = "&cid[]=".$row->id;
                                    }
                                    $cidVar = implode("&",$tempArr);
                                    $cidVar = "&".$cidVar;
                                }
                                $session->set('osm_return_url',Route::_('index.php?option=com_osproperty&task=property_upgrade&type=2'.$cidVar));
                                ?>
                                2. <?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?>:
                                    <?php
                                    $link = OspropertyMembership::generateLink($usertype,1,0);
                                    ?>
                                    <a href="<?php echo $link?>" title="<?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?>"><?php echo Text::_('OS_PURCHASE_SUBSCRIPTION');?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php
        }else{
            $canSubmit = 1;
        }
        ?>
		<form method="POST" action="<?php echo Route::_('index.php?Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm">
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
            <table  width="100%" class="border0">
                <tr>
                    <td width="95%" class="header_td paddingleft20">
                        <?php
                            echo Text::_('OS_PROPERTY');
                        ?>
                    </td>
                    <td width="5%" class="header_td">
                        <?php
                            echo Text::_('OS_REMOVE');
                        ?>
                    </td>
                </tr>
                <?php
                $total = 0;
                for($i=0;$i<count($rows);$i++){
                    $row = $rows[$i];
                    $total = $total + $amount;
                    ?>
                    <input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
                    <tr>
                        <td class="data_td paddingleft5" width="20%">
                            <?php
                                echo $row->pro_name;
                            ?>
                        </td>
                        <td class="data_td center">
                            <a href="javascript:removeItem('<?php echo $row->id?>')">
                                <img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/delete.png" />
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <h4>
                    <?php echo Text::_('OS_YOUR_CREDITS');?>
                </h4>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <?php
                //$agentAcc = HelperOspropertyCommon::returnAccountValueFeatured(count($rows));
                $agentAcc = OspropertyMembership::getUserFeaturedCredit();
                HelperOspropertyCommon::generateMembershipFormUpgradeProperties($agentAcc);
                ?>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> center">
                <input type="button" value="<?php echo Text::_('OS_BACK')?>" class="btn btn-warning" onclick="javascript:history.go(-1);"/>
                <?php
                if($canSubmit == 1){
                ?>
                <input type="button" value="<?php echo Text::_('OS_CONFIRM')?>" class="btn btn-info" onclick="javascript:document.ftForm.submit();" />
                <?php } ?>
            </div>
        </div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_membershipprocess" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		<input type="hidden" name="remove_value" id="remove_value" value="" />
		<input type="hidden" name="live_site" value="<?php echo Uri::root()?>" />
        <input type="hidden" name="type" value="2" />
		</form>
		<?php
	}
	
	/**
	 * Confirmation Upgrade
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 * @param unknown_type $coupon
	 * @param unknown_type $awarded
	 */
	static function confirmUpgrade($option,$rows,$coupon,$awarded){
		global $bootstrapHelper, $mainframe,$jinput,$configs,$configClass;
		$db = Factory::getDBO();
		OSPHelperJquery::colorbox('a.osmodal');
		
		$amount = $configClass['general_featured_upgrade_amount'];
		?>
		<form method="POST" action="<?php echo Route::_('index.php?Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm">
		<table  width="100%" class="border0">
			<tr>
				<td width="100%">
					<span class="fontbold fontsize18">
						<?php echo Text::_('OS_CONFIRM_UPGRADE_PROPERTIES_TO_FEATURE')?>
					</span>
				</td>
			</tr>
			<tr>
				<td width="100%" class="border0">
					<table  width="100%" class="border0">
						<tr>
							<td width="80%" class="header_td alignleft paddingleft20">
								<?php
									echo Text::_('OS_PROPERTY');
								?>
							</td>
							<td width="10%" class="header_td alignright">
								<?php
									echo Text::_('OS_TOTAL');
								?>
							</td>
						</tr>
						<?php
						$total = 0;
						for($i=0;$i<count($rows);$i++){
							$row = $rows[$i];
							$total = $total + $amount;
							
							$link = Uri::root()."index.php?option=com_osproperty&task=property_details&id=".$row->id;
							?>
							<input type="hidden" name="cid[]" value="<?php echo $row->id?>" />
							<tr>
								<td class="data_td" width="80%">
									<table  width="100%" class="border0">
										<tr>
											<td width="70">
												<?php
												if($row->image != ""){
													?>
													<a href="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id?>/<?php echo $row->image?>" class="osmodal">
													<?php
													OSPHelper::showPropertyPhoto($row->image,'thumb',$row->id,'width:70px;','img-rounded','');
													?>
													</a>
													<?php
												}
												?>
											</td>
											<td align="left" class="paddingleft20">
												<a href="<?php echo $link?>" class="osmodal" rel="{handler: 'iframe', size: {x: 900, y: 500}, onClose: function() {}}">
												<strong>
												<?php
													echo $row->pro_name;
												?>
												</strong>
												</a>
											</td>
										</tr>
									</table>
								</td>
							
								<td class="data_td alignright">
								    <?php echo HelperOspropertyCommon::loadDefaultCurrency(1)?>&nbsp;
									<?php echo OSPHelper::showPrice($amount)?>
								</td>
							</tr>
							<?php	
						}
						?>
						<?php
						if($awarded == 1){
							?>
							<tr>
								<td class="data_td alignright fontbold" width="80%" colspan="1" style="background-color:#FBECEC;">
									<?php echo Text::_('OS_COUPON')?> [<?php echo $coupon->coupon_name?>]
								</td>
								<td class="data_td alignright" style="background-color:#FBECEC;">
									- <?php 
									  $discount = ($coupon->discount/100)*$total;
									  echo OSPHelper::showPrice($discount);
									  $total = $total - $discount;
									  echo $configClass['general_currency_default']; ?>
								</td>
							</tr>
							<?php
						}
						?>
						<tr>
							<td class="data_td alignright fontbold" width="80%" colspan="1" style="background-color:#efefef;">
								<?php echo Text::_('OS_TOTAL')?>
							</td>
							<td class="data_td alignright" style="background-color:#efefef;">
								<?php echo HelperOspropertyCommon::loadDefaultCurrency(1)?>&nbsp;
								<?php echo OSPHelper::showPrice($total);?> 
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<BR />
		<table  width="100%" class="border0">
			<tr>
				<td width="100%" class="border0 alignright">
					<input type="button" value="<?php echo Text::_('OS_BACK')?>" class="btn btn-warning" onclick="javascript:history.go(-1);" />
					<input type="button" value="<?php echo Text::_('OS_PAY')?>" class="btn btn-info" onclick="javascript:document.ftForm.submit();" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_paymentprocess" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		<input type="hidden" name="live_site" value="<?php echo Uri::root()?>" />
		</form>
		<?php
	}
	
	static function favorites($option,$countFav){
		global $bootstrapHelper, $mainframe,$jinput;
		$needs = [];
		$needs[] = "property_favorites";
		$itemid = OSPRoute::getItemid($needs);
		$itemid = OSPRoute::confirmItemid($itemid,'property_favorites');
		?>
		<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=property_favorites&Itemid='.$itemid);?>" name="ftForm" id="ftForm">
		<?php 
		OSPHelper::generateHeading(2,Text::_('OS_MY_FAVORITES'));
		?>
		<?php
		if($countFav > 0){
			$filterParams = [];
			//show cat
			$filterParams[0] = 1;
			//agent
			$filterParams[1] = 1;
			//keyword
			$filterParams[2] = 1;
			//bed
			$filterParams[3] = 0;
			//bath
			$filterParams[4] = 0;
			//rooms
			$filterParams[5] = 0;
			//price
			$filterParams[6] = 0;
			OspropertyListing::listProperties($option,'',$jinput->get('catIds',array(),'ARRAY'),$jinput->getInt('agent_id',0),$jinput->getInt('property_type',0),$jinput->getString('keyword'),'','','',0,'',$jinput->getString('orderby','a.created'),$jinput->getString('ordertype','desc'),$jinput->getInt('limitstart',0),$jinput->getInt('limit',20),1,'',$filterParams,0,0,0,0,1);
		}else{
			?>
			<div class="width100pc padding10 center">
				<h1 class="border0">
					<?php echo Text::_('OS_NO_ITEMS_IN_FAV_LIST');?>
				</h1>
				<?php
				printf(Text::_('CLICK_HERE_TO_GO_BACK'),"<a href='javascript:history.go(-1)'>","</a>");
				?>
			</div>
			<?php
		}
		?>
		
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_favorites" />
		<input type="hidden" name="Itemid" value="<?php echo $itemid;?>" />
		</form>
		<?php
	}
	
	
	/**
	 * Property details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 * @param unknown_type $amenities
	 * @param unknown_type $amenitylists
	 * @param unknown_type $groups
	 */
	static function printPropertyEmail($option,$row,$lists,$amenities,$amenitylists,$groups,$configs){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$db = Factory::getDBO();
		$document =& Factory::getDocument();
		//OSPHelper::loadTooltip();
		
		//require(JPATH_COMPONENT."/helpers/layouts/propertyprint.php");
		
	}
	
	/**
	 * Generic the pdf layout page
	 *
	 * @param unknown_type $option
	 * @param unknown_type $row
	 * @param unknown_type $lists
	 * @param unknown_type $amenities
	 * @param unknown_type $amenitylists
	 * @param unknown_type $groups
	 * @param unknown_type $configs
	 */
	static function printPropertyPdf($option,$row,$lists,$amenities,$amenitylists,$groups){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$db = Factory::getDBO();
		$document =& Factory::getDocument();
		//$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		jimport('joomla.filesystem.file');
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/propertypdf.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('db',$db);
		$tpl->set('document',$document);
		$tpl->set('row',$row);
		$tpl->set('lists',$lists);
		$tpl->set('amenities',$amenities);	
		$tpl->set('amenitylists',$amenitylists);
		$tpl->set('groups',$groups);
		$tpl->set('configClass',$configClass);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$body = $tpl->fetch("propertypdf.php");
		echo $body;
	}
	
	/**
	 * Thank you page
	 *
	 * @param unknown_type $option
	 * @param unknown_type $configs
	 * @param unknown_type $property
	 * @param unknown_type $expired
	 */
	static function thankyouPage($option,$property,$expired,$photos,$amenities,$groups,$msg = array()){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		OSPHelperJquery::colorbox('a.osmodal');
		$pane =& JPane::getInstance('tabs');
		$db = Factory::getDBO();
		$document =& Factory::getDocument();
		jimport('joomla.filesystem.file');
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/propertyinformation.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('pane',$pane);
		$tpl->set('document',$document);
		$tpl->set('property',$property);
		$tpl->set('amenities',$amenities);	
		$tpl->set('expired',$expired);
		$tpl->set('groups',$groups);
		$tpl->set('configClass',$configClass);
		$tpl->set('photos',$photos);
		$tpl->set('msg',$msg);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$body = $tpl->fetch("propertyinformation.php");
		echo $body;
	}
	
	/**
	 * Approval details
	 *
	 * @param unknown_type $option
	 * @param unknown_type $property
	 * @param unknown_type $expired
	 */
	static function approvalDetails($option,$property,$expired){
		global $bootstrapHelper, $mainframe;
		?>
		<table  width="100%">
			<tr>
				<td width="100%" valign="top"  class="padding10">
					<span class="fontsize18 fontbold">
						<?php echo Text::_('OS_APPROVAL_INFO');
							  echo ' ['.$property->pro_name.']';?>
					</span>
					<BR />
					<BR />
					<?php
					$current_time = time();
					$expired_time = strtotime($expired->expired_time);
					echo "<strong>".Text::_('Status').": ";
					if($expired_time < $current_time){
						echo "<span color='red'>".Text::_('OS_UNAPPROVED')."</span>";
					}else{
						echo "<span color='green'>".Text::_('OS_APPROVED')."</span>";
					}
					?>
				</td>
			</tr>
		</table>
		<?php
	}
	
	
	/**
	 * Confirm approval
	 *
	 * @param unknown_type $option
	 * @param unknown_type $rows
	 */
	static function confirmApproval($option,$rows){
		global $bootstrapHelper, $mainframe,$jinput;
		$db = Factory::getDbo();
		?>
		<form method="POST" action="<?php echo Route::_('index.php?Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm">
		<table  width="100%" class="border0">
			<tr>
				<td width="100%">
					<span class="fontsize18 fontbold">
						<?php echo Text::_('OS_CONFIRM_REQUEST_APPROVAL_PROPERTIES');?>
					</span>
					<BR />
					<BR />
					<?php
					echo Text::_('OS_PLEASE_CHECK_PROPERTY_LISTS_YOU_WANT_TO_SEND_REQUEST_APPROVAL');
					?>
					<BR /><BR />
					<div class="border1 padding10 backgroundlightgray" style="width:400px;">
					<?php
					for($i=0;$i<count($rows);$i++){
						$row = $rows[$i];
						?>
						<input type="checkbox" value="<?php echo $row->id?>" name="cid[]" id="id<?php echo $row->id?>" checked /> &nbsp; <?php echo $row->property?>
						<BR />
						<?php
					}
					?>
					</div>
					<BR />
					<input type="button" class="btn btn-warning" value="<?php echo Text::_('OS_BACK')?>" onclick="javascript:history.go(-1)" />
					<input type="submit" class="btn btn-info" value="<?php echo Text::_('OS_SUBMIT')?>" />
				</td>
			</tr>
		</table>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_processrequestapproval" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
		</form>
		<?php
	}
	
	/**
	 * Generate photo by manually
	 *
	 * @param unknown_type $option
	 * @param unknown_type $id
	 * @param unknown_type $photoIds
	 * @param unknown_type $save
	 * @param unknown_type $pro_name
	 */
	static function generatePhotoCrop($option,$id,$photoIds,$save,$pro_name){
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		//JToolBarHelper::title(Text::_('OS_CREATE_PHOTO_BY_MANUAL')." [".$pro_name."]");
		//JToolBarHelper::apply('properties_savephoto');
		//JToolBarHelper::custom('properties_completesaving','forward.png','forward.png',Text::_('OS_SKIP'));		
		$db = Factory::getDbo();
		$db->setQuery("Select * from #__osrs_photos where id in ($photoIds)");
		$photos = $db->loadObjectList();
		//jimport('joomla.html.pane');
        
		?>
		<script type="text/javascript">
		//enable passthrough of errors from YUI Event:
		if ((typeof YAHOO !== "undefined") && (YAHOO.util) && (YAHOO.util.Event)) {
			YAHOO.util.Event.throwErrors = true;
		}
		function showDiv(photo_id){
			var div = document.getElementById('div_' + photo_id);
			var href = document.getElementById('link_' + photo_id);
			if(div.style.display == "block"){
				div.style.display = "none";
				href.innerHTML = "[+]";
			}else{
				div.style.display = "block";
				href.innerHTML = "[-]";
			}
		}
		function submitForm(task){
			var form  = document.adminForm;
			form.task.value = task;
			form.submit();
		}
		</script>
		<form method="POST" action="index.php" name="adminForm">
		<table width="100%">
			<tr>
				<td width="50%" align="left">
					<h1 class="componentheading">
						<?php echo Text::_('OS_CREATE_PHOTO_BY_MANUAL')." [".$pro_name."]" ?>
					</h1>
				</td>
				<td width="50%" align="right">
					<div class="btn-toolbar">
						<div class="btn-group pull-right">
							<button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_SAVE');?>" onclick="javascript:submitForm('property_savephoto');">
			                    <i class="osicon-save"></i> <?php echo Text::_('OS_SAVE');?>
			                </button>
			                <button type="button" class="btn hasTooltip" title="<?php echo Text::_('OS_SKIP');?>" onclick="javascript:submitForm('property_skip');">
			                    <i class="osicon-cancel"></i> <?php echo Text::_('OS_SKIP');?>
			                </button>
						</div>
					</div>
				</td>
			</tr>
		</table>
		<table width="100%" class="admintable">
			<?php
			if($photos > 0){
			
				for($i=0;$i<count($photos);$i++){
					
				$photo = $photos[$i];
				$photo_id = $photo->id;
				
				if($i == 0){
					$display = "block";
				}else{
					$display = "none";
				}
				
				 $medium_width = $configClass['images_large_width'];
				 $medium_height = $configClass['images_large_height'];
				 $original_info = getimagesize(JPATH_ROOT."/images/osproperty/properties/".$id."/".$photo->image);
				
				 $origin_width  = $original_info[0];
				 $origin_height = $original_info[1];
				?>
				<tr>
					<td width="100%">
						<table  width="100%" class="border1">
							<tr>
								<td class="padding3 colorwhite fontbold center" style="background-color:#7A7676;">
									<?php echo Text::_('OS_PHOTO')?> <?php echo $i+1;?> &nbsp;&nbsp;
									<?php
									if($display == "block"){
										?>
										<a href="javascript:showDiv(<?php echo $photo_id?>)" id="link_<?php echo $photo_id?>">[-]</a>
										<?php
									}else{
										?>
										<a href="javascript:showDiv(<?php echo $photo_id?>)" id="link_<?php echo $photo_id?>">[+]</a>
										<?php
									}
									?>
								</td>
							</tr>
							<tr>
								<td width="100%" valign="top">
									<div id="div_<?php echo $photo_id?>" style="display:<?php echo $display?>;">
									<?php
									//$pane =& JPane::getInstance('tabs');
									//ul
									?>
									<table width="100%"> 
										<tr>
											<td width="100%" valign="top">
												<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
													<ul class="nav nav-tabs">
														<li class="active"><a href="#tab1<?php echo $i;?>" data-toggle="tab"><?php echo Text::_('OS_THUMBNAIL_PHOTO');?></a></li>
														<?php
														if(($medium_height < $origin_height) or ($medium_width < $origin_width)){
														?>
														<li><a href="#tab2<?php echo $i?>" data-toggle="tab"><?php echo Text::_('OS_MEDIUM_PHOTO');?></a></li>
														<?php
														}
														?>
													</ul>
												<?php
												//echo $pane->startPane( 'pane' );
												//echo $pane->startPanel( Text::_('OS_THUMBNAIL_PHOTO'), 'tab1' );
												?>
												<div class="tab-content">	
												<div class="tab-pane active" id="tab1<?php echo $i;?>">
												<table width="100%" class="admintable">
													<tr>
														<td class="key alignleft">
															<input type="radio" name="tb_<?php echo $photo_id?>" id="tb_<?php echo $photo_id?>" value="0">
															&nbsp;
															<?php echo Text::_('OS_THUMBNAIL_PHOTO_IS_CREATED_BY_OSPROPERTY')?>
															
														</td>
													</tr>
													<tr>
														<td width="100%" class="center">
															<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/thumb/<?php echo $photo->image?>" width="<?php echo $configClass['images_thumbnail_width']?>" height="<?php echo $configClass['images_thumbnail_height']?>">
														</td>
													</tr>
													<tr>
														<td class="key alignleft">
															<input type="radio" name="tb_<?php echo $photo_id?>" id="tb_<?php echo $photo_id?>" value="1">
															&nbsp;
															<?php echo Text::_('OS_CREATE_THUMBNAIL_PHOTO_MANUALLY')?>
														</td>
													</tr>
													<tr>
														<td width="100%" class="center">
															<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/<?php echo $photo->image?>" id="tb_yui_img_<?php echo $photo_id?>" width="<?php echo $origin_width?>"  height="<?php echo $origin_height?>" />
															<input type="hidden" name="tb_h_<?php echo $photo_id?>" id="tb_h_<?php echo $photo_id?>" value="" />
															<input type="hidden" name="tb_w_<?php echo $photo_id?>" id="tb_w_<?php echo $photo_id?>" value="" />
															<input type="hidden" name="tb_t_<?php echo $photo_id?>" id="tb_t_<?php echo $photo_id?>" value="" />
															<input type="hidden" name="tb_l_<?php echo $photo_id?>" id="tb_l_<?php echo $photo_id?>" value="" />
														</td>
														<td width="30%" valign="top">
														</td>
													</tr>
												</table>
												<script>
												(function() {
											    var Dom = YAHOO.util.Dom,
											        Event = YAHOO.util.Event,
											        results = null;
											    
											   	 	Event.onDOMReady(function() {
											            var crop = new YAHOO.widget.ImageCropper('tb_yui_img_<?php echo $photo_id?>', {
											                initialXY: [20, 20],
											                initHeight:<?php echo $configClass['images_thumbnail_height']?>,
											                initWidth:<?php echo $configClass['images_thumbnail_width']?>,
											                useKeys:false,
											                keyTick: 5,
											                ratio:true,
											                shiftKeyTick: 50
											            });
											            crop.on('moveEvent', function() {
											                var region = crop.getCropCoords();
											                Dom.get('tb_t_<?php echo $photo_id?>').value = region.top;
											                Dom.get('tb_l_<?php echo $photo_id?>').value = region.left;
											                Dom.get('tb_h_<?php echo $photo_id?>').value = region.height;
											                Dom.get('tb_w_<?php echo $photo_id?>').value = region.width;
											            });
											            
											    });
											})();
											</script>
											</div>
											<?php
											 //echo $pane->endPanel();
											 if(($medium_height < $origin_height) or ($medium_width < $origin_width)){
											 
											 	if(($medium_height > $origin_height) and ($medium_width < $origin_width)){
											 		$height = $origin_height;
											 		$width = round($medium_width*$height/$medium_height);
											 	}elseif(($medium_height < $origin_height) and ($medium_width > $origin_width)){
											 		$width = $origin_width;
											 		$height = round($medium_height*$width/$medium_width);
											 	}else{
											 		$width = $medium_width;
											 		$height = $medium_height;
											 	}
											 	
	        								    //echo $pane->startPanel( Text::_('OS_MEDIUM_PHOTO'), 'tab2' );
												?>
												
												<div class="tab-pane" id="tab2<?php echo $i;?>">
												<table width="100%" class="admintable">
													<tr>
														<td class="key alignleft">
															<input type="radio" name="me_<?php echo $photo_id?>" id="me_<?php echo $photo_id?>" value="0">
															&nbsp;
															<?php echo Text::_('OS_MEDIUM_PHOTO_IS_CREATED_BY_OSPROPERTY')?>
															
														</td>
													</tr>
													<tr>
														<td width="100%" class="center">
															<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/medium/<?php echo $photo->image?>" width="<?php echo $configClass['images_large_width']?>" height="<?php echo $configClass['images_large_height']?>">
														</td>
													</tr>
													<tr>
														<td class="key alignleft">
															<input type="radio" name="me_<?php echo $photo_id?>" id="me_<?php echo $photo_id?>" value="1">
															&nbsp;
															<?php echo Text::_('OS_CREATE_MEDIUM_PHOTO_MANUALLY')?>
														</td>
													</tr>
													<tr>
														<td width="100%" class="center">
															<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id?>/<?php echo $photo->image?>" id="me_yui_img_<?php echo $photo_id?>" width="<?php echo $origin_width?>"  height="<?php echo $origin_height?>" />
															<input type="hidden" name="me_h_<?php echo $photo_id?>" id="me_h_<?php echo $photo_id?>" value="" />
															<input type="hidden" name="me_w_<?php echo $photo_id?>" id="me_w_<?php echo $photo_id?>" value="" />
															<input type="hidden" name="me_t_<?php echo $photo_id?>" id="me_t_<?php echo $photo_id?>" value="" />
															<input type="hidden" name="me_l_<?php echo $photo_id?>" id="me_l_<?php echo $photo_id?>" value="" />
														</td>
														<td width="30%" valign="top">
														</td>
													</tr>
												</table>
												<script>
												(function() {
											    var Dom = YAHOO.util.Dom,
											        Event = YAHOO.util.Event,
											        results = null;
											    
											   	 	Event.onDOMReady(function() {
											            var crop = new YAHOO.widget.ImageCropper('me_yui_img_<?php echo $photo_id?>', {
											                initialXY: [0, 0],
											                initHeight:<?php echo $height?>,
											                initWidth:<?php echo $width?>,
											                useKeys:false,
											                keyTick: 5,
											                ratio:true,
											                shiftKeyTick: 50
											            });
											            crop.on('moveEvent', function() {
											                var region = crop.getCropCoords();
											                Dom.get('me_t_<?php echo $photo_id?>').value = region.top;
											                Dom.get('me_l_<?php echo $photo_id?>').value = region.left;
											                Dom.get('me_h_<?php echo $photo_id?>').value = region.height;
											                Dom.get('me_w_<?php echo $photo_id?>').value = region.width;
											            });
											            
											    });
												})();
												</script>
												<?php
												 //echo $pane->endPanel();
												 echo "</div>";
											 }
        									 //echo $pane->endPane();
        									 echo "</div>";
											?>
											</td>
										</tr>
									</table>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
<?php
				}
			}
			?>
		</table>
		<input type="hidden" name="isNew" id="isNew" value="<?php echo $jinput->getInt('isNew',0)?>" />
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="pid" id="pid" value="<?php echo $id?>" />
		<input type="hidden" name="photoIds" id="photoIds" value="<?php echo $photoIds?>" />
		<input type="hidden" name="save" id="save" value="<?php echo $save?>" />
		</form>
<?php
	}
	
	/**
	 * Show Report form
	 *
	 * @param unknown_type $lists
	 * @param unknown_type $id
	 */
	static function reportForm($lists,$id,$item_type){
		global $bootstrapHelper, $mainframe,$jinput;
		$user = Factory::getUser();
		?>
		<form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_doreport&type=property&id=<?php echo $id?>&tmpl=component" name="reportForm" id="reportForm" class="form-horizontal">
			<H2>
				<?php echo Text::_('OS_REPORT_A_PROBLEM')?>
			</H2>
			<div class="clearfix"></div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> noleftmargin">
				<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PROBLEM')?></label>
				<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
					<?php echo $lists['report_reason']; ?>
				</div>
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> noleftmargin">
				<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_DETAILS')?></label>
				<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
					<textarea class="input-large form-control" cols="50" rows="4" name="report_details"></textarea>
				</div>
			</div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> noleftmargin">
				<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_YOUR_EMAIL')?></label>
				<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
					<input type="text" class="input-large form-control" name="your_email" id="your_email" value="<?php echo $user->email;?>" placeholder = '<?php echo Text::_('OS_EMAIL');?>' />
				</div>
			</div>
            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> noleftmargin">
                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                    <span class="grey_small lineheight"><?php echo Text::_('OS_PLEASE_INSERT_THE_SYMBOL_FROM_THE_INAGE_TO_FIELD_BELOW')?></span>
                    <div class="clearfix"></div>
                    <?php
                    $RandomStr = md5(microtime());// md5 to generate the random string
                    $ResultStr = substr($RandomStr,0,5);//trim 5 digit
                    ?>
                    <img src="<?php echo Uri::root()?>index.php?option=com_osproperty&no_html=1&task=property_captcha&ResultStr=<?php echo $ResultStr?>" id="security_code_image">
                    <input type="text" class="input-small form-control" id="security_code" name="security_code" maxlength="5"/>
                </div>
            </div>
			<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?> noleftmargin">
				<button class="btn btn-primary" title="<?php echo Text::_('OS_SUBMIT')?>" onClick="javascript:submitReportForm();" style="width:120px;"><?php echo Text::_('OS_SUBMIT')?></button>
				<input type="reset" class="btn btn-warning" value="<?php echo Text::_('OS_RESET')?>" style="width:120px;" />
			</div>
		<?php
		?>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="property_doreportproperty" />
		<input type="hidden" name="id" value="<?php echo $id?>" />
		<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid');?>" />
		<input type="hidden" name="item_type" id="item_type" value="<?php echo $item_type?>" />
        <input type="hidden" name="property_captcha_str" id="property_captcha_str" value="<?php echo $ResultStr; ?>" />
		</form>
        <script type="text/javascript">
        function submitReportForm(){
            var security_code = document.getElementById('security_code');
            var captcha_str = document.getElementById('property_captcha_str');
            if(security_code.value == ""){
                alert(" <?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
                security_code.focus();
                return false;
            }else if(security_code.value != captcha_str.value){
                alert(" <?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
                security_code.focus();
                return false;
            }else{
                document.reportForm.submit();
            }
        }
        </script>
<?php
	}

    /**
     * @param $comment
     */
    public static function editCommentForm($comment){
        global $bootstrapHelper, $configClass;
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <h1 class="componentheading">
                    <?php echo Text::_('OS_EDIT_COMMENT'); ?>
                </h1>
            </div>
        </div>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <form method="POST" action="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_submitcomment&Itemid=<?php echo $itemid;?>" name="commentForm" id="commentForm" class="form-horizontal">
                    <?php
                    if($configClass['show_rating'] == 1){
                        ?>
                        <div class="control-group">
                            <label class="control-label">
                                <?php echo Text::_('OS_RATING');?>
                            </label>
                            <div class="controls">
                                <i><?php echo Text::_('OS_WORST');?>
                                    &nbsp;
                                    <?php
                                    for($i=1;$i<=5;$i++){
                                        if($i==$comment->rate){
                                            $checked = "checked";
                                        }else{
                                            $checked = "";
                                        }
                                        ?>
                                        <input type="radio" name="rating" id="rating<?php echo $i?>" value="<?php echo $i?>" <?php echo $checked?> />
                                    <?php
                                    }
                                    ?>
                                    &nbsp;&nbsp;<?php echo Text::_('OS_BEST');?></i>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="control-group">
                        <label class="control-label">
                            <?php echo Text::_('OS_AUTHOR');?>
                        </label>
                        <div class="controls">
                            <input class="input-large" type="text" id="comment_author" name="comment_author" maxlength="30" value="<?php echo $comment->name;?>" />
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <?php echo Text::_('OS_TITLE');?>
                        </label>
                        <div class="controls">
                            <input class="input-large" type="text" id="comment_title" name="comment_title" size="40" placeholder="<?php echo Text::_('OS_TITLE');?>" value="<?php echo $comment->title;?>"/>
                        </div>
                    </div>

                    <div class="control-group">
                        <label class="control-label">
                            <?php echo Text::_('OS_MESSAGE');?>
                        </label>
                        <div class="controls">
                            <textarea id="comment_message" name="comment_message" rows="6" cols="50" class="input-large"><?php echo $comment->content;?></textarea>
                        </div>
                    </div>
                    <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                        <input onclick="javascript:submitForm('commentForm')" class="btn btn-warning margin0 width100px" type="button" name="finish" value="<?php echo Text::_('OS_SUBMIT')?>" />
                        <span id="comment_loading" class="reg_loading">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </div>
                    <input type="hidden" name="option" value="com_osproperty" />
                    <input type="hidden" name="task" value="property_submiteditcomment" />
                    <input type="hidden" name="id" value="<?php echo $comment->id?>" />
                    <input type="hidden" name="require_field" id="require_field" value="comment_author,comment_title,comment_message" />
                    <input type="hidden" name="require_label" id="require_label" value="<?php echo Text::_('OS_AUTHOR');?>,<?php echo Text::_('OS_TITLE');?>,<?php echo Text::_('OS_MESSAGE');?>" />
                </form>
            </div>
        </div>
        <script type="text/javascript">
            function submitForm(form_id){
                var form = document.getElementById(form_id);
                var temp1,temp2;
                var cansubmit = 1;
                var require_field = form.require_field;
                require_field = require_field.value;
                var require_label = form.require_label;
                require_label = require_label.value;
                var require_fieldArr = require_field.split(",");
                var require_labelArr = require_label.split(",");
                for(i=0;i<require_fieldArr.length;i++){
                    temp1 = require_fieldArr[i];
                    temp2 = document.getElementById(temp1);

                    if(temp2 != null){
                        if(temp2.value == ""){
//                            alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY_FIELD')?>");
							alert(require_labelArr[i] + " " + Joomla.JText._('<?php echo Text::plural("OS_IS_MANDATORY_FIELD", 1, array("script"=>true));?>'));
                            temp2.focus();
                            cansubmit = 0;
                            return false;
                        }else if(temp1 == "comment_security_code"){
                            var captcha_str = document.getElementById('captcha_str');
                            captcha_str = captcha_str.value;
                            if(captcha_str != temp2.value){
                                //alert(" <?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
								alert(" " + Joomla.JText._('<?php echo Text::plural("OS_SECURITY_CODE_IS_WRONG", 1, array("script"=>true));?>'));
                                temp2.focus();
                                cansubmit = 0;
                                return false;
                            }
                        }else if(temp1 == "request_security_code"){
                            var captcha_str = document.getElementById('captcha_str');
                            captcha_str = captcha_str.value;
                            if(captcha_str != temp2.value){
                                //alert("<?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
								alert(Joomla.JText._('<?php echo Text::plural("OS_SECURITY_CODE_IS_WRONG", 1, array("script"=>true));?>'));
                                temp2.focus();
                                cansubmit = 0;
                                return false;
                            }
                            <?php
                            if($configClass['request_term_condition'] == 1){
                                ?>
                            if(document.getElementById('termcondition').checked == false){
                               // alert(" <?php echo Text::_('OS_PLEASE_AGREE_WITH_OUT_TERM_AND_CONDITION')?>");
							   alert(Joomla.JText._('<?php echo Text::plural("OS_PLEASE_AGREE_WITH_OUT_TERM_AND_CONDITION", 1, array("script"=>true));?>'));
                                document.getElementById('termcondition').focus();
                                cansubmit = 0;
                                return false;
                            }
                            <?php
                        }
                        ?>
                        }else if(temp1 == "sharing_security_code"){
                            var captcha_str = document.getElementById('captcha_str');
                            captcha_str = captcha_str.value;
                            if(captcha_str != temp2.value){
                                //alert("<?php echo Text::_('OS_SECURITY_CODE_IS_WRONG')?>");
								alert(Joomla.JText._('<?php echo Text::plural("OS_SECURITY_CODE_IS_WRONG", 1, array("script"=>true));?>'));
                                temp2.focus();
                                cansubmit = 0;
                                return false;
                            }
                        }
                    }
                }
                if(cansubmit == 1){
                    form.submit();
                }
            }
        </script>
        <?php
    }

    /**
     * @param $rows
     * @param $lists
     * @param $pageNav
     */
    static function manageAllProperties($rows,$lists,$pageNav)
	{
        global $bootstrapHelper, $configClass,$jinput,$mainframe;
		//HTMLHelper::_('bootstrap.tooltip');
		OSPHelper::loadTooltip();
        ?>
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
                <form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&view=lmanageproperties&Itemid='.$jinput->getInt('Itemid',0));?>" name="ftForm" id="ftForm" class="form-horizontal">
                    <?php
                    if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/manageproperties.php')){
                        $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
                    }else{
                        $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
                    }
                    $tpl->set('option','com_osproperty');
                    $tpl->set('rows',$rows);
                    $tpl->set('lists',$lists);
                    $tpl->set('pageNav',$pageNav);
                    $tpl->set('itemid',$jinput->getInt('Itemid',0));
                    $tpl->set('configClass',$configClass);
                    $tpl->set('jinput',$jinput);
                    $tpl->set('supervisor',1);
					$tpl->set('bootstrapHelper',$bootstrapHelper);
                    $body = $tpl->fetch("manageproperties.php");
                    echo $body;
                    ?>
					<input type="hidden" name="option" value="com_osproperty" />
					<input type="hidden" name="task" value="property_manageallproperties" />
					<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
					<input type="hidden" name="view" value="lmanageproperties" />
                </form>
                <script type="text/javascript">
                    function allCheck(id){
                        var temp = document.getElementById(id);
                        var count = "<?php echo count($rows)?>";
                        if(temp.value == 0){
                            temp.value = 1;
                            for(i=0;i<count;i++){
                                cb = document.getElementById('cb'+ i);
                                if(cb != null){
                                    cb.checked = true;
                                }
                            }
                        }else{
                            temp.value = 0;
                            for(i=0;i<count;i++){
                                cb = document.getElementById('cb'+ i);
                                if(cb != null){
                                    cb.checked = false;
                                }
                            }
                        }
                    }

					function changeStatus(id,type,value){
						location.href = "<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_changevalue" + type + "&value=" + value + "&id=" + id;
					}

					function submitForm(t){
						var total = 0;
						var temp;
						total = <?php echo count($rows)?>;
						if(total > 0){
							var check = 0;
							for(i=0;i<total;i++){
								temp = document.getElementById('cb' + i);
								if(temp != null){
									if(temp.checked == true){
										check = 1;
									}
								}
							}
							if(check == 0)
							{
								alert("<?php echo Text::_('OS_PLEASE_ITEM');?>");
							}
							else
							{
								if(t == "deleteproperties")
								{
									var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_REMOVE_ITEMS')?>");
									if(answer == 1){
										document.ftForm.task.value = "property_deleteproperties";
										document.ftForm.submit();
									}
								}
								else
								{
									document.ftForm.task.value = "property_" + t;
									document.ftForm.submit();
								}
							}
						}
					}
                </script>
            </div>
        </div>
        <?php
    }

}
?>
