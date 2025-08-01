<?php

use Joomla\CMS\Factory;
class OSFSynchronizerCommunitybuilder
{

	public function getData($userId, $mappings)
	{
		$data = array();
		$db = Factory::getContainer()->get('db');
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__comprofiler')
			->where('user_id=' . $userId);
		$db->setQuery($query);
		$profile = $db->loadObject();
		if ($profile)
		{
			foreach ($mappings as $fieldName => $mappingFieldName)
			{
				if ($mappingFieldName && isset($profile->{$mappingFieldName}))
				{
					if (stristr($profile->{$mappingFieldName}, "|*|"))
					{
						$profile->{$mappingFieldName} = explode('|*|', $profile->{$mappingFieldName});
					}
					$data[$fieldName] = $profile->{$mappingFieldName};
				}
			}
		}
		return $data;
	}
}
