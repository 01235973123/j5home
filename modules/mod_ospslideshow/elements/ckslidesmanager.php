<?php

use Joomla\CMS\Language\Text;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
/**
 * @copyright	Copyright (C) 2016 joomdonation.com. All Rights Reserved.
 * http://www.joomdonation.com
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die('Restricted access');

Text::script('MOD_SLIDESHOWCK_ADDSLIDE');
Text::script('MOD_SLIDESHOWCK_SELECTIMAGE');
Text::script('MOD_SLIDESHOWCK_CAPTION');
Text::script('MOD_SLIDESHOWCK_USETOSHOW');
Text::script('MOD_SLIDESHOWCK_IMAGE');
Text::script('MOD_SLIDESHOWCK_VIDEO');
Text::script('MOD_SLIDESHOWCK_IMAGEOPTIONS');
Text::script('MOD_SLIDESHOWCK_LINKOPTIONS');
Text::script('MOD_SLIDESHOWCK_VIDEOOPTIONS');
Text::script('MOD_SLIDESHOWCK_ALIGNEMENT_LABEL');
Text::script('MOD_SLIDESHOWCK_TOPLEFT');
Text::script('MOD_SLIDESHOWCK_TOPCENTER');
Text::script('MOD_SLIDESHOWCK_TOPRIGHT');
Text::script('MOD_SLIDESHOWCK_MIDDLELEFT');
Text::script('MOD_SLIDESHOWCK_CENTER');
Text::script('MOD_SLIDESHOWCK_MIDDLERIGHT');
Text::script('MOD_SLIDESHOWCK_BOTTOMLEFT');
Text::script('MOD_SLIDESHOWCK_BOTTOMCENTER');
Text::script('MOD_SLIDESHOWCK_BOTTOMRIGHT');
Text::script('MOD_SLIDESHOWCK_LINK');
Text::script('MOD_SLIDESHOWCK_TARGET');
Text::script('MOD_SLIDESHOWCK_SAMEWINDOW');
Text::script('MOD_SLIDESHOWCK_NEWWINDOW');
Text::script('MOD_SLIDESHOWCK_VIDEOURL');
Text::script('MOD_SLIDESHOWCK_REMOVE');
Text::script('MOD_SLIDESHOWCK_IMPORTFROMFOLDER');
Text::script('MOD_SLIDESHOWCK_ARTICLEOPTIONS');
Text::script('MOD_SLIDESHOWCK_SLIDETIME');
Text::script('MOD_SLIDESHOWCK_CLEAR');
Text::script('MOD_SLIDESHOWCK_SELECT');
Text::script('MOD_SLIDESHOWCK_TITLE');
Text::script('MOD_SLIDESHOWCK_STARTDATE');
Text::script('MOD_SLIDESHOWCK_ENDDATE');

class JFormFieldCkslidesmanager extends FormField {

	protected $type = 'ckslidesmanager';

	protected function getInput() {

		$document = Factory::getDocument();
		$document->addScriptDeclaration("JURI='" . JURI::root() . "';");
		$path = 'modules/mod_slideshowck/elements/ckslidesmanager/';
		HTMLHelper::_('jquery.framework');
		HTMLHelper::_('jquery.ui', array('core', 'sortable'));
		JHTML::_('behavior.modal');
		JHTML::_('script', 'modules/mod_slideshowck/elements/assets/jquery-ui.min.js');
		
		JHTML::_('script', $path . 'ckslidesmanager.js');
		JHTML::_('script', $path . 'FancySortable.js');
		JHTML::_('stylesheet', 'modules/mod_slideshowck/elements/assets/jquery-ui.min.css');
		JHTML::_('stylesheet', $path . 'ckslidesmanager.css');

		$html = '<input name="' . $this->name . '" id="ckslides" type="hidden" value="' . $this->value . '" />'
				. '<input name="ckaddslide" id="ckaddslide" type="button" value="' . Text::_('MOD_SLIDESHOWCK_ADDSLIDE') . '" onclick="javascript:addslideck();"/>'
				//. '<input name="ckaddslidesfromfolder" id="ckaddslidesfromfolder" type="button" value="' . JText::_('MOD_SLIDESHOWCK_ADDSLIDESFROMFOLDER') . '" onclick="javascript:addslidesfromfolderck($(\'ckfoldername\').value);"/>'
				//. '<input name="ckfoldername" id="ckfoldername" value="modules/mod_slideshowck/slides" onclick=""/>'
				//.'<input name="ckaddfromfolder" id="ckaddfromfolder" type="button" value="Import from a folder" onclick="javascript:addfromfolderck();"/>'
				//.'<input name="ckstoreslide" id="ckstoreslide" type="button" value="Save" onclick="javascript:storeslideck();"/>'
				. '<ul id="ckslideslist" style="clear:both;"></ul>'
//				.'<p>Date: <input type="text" id="datepicker"></p>'
				. '<input name="ckaddslide" id="ckaddslide" type="button" value="' . Text::_('MOD_SLIDESHOWCK_ADDSLIDE') . '" onclick="javascript:addslideck();"/>';

		return $html;
	}

	protected function getPathToImages() {
		$localpath = dirname(__FILE__);
		$rootpath = JPATH_ROOT;
		$httppath = trim(JURI::root(), "/");
		$pathtoimages = str_replace("\\", "/", str_replace($rootpath, $httppath, $localpath));
		return $pathtoimages;
	}

	protected function getLabel() {

		return '';
	}

	protected function getArticlesList() {
		$db = & Factory::getDBO();

		$query = "SELECT id, title FROM #__content WHERE state = 1 LIMIT 2;";
		$db->setQuery($query);
		$row = $db->loadObjectList('id');
		var_dump($row);
		return json_encode($row);
	}

}

