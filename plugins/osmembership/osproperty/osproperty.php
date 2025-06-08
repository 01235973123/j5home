<?php

/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	OS Membership
 * @author  Tuan Pham Ngoc
 * @copyright	Copyright (C) 2024 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */

defined( '_JEXEC' ) or die ;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class plgOSMembershipOSProperty extends CMSPlugin
{	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		Factory::getLanguage()->load('plg_osmembership_osproperty', JPATH_ADMINISTRATOR);			
		Table::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_osmembership/table');
	}
	/**
	 * Render settings from
	 * @param PlanOSMembership $row
	 */
	function onEditSubscriptionPlan($row) {	
		ob_start();
			$this->_drawSettingForm($row);		
			$form = ob_get_contents();	
		ob_end_clean();		
		return array('title' => Text::_('PLG_OSMEMBERSHIP_OSP_SETTINGS'),							
					'form' => $form
		) ;				
	}

	/**
	 * Store setting into database
	 * @param PlanOsMembership $row
	 * @param Boolean $isNew true if create new plan, false if edit
	 */
	function onAfterSaveSubscriptionPlan($context, $row, $data, $isNew) {
		// $row of table osmembership_groups
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new Registry($row->params);
		} else {
			$params = new JParameter($row->params);
		}
		$params->set('proType'	, $data['proType']);
		$params->set('nproperties',$data['nproperties']);		
		$params->set('usertype',$data['usertype']);
		$params->set('isospplugin',$data['isospplugin']);
		$row->params = $params->toString();
		
		$row->store();
	}
	/**
	 * Run when a membership activated
	 * @param PlanOsMembership $row
	 */		
	function onMembershipActive($row) 
	{	
		$db = Factory::getDbo();
		include_once(JPATH_ROOT . "/components/com_osproperty/helpers/helper.php");
		define('DS', DIRECTORY_SEPARATOR);
		$user = & Factory::getUser($row->user_id);
		$plan =  &Table::getInstance('Osmembership','Plan');
		$plan->load($row->plan_id);
		$params = new Registry($plan->params);
        $isospplugin = $params->get('isospplugin',0);
		$proType = $params->get('proType',0);
		$usertype = $params->get('usertype',0);
		$nproperties = $params->get('nproperties',5);
		if(($nproperties > 0) and ($isospplugin == 1)){
			if ($row->user_id > 0) {
                if($usertype != 2) { //agent or owner
                    $db->setQuery("Select count(id) from #__osrs_agents where user_id = '$row->user_id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $db->setQuery("Select id from #__osrs_agents where user_id = '$row->user_id'");
                        $agent_id = $db->loadResult();
                    } else {
                        //create new agent account
                        include_once(JPATH_ROOT . "/administrator/components/com_osproperty/tables/agent.php");
                        $agent = &Table::getInstance('Agent', 'OspropertyTable');
                        $agent->id = 0;
                        $agent->agent_type = $usertype;
                        $agent->user_id = $row->user_id;
                        $usergent = Factory::getUser($row->user_id);
                        $agent->name = $usergent->name;
                        $agent->email = $usergent->email;
                        $db->setQuery("Select ordering from #__osrs_agents order by ordering desc");
                        $ordering = $db->loadResult();
                        $ordering++;
                        $agent->ordering = $ordering;
                        $agent->published = 1;
                        $agent->store();
                        $agent_id = $db->insertid();
                        //update alias
                        $alias = OSPHelper::generateAlias('agent', $agent_id, '');
                        $db->setQuery("Update #__osrs_agents set alias = '$alias' where id = '$agent_id'");
                        $db->execute();
                    }
                    //Update into agent account
                    $db->setQuery("SELECT  COUNT(id) FROM #__osrs_agent_account WHERE agent_id = '$agent_id' AND sub_id = '$row->id'");
                    $count = $db->loadResult();
                    if($count == 0){
                        $db->setQuery("INSERT INTO #__osrs_agent_account (id,sub_id,agent_id,`type`,nproperties,`status`) VALUES (NULL,'$row->id','$agent_id','$proType','$nproperties','1')");
                        $db->execute();
                    }else{
                        $db->setQuery("UPDATE #__osrs_agent_account SET `status` = '1' WHERE agent_id = '$agent_id' AND sub_id = '$row->id'");
                        $db->execute();
                    }
                }else{
                    $db->setQuery("Select count(id) from #__osrs_companies where user_id = '$row->user_id'");
                    $count = $db->loadResult();
                    if ($count > 0) {
                        $db->setQuery("Select id from #__osrs_companies where user_id = '$row->user_id'");
                        $company_id = $db->loadResult();
                    } else {
                        //create new agent account
                        include_once(JPATH_ROOT . "/administrator/components/com_osproperty/tables/companies.php");
                        $company = &Table::getInstance('Companies', 'OspropertyTable');
                        $company->id = 0;
                        $usercompany = Factory::getUser($row->user_id);
                        $company->company_name = $usercompany->name;
                        $company->user_id = $row->user_id;
                        $company->email = $usercompany->email;
                        $company->published = 1;
                        $company->store();
                        $company_id = $db->insertid();
						
                        //update company alias
                        $alias = OSPHelper::generateAlias('company',$company_id,'');
                        $db->setQuery("Update #__osrs_companies set company_alias = '$alias' where id = '$company_id'");
                        $db->execute();
                    }

                    //Update into agent account
                    $db->setQuery("SELECT  COUNT(id) FROM #__osrs_agent_account WHERE company_id = '$company_id' AND sub_id = '$row->id'");
                    $count = $db->loadResult();
                    if($count == 0){
                        $db->setQuery("INSERT INTO #__osrs_agent_account (id,sub_id,company_id,`type`,nproperties,`status`) VALUES (NULL,'$row->id','$company_id','$proType','$nproperties','1')");
                        $db->execute();
                    }else{
                        $db->setQuery("UPDATE #__osrs_agent_account SET `status` = '1' WHERE company_id = '$company_id' AND sub_id = '$row->id'");
                        $db->execute();
                    }
                }
			}
		}						
	}
	/**
	 * Run when a membership expiried die
	 * @param PlanOsMembership $row
	 */		
	function onMembershipExpire($row) {
		$db = Factory::getDbo();
		define('DS', DIRECTORY_SEPARATOR);

        $plan =  &Table::getInstance('Osmembership','Plan');
        $plan->load($row->plan_id);
        $params = new Registry($plan->params);

        $usertype = $params->get('usertype',0);

		if ($row->user_id) {
            $user = & Factory::getUser($row->user_id);
            $currentGroups  = $user->get('groups') ;
            /*
            $groups = explode(',', $params->get('joomla_expried_group_ids'));
            $currentGroups = array_unique(array_diff($currentGroups, $groups)) ;
            $user->set('groups', $currentGroups);
            $user->save(true);
            */
            if($usertype == 2){
                $db->setQuery("Select id from #__osrs_companies where user_id = '$row->user_id'");
                $company_id = $db->loadResult();

                //Update into agent account
                $db->setQuery("SELECT  COUNT(id) FROM #__osrs_agent_account WHERE company_id = '$company_id' AND sub_id = '$row->id'");
                $count = $db->loadResult();
                if ($count > 0) {
                    $db->setQuery("UPDATE #__osrs_agent_account SET `status` = '0' WHERE company_id = '$company_id' AND sub_id = '$row->id'");
                    $db->execute();
                }
            }else {

                $db->setQuery("Select id from #__osrs_agents where user_id = '$row->user_id'");
                $agent_id = $db->loadResult();

                //Update into agent account
                $db->setQuery("SELECT  COUNT(id) FROM #__osrs_agent_account WHERE agent_id = '$agent_id' AND sub_id = '$row->id'");
                $count = $db->loadResult();
                if ($count > 0) {
                    $db->setQuery("UPDATE #__osrs_agent_account SET `status` = '0' WHERE agent_id = '$agent_id' AND sub_id = '$row->id'");
                    $db->execute();
                }
            }
		}		
	}
	/**
	 * Display form allows users to change setting for this subscription plan 
	 * @param object $row
	 * 
	 */	
	function _drawSettingForm($row) {
		// $row of table osmembership_plans
		if (version_compare(JVERSION, '1.6.0', 'ge')) {
			$params = new Registry($row->params);
		} else {
			$params = new JParameter($row->params);
		}
		$proType = $params->get('proType',0);
		$nproperties = $params->get('nproperties',5);
		$isospplugin = $params->get('isospplugin',1);
		$usertype	 = $params->get('usertype',0);
		?>	
		<table class="admintable adminform" style="width: 90%;">
			<tr>
				<td width="220" class="key">
					<?php echo  Text::_('PLG_OSMEMBERSHIP_IS_OSPROPERTY_SUBSCRIPTION_PLAN'); ?>
				</td>
				<td>
					<?php
					$proTypeArr   = array();
					$proTypeArr[] = JHTML::_('select.option','1',Text::_('PLG_OSMEMBERSHIP_YES'));
					$proTypeArr[] = JHTML::_('select.option','0',Text::_('PLG_OSMEMBERSHIP_NO'));
					echo JHTML::_('select.genericlist',$proTypeArr,'isospplugin','class="input-small"','value','text',$isospplugin);
					?>
				</td>
				<td>
					<?php echo Text::_('PLG_OSMEMBERSHIP_IS_OSPROPERTY_SUBSCRIPTION_PLAN_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo  Text::_('PLG_OSMEMBERSHIP_SELECT_PROPERTY_TYPE'); ?>
				</td>
				<td>
					<?php
					$proTypeArr   = array();
					$proTypeArr[] = JHTML::_('select.option','0',Text::_('PLG_OSMEMBERSHIP_NORMAL'));
					$proTypeArr[] = JHTML::_('select.option','1',Text::_('PLG_OSMEMBERSHIP_FEATURE'));
					echo JHTML::_('select.genericlist',$proTypeArr,'proType','class="input-medium"','value','text',$proType);
					?>
				</td>
				<td>
					<?php echo Text::_('PLG_OSMEMBERSHIP_SELECT_PROPERTY_TYPE_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo  Text::_('PLG_OSMEMBERSHIP_NUMBER_PROPERTIES'); ?>
				</td>
				<td>
					<input type="text" size="5" name="nproperties" id="nproperties" value="<?php echo $nproperties?>" class="input-mini">
				</td>
				<td>
					<?php echo Text::_('PLG_OSMEMBERSHIP_NUMBER_PROPERTIES_EXPLAIN'); ?>
				</td>
			</tr>
			<tr>
				<td width="220" class="key">
					<?php echo  Text::_('PLG_OSMEMBERSHIP_USERTYPE'); ?>
				</td>
				<td>
					<?php
					$proTypeArr   = array();
					$proTypeArr[] = JHTML::_('select.option','0',Text::_('PLG_OSMEMBERSHIP_AGENT'));
					$proTypeArr[] = JHTML::_('select.option','1',Text::_('PLG_OSMEMBERSHIP_OWNER'));
                    $proTypeArr[] = JHTML::_('select.option','2',Text::_('PLG_OSMEMBERSHIP_COMPANY'));
					echo JHTML::_('select.genericlist',$proTypeArr,'usertype','class="input-medium"','value','text',$usertype);
					?>
				</td>
				<td>
					<?php echo Text::_('PLG_OSMEMBERSHIP_USERTYPE_EXPLAIN'); ?>
				</td>
			</tr>
		</table>	
	<?php							
	}
}	
