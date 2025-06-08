<?php
/**
 * @copyright    Copyright (C) 2011-2016 Ossolution - All rights reserved..
 * @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Pagination\Pagination;

class plgAcymailingOsproperty extends CMSPlugin
{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = PluginHelper::getPlugin('acymailing', 'osproperty');
			$this->params = new acyParameter($plugin->params);
		}
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
	}

	function acymailing_getPluginType(){

		$onePlugin = new stdClass();
		$onePlugin->name = 'OS Property';
		$onePlugin->function = 'acymailingtagosproperty_show';
		$onePlugin->help = 'plugin-osproperty';

		return $onePlugin;
	}

	function acymailingtagosproperty_show(){
		$config = acymailing_config();
		//if($config->get('version') < '5.5.0'){
			//acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
			//return;
		//}

		// Load language file
		$lang = Factory::getLanguage();
		$lang->load('com_osproperty', JPATH_ADMINISTRATOR);

		$app = Factory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		require_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
		require_once(JPATH_ROOT.'/components/com_osproperty/helpers/common.php');

		$paramBase = ACYMAILING_COMPONENT.'.osproperty';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.id', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->filter_cat = $app->getUserStateFromRequest($paramBase.".filter_cat", 'filter_cat', '', 'int');
		$pageInfo->filter_type = $app->getUserStateFromRequest($paramBase.".filter_type", 'filter_type', '', 'int');
		$pageInfo->filter_featured = $app->getUserStateFromRequest($paramBase.".filter_featured", 'filter_featured', '0', 'int');
		
		$pageInfo->titlelink = $app->getUserStateFromRequest($paramBase.".titlelink", 'titlelink', 'link', 'string');
		$pageInfo->showprice = $app->getUserStateFromRequest($paramBase.".showprice", 'showprice', 'showprice', 'string');
		$pageInfo->showcreated = $app->getUserStateFromRequest($paramBase.".showcreated", 'showcreated', 'showcreated', 'showcreated');

		$pageInfo->bath = $app->getUserStateFromRequest($paramBase.".bath", 'bath', 'bath', 'string');
		$pageInfo->bed = $app->getUserStateFromRequest($paramBase.".bed", 'bed', 'bed', 'string');
		$pageInfo->square = $app->getUserStateFromRequest($paramBase.".square", 'square', 'square', 'string');

		$pageInfo->address = $app->getUserStateFromRequest($paramBase.".address", 'address', 'address', 'string');
		$pageInfo->category = $app->getUserStateFromRequest($paramBase.".category", 'category', 'category', 'string');

		$pageInfo->lang = $app->getUserStateFromRequest($paramBase.".lang", 'lang', '', 'string');
		$pageInfo->author = $app->getUserStateFromRequest($paramBase.".author", 'author', $this->params->get('default_author', '0'), 'string');
		$pageInfo->images = $app->getUserStateFromRequest($paramBase.".images", 'images', '1', 'string');
		$pageInfo->contenttype = $app->getUserStateFromRequest($paramBase.".contenttype", 'contenttype', 'intro', 'string');
		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->autotitlelink = $app->getUserStateFromRequest($paramBase.".autotitlelink", 'autotitlelink', 'link', 'string');
		$pageInfo->autoimages = $app->getUserStateFromRequest($paramBase.".autoimages", 'autoimages', '1', 'string');
		$pageInfo->automaxvalue = $app->getUserStateFromRequest($paramBase.".max", 'max', '20', 'int');
		$pageInfo->contentfilter = $app->getUserStateFromRequest($paramBase.".contentfilter", 'contentfilter', 'created', 'string');
		$pageInfo->contentorder = $app->getUserStateFromRequest($paramBase.".contentorder", 'contentorder', 'id', 'string');
		$pageInfo->autocontenttype = $app->getUserStateFromRequest($paramBase.".autocontenttype", 'autocontenttype', 'intro', 'string');
		$pageInfo->pict = $app->getUserStateFromRequest($paramBase.".pict", 'pict', $this->params->get('default_pict', 1), 'string');
		$pageInfo->pictheight = $app->getUserStateFromRequest($paramBase.".pictheight", 'pictheight', $this->params->get('maxheight', 150), 'string');
		$pageInfo->pictwidth = $app->getUserStateFromRequest($paramBase.".pictwidth", 'pictwidth', $this->params->get('maxwidth', 150), 'string');


		$db = Factory::getDBO();

		$searchFields = array('a.id', 'a.pro_alias', 'a.pro_name', 'agent_name', 'd.type_name','e.country_name','g.state_name','city_name');

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		$my = Factory::getUser();
		
		$filters[] = "a.published = '1' and a.approved = '1'";

		$query = "SELECT SQL_CALC_FOUND_ROWS a.*,c.name as agent_name,d.type_name,e.country_name,g.state_name,h.city as city_name";
		$query .= " FROM `#__osrs_properties` as a "
				." INNER JOIN #__osrs_agents as c on c.id = a.agent_id"
				." LEFT JOIN #__osrs_types as d on d.id = a.pro_type"
				." LEFT JOIN #__osrs_countries as e on e.id = a.country"
				." LEFT JOIN #__osrs_states as g on g.id = a.state"
				." LEFT JOIN #__osrs_cities as h on h.id = a.city";
		$filters[] = " 1=1 ";

		if(!empty($pageInfo->filter_cat)) $filters[] = "a.id in (Select pid from #__osrs_property_categories where category_id = '".$pageInfo->filter_cat."')";

		if(!empty($pageInfo->filter_featured)) {
			if($pageInfo->filter_featured == 1){
				$filters[] = "a.isFeatured = '1' ";
			}elseif($pageInfo->filter_featured == 2){
				$filters[] = "a.isFeatured = '0' ";
			}
		}

		if(!empty($pageInfo->filter_type)) $filters[] = "a.pro_type = '".$pageInfo->filter_type."'";

		if(!empty($filters)){
			$query .= ' WHERE ('.implode(') AND (', $filters).')';
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();
		if(!empty($rows[0]) && !isset($rows[0]->acy_created)){
			$db->setQuery('ALTER TABLE `#__osrs_properties` ADD `acy_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
			$db->query();
		}

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new Pagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$type = JRequest::getString('type');

		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedContents = new Array();
			function applyContent(contentid, rowClass){
				var tmp = selectedContents.indexOf(contentid)
				if(tmp != -1){
					window.document.getElementById('content' + contentid).className = rowClass;
					delete selectedContents[tmp];
				}else{
					window.document.getElementById('content' + contentid).className = 'selectedrow';
					selectedContents.push(contentid);
				}
				updateTag();
			}

			function updateTag(){
				var tag = '';
				var otherinfo = '';
				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						otherinfo += '|type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelink.length; i++){
					if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value.length > 1){
						otherinfo += '|' + document.adminForm.titlelink[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.showprice.length; i++){
					if(document.adminForm.showprice[i].checked && document.adminForm.showprice[i].value.length > 1){
						otherinfo += '|' + document.adminForm.showprice[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.bath.length; i++){
					if(document.adminForm.bath[i].checked && document.adminForm.bath[i].value.length > 1){
						otherinfo += '|' + document.adminForm.bath[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.bed.length; i++){
					if(document.adminForm.bed[i].checked && document.adminForm.bed[i].value.length > 1){
						otherinfo += '|' + document.adminForm.bed[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.address.length; i++){
					if(document.adminForm.address[i].checked && document.adminForm.address[i].value.length > 1){
						otherinfo += '|' + document.adminForm.address[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.square.length; i++){
					if(document.adminForm.square[i].checked && document.adminForm.square[i].value.length > 1){
						otherinfo += '|' + document.adminForm.square[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.category.length; i++){
					if(document.adminForm.category[i].checked && document.adminForm.category[i].value.length > 1){
						otherinfo += '|' + document.adminForm.category[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.showcreated.length; i++){
					if(document.adminForm.showcreated[i].checked && document.adminForm.showcreated[i].value.length > 1){
						otherinfo += '|' + document.adminForm.showcreated[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.author.length; i++){
						if(document.adminForm.author[i].checked && document.adminForm.author[i].value.length > 1){
							otherinfo += '|' + document.adminForm.author[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pict.length; i++){
						if(document.adminForm.pict[i].checked){
							otherinfo += '|pict:' + document.adminForm.pict[i].value;
							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) otherinfo += '|maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) otherinfo += '|maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
				}

				if(window.document.getElementById('jflang') && window.document.getElementById('jflang').value != ''){
					otherinfo += '|lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents){
					if(selectedContents[i] && !isNaN(i)){
						tag = tag + '{osp:' + selectedContents[i] + otherinfo + '}<br />';
					}
				}
				setTag(tag);
			}

			var selectedCat = new Array();
			var selectedType = new Array();
			var selectedFeatured = new Array();
			function applyAuto(catid, rowClass){
				if(selectedCat[catid]){
					window.document.getElementById('cat' + catid).className = rowClass;
					delete selectedCat[catid];
				}else{
					window.document.getElementById('cat' + catid).className = 'selectedrow';
					selectedCat[catid] = 'selectedone';
				}

				updateAutoTag();
			}

			function applyFeaturedAuto(featuredId,rowClass){
				if(selectedFeatured[featuredId]){
					window.document.getElementById('featured' + featuredId).className = rowClass;
					delete selectedFeatured[featuredId];
				}else{
					window.document.getElementById('featured' + featuredId).className = 'selectedrow';
					selectedFeatured[featuredId] = 'selectedone';
				}
				updateAutoTag();
			}

			function applyTypeAuto(catid, rowClass){
				if(selectedType[catid]){
					window.document.getElementById('type' + catid).className = rowClass;
					delete selectedType[catid];
				}else{
					window.document.getElementById('type' + catid).className = 'selectedrow';
					selectedType[catid] = 'selectedone';
				}

				updateAutoTag();
			}

			function updateAutoTag(){
				tag = '{autoosp:';

				for(var icat in selectedCat){
					if(selectedCat[icat] == 'selectedone'){
						tag += icat + '-';
					}
				}
				tag += "|TYPE:";
				for(var itype in selectedType){
					if(selectedType[itype] == 'selectedone'){
						tag += itype + '-';
					}
				}
				tag += "|FEATURED:";
				for(var itype in selectedFeatured){
					if(selectedFeatured[itype] == 'selectedone'){
						tag += itype + '-';
					}
				}

				if(document.adminForm.min_article && document.adminForm.min_article.value && document.adminForm.min_article.value != 0){
					tag += '|min:' + document.adminForm.min_article.value;
				}
				if(document.adminForm.max_article.value && document.adminForm.max_article.value != 0){
					tag += '|max:' + document.adminForm.max_article.value;
				}
				if(document.adminForm.contentorder.value){
					tag += document.adminForm.contentorder.value;
				}
				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value){
					tag += document.adminForm.contentfilter.value;
				}
				if(document.adminForm.meta_article && document.adminForm.meta_article.value){
					tag += '|meta:' + document.adminForm.meta_article.value;
				}

				for(var i = 0; i < document.adminForm.contenttypeauto.length; i++){
					if(document.adminForm.contenttypeauto[i].checked){
						selectedtype = document.adminForm.contenttypeauto[i].value;
						tag += '|type:' + document.adminForm.contenttypeauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelinkauto.length; i++){
					if(document.adminForm.titlelinkauto[i].checked && document.adminForm.titlelinkauto[i].value.length > 1){
						tag += '|' + document.adminForm.titlelinkauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.showprice.length; i++){
					if(document.adminForm.showprice[i].checked && document.adminForm.showprice[i].value.length > 1){
						tag += '|' + document.adminForm.showprice[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.bath.length; i++){
					if(document.adminForm.bath[i].checked && document.adminForm.bath[i].value.length > 1){
						tag += '|' + document.adminForm.bath[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.bed.length; i++){
					if(document.adminForm.bed[i].checked && document.adminForm.bed[i].value.length > 1){
						tag += '|' + document.adminForm.bed[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.address.length; i++){
					if(document.adminForm.address[i].checked && document.adminForm.address[i].value.length > 1){
						tag += '|' + document.adminForm.address[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.square.length; i++){
					if(document.adminForm.square[i].checked && document.adminForm.square[i].value.length > 1){
						tag += '|' + document.adminForm.square[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.category.length; i++){
					if(document.adminForm.category[i].checked && document.adminForm.category[i].value.length > 1){
						tag += '|' + document.adminForm.category[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.showcreated.length; i++){
					if(document.adminForm.showcreated[i].checked && document.adminForm.showcreated[i].value.length > 1){
						tag += '|' + document.adminForm.showcreated[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.authorauto.length; i++){
						if(document.adminForm.authorauto[i].checked && document.adminForm.authorauto[i].value.length > 1){
							tag += '|' + document.adminForm.authorauto[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pictauto.length; i++){
						if(document.adminForm.pictauto[i].checked){
							tag += '|pict:' + document.adminForm.pictauto[i].value;
							if(document.adminForm.pictauto[i].value == 'resized'){
								document.getElementById('pictsizeauto').style.display = '';
								if(document.adminForm.pictwidthauto.value) tag += '|maxwidth:' + document.adminForm.pictwidthauto.value;
								if(document.adminForm.pictheightauto.value) tag += '|maxheight:' + document.adminForm.pictheightauto.value;
							}else{
								document.getElementById('pictsizeauto').style.display = 'none';
							}
						}
					}
				}
				if(document.adminForm.cols && document.adminForm.cols.value > 1){
					tag += '|cols:' + document.adminForm.cols.value;
				}
				if(window.document.getElementById('jflangauto') && window.document.getElementById('jflangauto').value != ''){
					tag += '|lang:';
					tag += window.document.getElementById('jflangauto').value;
				}

				tag += '}';

				setTag(tag);
			}
			//-->
		</script>
		<?php

		$picts = [];
		$picts[] = HTMLHelper::_('select.option', "1", Text::_('JOOMEXT_YES'));
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()) $picts[] = HTMLHelper::_('select.option', "resized", Text::_('RESIZED'));
		$picts[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Content type
		$contenttype = [];
		$contenttype[] = HTMLHelper::_('select.option', "title", Text::_('TITLE_ONLY'));
		$contenttype[] = HTMLHelper::_('select.option', "intro", Text::_('INTRO_ONLY'));
		$contenttype[] = HTMLHelper::_('select.option', "full", Text::_('FULL_TEXT'));

		//Title link params
		$featuredarray = [];
		$featuredarray[] = HTMLHelper::_('select.option', "featured", Text::_('JOOMEXT_YES'));
		$featuredarray[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Title link params
		$titlelink = [];
		$titlelink[] = HTMLHelper::_('select.option', "link", Text::_('JOOMEXT_YES'));
		$titlelink[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Price params
		$priceshow = [];
		$priceshow[] = HTMLHelper::_('select.option', "showprice", Text::_('JOOMEXT_YES'));
		$priceshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Bath params
		$bathshow = [];
		$bathshow[] = HTMLHelper::_('select.option', "bath", Text::_('JOOMEXT_YES'));
		$bathshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Bed params
		$bedshow = [];
		$bedshow[] = HTMLHelper::_('select.option', "bed", Text::_('JOOMEXT_YES'));
		$bedshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Square params
		$squareshow = [];
		$squareshow[] = HTMLHelper::_('select.option', "square", Text::_('JOOMEXT_YES'));
		$squareshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Address params
		$addressshow = [];
		$addressshow[] = HTMLHelper::_('select.option', "address", Text::_('JOOMEXT_YES'));
		$addressshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Category params
		$categoryshow = [];
		$categoryshow[] = HTMLHelper::_('select.option', "category", Text::_('JOOMEXT_YES'));
		$categoryshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		//Show Created params
		$createdshow = [];
		$createdshow[] = HTMLHelper::_('select.option', "showcreated", Text::_('JOOMEXT_YES'));
		$createdshow[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));


		//Author name
		$authorname = [];
		$authorname[] = HTMLHelper::_('select.option', "author", Text::_('JOOMEXT_YES'));
		$authorname[] = HTMLHelper::_('select.option', "0", Text::_('JOOMEXT_NO'));

		$ordering = [];
		$ordering[] = HTMLHelper::_('select.option', "|order:id,DESC", Text::_('ACY_ID'));
		$ordering[] = HTMLHelper::_('select.option', "|order:created,DESC", Text::_('CREATED_DATE'));
		$ordering[] = HTMLHelper::_('select.option', "|order:modified,DESC", Text::_('MODIFIED_DATE'));
		$ordering[] = HTMLHelper::_('select.option', "|order:pro_name,ASC", Text::_('FIELD_TITLE'));
		$ordering[] = HTMLHelper::_('select.option', "|order:rand", Text::_('ACY_RANDOM'));

		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane('osproperty_tab');
		echo $tabs->startPanel(Text::_('TAG_ELEMENTS'), 'osproperty_listings');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td><?php echo Text::_('DISPLAY');?></td>
				<td colspan="2"><?php echo HTMLHelper::_('acyselect.radiolist', $contenttype, 'contenttype', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->contenttype); ?></td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateTag();"';
					echo $jflanguages->display('lang', $pageInfo->lang); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $titlelink, 'titlelink', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
				<td>
					<?php echo Text::_('OWNER'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $authorname, 'author', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->author); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Property Price'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $priceshow, 'showprice', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->showprice);?>
				</td>
				<td>
					<?php echo Text::_('Show Address'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $addressshow, 'address', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->address); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Created Date'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $createdshow, 'showcreated', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->showcreated); ?>
				</td>
				<td>
					<?php echo Text::_('Show Square'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $squareshow, 'square', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->square); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Bath'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $bathshow, 'bath', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->bath); ?>
				</td>
				<td>
					<?php echo Text::_('Show Bed'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $bedshow, 'bed', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->bed); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Category'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $categoryshow, 'category', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->category); ?>
				</td>
				<td valign="top">
					<?php echo Text::_('DISPLAY_PICTURES');?>
				</td>
				<td valign="top"><?php echo HTMLHelper::_('acyselect.radiolist', $picts, 'pict', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
					<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?>><br/><?php echo Text::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;"/>
					× <?php echo Text::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheight" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;"/>
				</span>
				</td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="100%">
					<input placeholder="<?php echo Text::_('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();"/>
					<button class="btn" onclick="this.form.submit();"><?php echo Text::_('JOOMEXT_GO'); ?></button>
					<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo Text::_('JOOMEXT_RESET'); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->_featured($pageInfo->filter_featured); ?>
					<?php echo $this->_propertytypes($pageInfo->filter_type); ?>
					<?php echo $this->_categories($pageInfo->filter_cat); ?>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<thead>
			<tr>
				<th class="title">
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('grid.sort', Text::_('FIELD_TITLE'), 'a.title', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('grid.sort', Text::_('OS_OWNER'), 'b.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('grid.sort', Text::_('OS_PRICE'), 'a.price', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo HTMLHelper::_('grid.sort', Text::_('OS_ADDRESS'), 'a.address', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo HTMLHelper::_('grid.sort', Text::_('OS_PHOTO'), '', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo HTMLHelper::_('grid.sort', Text::_('ACY_ID'), 'a.id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pagination->getListFooter(); ?>
					<?php echo $pagination->getResultsCounter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for($i = 0, $a = count($rows); $i < $a; $i++){
				$row =& $rows[$i];
				?>
				<tr id="content<?php echo $row->id; ?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->id.",'row$k'"?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						$text = '<b>'.Text::_('JOOMEXT_ALIAS').' : </b>'.$row->pro_alias;
						echo acymailing_tooltip($text, $row->pro_name, '', $row->pro_name);
						?>
							<BR />
							<span style="color:#0088CC;font-size:11px;">
							<strong><?php echo Text::_('OS_CATEGORY') ?>: </strong>
							<?php //echo $row->category_name
							$cat_name = OSPHelper::getCategoryNamesOfProperty($row->id);
							$cat_name_arr = explode(" ", $cat_name);
							if (count($cat_name_arr) > 10) {
								$j = 0;
								foreach ($cat_name_arr as $cat) {
									$j++;
									if ($j <= 10) {
										echo $cat . " ";
									}
								}
								echo "...";
							} else {
								echo $cat_name;
							}
							?>
							<BR />
							<strong><?php echo Text::_('OS_TYPE') ?>: </strong><?php echo $row->type_name ?>
							<br/>
							<?php
							echo Text::_('OS_CREATED').": ";
							echo HTMLHelper::_('date', $row->created , 'D, jS F Y');
							if(($row->modified != "") and ($row->modified != "0000-00-00") and ($row->modified != $row->created)){
								echo "<BR />";
								echo Text::_('OS_MODIFIED').": ";
								echo HTMLHelper::_('date', $row->modified , 'D, jS F Y');
							}
							?>
						</span>
					</td>
					<td>
						<?php
						if(!empty($row->agent_name)){
							$text = '<b>'.Text::_('ACY_NAME', true).' : </b>'.$row->agent_name;
							$text .= '<br /><b>'.Text::_('ACY_TYPE', true).' : </b>'.OSPHelper::loadAgentType($row->agent_id);
							echo acymailing_tooltip($text, $row->agent_name, '', $row->agent_name);
						}
						?>
					</td>
					<td>
						<?php
						if($row->price_call == 1){
							echo Text::_('OS_CALL_FOR_PRICE');
						}else{
							echo OSPHelper::generatePrice('',$row->price);
						}
						?>
					</td>
					<td>
						<?php
						echo OSPHelper::generateAddress($row);
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php 
						$db->setQuery("Select * from #__osrs_photos where pro_id = '$row->id' order by ordering limit 1");
						$photo = $db->loadObjectList();
						if (count($photo) > 0) {
							$photo = $photo[0];
							?>
							<a href="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id?>/<?php echo $photo->image?>"
							   class="osmodal">
								<IMG src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $row->id?>/thumb/<?php echo $photo->image?>" class="img-polaroid" style="width:90px; max-width:none !important;" />
							</a>
						<?php
						} else {
							?>
							<img src="<?php echo Uri::root()?>components/com_osproperty/images/assets/noimage.png" style="width:70px;" class="img-polaroid" />
						<?php
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>"/>
		<?php

		echo $tabs->endPanel();
		
		echo $tabs->startPanel(Text::_('Auto generate properties'), 'osproperty_auto');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo Text::_('DISPLAY');?>
				</td>
				<td colspan="2">
					<?php echo HTMLHelper::_('acyselect.radiolist', $contenttype, 'contenttypeauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_type', 'intro'));?>
				</td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateAutoTag();"';
					$jflanguages->id = 'jflangauto';
					echo $jflanguages->display('langauto'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $titlelink, 'titlelinkauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_titlelink', 'link'));?>
				</td>
				<td>
					<?php echo Text::_('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $authorname, 'authorauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$this->params->get('default_author', '0')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Property Price'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $priceshow, 'showprice', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$pageInfo->showprice);?>
				</td>
				<td>
					<?php echo Text::_('Show Address'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $addressshow, 'address', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$pageInfo->address); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Created Date'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $createdshow, 'showcreated', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->showcreated); ?>
				</td>
				<td>
					<?php echo Text::_('Show Square'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $squareshow, 'square', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->square); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Bath'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $bathshow, 'bath', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->bath); ?>
				</td>
				<td>
					<?php echo Text::_('Show Bed'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $bedshow, 'bed', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->bed); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('Show Category'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.radiolist', $categoryshow, 'category', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->category); ?>
				</td>
				<td valign="top"><?php echo Text::_('DISPLAY_PICTURES'); ?></td>
				<td valign="top"><?php echo HTMLHelper::_('acyselect.radiolist', $picts, 'pictauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_pict', '1')); ?>

					<span id="pictsizeauto" <?php if($this->params->get('default_pict', '1') != 'resized') echo 'style="display:none;"'; ?> ><br/><?php echo Text::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidthauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxwidth', '150');?>" style="width:30px;"/>
					× <?php echo Text::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheightauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxheight', '150');?>" style="width:30px;"/>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo Text::_('MAX_ARTICLE'); ?>
				</td>
				<td>
					<input type="text" name="max_article" style="width:50px" value="20" onchange="updateAutoTag();"/>
				</td>
				<td>
					<?php echo Text::_('ACY_ORDER'); ?>
				</td>
				<td>
					<?php echo HTMLHelper::_('acyselect.genericlist', $ordering, 'contentorder', 'size="1" style="width:150px;" onchange="updateAutoTag();"', 'value', 'text', $pageInfo->contentorder); ?>
				</td>
			</tr>
			<?php if($type == 'autonews'){ ?>
				<tr>
					<td>
						<?php echo Text::_('MIN_ARTICLE'); ?>
					</td>
					<td>
						<input type="text" name="min_article" style="width:50px" value="1" onchange="updateAutoTag();"/>
					</td>
					<td>
						<?php echo Text::_('JOOMEXT_FILTER'); ?>
					</td>
					<td>
						<?php $filter = acymailing_get('type.contentfilter');
						$filter->onclick = "updateAutoTag();";
						echo $filter->display('contentfilter', '|filter:created'); ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		<h4><?php echo Text::_('OS_FEATURED');?></h4>
		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php $k = 0;
			foreach($featuredarray as $oneCat){
				if(empty($oneCat->value)) continue;
				?>
				<tr id="featured<?php echo $oneCat->value ?>" class="<?php echo "row$k"; ?>" onclick="applyFeaturedAuto('<?php echo $oneCat->value ?>','<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->text;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<h4><?php echo Text::_('OS_CATEGORIES');?></h4>
		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php $k = 0;
			foreach($this->catvalues as $oneCat){
				if(empty($oneCat->value)) continue;
				?>
				<tr id="cat<?php echo $oneCat->value ?>" class="<?php echo "row$k"; ?>" onclick="applyAuto(<?php echo $oneCat->value ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->text;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<h4><?php echo Text::_('OS_PROPERTY_TYPE');?></h4>
		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php $k = 0;
			foreach($this->typevalues as $oneCat){
				if(empty($oneCat->value)) continue;
				?>
				<tr id="type<?php echo $oneCat->value ?>" class="<?php echo "row$k"; ?>" onclick="applyTypeAuto(<?php echo $oneCat->value ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->text;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
	}

	private function _categories($filter_cat){
		//select all cats
		$db = Factory::getDBO();
		$db->setQuery('SELECT id,category_alias,category_name,parent_id FROM `#__osrs_categories` WHERE published = 1 ORDER BY `ordering` ASC');
		$mosetCats = $db->loadObjectList();
		$this->cats = [];
		foreach($mosetCats as $oneCat){
			$this->cats[$oneCat->parent_id][] = $oneCat;
		}

		$this->catvalues = [];
		$this->catvalues[] = HTMLHelper::_('select.option', 0, Text::_('OS_SELECT_CATEGORY'));
		$this->_handleChildrens();
		return HTMLHelper::_('select.genericlist', $this->catvalues, 'filter_cat', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$filter_cat);
	}

	private function _handleChildrens($parentId = 0, $level = 0){
		if(!empty($this->cats[$parentId])){
			foreach($this->cats[$parentId] as $cat){
				//$cat->name = str_repeat($this->separator,$level).$cat->cat_name;
				$this->catvalues[] = HTMLHelper::_('select.option', $cat->id, str_repeat(" - - ", $level).$cat->category_name);
				$this->_handleChildrens($cat->id, $level + 1);
			}
		}
	}

	private function _featured($filter_featured){
		$this->typevalues = [];
		$this->typevalues[] = HTMLHelper::_('select.option', 0, Text::_('All Status'));
		$this->typevalues[] = HTMLHelper::_('select.option', 1, Text::_('Show only Featured properties'));
		$this->typevalues[] = HTMLHelper::_('select.option', 2, Text::_('Show only Non-Featured properties'));
		return HTMLHelper::_('select.genericlist', $this->typevalues, 'filter_featured', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$filter_featured);
	}

	private function _propertytypes($filter_type){
		//select all cats
		$db = Factory::getDBO();
		$db->setQuery('SELECT id as value,type_name as text FROM `#__osrs_types` WHERE published = 1 ORDER BY `ordering` ASC');
		$mosetTypes = $db->loadObjectList();
		
		$this->typevalues = [];
		$this->typevalues[] = HTMLHelper::_('select.option', 0, Text::_('OS_ALL_PROPERTY_TYPES'));
		$this->typevalues = array_merge($this->typevalues,$mosetTypes);
		return HTMLHelper::_('select.genericlist', $this->typevalues, 'filter_type', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$filter_type);
	}


	public function acymailing_replacetags(&$email, $send = true){
		$this->_replaceAuto($email);
		$this->_replaceOne($email);
	}

	private function _replaceOne(&$email){
		$lang = Factory::getLanguage();
		$lang->load('com_osproperty', JPATH_ADMINISTRATOR);
		$match = '#{osp:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything...
		if(!$found) return;

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$db = Factory::getDBO();
			$db->setQuery('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.$db->quote($email->language).' LIMIT 1');
			$this->newslanguage = $db->loadObject();
		}

		//we set the current catid so it can work with several Newsletters...
		$this->currentcatid = -1;
		//Set the read more link:
		$this->readmore = '';

		//Load the K2 model file
		require_once(JPATH_ROOT.'/components/com_osproperty/helpers/helper.php');
		require_once(JPATH_ROOT.'/components/com_osproperty/helpers/common.php');
		require_once(JPATH_ROOT.'/components/com_osproperty/helpers/route.php');

		//We will need the mailer class as well
		$this->mailerHelper = acymailing_get('helper.mailer');

		$htmlreplace = [];
		$textreplace = [];
		$subjectreplace = [];

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				//Don't need to process twice a tag we already have!
				if(isset($htmlreplace[$oneTag])) continue;

				$content = $this->_replaceContent($allresults, $i);
				$htmlreplace[$oneTag] = $content;
				$textreplace[$oneTag] = $this->mailerHelper->textVersion($content, true);
				$subjectreplace[$oneTag] = strip_tags($content);
			}
		}

		$email->body = str_replace(array_keys($htmlreplace), $htmlreplace, $email->body);
		$email->altbody = str_replace(array_keys($textreplace), $textreplace, $email->altbody);
		$email->subject = str_replace(array_keys($subjectreplace), $subjectreplace, $email->subject);
	}

	private function _replaceContent(&$results, $i){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$app = Factory::getApplication();
		$db = Factory::getDBO();
		$configClass = OSPHelper::loadConfig();
		//1 : Transform the tag properly...
		$arguments = explode('|', strip_tags($results[1][$i]));
		$tag = new stdClass();
		$tag->id = (int)$arguments[0];
		$tag->itemid = (int)$this->params->get('itemid');
		$tag->wrap = (int)$this->params->get('wordwrap', 0);
		for($i = 1, $a = count($arguments); $i < $a; $i++){
			$args = explode(':', $arguments[$i]);
			$arg0 = trim($args[0]);
			if(empty($arg0)) continue;
			if(isset($args[1])){
				$tag->$arg0 = $args[1];
			}else{
				$tag->$arg0 = true;
			}
		}

		//We used to call it "images" but to make it consistent with the joomlacontent plugin, we rename it into pict.
		if(isset($tag->images) && !isset($tag->pict)) $tag->pict = $tag->images;

		//2 : Load the Joomla article... with the author, the section and the categories to create nice links

		//We just loaded the article but we may need to translate it depending on tag->lang...
		if(empty($tag->lang) && !empty($this->newslanguage) && !empty($this->newslanguage->lang_code)) $tag->lang = $this->newslanguage->lang_code.','.$this->newslanguage->lang_id;

		$acypluginsHelper->translateItem($article, $tag, 'osproperty');
		$suffix = "";
		if(!empty($tag->lang)){
			//We will load the correct article in the jf tables
			$langid = (int)substr($tag->lang, strpos($tag->lang, ',') + 1);
			$suffix = "_".substr($tag->lang,0,2);
		}

		$query = 'SELECT a.*, b.name as author_name, b.alias as author_alias,c.type_name'.$suffix.' FROM `#__osrs_properties` as a inner join #__osrs_agents as b on b.id = a.agent_id inner join #__osrs_types as c on c.id = a.pro_type ';
		$query .= ' WHERE a.id = '.intval($tag->id).' LIMIT 1';

		$db->setQuery($query);
		$property = $db->loadObject();

		$result = '';

		//In case of we could not load the article for any reason...
		if(empty($property)){
			if($app->isAdmin()) $app->enqueueMessage('The property "'.$tag->id.'" could not be loaded', 'notice');
			return $result;
		}

		$varFields = [];
		foreach($property as $fieldName => $oneField){
			$varFields['{'.$fieldName.'}'] = $oneField;
		}

		//When we load an artice, we may have a wrong content... we clean it.
		$property_small_desc = OSPHelper::getLanguageFieldValueBackend($property,'pro_small_desc',$suffix);
		$property_full_desc  = OSPHelper::getLanguageFieldValueBackend($property,'property_full_desc',$suffix);
		$acypluginsHelper->cleanHtml($property_small_desc);
		$acypluginsHelper->cleanHtml($property_full_desc);

		//get the link
		$needs		= [];
		$needs[]	= "property_details";
		$needs[]	= $property->id;
		$itemid		= OSPRoute::getItemid($needs);
		$link		= "index.php?option=com_osproperty&task=property_details&id=".$property->id."&Itemid=".$itemid;

		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		//Add the title with a link or not on it.
		//If we add a link, we add in the same time a name="content-CONTENTID" so that we will be able to parse the content to create a nice summary
		$styleTitle = '';
		$styleTitleEnd = '';
		if($tag->type != "title"){
			$styleTitle = '<h2 class="acymailing_title" style="font-size:20px;font-weight:bold;">';
			$styleTitleEnd = '</h2>';
		}

		$resultTitle = '';

		if(empty($tag->notitle)){
			if(!empty($tag->link)){
				$resultTitle = '<a href="'.$link.'" ';
				if($tag->type != "title") $resultTitle .= 'style="text-decoration:none;font-style:normal !important;" name="osproperty-'.$property->id.'" ';
				$resultTitle .= 'target="_blank">'.OSPHelper::getLanguageFieldValueBackend($property,'pro_name',$suffix).'</a>';
			}else{
				$resultTitle = OSPHelper::getLanguageFieldValueBackend($property,'pro_name',$suffix);
			}
			$resultTitle = $styleTitle.$resultTitle.$styleTitleEnd;
		}

		//show property price
		if((!empty($tag->showprice)) && ($property->price > 0))
		{
			$price = "";
			$price = '<span class="price" style="font-size:20px;font-weight:bold;color:#821220;font-family:Arial;">'.OSPHelper::generatePrice($property->curr,$property->price);
			if($property->rent_time != ""){
				$price . " / ".Text::_($property->rent_time);
			}

			$price .= '</span>&nbsp;&nbsp;<span class="property_type" style="font-size:15px;font-weight:bold;color:#128234;">'.OSPHelper::getLanguageFieldValueBackend($property,'type_name',$suffix).'</span>';
			if(($configClass['active_market_status'] == 1) && ($property->isSold)){
				$price .= '</span>&nbsp;&nbsp;<span class="property_market_status" style="font-size:15px;font-weight:bold;color:#d12121;">['.OSPHelper::returnMarketStatus($property->isSold).']</span>';
			}
			$price .= "<BR />";
			$result .= $price;

			$varFields['{price}'] = $price;
		}

		if(!empty($tag->showcreated)){
			$dateFormat = empty($tag->dateformat) ? Text::_('DATE_FORMAT_LC2') : $tag->dateformat;
			$result .= '<span class="createddate" style="font-size:11px;color:#AAA;font-family:Arial;">'.HTMLHelper::_('date', $property->created, $dateFormat).'</span><br />';
			$varFields['{created}'] = HTMLHelper::_('date', $property->created, $dateFormat);
		}

		if(!empty($tag->address)){
			$result .= '<SPAN class="propertyaddress">'.OSPHelper::generateAddress($property).'</SPAN><BR />';
		}

		//Add the author...
		if(!empty($tag->author)){
			$authorName = empty($property->author_alias) ? $property->author_alias : $property->author_name;
			if($tag->type == 'title') $result .= '<br />';
			$result .= '<span class="acymailing_authorname" style="font-weight:bold;">'.Text::_('OS_OWNER').': '.$authorName.'</span><br />';
			$varFields['{author}'] = $authorName;
		}

		$addtionalArr = [];
		if(!empty($tag->bath) and ($property->bed_room > 0)){
			$addtionalArr[] = $property->bed_room." bd";
		}
		if(!empty($tag->bath) and ($property->bath_room > 0)){
			$addtionalArr[] = OSPHelper::showBath($property->bath_room)." ba";
		}
		if(!empty($tag->square) and ($property->square_feet > 0)){
			$addtionalArr[] = OSPHelper::showSquare($property->square_feet)." ".OSPHelper::showSquareSymbol();
		}
		if(count($addtionalArr) > 0){
			$result .= '<SPAN class="additional_information" style="padding-top:10px;">'.implode(" | ",$addtionalArr).'</SPAN><BR />';
		}

		//We add the intro text
		$desc = "";
		if($tag->type != "title"){

			if($tag->type == "intro"){
				$forceReadMore = false;
				$property->introtext = $acypluginsHelper->wrapText($property_small_desc, $tag);
				if(!empty($acypluginsHelper->wraped)) $forceReadMore = true;
			}
			if(empty($property_full_desc) OR $tag->type != "text"){
				$desc .= $property_small_desc;
			}

			//We add the full text
			if($tag->type == "intro"){
				//We add the read more link but only if we have a fulltext after...
				if(empty($tag->noreadmore) && (!empty($article->fulltext) OR $forceReadMore)){
					$readMoreText = empty($tag->readmore) ? $this->readmore : $tag->readmore;
					//$desc .= '<a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$readMoreText.'</span></a>';
				}
			}elseif(!empty($property_small_desc)){
				if($tag->type != "text" && !empty($article->introtext) && !preg_match('#^<[div|p]#i', trim($article->fulltext))) $result .= '<br />';
				$desc .= $property_small_desc;
			}
			
			if(!empty($tag->showprice)){
				$desc .= '<BR /><SPAN class="cattitle" style="font-weight:bold;">'.Text::_('OS_CATEGORIES').': '.OSPHelper::getCategoryNamesOfPropertyWithLinks($property->id).'</SPAN><BR />';
			}

			$desc .= '<BR /><a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore" style="font-style:normal;padding:10px;font-family:Arial;background-color:#0DBAE8;">Read More</span></a>';

			$db->setQuery("Select * from #__osrs_photos where pro_id = '$property->id' order by ordering limit 1");
			$photo = $db->loadObjectList();
			if (count($photo) > 0) {
				$photo = $photo[0];
				if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$property->id.'/thumb/'.$photo->image)){
					$imagePath = Uri::root().'/images/osproperty/properties/'.$property->id.'/thumb/'.$photo->image;

					if(empty($imagePath) || !empty($tag->removemainpict)){
						$result = $resultTitle.$result;
					}else{
						$varFields['{imagePath}'] = $imagePath;
						$imageLink = '<img src="'.$imagePath.'" alt="'.$article->image_caption.'" />';
						if(!empty($tag->link)) $imageLink = '<a href="'.$link.'" target="_blank" style="text-decoration:none" >'.$imageLink.'</a>';
						$varFields['{imageLink}'] = $imageLink;
						$result = $resultTitle.'<table cellspacing="5" cellpadding="0" border="0" ><tr><td valign="top" width="35%">'.$imageLink.'</td><td valign="top" width="65%">'.$result.'</td></tr><tr><td valign="top" colspan="2">'.$desc.'</td></tr></table>';
					}
				}
			} 
			$result = '<div class="acymailing_content" style="clear:both;font-family:Arial;padding-bottom:10px;">'.$result.'</div>';
		}else{
			$result = $resultTitle.$result;
		}

		//Add the cat title...
		//if(!empty($tag->cattitle)){
			
		//}

		if(file_exists(ACYMAILING_MEDIA.'plugins/osproperty.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins/osproperty.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields), $varFields, $result);
		}

		$result = $acypluginsHelper->removeJS($result);

		//We have our content... lets check the pictures options
		if(isset($tag->pict)){
			$pictureHelper = acymailing_get('helper.acypict');
			if($tag->pict == '0'){
				$result = $pictureHelper->removePictures($result);
			}elseif($tag->pict == 'resized'){
				$pictureHelper->maxHeight = empty($tag->maxheight) ? $this->params->get('maxheight', 150) : $tag->maxheight;
				$pictureHelper->maxWidth = empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth;
				if($pictureHelper->available()){
					$result = $pictureHelper->resizePictures($result);
				}elseif($app->isAdmin()){
					$app->enqueueMessage($pictureHelper->error, 'notice');
				}
			}
		}

		return $result;
	}

	private function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags), $this->tags, $email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($this->tags), $this->tags, $email->altbody);
			foreach($this->tags as $tag => $result){
				$email->subject = str_replace($tag, strip_tags(preg_replace('#</tr>[^<]*<tr[^>]*>#Uis', ' | ', $result)), $email->subject);
			}
		}
	}

	public function acymailing_generateautonews(&$email){

		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		$time = time();


		//Check if we should generate the SmartNewsletter or not...
		$match = '#{autoosp:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything... so we won't try to stop the generation
		if(!$found) return $return;

		$this->tags = [];
		$db = Factory::getDBO();

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($this->tags[$oneTag])) continue;

				$arguments = explode('|', strip_tags($allresults[1][$i]));
				//The first argument is a list of sections and cats...
				$allcats = explode('-', $arguments[0]);
				
				$alltypes = $arguments[1];
				$alltypes = str_replace("TYPE:","",$alltypes);
				$alltypes = explode('-', $alltypes);

				$featured = $arguments[2];
				$featured = str_replace("FEATURED:","",$featured);
				$featured = str_replace("-","",$featured);
				$featured = trim($featured);

				$parameter = new stdClass();
				for($i = 2; $i < count($arguments); $i++){
					$args = explode(':', $arguments[$i]);
					$arg0 = trim($args[0]);
					if(empty($arg0)) continue;
					if(isset($args[1])){
						$parameter->$arg0 = $args[1];
					}else{
						$parameter->$arg0 = true;
					}
				}
				//Load the articles based on all arguments...
				$selectedArea = [];
				foreach($allcats as $oneCat){
					if(empty($oneCat)) continue;
					$selectedArea[] = (int)$oneCat;
				}

				$query = 'SELECT a.id FROM `#__osrs_properties` as a';
				$where = [];

				if(!empty($selectedArea)){
					$where[] = ' a.id IN (Select pid from #__osrs_property_categories where category_id in ('.implode(',', $selectedArea).'))';
				}

				if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate'])){
					$condition = '`publish_up` > \''.date('Y-m-d', $email->params['lastgenerateddate'] - date('Z')).'\'';
					// We need acy_created, the hour is not stored by K2 in the created date field
					$condition .= ' OR `acy_created` > DATE_FORMAT(CURRENT_TIMESTAMP()-SEC_TO_TIME('.intval(time() - $email->params['lastgenerateddate']).'), \'%Y-%m-%d %H:%i:%s\')';
					if($parameter->filter == 'modify'){
						$condition .= ' OR `modified` > \''.date('Y-m-d', $email->params['lastgenerateddate'] - date('Z')).'\'';
					}

					$where[] = $condition;
				}

				/*
				if(!empty($parameter->featured)){
					$where[] = '`featured` = 1';
				}elseif(!empty($parameter->nofeatured)){
					$where[] = '`featured` = 0';
				}
				*/
				if($featured == "featured"){
					$where[] = '`isFeatured` = 1';
				}

				$where[] = '`publish_up` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\'';
				if(empty($parameter->nopublished)) $where[] = '`published` = 1';

				//Handle a date range in the query
				if(!empty($parameter->maxcreated)){
					$date = strtotime($parameter->maxcreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->maxcreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = '`created` < '.$db->Quote(date('Y-m-d H:i:s', $date));
				}

				if(!empty($parameter->mincreated)){
					$date = strtotime($parameter->mincreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->mincreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = '`created` > '.$db->Quote(date('Y-m-d H:i:s', $date));
				}

				//Access for J1.5.0 only
				if(!ACYMAILING_J16){
					if(isset($parameter->access)){
						$where[] = 'access <= '.intval($parameter->access);
					}else{
						if($this->params->get('contentaccess', 'registered') == 'registered'){
							$where[] = 'access <= 1';
						}elseif($this->params->get('contentaccess', 'registered') == 'public') $where[] = 'access = 0';
					}
				}elseif(isset($parameter->access)){
					//We set it only if the access is defined in the tag
					$where[] = 'access = '.intval($parameter->access);
				}

				$query .= ' WHERE ('.implode(') AND (', $where).')';

				if(!empty($parameter->order)){
					if($parameter->order == 'rand'){
						$query .= ' ORDER BY rand()';
					}else{
						$ordering = explode(',', $parameter->order);
						$query .= ' ORDER BY `'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
					}
				}

				$start = '';
				if(!empty($parameter->start)) $start = intval($parameter->start).',';
				if(empty($parameter->max)) $parameter->max = 100;
				//We add a limit for the preview otherwise we could break everything
				$query .= ' LIMIT '.$start.(int)$parameter->max;

				$db->setQuery($query);
				$allArticles = acymailing_loadResultArray($db);

				if(!empty($parameter->min) && count($allArticles) < $parameter->min){
					//We won't generate the Newsletter
					$return->status = false;
					$return->message = 'Not enough k2 content for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min.' between '.acymailing_getDate($email->params['lastgenerateddate']).' and '.acymailing_getDate($time);
				}

				$stringTag = empty($parameter->noentrytext) ? '' : $parameter->noentrytext;
				if(!empty($allArticles)){
					//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
					$arrayElements = [];
					unset($parameter->id);
					$numArticle = 1;
					foreach($allArticles as $oneArticleId){
						$args = [];
						$args[] = 'osp:'.$oneArticleId;
						foreach($parameter as $oneParam => $val){
							if(is_bool($val)){
								$args[] = $oneParam;
							}else $args[] = $oneParam.':'.$val;
						}
						$arrayElements[] = '{'.implode('|', $args).'}';
					}
					$stringTag = $this->acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}

				$this->tags[$oneTag] = $stringTag;
			}
		}

		return $return;
	}
}//endclass