<?php
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2018 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class PlgUserOsproperty extends CMSPlugin
{
	/**
	 * Remove all subscriptions for the user if configured
	 *
	 * Method is called after user data is deleted from the database
	 *
	 * @param   array   $user    Holds the user data
	 * @param   boolean $success True if user was successfully stored in the database
	 * @param   string  $msg     Message
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function onUserAfterDelete($user, $success, $msg)
	{
	    include_once JPATH_ROOT.'/components/com_osproperty/helpers/common.php';
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$userId = (int) $user['id'];

		if (!$userId)
		{
			return true;
		}

		if ($this->params->get('delete_user_properties'))
		{
		    //is Agent
            if(HelperOspropertyCommon::isRegisteredAgent($userId))
            {
                $query->clear();
                $query->select('id')->from('#__osrs_agents')->where('user_id = '.$userId);
                $db->setQuery($query);

                $agent_id    = $db->loadResult();
                $query->clear();
                $query->select('id')
                    ->from('#__osrs_properties')
                    ->where('agent_id = ' . $agent_id);
                $db->setQuery($query);
                $propertyIds = $db->loadColumn();
            }
            else if(HelperOspropertyCommon::isRegisteredCompanyAdmin($userId))
            {
                //is Company admin
                $query->clear();
                $query->select('id')->from('#__osrs_companies')->where('user_id = '.$userId);
                $db->setQuery($query);
                $company_id    = $db->loadResult();

                $query1 = "Select a.id from #__osrs_properties as a"
                        ." left join #__osrs_company_agents as b on a.agent_id = b.agent_id"
                        ." where b.company_id = '$company_id'";
                $db->setQuery($query1);
                $propertyIds = $db->loadColumn();
            }

			if (count((array)$propertyIds))
			{
				$this->deleteProperties((array)$propertyIds);
			}
		}

		if ($this->params->get('delete_user_account'))
		{
            //is Agent
            if(HelperOspropertyCommon::isRegisteredAgent($userId))
            {
                if($agent_id == 0)
                {
                    $query->clear();
                    $query->select('id')->from('#__osrs_agents')->where('user_id = '.$userId);
                    $db->setQuery($query);
                    $agent_id    = $db->loadResult();
                }
                $this->deleteAgent($agent_id);
            }
            else if(HelperOspropertyCommon::isRegisteredCompanyAdmin())
            {
                //is Company admin
                if($company_id == 0)
                {
                    $query->clear();
                    $query->select('id')->from('#__osrs_companies')->where('user_id = '.$userId);
                    $db->setQuery($query);
                    $company_id    = $db->loadResult();
                }
                $this->deleteCompany($company_id);
            }
		}

		return true;
	}

	/**
	 * Method to delete events
	 *
	 * @param array $cid
	 */
	private function deleteProperties($cid)
	{
	    include_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
		OSPHelper::removeProperties($cid);
	}

    /**
     * Delete Agent
     * @param $id
     */
	private function deleteAgent($id){
        define('PATH_STORE_PHOTO_AGENT_FULL',JPATH_ROOT."/images/osproperty/agent");
        define('PATH_STORE_PHOTO_AGENT_THUMB',PATH_STORE_PHOTO_AGENT_FULL.'/thumbnail');
        define('PATH_URL_PHOTO_AGENT_FULL',str_replace(DS,'/',str_replace(JPATH_SITE,JURI::root(),PATH_STORE_PHOTO_AGENT_FULL)).'/');
        define('PATH_URL_PHOTO_AGENT_THUMB',str_replace(DS,'/',str_replace(JPATH_SITE,JURI::root(),PATH_STORE_PHOTO_AGENT_THUMB)).'/');
        
	    $db = Factory::getDbo();
        $db->setQuery("SELECT photo FROM #__osrs_agents WHERE id = $id");
        $photo = $db->loadResult();
        if (is_file(PATH_STORE_PHOTO_AGENT_FULL.'/'.$photo)) unlink(PATH_STORE_PHOTO_AGENT_FULL.'/'.$photo);
        if (is_file(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$photo)) unlink(PATH_STORE_PHOTO_AGENT_THUMB.'/'.$photo);

        $db->setQuery("DELETE FROM #__osrs_company_agents WHERE agent_id = '$id'");
        $db->execute();
        $db->setQuery("DELETE FROM #__osrs_agents WHERE id = '$id'");
        $db->execute();
    }

    private function deleteCompany($id){
        define('PATH_STORE_PHOTO_COMPANY_FULL',JPATH_ROOT.'/images/osproperty/company');
        define('PATH_STORE_PHOTO_COMPANY_THUMB',PATH_STORE_PHOTO_COMPANY_FULL.'/thumbnail');
        define('PATH_URL_PHOTO_COMPANY_FULL',str_replace(DS,'/',str_replace(JPATH_SITE,JURI::root(),PATH_STORE_PHOTO_COMPANY_FULL)).'/');
        define('PATH_URL_PHOTO_COMPANY_THUMB',str_replace(DS,'/',str_replace(JPATH_SITE,JURI::root(),PATH_STORE_PHOTO_COMPANY_THUMB)).'/');

        $db = Factory::getDbo();
        $db->setQuery("SELECT photo FROM #__osrs_companies WHERE id = $id)");
        $photo = $db->loadResult();

        if (is_file(PATH_STORE_PHOTO_COMPANY_FULL.'/'.$photo)) unlink(PATH_STORE_PHOTO_COMPANY_FULL.'/'.$photo);
        if (is_file(PATH_STORE_PHOTO_COMPANY_THUMB.'/'.$photo)) unlink(PATH_STORE_PHOTO_COMPANY_THUMB.'/'.$photo);

        $db->setQuery("DELETE FROM #__osrs_companies WHERE id = '$id'");
        $db->execute();
    }
}
