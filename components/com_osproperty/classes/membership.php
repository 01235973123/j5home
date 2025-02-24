<?php
/*------------------------------------------------------------------------
# membership.php - Ossolution Property
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

class OspropertyMembership{
	/**
	 * Payment process
	 *
	 * @param unknown_type $option
	 * @param unknown_type $task
	 */
	static function display($option,$task){
		global $jinput, $mainframe;
		$id     = $jinput->getInt('id',0);
        require_once JPATH_ADMINISTRATOR . '/components/com_osmembership/loader.php';
		switch ($task){
			case "membership_activelisting":
				self::activelisting($id);
				HelperOspropertyCommon::loadFooter($option);
			break;
            case "membership_listsubscriptions":
                self::listSubscriptions();
                HelperOspropertyCommon::loadFooter($option);
                break;
		}
	}

	static function listSubscriptions()
	{
        global $configClass,$mainframe,$jinput,$bootstrapHelper;
        if($configClass['integrate_membership'] == 1) 
		{
            OSPHelper::generateHeading(1,Text::_('OS_PURCHASE_SUBSCRIPTION'));
            $planArr    = array();
            OSMembershipHelper::loadLanguage();
            $usertype   = $jinput->getString('usertype', '');
            $proType    = $jinput->getString('proType', '');

            $plans = self::getAllPlans();
            foreach($plans as $plan)
			{
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $plan_type      = $params->get('isospplugin',0);
                if($plan_type == 1)
				{
                    $plan_usertype = $params->get('usertype','');
                    $plan_proType  = $params->get('proType','');
                    $pu			   = 1;
                    if(trim($usertype) == '0' || trim($usertype) == '2')
					{
                        if($usertype != $plan_usertype)
						{
                            $pu = 0;
                        }
                    }

                    $pp = 1;
                    if(trim($proType) == '0' || trim($proType) == '1')
					{
                        if($proType != $plan_proType)
						{
                            $pp = 0;
                        }
                    }

                    if($pu == 1 && $pp == 1)
					{
                        $planArr[] = $plan->id;
                    }
                }
            }
            if(count($planArr) > 0) 
			{
                ob_start();
                jimport('joomla.filesystem.file');
                $request = array('option' => 'com_osmembership', 'view' => 'plans', 'layout' => 'pricingtable', 'filter_plan_ids' => implode(",", $planArr), 'limit' => 0, 'hmvc_call' => 1, 'Itemid' => OSMembershipHelper::getItemid());
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

                $plans = ob_get_contents();
                ob_end_clean();
            }
			else
			{
                $plans = "";
            }

            if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/plans.php'))
			{
                $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
            }
			else
			{
                $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
            }
            $tpl->set('plans',$plans);
            $tpl->set('bootstrapHelper',$bootstrapHelper);
            $tpl->set('planArr',$planArr);
            $body = $tpl->fetch("plans.php");
            echo $body;
        }
    }
    /**
     * This static function is used to return all available subscription plans of Membership Pro
     * @return mixed
     */
	static function getAllPlans(){
        global $configClass;
        if($configClass['integrate_membership'] == 1){
            $db = Factory::getDbo();
            $nullDate = $db->quote($db->getNullDate());
            $nowDate  = $db->quote(HTMLHelper::_('date', 'now', 'Y-m-d H:i:s', false));
            $query = $db->getQuery(true);
            $query->select('tbl.*')->from('#__osmembership_plans as tbl');
            $query->where('tbl.published = 1')
                ->where('tbl.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')')
                ->where('(tbl.publish_up = ' . $nullDate . ' OR tbl.publish_up <= ' . $nowDate . ')')
                ->where('(tbl.publish_down = ' . $nullDate . ' OR tbl.publish_down >= ' . $nowDate . ')');
            $db->setQuery($query);
            $allPlans = $db->loadObjectList();
            return $allPlans;
        }
    }

    /**
     * Is this Membership Pro subscription
     * @param $id
     * @return bool
     */
    static function checkPlan($id){
        $db         = Factory::getDbo();
        $nullDate   = $db->quote($db->getNullDate());
        $nowDate    = $db->quote(HTMLHelper::_('date', 'now', 'Y-m-d H:i:s', false));
        $query      = $db->getQuery(true);
        $query->select('count(tbl.id)')->from('#__osmembership_plans as tbl');
        $query->where('tbl.id = "'.$id.'"')
            ->where('tbl.published = 1')
            ->where('tbl.access IN (' . implode(',', Factory::getUser()->getAuthorisedViewLevels()) . ')')
            ->where('(tbl.publish_up = ' . $nullDate . ' OR tbl.publish_up <= ' . $nowDate . ')')
            ->where('(tbl.publish_down = ' . $nullDate . ' OR tbl.publish_down >= ' . $nowDate . ')');
        $db->setQuery($query);
        $count = $db->loadResult();
        if($count > 0){
            return true;
        }else{
            return false;
        }
    }

    /**
     * Check Plan Type
     * @param $id
     * @return array
     */
    static function checkPlanType($id){
        $returnArr          = array();
        if(self::checkPlan($id)){
            $db             = Factory::getDbo();
            $query          = $db->getQuery(true);
            $query->select('*')->from('#__osmembership_plans')->where('id = "'.$id.'"');
            $db->setQuery($query);
            $plan           = $db->loadObject();
            $params         = new Registry() ;
            $params->loadString($plan->params);
            $usertype       = $params->get('usertype','');
            $proType        = $params->get('proType','');
            $returnArr[0]   = $proType;
            $returnArr[1]   = $usertype;
        }
        return $returnArr;
    }

    /**
     * This static function is used to retrieve OS Property Membership Plans
     */
	static function checkExistingPropertyMembership(){
	    global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $isOsproperty   = $params->get('isospplugin',0);
                if($isOsproperty == 1){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return array
     */
    static function returnExistingPropertyMembership(){
        global $mainframe,$configClass;
        $user                   = Factory::getUser();
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $isOsproperty   = $params->get('isospplugin',0);
                if($isOsproperty == 1){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkExistStandardPropertyPlan(){
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $proType        = $params->get('proType','');
                if($proType == 0){
                    return true;
                }
            }
        }
        return false;
    }

    static function returnStandardPlans(){
        global $mainframe,$configClass;
        $user                   = Factory::getUser();
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $proType        = $params->get('proType','');
                if($proType == 0){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkExistFeaturedPropertyPlan(){
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $proType        = $params->get('proType','');
                if($proType == 1){
                    return true;
                }
            }
        }
        return false;
    }

    static function returnFeaturedPlans(){
        global $mainframe,$configClass;
        $user                   = Factory::getUser();
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $proType        = $params->get('proType','');
                if($proType == 1){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkAgentPlans()
    {
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                if($usertype == 0)
                {
                    return true;
                }
            }
        }
        return false;
    }

    static function checkOwnerPlans()
    {
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                if($usertype == 1)
                {
                    return true;
                }
            }
        }
        return false;
    }

    static function returnAgentPlans(){
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                if($usertype == 0){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function returnOwnerPlans()
    {
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                if($usertype == 1)
                {
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkAgentStandardPlans()
    {
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 0 && $proType == 0)
                {
                    return true;
                }
            }
        }
        return false;
    }

    static function checkOwnerStandardPlans()
    {
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 1 && $proType == 0)
                {
                    return true;
                }
            }
        }
        return false;
    }

    static function returnAgentStandardPlans()
    {
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 0 && $proType == 0)
                {
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function returnOwnerStandardPlans()
    {
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 1 && $proType == 0)
                {
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkAgentFeaturedPlans()
    {
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 0 && $proType == 1)
                {
                    return true;
                }
            }
        }
        return false;
    }

    static function checkOwnerFeaturedPlans()
    {
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 1 && $proType == 1)
                {
                    return true;
                }
            }
        }
        return false;
    }

    static function returnAgentFeaturedPlans()
    {
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 0 && $proType == 1){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function returnOwnerFeaturedPlans()
    {
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0)
        {
            foreach ($available_plans  as $plan)
            {
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype       = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 1 && $proType == 1){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkCompanyPlans(){
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                if($usertype == 2){
                    return true;
                }
            }
        }
        return false;
    }

    static function returnCompanyPlans(){
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                if($usertype == 2){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkCompanyStandardPlans(){
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 2 && $proType == 0){
                    return true;
                }
            }
        }
        return false;
    }

    static function returnCompanyStandardPlans(){
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 2 && $proType == 0){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    static function checkCompanyFeaturedPlans(){
        global $mainframe,$configClass;
        $available_plans        = self::getAllPlans();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 2 && $proType == 1){
                    return true;
                }
            }
        }
        return false;
    }

    static function returnCompanyFeaturedPlans(){
        $available_plans        = self::getAllPlans();
        $active_plans           = array();
        if(count($available_plans) > 0){
            foreach ($available_plans  as $plan){
                $params         = new Registry() ;
                $params->loadString($plan->params);
                $usertype        = $params->get('usertype','');
                $proType        = $params->get('proType','');
                if($usertype == 2 && $proType == 1){
                    $active_plans[] = $plan->id;
                }
            }
        }
        return $active_plans;
    }

    /**
     *
     * @return bool
     */
    static function checkExistingSubscribers()
    {
        $db = Factory::getDbo();
        if (self::checkExistingPropertyMembership())
        {
            if (HelperOspropertyCommon::isAgent())
            {
                //$agent_plans        = self::returnAgentPlans();
                $agent_id           = HelperOspropertyCommon::getAgentID();
                $db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
                $agent_type         = $db->loadResult();
                if($agent_type == 0)
                {
                    $agent_plans    = self::returnAgentPlans();
                }
                elseif($agent_type == 1)
                {
                    $agent_plans    = self::returnOwnerPlans();
                }
                $user_subscribers   = OSMembershipHelper::getActiveMembershipPlans();
				$user_subscribers	= (array) $user_subscribers;
                if (count($user_subscribers) > 1 && count($agent_plans) > 0)
                {
                    for ($i = 1; $i < count($user_subscribers); $i++)
                    {
                        //$plan_id    = self::getMembershipPlan($user_subscribers[$i]);
                        if (in_array($user_subscribers[$i], $agent_plans))
                        {
                            return true;
                        }
                    }
                }
            }
            elseif (HelperOspropertyCommon::isCompanyAdmin() && self::checkCompanyPlans())
            {
                $c_plans            = self::returnCompanyPlans();
                $user_subscribers   = OSMembershipHelper::getActiveMembershipPlans();
				$user_subscribers	= (array) $user_subscribers;
                if (count($user_subscribers) > 1)
                {
                    for ($i = 1; $i < count($user_subscribers); $i++)
                    {
                        //$plan_id    = self::getMembershipPlan($user_subscribers[$i]);
                        if (in_array($user_subscribers[$i], $c_plans))
                        {
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }


    /**
     * This static function is used to retrieve Plan ID
     * @param $sub_id
     * @return mixed
     */
    static function getMembershipPlan($sub_id){
        $db = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('plan_id')->from('#__osmembership_subscribers')->where('id = "'.$sub_id.'"');
        $db->setQuery($query);
        return $db->loadResult();
    }

    static function returnAvailablePlans(){
        $db = Factory::getDbo();
        $returnArr = array();
        if (self::checkExistingPropertyMembership())
        {
            if (HelperOspropertyCommon::isAgent())
            {
                $agent_id           = HelperOspropertyCommon::getAgentID();
                $db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
                $agent_type         = $db->loadResult();
                if($agent_type == 0)
                {
                    $agent_plans    = self::returnAgentPlans();
                }
                elseif($agent_type == 1)
                {
                    $agent_plans    = self::returnOwnerPlans();
                }

                $user_subscribers   = OSMembershipHelper::getActiveMembershipPlans();
				$user_subscribers	= (array) $user_subscribers;
                if (count($user_subscribers) > 1 && count($agent_plans) > 0)
                {
                    for ($i = 1; $i < count($user_subscribers); $i++)
                    {
                        if (in_array($user_subscribers[$i], $agent_plans))
                        {
                            $returnArr[] = $user_subscribers[$i];
                        }
                    }
                }
            }
            elseif (HelperOspropertyCommon::isCompanyAdmin() && self::checkCompanyPlans())
            {
                $c_plans            = self::returnCompanyPlans();
                $user_subscribers   = OSMembershipHelper::getActiveMembershipPlans();
				$user_subscribers	= (array) $user_subscribers;
                if (count($user_subscribers) > 1)
                {
                    for ($i = 1; $i < count($user_subscribers); $i++)
                    {
                        if (in_array($user_subscribers[$i], $c_plans))
                        {
                            $returnArr[] = $user_subscribers[$i];
                        }
                    }
                }
            }
        }
        return $returnArr;
    }

    static function returnAvailableFeaturedPlans()
    {
        $db = Factory::getDbo();
        $returnArr = array();
        if (self::checkExistingPropertyMembership())
        {
            if (HelperOspropertyCommon::isAgent())
            {
                $agent_id           = HelperOspropertyCommon::getAgentID();
                $db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
                $agent_type         = $db->loadResult();
                if($agent_type == 0)
                {
                    $agent_plans    = self::returnAgentFeaturedPlans();
                }
                elseif($agent_type == 1)
                {
                    $agent_plans    = self::returnOwnerFeaturedPlans();
                }

                $user_subscribers   = OSMembershipHelper::getActiveMembershipPlans();
				$user_subscribers   = (array) $user_subscribers;
				$agent_plans		= (array) $agent_plans;
                if (count($user_subscribers) > 1 && count($agent_plans) > 0)
                {
                    for ($i = 1; $i < count($user_subscribers); $i++)
                    {
                        if (in_array($user_subscribers[$i], $agent_plans))
                        {
                            $returnArr[] = $user_subscribers[$i];
                        }
                    }
                }
            }
            elseif (HelperOspropertyCommon::isCompanyAdmin() && self::checkCompanyFeaturedPlans())
            {
                $c_plans            = self::returnCompanyFeaturedPlans();
                $user_subscribers   = OSMembershipHelper::getActiveMembershipPlans();
				$user_subscribers   = (array) $user_subscribers;
                if (count($user_subscribers) > 1)
                {
                    for ($i = 1; $i < count($user_subscribers); $i++)
                    {
                        if (in_array($user_subscribers[$i], $c_plans))
                        {
                            $returnArr[] = $user_subscribers[$i];
                        }
                    }
                }
            }
        }
        return $returnArr;
    }

    /**
     * Check to see if user still has enough credits
     * @param $subid
     * @param $type
     * @param $count
     * @return bool
     */
    static function checkCreditAvailable($subid,$type,$count){
        $db = Factory::getDbo();
        if(HelperOspropertyCommon::isAgent()){
            $agentId = HelperOspropertyCommon::getAgentID();
            $db->setQuery("Select nproperties from #__osrs_agent_account where agent_id = '$agentId' and sub_id = '$subid' and `type` = '$type'");
            $remainCredit = $db->loadResult();
            if($remainCredit < $count){
                return false;
            }else{
                return true;
            }
        }elseif(HelperOspropertyCommon::isCompanyAdmin()){
            $companyId = HelperOspropertyCommon::getCompanyId();
            $db->setQuery("Select nproperties from #__osrs_agent_account where company_id = '$companyId' and sub_id = '$subid' and `type` = '$type'");
            $remainCredit = $db->loadResult();
            if($remainCredit < $count){
                return false;
            }else{
                return true;
            }
        }
    }

    static function getUserStandardCredit()
    {
        $db         = Factory::getDbo();
        if(HelperOspropertyCommon::isAgent())
        {
            $agent_id           = HelperOspropertyCommon::getAgentID();
            $db->setQuery("Select agent_type from #__osrs_agents where id = '$agent_id'");
            $agent_type = $db->loadResult();
            if($agent_type == 0)
            {
                $agentStandardPlans = self::returnAgentStandardPlans();
            }
            else
            {
                $agentStandardPlans = self::returnOwnerStandardPlans();
            }

            if(count($agentStandardPlans) > 0)
            {
                $query = "Select sum(a.nproperties) from #__osrs_agent_account as a "
                        ." left join #__osmembership_subscribers as b on b.id = a.sub_id"
                        ." where a.status = '1'"
                        ." and a.agent_id = '$agent_id'"
                        ." and a.type = '0'"
                        ." and b.plan_id in (".implode(",",$agentStandardPlans).")";
                $db->setQuery($query);
                $standardCredit = $db->loadResult();
                return $standardCredit;
            }
        }
        elseif(HelperOspropertyCommon::isCompanyAdmin())
        {
            $companytStandardPlans = self::returnCompanyStandardPlans();
            $company_id            = HelperOspropertyCommon::getCompanyId();
            if(count($companytStandardPlans) > 0)
            {
                $query = "Select sum(a.nproperties) from #__osrs_agent_account as a "
                    ." left join #__osmembership_subscribers as b on b.id = a.sub_id"
                    ." where a.status = '1'"
                    ." and a.company_id = '$company_id'"
                    ." and a.type = '0'"
                    ." and b.plan_id in (".implode(",",$companytStandardPlans).")";
                $db->setQuery($query);
                $standardCredit = $db->loadResult();
                return $standardCredit;
            }
        }
        return 0;
    }

    static function getUserFeaturedCredit(){
        $db         = Factory::getDbo();
        if(HelperOspropertyCommon::isAgent()){
            $agent_id           = HelperOspropertyCommon::getAgentID();
            $agentStandardPlans = self::returnAvailableFeaturedPlans();

            if(count($agentStandardPlans) > 0)
            {
                $query = "Select sum(a.nproperties) from #__osrs_agent_account as a "
                    ." left join #__osmembership_subscribers as b on b.id = a.sub_id"
                    ." where a.status = '1'"
                    ." and a.agent_id = '$agent_id'"
                    ." and a.type = '1'"
                    ." and b.plan_id in (".implode(",",$agentStandardPlans).")";
                $db->setQuery($query);
                $standardCredit = $db->loadResult();
                return $standardCredit;
            }
        }
        elseif(HelperOspropertyCommon::isCompanyAdmin())
        {
            $companytStandardPlans = self::returnCompanyFeaturedPlans();
            $company_id            = HelperOspropertyCommon::getCompanyId();
            if(count($companytStandardPlans) > 0)
            {
                $query = "Select sum(a.nproperties) from #__osrs_agent_account as a "
                    ." left join #__osmembership_subscribers as b on b.id = a.sub_id"
                    ." where a.status = '1'"
                    ." and a.company_id = '$company_id'"
                    ." and a.type = '1'"
                    ." and b.plan_id in (".implode(",",$companytStandardPlans).")";
                $db->setQuery($query);
                $standardCredit = $db->loadResult();
                return $standardCredit;
            }
        }
        return 0;
    }

    /**
     * Retrieve User Credit
     * @return array
     */
    static function getUserCredit(){
        $returnArr      = array();
        $standardCredit = self::getUserStandardCredit();
        $featuredCredit = self::getUserFeaturedCredit();
        $returnArr[0]   = $standardCredit;
        $returnArr[1]   = $featuredCredit;
        return $returnArr;
    }

    static function getUserSubscription($propertype){
        $db = Factory::getDbo();
        if(HelperOspropertyCommon::isAgent()){
            $agent_id = HelperOspropertyCommon::getAgentID();
            $query = $db->getQuery(true);
            $query->select('id,nproperties')->from('#__osrs_agent_account')
                ->where('agent_id="'.$agent_id.'"')
                ->where('`type` = "'.$propertype.'"')
                ->where('status = 1');
            $db->setQuery($query);
            return $db->loadObjectList();
        }elseif(HelperOspropertyCommon::isCompanyAdmin()){
            $company_id = HelperOspropertyCommon::getCompanyId();
            $query = $db->getQuery(true);
            $query->select('id,nproperties')->from('#__osrs_agent_account')
                ->where('company_id="'.$company_id.'"')
                ->where('`type` = "'.$propertype.'"')
                ->where('status = 1');
            $db->setQuery($query);
            return $db->loadObjectList();
        }
    }

    static function discountCredit($property_type, $number_items){
        $db = Factory::getDbo();
        $userSubs = self::getUserSubscription($property_type);
        if(count($userSubs) > 0){
            foreach($userSubs as $sub){
                $sub_id = $sub->id;
                $nproperties = $sub->nproperties;
                if($nproperties > 0) {
                    if ($number_items <= $nproperties) {
                        $db->setQuery("UPDATE #__osrs_agent_account SET nproperties = nproperties - " . $number_items . " WHERE id = '$sub_id'");
                        $db->execute();
                        $number_items = 0;
                    } elseif ($number_items > $nproperties) {
                        $db->setQuery("UPDATE #__osrs_agent_account SET nproperties = 0 WHERE id = '$sub_id'");
                        $db->execute();
                        $number_items -= $nproperties;
                    }
                }
            }
        }
    }

    /**
     * Active listing with membership
     * @param $id
     */
	static function activelisting($id)
    {
        global $mainframe,$jinput,$bootstrapHelper;
        if(HelperOspropertyCommon::isAgent()){
            $usertype = 0;
        }elseif(HelperOspropertyCommon::isCompanyAdmin()){
            $usertype = 2;
        }

        $db         = Factory::getDbo();
        $query      = $db->getQuery(true);
        $query->select('*')->from('#__osrs_properties')->where('id = "'.$id.'"');
        $db->setQuery($query);
        $property = $db->loadObject();
        if(self::checkExistingSubscribers()){
            $userCredit = self::getUserCredit();
            if((int)$userCredit[0] == 0 && (int)$userCredit[1] == 0){
                $msg = Text::_('OS_PLEASE_PURCHASE_SUBSCRIPTION_FIRST');
                $session = Factory::getSession();
                $session->set('osm_return_url',Route::_('index.php?option=com_osproperty&task=property_edit_activelisting&id='.$id));
                self::generatePlanPurchasing($usertype,'',$msg,1);
            }
            if(File::exists(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/selectplans.php')){
                $tpl = new OspropertyTemplate(JPATH_ROOT.'/templates/'.$mainframe->getTemplate().'/html/com_osproperty/layouts/');
            }else{
                $tpl = new OspropertyTemplate(JPATH_COMPONENT.'/helpers/layouts/');
            }
            $itemid = $jinput->getInt('Itemid',0);
            $tpl->set('itemid',$itemid);
            $tpl->set('mainframe',$mainframe);
            $tpl->set('property',$property);
            $tpl->set('bootstrapHelper',$bootstrapHelper);
            $tpl->set('userCredit',$userCredit);
            $tpl->set('type',1);
            $body = $tpl->fetch("selectplans.php");
            echo $body;
        }else {
            $msg = Text::_('OS_PLEASE_PURCHASE_SUBSCRIPTION_FIRST');
            $session = Factory::getSession();
            $session->set('osm_return_url',Route::_('index.php?option=com_osproperty&task=property_edit_activelisting&id='.$id));
            self::generatePlanPurchasing($usertype,'',$msg,1);
        }
    }

    /**
     * @param $usertype
     * @param $protype
     */
    static function generatePlanPurchasing($usertype = -1,$protype = -1,$msg, $setSession = 0){
        global $mainframe;
        if((int)$usertype >= 0){
            if($usertype == 0){
                $plans = self::returnAgentPlans();
            }elseif($usertype == 2){
                $plans = self::returnCompanyPlans();
            }
        }
        $session = Factory::getSession();
        $session->set('required_plan_ids',$plans);

        $link = self::generateLink($usertype,$protype,$setSession);
        $mainframe->enqueueMessage($msg);
        $mainframe->redirect($link);
    }

    /**
     * Generate Link
     * @param int $usertype
     * @param int $protype
     * @param int $setSession
     * @return string
     */
    static function generateLink($usertype = -1,$protype = -1, $setSession = 0){
        if((int)$usertype >= 0){
            $uSql = "&usertype=".$usertype;
        }
        if((int)$protype >= 0){
            $tSql = "&proType=".$protype;
        }
        return Route::_('index.php?option=com_osproperty&view=lmembership&setSession='.$setSession.$uSql.$tSql);
    }
}
