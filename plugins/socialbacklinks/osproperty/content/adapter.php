<?php
/**
 * SocialBacklinks Redshop plugin
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Plugin for default Redshop content
 */
class PlgSBOspropertyAdapter extends SBPluginsContent
{
	/**
	 * The lowercase lang tag
	 * ex fr_fr
	 * @var string
	 */
	protected $_lang_tag;
	/**
	 * The fields map
	 * @var array
	 */
	protected $_map = array(
		'items_table' => array(
			'__table' => '#__osrs_properties',
			'id' => 'id',
			'title' => 'pro_name',
			'content' => 'pro_small_desc',
			'created' => 'created',
			'created_by' => null,
			'modified' => 'modified',
			'modified_by' => null,
			'publish_up' => null,
			'publish_down' => null,
		),
		'categories_table' => array(
			'__table' => '#__osrs_types' ,
			'id' => 'id',
			'title' => 'type_name'
		)
	);

	/**
	 * Constructor
	 * @param  Jplugin Object that has registered current plugin
	 * @param  array  The list of plugin options
	 * @return void
	 */
	public function __construct( $caller, $options = array() )
	{
		$default = array(
			'sync_desc' => false,
			'fields_to_show_in_query_list' => array(
				array(
					'title' => 'Title',
					'field' => 'title',
					'class' => 'first'
				),
				array(
					'title' => 'Categories',
					'field' => 'cctitle',
					'width' => '140'
				),
				array(
					'title' => 'Modification',
					'field' => 'modified',
					'width' => '40'
				),
				array(
					'title' => 'Creation',
					'field' => 'created',
					'width' => '40'
				),
				array(
					'title' => 'ID',
					'field' => 'id',
					'width' => '10'
				)
			)
		);

		$this->_lang_tag = JComponentHelper::getParams('com_languages')->get('site', 'en-GB');
		$options = array_merge( $default, $options );
		parent::__construct( $caller, $options );
	}

	/**
	 * @see SBAdaptersPlugin::getAlias()
	 */
	public function getAlias( )
	{
		return 'osproperty';
	}

	/**
	 * @see SBPluginsContentsInterface::getNewItemsConditions()
	 */
	public function getNewItemsConditions( $settings )
	{
		$where			= array();
		$nowdate		= $settings['nowdate'];
		$last_sync		= $settings['last_sync'];
		$nulldate		= $settings['nulldate'];

		if ( $this->sync_updated ) {
			$where[] = '( TIMEDIFF(tbl.`' . $this->get( 'items_table.modified' ) . "`, $last_sync) > 0 OR TIMEDIFF(tbl.`" . $this->get( 'items_table.created' ) . "`, $last_sync) > 0)";
		}
		else {
			$where[] = "(TIMEDIFF(tbl.`" . $this->get( 'items_table.created' ) . "`, $last_sync) > 0)";
		}

		if ( $this->selected_content ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' tbl.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				$condition .= !empty( $condition ) ? 'OR ' : '';
				$condition .= ' tbl.`' . $this->get( 'items_table.pro_type' ) . '` IN (' . implode( ', ', $this->categories ) . ') ';
			}
			if ( !empty( $condition ) ) {
				$where['selected_content'] = "$condition";
			}
		}

		$where[] = 'tbl.`published` = 1';

		if ( isset( $where['selected_content'] ) ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' tbl.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				// nothing
			}
			if ( !empty( $condition ) ) {
				$where['selected_content'] = "$condition";
			}
		}
		return $where;
	}

	/**
	 * @see SBPluginsContentsInterface::getItemRoute()
	 */
	public function getItemRoute( $item )
	{
		$id = ($item->id) ? $item->id : $item->item_id;
		return 'index.php?option=com_osproperty&task=property_details&id=' . $id;
	}

	/**
	 * @see SBPluginsContentsInterface::getTreeOfCategories()
	 */
	public function getTreeOfCategories( )
	{
		$db = JFactory::getDBO ();

		$query = 'SELECT a.* , a.type_name AS title
                        FROM #__osrs_types AS a 
                        WHERE  a.published=1 ORDER BY a.id ';
		$db->setQuery ($query);
		$cats = $db->loadObjectList ();

		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[] = array(
				'_type' => 'category',
				'title' => $cat->title,
				'id' => $cat->id,
				'parent_id' => 0,
				'_hasChildren' => false,
				'_children' => array()
			);
		}

		$root = array(
			'_type' => 'category',
			'title' => 'SB_UNCATEGORISED',
			'id' => 0,
			'parent_id' => null,
			'_hasChildren' => false,
			'_children' => array( )
		);
		$this->assignChildren( $root, $categories );

		$result[] = $root;

		return $result;
	}

	/**
	 * Recursive function that uses pointers to get the Tree
	 */
	public function assignChildren( &$item, &$categories )
	{
		if ($item['_hasChildren'])
			return;

		$item['_hasChildren'] = true;
		foreach( $categories as &$category )
		{
			if ( $category['parent_id'] == $item['id'] )
			{
				$item['_children'][] = &$category;
				$this->assignChildren( $category, $categories );
			}
		}
	}

	/**
	 * @see SBPluginsContentsInterface::getCategoryItems()
	 */
	public function getCategoryItems( $category_id, $level )
	{
		$query = "Select *, pro_name as title from #__osrs_properties where pro_type = '$category_id' and published = '1'";
		return $query;
	}

	/**
	 * @see SBPluginsContentsInterface::getItemsDetailed()
	 */
	public function getItemsDetailed()
	{
		$query = new stdClass();

		$query->select = 'SELECT tbl.`'. $this->get('items_table.id') .'` AS id, tbl.`' . $this->get('items_table.created') . '` AS created, tbl.`' . $this->get('items_table.modified') . '` AS modified, tbl.`' . $this->get( 'items_table.title' ) . '` AS title, \'\' AS rien'
			. ' FROM ' . $this->get( 'items_table.__table' ) . ' AS tbl';

		$query->join = array();
		return $query;
	}

}
