<?php
/**
 * @version 3.2 2013-11-19
 * @package Joomla
 * @subpackage OS Property
 * @copyright (C) 2016 the Ossolution
 * @license GNU/GPL see LICENSE.php
 */

defined('_JEXEC') or die('Restricted access');

class plgSearchOsproperty extends JPlugin
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

    public function onContentSearchAreas()
	{
		static $areas = array(
			'osproperty' => 'PLG_OS_SEARCH_PROPERTIES'
		);
		return $areas;
	}
	
	public function onContentSearch( $text, $phrase='', $ordering='', $areas=null )
	{	
		$db		= JFactory::getDBO();
		$user	= JFactory::getUser();
        $groups	= $user->getAuthorisedViewLevels();
	
		require_once(JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php');
        require_once(JPATH_SITE.'/components/com_osproperty/helpers/helper.php');
		require_once(JPATH_SITE.'/components/com_osproperty/helpers/common.php');
        require_once(JPATH_SITE.'/components/com_osproperty/helpers/route.php');
        
        $settings = OSPHelper::loadConfig();
	
		$searchText = $text;
		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) )) {
				return array();
			}
		}
	
		$sdesc 			= $this->params->get( 'search_short', 		1 );
        $fdesc 			= $this->params->get( 'search_full', 		1 );
		$limit 			= $this->params->def( 'search_limit', 		50 );
	
		$text = trim( $text );
		if ($text == '') {
			return array();
		}
	
		$wheres = array();
		switch ($phrase) {
			case 'exact':
				$text		= $db->Quote( '%'.$db->escape( $text, true ).'%', false );
				$wheres2 	= array();
				$wheres2[] 	= 'p.ref LIKE '.$text;
				$wheres2[] 	= 'p.address LIKE '.$text;
                $wheres2[] 	= 'p.pro_name LIKE '.$text;
                $wheres2[] 	= 'ci.city LIKE '.$text;
                $wheres2[] 	= 's.state_name LIKE '.$text; //1.5.5
                $wheres2[] 	= 'p.region LIKE '.$text; //1.5.5
				if($sdesc ) {
                    $wheres2[] 	= 'p.pro_small_desc LIKE '. $text;
                }
                if($fdesc ) {
                    $wheres2[] 	= 'p.pro_full_desc LIKE '. $text;
                }
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';
				break;
			case 'all':
			case 'any':
			default:
				$words = explode( ' ', $text );
				$wheres = array();
				foreach ($words as $word) {
					$word		= $db->Quote( '%'.$db->escape( $word, true ).'%', false );
					$wheres2 	= array();
					$wheres2[] 	= 'p.ref LIKE '.$word;
					$wheres2[] 	= 'p.address LIKE '.$word;
                    $wheres2[] 	= 'p.pro_name LIKE '.$word;
                    $wheres2[] 	= 'ci.city LIKE '.$word;
                    $wheres2[] 	= 's.state_name LIKE '.$word;
                    $wheres2[] 	= 'p.region LIKE '.$word; //1.5.5
					if($sdesc ) {
						$wheres2[] 	= 'p.pro_small_desc LIKE '. $word;
					}
                    if($fdesc ) {
						$wheres2[] 	= 'p.pro_full_desc LIKE '. $word;
					}
					$wheres[] 	= implode( ' OR ', $wheres2 );
				}
				$where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				break;
		}
	
		$morder = '';
		switch ($ordering) {
			case 'oldest':
				$order = 'p.created ASC';
				break;
	
			case 'popular':
				$order = 'p.hits DESC';
				break;
	
			case 'alpha':
				$order = ($settings->showtitle) ? 'p.title ASC' : 'p.street ASC';
				break;
			
			case 'category':
				$order = ($settings->showtitle) ? 'pm.cat_id ASC, p.title ASC' : 'pm.cat_id ASC, p.street ASC';
				
			case 'newest':
				default:
				$order = 'p.created DESC';
				break;
		}
	
		$rows = array();
		
		if ( $limit > 0 )
		{
            // Filter by start and end dates.
            $nullDate   = $db->Quote($db->getNullDate());
            $date       = JFactory::getDate();
            $nowDate    = $db->Quote($date->toSql());
            
            $query = $db->getQuery(true);
            $query->select('p.*, p.id AS property_id, c.id AS cat_id, s.state_name as state_name')
                    ->from('#__osrs_properties as p')
                    ->innerJoin('#__osrs_types as pm on pm.id = p.pro_type')
					->leftJoin('#__osrs_property_categories as pc on pc.pid = p.id')
                    ->innerJoin('#__osrs_categories as c on c.id = pc.category_id')
                    ->innerJoin('#__osrs_states as s on s.id = p.state')
					->innerJoin('#__osrs_cities as ci on ci.id = p.city')
                    ->where('p.published = 1 and p.approved = 1 and c.published = 1')
                    ->where($where);
            if(is_array($groups) && !empty($groups)){
                $query->where('p.access IN ('.implode(",", $groups).')')
                    ->where('c.access IN ('.implode(",", $groups).')');
            }
            $query->group('p.id');
            $query->order($order);
			
			$db->setQuery( $query, 0, $limit );
			$list = $db->loadObjectList();
			$limit -= count($list);
	
			if(isset($list))
			{
				foreach($list as $key => $item)
				{
                    $desc_text = ($item->pro_small_desc) ? $item->pro_small_desc : $item->pro_full_desc;
                    if($item->hidden){
                        $list[$key]->title = $item->pro_name.': '.JText::_('PLG_OS_SEARCH_ADDRESS_HIDDEN');
                    }else{
                        $list[$key]->title = $item->pro_name.': '.OSPHelper::generateAddress($item);
                    }
                    $list[$key]->text = $desc_text;
					$needs = array();
					$needs[] = "property_details";
					$needs[] = $item->property_id;
					$itemid = OSPRoute::getItemid($needs);
					$list[$key]->href = JRoute::_('index.php?option=com_osproperty&task=property_details&id='.$item->property_id.'&Itemid='.$itemid);

				}
			}
			$rows[] = $list;
		}
	

		$results = array();
		if(count($rows))
		{
			foreach($rows as $row)
			{
				$new_row = array();
				foreach($row AS $key => $post) {
					$new_row[] = $post;
				}
				$results = array_merge($results, (array) $new_row);
			}
		}
	
		return $results;
	}
}