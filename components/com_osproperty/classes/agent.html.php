<?php

/*------------------------------------------------------------------------
# agent.html.php - Ossolution Property
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
use Joomla\CMS\Editor\Editor;

class HTML_OspropertyAgent{
	/**
	 * Agent layout
	 *
	 * @param unknown_type $option
	 */
	static function agentLayout($option,$rows,$pageNav,$lists)
	{
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$ismobile,$bootstrapHelper;

		$db = Factory::getDbo();
		//HTMLHelper::_('behavior.modal');
		$page = $jinput->getString('page','');
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="agentlisting">
			<script type="text/javascript">
			function updateOrderType(ordertype_value){
				var ordertype = document.getElementById('ordertype');
				if(ordertype.value != ordertype_value){
					ordertype.value = ordertype_value;
					document.ftForm.submit();
				}
			}
			</script>
			<?php 
			OSPHelper::generateHeading(2,Text::_('OS_LIST_AGENTS'));
			?>
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin noleftpadding">
				<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=agent_layout&Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
					<?php
					jimport('joomla.filesystem.file');
					if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/agentslist.php'))
					{
						$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
					}
					else
					{
						$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
					}
					$tpl->set('ordertype',$ordertype);
					$tpl->set('mainframe',$mainframe);
					$tpl->set('lists',$lists);
					$tpl->set('option',$option);
					$tpl->set('configClass',$configClass);
					$tpl->set('rows',$rows);
					$tpl->set('pageNav',$pageNav);
					$tpl->set('bootstrapHelper',$bootstrapHelper);
					$body = $tpl->fetch("agentslist.php");
					echo $body;
					?>
					<input type="hidden" name="option" value="com_osproperty" />
					<input type="hidden" name="task" value="agent_layout" />
					<input type="hidden" name="alphabet" id="alphabet" value="" />
					<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
					<input type="hidden" name="page" value="alb" />
					<input type="hidden" name="usertype" id="usertype" value="<?php echo $lists['agenttype']?>" />
				</form>
			</div>
		</div>
		<script type="text/javascript">
		function change_country_company(country_id,state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoStateCityLocator(country_id,state_id,city_id,'country','state_id',live_site);
		}
		function change_state(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCity(state_id,city_id,'state_id',live_site);
		}
		</script>
		<?php
	}

    /**
     * This function is used to show Most Viewed Properties
     * @param $option
     * @param $rows
     */
	static function showMostViewProperties($option,$rows)
    {
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
        if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/mostviewed.php'))
        {
            $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
        }
        else
        {
            $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
        }
        $tpl->set('option',$option);
        $tpl->set('rows',$rows);
        $tpl->set('configClass',$configClass);
        $tpl->set('bootstrapHelper',$bootstrapHelper);
        $body = $tpl->fetch("mostviewed.php");
        echo $body;
	}

    /**
     * This function is used to show Most Rated Properties
     * @param $option
     * @param $rows
     */
	static function showMostRatedProperties($option,$rows)
    {
        global $bootstrapHelper, $mainframe,$jinput,$configClass;
        if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/mostrated.php'))
        {
            $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
        }
        else
        {
            $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
        }
        $tpl->set('option',$option);
        $tpl->set('rows',$rows);
        $tpl->set('configClass',$configClass);
        $tpl->set('bootstrapHelper',$bootstrapHelper);
        $body = $tpl->fetch("mostrated.php");
        echo $body;
	}
	/**
	 * Edit profile
	 *
	 * @param unknown_type $option
	 * @param unknown_type $agent
	 */
	static function editProfile($option,$agent,$lists,$rows,$pageNav)
	{
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$ismobile;
		//HTMLHelper::_('behavior.modal','a.osmodal');
		OSPHelper::loadTooltip();
		jimport('joomla.filesystem.folder');
		//jimport('joomla.html.pane');
		//$panetab =& JPane::getInstance('Tabs');
		$db = Factory::getDbo();
		?>
		<script type="text/javascript">
		function submitAgentForm(form_name)
        {
		    var form = document.getElementById(form_name);
			var temp1,temp2;
			var cansubmit = 1;
			var require_field = document.getElementById('require_field_' + form_name);
			require_field = require_field.value;
			var require_label = document.getElementById('require_label_' + form_name);
			require_label = require_label.value;
			var require_fieldArr = require_field.split(",");
			var require_labelArr = require_label.split(",");
			for(i=0;i<require_fieldArr.length;i++)
			{
				temp1 = require_fieldArr[i];
				temp2 = form[temp1]; // hungvd repair
				//temp2 = document.getElementById(temp1);
				if(temp2 != null)
				{
					if((temp2.value == "") && (cansubmit == 1))
					{
						//alert(require_labelArr[i] + " <?php echo Text::_('OS_IS_MANDATORY_FIELD')?>");
						alert(require_labelArr[i] + " " + Joomla.JText._('<?php echo Text::plural("OS_IS_MANDATORY_FIELD", 1, array("script"=>true));?>'));
						temp2.focus();
						cansubmit = 0;
					}
				}
			}
			
			// hungvd modify
			if ((form_name == 'profileForm') && (cansubmit = 1)){
				password 	= form['password'];
				password2 	= form['password2'];
				if (password.value != '' && password.value != password2.value){
//					alert("<?php echo Text::_('OS_NEW_PASSWORD_IS_NOT_CORRECT')?>");
					alert(Joomla.JText._('<?php echo Text::plural("OS_NEW_PASSWORD_IS_NOT_CORRECT", 1, array("script"=>true));?>'));
					cansubmit = 0;
				}
			}
			
			
			if(cansubmit == 1){
				form.submit();
			}
		}
		function loadState(country_id,state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoStateCity(country_id,state_id,city_id,'country','state',live_site);
		}
		function loadCity(state_id,city_id){
			var live_site = '<?php echo Uri::root()?>';
			loadLocationInfoCityAddProperty(state_id,city_id,'state',live_site);
		}
		function savePassword(){
			var form = document.passwordForm;
			new_password = form.new_password;
			new_password1 = form.new_password1;
			if((new_password1.value == "")&&(new_password.value!="")){
				alert("<?php echo Text::_('Please re-enter new password')?>");
				new_password1.focus();
			}else if(new_password1.value != new_password.value){
				//alert("<?php echo Text::_('OS_NEW_PASSWORD_IS_NOT_CORRECT')?>");
				alert(Joomla.JText._('<?php echo Text::plural("OS_NEW_PASSWORD_IS_NOT_CORRECT", 1, array("script"=>true));?>'));
			}else{
				form.submit();
			}
			
		}
		function submitForm(t){
			var total = 0;
			var temp;
			total = <?php echo count($rows)?>;
            if(t == "new"){
                document.ftForm.task.value = "property_new";
                document.ftForm.submit();
                return false;
            }
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
					if(t == "deleteproperties"){
						var answer = confirm("<?php echo Text::_('OS_DO_YOU_WANT_TO_REMOVE_ITEMS')?>");
						if(answer == 1){
							document.ftForm.task.value = "agent_deleteproperties";
							document.ftForm.submit();
						}
					}else{
						if(t != "property_upgrade"){
							document.ftForm.task.value = "agent_" + t;
							document.ftForm.submit();
						}else{
							document.ftForm.task.value = t;
							document.ftForm.submit();
						}
					}
				}
			}
		}
		
		function openDiv(id){
			var atag = document.getElementById('a' + id);
			var divtag = document.getElementById('div' + id);
			if(atag.innerHTML == "[+]"){
				atag.innerHTML = "[-]";
				divtag.style.display = "block";
			}else{
				atag.innerHTML = "[+]"
				divtag.style.display = "none";
			}
		}

		function unfeaturedproperty(pro_id){
			var answer = confirm("<?php echo Text::_('OS_ARE_YOU_SURE_YOU_WANT_TO_UNFEATURED_PROPERTY');?>");
			if(answer == 1){
				location.href = "<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_unfeatured&id=" + pro_id + "&Itemid=<?php echo $jinput->getInt('Itemid',0);?>";
			}
		}
		</script>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="editprofile">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<h1 class="componentheading">
							<?php echo Text::_('OS_MY_PROFILE')?>
						</h1>
					</div>
				</div>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> <?php echo $bootstrapHelper->getClassMapping('hidden-phone'); ?>">
					<?php
					if((count((array)$lists['mostview']) > 0) || (count((array)$lists['mostrate']) > 0))
					{
						if($configClass['agent_mostrated'] == 1 && $configClass['agent_mostviewed'] == 1 && count($lists['mostview']) > 0 && count($lists['mostrate']) > 0)
						{
							$class = $bootstrapHelper->getClassMapping('span6');
							?>
							<!-- show the most view -->
							<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
								<?php
								if(count($lists['mostview']) > 0)
								{
									?>
									<div class="<?php echo $class; ?>">
										<?php
										HTML_OspropertyAgent::showMostViewProperties($option, $lists['mostview']);
										?>
									</div>
									<?php
								}
								if(count($lists['mostrate']) > 0)
								{
									?>
									<div class="<?php echo $class; ?>">
										<?php
										HTML_OspropertyAgent::showMostRatedProperties($option, $lists['mostrate']);
										?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
						else
						{
                            $class = $bootstrapHelper->getClassMapping('span12');
						}
					}
					?>
					</div>
                </div>
                <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> margintop10">
						<div class="tab-content">
							<?php
							echo HTMLHelper::_('bootstrap.startTabSet', 'agentprofile', array('active' => 'panel3'));
							echo HTMLHelper::_('bootstrap.addTab', 'agentprofile', 'panel3', Text::_('OS_YOUR_PROPERTIES', true));
							?>
							<div class="tab-pane active" id="panel3">
								<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&view=aeditdetails&Itemid='.$jinput->getInt('Itemid',0));?>" name="ftForm" id="ftForm" class="form-horizontal">
								<?php
									if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/manageproperties.php')){
										$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
									}else{
										$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
									}
									$tpl->set('option',$option);
									$tpl->set('rows',$rows);
									$tpl->set('lists',$lists);
									$tpl->set('pageNav',$pageNav);	
									$tpl->set('itemid',$jinput->getInt('Itemid',0));
									$tpl->set('configClass',$configClass);
									$tpl->set('jinput',$jinput);
									$tpl->set('supervisor',0);
									$tpl->set('bootstrapHelper',$bootstrapHelper);
									$body = $tpl->fetch("manageproperties.php");
									echo $body;
								?>
								<input type="hidden" name="option" value="com_osproperty" />
								<input type="hidden" name="task" value="agent_default" />
								<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
								<input type="hidden" name="view" value="aeditdetails" />
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
								</script>
							</div>
							<?php
							echo HTMLHelper::_('bootstrap.endTab');
                            if($configClass['integrate_membership'] == 1 && Folder::exists(JPATH_ROOT.'/components/com_osmembership'))
                            {
                                $planArr    = array();
                                OSMembershipHelper::loadLanguage();
                                $plans      = OspropertyMembership::getAllPlans();
                                $usertype   = $agent->agent_type;
                                foreach($plans as $plan)
                                {
                                    $params         = new Registry() ;
                                    $params->loadString($plan->params);
                                    $plan_type      = $params->get('isospplugin',0);
                                    if($plan_type == 1)
                                    {
                                        $plan_usertype = $params->get('usertype','');
                                        $pu = 1;
                                        if(trim($usertype) == '0' || trim($usertype) == '2')
                                        {
                                            if($usertype != $plan_usertype){
                                                $pu = 0;
                                            }
                                        }
                                        if($pu == 1)
                                        {
                                            $planArr[] = $plan->id;
                                        }
                                    }
                                }
                                if(count($planArr) > 0)
                                {
                                    echo HTMLHelper::_('bootstrap.addTab', 'agentprofile', 'panel4', Text::_('OS_YOUR_ORDERS_HISTORY', true));
                                    ?>
                                    <div class="tab-pane" id="panel4">
                                        <?php
                                        //OspropertyPayment::ordersHistory($lists['orders']);
                                        jimport('joomla.filesystem.file');
                                        $request = array('option' => 'com_osmembership', 'view' => 'subscriptions', 'layout' => 'default', 'filter_plan_ids' => implode(",", $planArr), 'limit' => 0, 'hmvc_call' => 1, 'Itemid' => OSMembershipHelper::getItemid());
                                        $input = new MPFInput($request);
                                        $config = array(
                                            'default_controller_class' => 'OSMembershipController',
                                            'default_view' => 'plans',
                                            'class_prefix' => 'OSMembership',
                                            'language_prefix' => 'OSM',
                                            'remember_states' => false,
                                            'ignore_request' => false,
                                        );
                                        MPFController::getInstance('com_osmembership', $input, $config)
                                            ->execute();
                                        ?>
                                    </div>
                                    <?php
                                    echo HTMLHelper::_('bootstrap.endTab');
                                }
                            }
							elseif($configClass['active_payment'] == 1)
                            {
								echo HTMLHelper::_('bootstrap.addTab', 'agentprofile', 'panel4', Text::_('OS_YOUR_ORDERS_HISTORY', true));
								?>
									<div class="tab-pane" id="panel4">
										<?php
                                        OspropertyPayment::ordersHistory($lists['orders']);
										?>
									</div>
								<?php
								echo HTMLHelper::_('bootstrap.endTab');
							}
							?>
							<?php
								if($configClass['integrate_membership'] == 1 && Folder::exists(JPATH_ROOT.'/components/com_osmembership'))
								{
									echo HTMLHelper::_('bootstrap.addTab', 'agentprofile', 'panel3a', Text::_('OS_YOUR_CREDITS', true));
									?>
									<div class="tab-pane" id="panel3a">
                                        <?php
                                        $db = Factory::getDbo();
                                        if(file_exists(JPATH_ROOT.DS."components/com_osmembership/helper/helper.php"))
										{
                                            include_once(JPATH_ROOT.DS."components/com_osmembership/helper/helper.php");
                                        }
                                        if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/usercredits.php'))
										{
                                            $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
                                        }
										else
										{
                                            $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
                                        }
                                        $userCredits = OspropertyMembership::getUserCredit();
                                        $tpl->set('agentAcc',$userCredits);
										$tpl->set('usertype',$usertype);
                                        $body = $tpl->fetch("usercredits.php");
                                        echo $body;
                                        ?>
									</div>
									<?php
									echo HTMLHelper::_('bootstrap.endTab');
								}
								/*
								echo HTMLHelper::_('bootstrap.addTab', 'agentprofile', 'panel1', Text::_('OS_PROFILE_INFO', true));
								?>
								<div class="tab-pane" id="panel1">
									<?php
									$user = Factory::getUser();
									?>
									<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=agent_saveprofile&Itemid='.$jinput->getInt('Itemid',0))?>" name="profileForm" id="profileForm" class="form-horizontal">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
											<div class="block_caption">
												<strong><?php echo Text::_('OS_PROFILE_INFO')?></strong>
											</div>
											<div class="clearfix"></div>
											<div class="blue_middle"><?php echo Text::_('OS_FIELDS_MARKED')?> <span class="red">*</span> <?php echo Text::_('OS_ARE_REQUIRED')?></div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
												<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_NAME')?> *</label>
												<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
													<input type="text" name="name" id="name" size="30" value="<?php echo $user->name?>" class="input-large" placeholder="<?php echo Text::_('OS_NAME')?>"/>
												</div>
											</div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
												<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_LOGIN_NAME')?> *</label>
												<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
													<input type="text" name="username" id="username" size="30" value="<?php echo $user->username?>" class="input-large" placeholder="<?php echo Text::_('OS_LOGIN_NAME')?>"/>
												</div>
											</div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
												<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PASSWORD')?>  *</label>
												<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
													<input type="password" name="password" id="password" size="30" class="input-large" autocomplete="off" />
												</div>
											</div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
												<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_CONFIRM_PASSWORD')?> *</label>
												<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
													<input type="password" name="password2" id="password2" size="30" class="input-large" autocomplete="off" />
												</div>
											</div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
												<label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_EMAIL')?> *</label>
												<div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
													<input type="text" name="email" id="email" size="30" value="<?php echo $user->email?>" class="input-large" placeholder="<?php echo Text::_('OS_EMAIL')?>"/>
												</div>
											</div>
											<div class="clearfix"></div>
											<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
												<input type="button" class="btn btn-info" value="<?php echo Text::_("OS_SAVE")?>" onclick="javascript:submitAgentForm('profileForm')" />
												<input type="reset" class="btn btn-danger" value="<?php echo Text::_("OS_CLEAR")?>" />
											</div>
										</div>
									</div>
									<input type="hidden" name="option" value="com_osproperty" />
									<input type="hidden" name="task" value="agent_saveprofile" />
									<input type="hidden" name="require_field_profileForm" id="require_field_profileForm" value="name,username,email" />
									<input type="hidden" name="require_label_profileForm" id="require_label_profileForm" value="<?php echo Text::_("Name")?>,<?php echo Text::_('OS_LOGIN_NAME')?>,<?php echo Text::_("OS_EMAIL")?>" />
									<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
									<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
									</form>
								</div>
								<?php
								echo HTMLHelper::_('bootstrap.endTab');
								*/
								echo HTMLHelper::_('bootstrap.addTab', 'agentprofile', 'panel2', Text::_('OS_ACCOUNT_INFO', true));
								?>
								<div class="tab-pane" id="panel2">
									<form method="POST" action="<?php echo Uri::root();?>index.php?option=com_osproperty" name="accountForm" id="accountForm" enctype="multipart/form-data" class="form-horizontal">
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
										<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
											<div class="block_caption">
												<strong><?php echo Text::_('OS_ACCOUNT_INFO')?></strong>
											</div>
                                        </div>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> agentprofilebox">
                                            <div class="blue_middle"><?php echo Text::_('OS_FIELDS_MARKED')?> <span class="red">*</span> <?php echo Text::_('OS_ARE_REQUIRED')?></div>
                                            <?php
                                            if($configClass['show_agent_image'] == 1){
                                            ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PHOTO')?></label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php
                                                    if($agent->photo != "")
                                                    {
                                                        ?>
                                                        <img src="<?php echo Uri::root()?>images/osproperty/agent/<?php echo $agent->photo?>" width="100" />
                                                        <div class="clearfix"></div>
                                                        <input type="checkbox" name="remove_photo" id="remove_photo" onclick="javascript:changeValue('remove_photo')" value="0"/> <?php echo Text::_('OS_REMOVE_PHOTO');?>
                                                        <div class="clearfix"></div>
                                                    <?php
                                                    }
                                                    ?>
                                                    <span id="photodiv">
                                                    <input type="file" name="photo" id="photo" class="input-medium form-control" onchange="javascript:checkUploadPhotoFiles('photo')" />
                                                    <div class="clearfix"></div>
                                                    <?php echo Text::_('OS_ONLY_SUPPORT_JPG_IMAGES');?>
                                                    </span>
                                                </div>
                                            </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_AGENT_NAME')?> *</label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <input type="text" name="name" id="name" size="30" value="<?php echo htmlspecialchars($agent->name);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_AGENT_NAME')?>"/>
                                                    <input type="hidden" name="alias" value="<?php echo htmlspecialchars($agent->alias);?>" />
                                                </div>
                                            </div>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_AGENT_EMAIL')?> *</label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <input type="text" name="email" id="email" size="30" value="<?php echo $agent->email?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_AGENT_EMAIL')?>"/>
                                                </div>
                                            </div>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_COMPANY');?></label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php echo $lists['company'];?>
                                                </div>
                                            </div>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_ADDRESS')?></label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <input type="text" name="address" id="address" size="30" value="<?php echo htmlspecialchars($agent->address);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_ADDRESS')?>"/>
                                                </div>
                                            </div>
                                            <?php
                                            if(HelperOspropertyCommon::checkCountry())
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_COUNTRY')?> *</label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php echo $lists['country']?>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            else
                                            {
                                                echo $lists['country'];
                                            }
                                            if(OSPHelper::userOneState())
                                            {
                                                echo $lists['state'];
                                            }
                                            else
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_STATE')?> *</label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="country_state">
                                                        <?php
                                                        echo $lists['state'];
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_CITY')?> *</label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>" id="city_div">
                                                    <?php
                                                    echo $lists['city'];
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span6'); ?> agentprofilebox">
                                            <?php
                                            if($configClass['show_agent_phone'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_PHONE')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="phone" id="phone" size="30" value="<?php echo htmlspecialchars($agent->phone);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_PHONE')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_mobile'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_MOBILE')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="mobile" id="mobile" size="30" value="<?php echo htmlspecialchars($agent->mobile);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_MOBILE')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_fax'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_FAX')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="fax" id="fax" size="30" value="<?php echo htmlspecialchars($agent->fax);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_FAX')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_skype'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('Skype')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="skype" id="skype" size="30" value="<?php echo htmlspecialchars($agent->skype);?>" class="input-large form-control" placeholder="<?php echo Text::_('Skype')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_linkin'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_LINKEDIN')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="yahoo" id="yahoo" size="30" value="<?php echo htmlspecialchars($agent->yahoo);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_LINKEDIN')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_gplus'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('Google Plus')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="gtalk" id="gtalk" size="30" value="<?php echo htmlspecialchars($agent->gtalk);?>" class="input-large form-control" placeholder="<?php echo Text::_('Google Plus')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_msn'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('Line messasges')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="msn" id="msn" size="30" value="<?php echo htmlspecialchars($agent->msn);?>" class="input-large form-control" placeholder="<?php echo Text::_('Line messasges')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_facebook'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('Facebook')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="facebook" id="facebook" size="30" value="<?php echo htmlspecialchars($agent->facebook);?>" class="input-large form-control" placeholder="<?php echo Text::_('Facebook')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_agent_twitter'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('Twitter')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="aim" id="aim" size="30" value="<?php echo htmlspecialchars($agent->aim);?>" class="input-large form-control" placeholder="<?php echo Text::_('Twitter')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            if($configClass['show_license'] == 1)
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_LICENSE')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <input type="text" name="license" id="license" size="30" value="<?php echo htmlspecialchars($agent->license);?>" class="input-large form-control" placeholder="<?php echo Text::_('OS_LICENSE')?>"/>
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
                                        <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> agentprofilebox">
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>"><?php echo Text::_('OS_BIO')?></label>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                    <?php
                                                    $editor = Editor::getInstance(Factory::getConfig()->get('editor'));
                                                    echo $editor->display( 'bio',  htmlspecialchars($agent->bio, ENT_QUOTES), '250', '200', '60', '20',false ) ;
                                                    ?>
                                                </div>
                                            </div>
                                            <?php
                                            if($configClass['use_privacy_policy'] && $configClass['allow_user_profile_optin'])
                                            {
                                            ?>
                                                <div class="<?php echo $bootstrapHelper->getClassMapping('control-group'); ?>">
                                                    <label class="<?php echo $bootstrapHelper->getClassMapping('control-label'); ?>" ><?php echo Text::_('OS_PUBLIC_MY_PROFILE')?></label>
                                                    <div class="<?php echo $bootstrapHelper->getClassMapping('controls'); ?>">
                                                        <?php echo $lists['optin']; ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="clearfix"></div>
                                            <div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?> noleftmargin">
                                                <input type="button" class="btn btn-info" value="<?php echo Text::_("OS_SAVE")?>" onclick="javascript:submitAgentForm('accountForm')" />
                                                <input type="reset" class="btn btn-danger" value="<?php echo Text::_("OS_RESET")?>" />
                                            </div>
                                        </div>
                                    </div>
									<input type="hidden" name="option" value="com_osproperty" />
									<input type="hidden" name="task" value="agent_saveaccount" />
									<?php
									$require_fields = "name,email,country,";
									$require_labels = Text::_("OS_NAME").",".Text::_("OS_EMAIL").",".Text::_("OS_COUNTRY").",";
									if($configClass['require_state']==1){
										$require_fields .= "state,";
										$require_labels .= Text::_("OS_STATE").",";
									}
									if($configClass['require_city']==1){
										$require_fields .= "city,";
										$require_labels .= Text::_("OS_CITY").",";
									}
									?>
									<input type="hidden" name="require_field_accountForm" id="require_field_accountForm" value="<?php echo $require_fields;?>" />
									<input type="hidden" name="require_label_accountForm" id="require_label_accountForm" value="<?php echo $require_labels;?>" />
									<input type="hidden" name="MAX_FILE_SIZE" value="9000000000" />
									<input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
								</form>
							</div>
							<?php
							echo HTMLHelper::_('bootstrap.endTab');
							echo HTMLHelper::_('bootstrap.endTabSet');
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Agent infor
	 *
	 * @param unknown_type $option
	 * @param unknown_type $agent
	 */
	static function agentInfoForm($option,$agent,$lists)
    {
		global $bootstrapHelper, $mainframe,$jinput,$configClass,$languages,$lang_suffix;
		$document = Factory::getDocument();
		$document->addStyleSheet(Uri::root()."components/com_osproperty/templates/theme2/style/font.css");
		?>
		<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>" id="agentdetails">
			<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
				<?php 
				jimport('joomla.filesystem.file');
				if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/agentdetails.php'))
				{
					$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
				}
				else
				{
					$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
				}
				$tpl->set('mainframe',$mainframe);
				$tpl->set('lists',$lists);
				$tpl->set('option',$option);
				$tpl->set('configClass',$configClass);
				$tpl->set('bootstrapHelper',$bootstrapHelper);
				$tpl->set('agent',$agent);
				$tpl->set('jinput', $jinput);
				$body = $tpl->fetch("agentdetails.php");
				echo $body;
				?>
				<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
					<div class="tab-content <?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
						<?php
						if ($configClass['show_agent_properties'] == 1)
						{
							?>
                            <form method="POST" action="<?php echo Route::_('index.php?Itemid='.$jinput->getInt('Itemid',0))?>" name="ftForm" id="ftForm">
                            <div class="block_caption">
                                <strong><?php echo Text::_('OS_AGENT_PROPERTIES')?></strong>
                            </div>
                            <?php
                            $filterParams       = array();
                            //show cat
                            $filterParams[0]    = 1;
                            //agent
                            $filterParams[1]    = 0;
                            //keyword
                            $filterParams[2]    = 1;
                            //bed
                            $filterParams[3]    = 1;
                            //bath
                            $filterParams[4]    = 1;
                            //rooms
                            $filterParams[5]    = 1;
                            //price
                            $filterParams[6]    = 1;

                            $category_id 	    = $jinput->get('category_id',array(),'ARRAY');
                            $property_type	    = $jinput->getInt('property_type',0);
                            $keyword		    = OSPHelper::getStringRequest('keyword','','');
                            $nbed			    = $jinput->getInt('nbed','');
                            $nbath			    = $jinput->getInt('nbath','');
                            $isfeatured		    = $jinput->getInt('isfeatured','');
                            $nrooms			    = $jinput->getInt('nrooms','');
                            $orderby		    = $jinput->getString('orderby','a.id');
                            $ordertype		    = $jinput->getString('ordertype','desc');
                            $limitstart		    = OSPHelper::getLimitStart();
                            $limit			    = $jinput->getInt('limit',$configClass['general_number_properties_per_page']);
                            $favorites		    = $jinput->getInt('favorites',0);
                            $price			    = $jinput->getInt('price',0);
                            $city_id		    = $jinput->getInt('city',0);
                            $state_id		    = $jinput->getInt('state_id',0);
                            $country_id		    = $jinput->getInt('country_id',HelperOspropertyCommon::getDefaultCountry());
                            OspropertyListing::listProperties($option,'',null,$agent->id,$property_type,$keyword,$nbed,$nbath,0,0,$nrooms,$orderby,$ordertype,$limitstart,$limit,'',$price,$filterParams,$city_id,$state_id,$country_id,0,1,-1);
                            ?>
                            <input type="hidden" name="option" value="com_osproperty" />
                            <input type="hidden" name="task" value="agent_info" />
                            <input type="hidden" name="Itemid" value="<?php echo $jinput->getInt('Itemid',0)?>" />
                            <input type="hidden" name="id" value="<?php echo $agent->id?>" />
                            </form>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	/**
	 * Agent register form
	 *
	 * @param unknown_type $option
	 * @param unknown_type $user
	 */
	static function agentRegisterForm($user,$lists,$companies)
    {
		global $bootstrapHelper, $mainframe,$jinput,$configClass;
		$itemid = $jinput->getInt('Itemid',0);
		$user = Factory::getUser();
		OSPHelper::generateHeading(2,Text::_('OS_AGENT_REGISTER'));
		jimport('joomla.filesystem.file');
		if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/agentregistration.php')){
			$tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
		}else{
			$tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
		}
		$tpl->set('itemid',$itemid);
		$tpl->set('user',$user);
		$tpl->set('companies',$companies);
		$tpl->set('lists',$lists);
		$tpl->set('configClass',$configClass);
		$tpl->set('bootstrapHelper',$bootstrapHelper);
		$body = $tpl->fetch("agentregistration.php");	
		echo $body;	
	}
}

?>
