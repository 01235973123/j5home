<?php
/**
 * @copyright	Copyright (C) 2024 joomdonation.com. All Rights Reserved.
 * http://www.joomdonation.com
 * OSP Slideshow
 * @license		GNU/GPL
 * */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Filesystem\Folder;

$com_path = JPATH_SITE . '/components/com_osproperty/';
require_once $com_path . 'router.php';
require_once $com_path . 'helpers/route.php';
//JModelLegacy::addIncludePath($com_path . '/models', 'ContentModel');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class modOspslideshowHelper {

	private static $slideshowparams;
	/**
	 * Get a list of the items.
	 *
	 * @param	JRegistry	$params	The module options.
	 *
	 * @return	array
	 */
	static function getItems(&$params) {
		// Initialise variables.
		self::$slideshowparams = $params;
		$db = Factory::getDbo();
		$document = Factory::getDocument();

		//get list of properties
		$properties_ids = $params->get('properties_ids','');
		$categories_ids = $params->get('categories_ids','');
		$types_ids		= $params->get('types_ids','');
		$cities_ids		= $params->get('cities_ids','');
		$states_ids		= $params->get('states_ids','');
		$countries_ids  = $params->get('countries_ids','');
		$show_featured  = $params->get('show_featured',0);
		$show_sold		= $params->get('show_sold',0);
		$ordering		= $params->get('ordering','created-asc');
		$limit_items	= $params->get('limit_items',5);
		$items			= self::findProperties($properties_ids,$categories_ids,$types_ids,$cities_ids,$states_ids,$countries_ids,$show_featured,$show_sold,$ordering,$limit_items);

		// load the libraries
		foreach ($items as $i => $item) 
		{
			if (!$item->imgname) 
			{
				unset($items[$i]);
				continue;
			}

			$item->article = null;

			// create new images for mobile
			if ($params->get('usemobileimage', '0')) 
			{ 
				$resolutions = explode(',', $params->get('mobileimageresolution', '640'));
				foreach ($resolutions as $resolution) 
				{
					self::resizeImage($item->imgname, (int)$resolution, '', (int)$resolution, '');
				}
			}

			if (stristr($item->imgname, "http")) {
				$item->imgthumb = $item->imgname;
			} else {
				// renomme le fichier
				$thumbext = explode(".", $item->imgname);
				$thumbext = end($thumbext);
				// crée la miniature
				if ($params->get('thumbnails', '1') == '1' && $params->get('autocreatethumbs','1')) {
					$item->imgthumb = JURI::base(true) . '/' . self::resizeImage($item->imgname, $params->get('thumbnailwidth', '182'), $params->get('thumbnailheight', '187'));
				} else {
					$thumbfile = str_replace(File::getName($item->imgname), "th/" . File::getName($item->imgname), $item->imgname);
					$thumbfile = str_replace("." . $thumbext, "_th." . $thumbext, $thumbfile);
					$item->imgthumb = JURI::base(true) . '/' . $thumbfile;
				}
				$item->imgname = JURI::base(true) . '/' . $item->imgname;
			}

			// manage the title and description
			$item->imgcaption = '<div class="camera_caption_title">' . $item->title . '</div><div class="camera_caption_desc">' . $item->desc . '</div>';
			
			// route the url
			if (strcasecmp(substr($item->imglink, 0, 4), 'http') && (strpos($item->imglink, 'index.php?') !== false)) {
				$item->imglink = Route::_($item->imglink, true, false);
			} else {
				$item->imglink = Route::_($item->imglink);
			}
			
			if (!isset($item->imgtitle)) $item->imgtitle = '';
		}

		return $items;
	}

	static function findProperties($properties_ids,$categories_ids,$types_ids,$cities_ids,$states_ids,$countries_ids,$show_featured,$show_sold,$ordering,$limit_items){
		$db = Factory::getDbo();
		$lang_suffix = self::getLanguageSuffix();
		$query = "SELECT a.*,c.type_name$lang_suffix as type_name,d.country_name,e.state_name,q.name as agent_name FROM #__osrs_properties AS a "
			." INNER JOIN #__osrs_types AS c ON c.id = a.pro_type"
			." INNER JOIN #__osrs_countries AS d ON  d.id = a.country"
			." LEFT JOIN #__osrs_states AS e ON e.id = a.state"
			." INNER JOIN #__osrs_agents AS q ON q.id = a.agent_id"
			." WHERE a.approved = '1' and a.published = '1' ";
		if($properties_ids != ""){
			$query .= " AND a.id in ($properties_ids)";
		}
		if($categories_ids != ""){
			$query .= " AND a.id in (Select pid from #__osrs_property_categories where category_id in ($categories_ids))";
		}
		if($types_ids != ""){
			$query .= " AND a.pro_type in ($types_ids)";
		}
		if($cities_ids != ""){
			$query .= " AND a.city in ($cities_ids)";
		}
		if($states_ids != ""){
			$query .= " AND a.state in ($states_ids)";
		}
		if($countries_ids != ""){
			$query .= " AND a.country in ($countries_ids)";
		}
		if($show_featured == 1){
			$query .= " AND a.isFeatured = '1'";
		}
		if($show_sold == 1){
			$query .= " AND a.isSold = '1'";
		}
		$query .= " Group by a.id ";
		switch ($ordering){
			case "id-asc":
				$query .= " ORDER BY a.id";
			break;
			case "id-desc":
				$query .= " ORDER BY a.id DESC";
			break;
			case "hits-asc":
				$query .= " ORDER BY a.hits";
			break;
			case "hit-desc":
				$query .= " ORDER BY a.hits DESC";
			break;
			case "rand-":
				$query .= " ORDER BY rand()";
			break;
			case "created-desc":
				$query .= " ORDER BY a.created DESC";
			break;
			case "created-asc":
				$query .= " ORDER BY a.created";
			break;
			case "isFeatured-desc":
				$query .= " ORDER BY a.isFeatured desc";
			break;
			case "isFeatured-asc":
				$query .= " ORDER BY a.isFeatured";
			break;
		}
		if(intval($limit_items) > 0){
			$query .= " LIMIT ".intval($limit_items);
		}
		$db->setQuery($query);
		$items = $db->loadObjectList();
		if(count($items) > 0){
			foreach($items as $item){
				$db->setQuery("Select image from #__osrs_photos where pro_id = '$item->id' order by ordering");
				$image = $db->loadResult();
				if($image != ""){
					if(file_exists(JPATH_ROOT.'/images/osproperty/properties/'.$item->id.'/'.$image)){
						$item->imgname = Uri::root().'images/osproperty/properties/'.$item->id.'/'.$image;
					}
				}
				$needs = array();
				$needs[] = "property_details";
				$needs[] = $item->id;
				$itemid  = OSPRoute::getItemid($needs);
				$item->imglink = Route::_('index.php?option=com_osproperty&task=property_details&id='.$item->id.'&Itemid='.$itemid);

				$item->title = OSPHelper::getLanguageFieldValue($item,'pro_name');
				$item->desc  = OSPHelper::getLanguageFieldValue($item,'pro_small_desc');

				$price = "";
				if(OSPHelper::getLanguageFieldValue($item,'price_text') != "")
				{
					$price .= " <span class='camera_price'>". OSPHelper::showPriceText(OSPHelper::getLanguageFieldValue($item,'price_text') )."</span>";
				}
				elseif($item->price_call == 0)
				{
					if($item->price > 0){
						$price .= " <span class='camera_price'>".OSPHelper::generatePrice($item->curr,$item->price);
						if($item->rent_time != "")
						{
							$price .= "/".Text::_($item->rent_time);
						}
						$price .= "</span>";
					}
				}
				elseif($item->price_call == 1)
				{
					  $price .= " <span class='camera_price'>".Text::_('MOD_OSPSLIDESHOW_CALL_FOR_PRICE')."</span>";
				}
				$price			.= " <span class='property-status-tag'>".$item->type_name."</span>";
				$item->desc		= $price."<div class='clearfix'></div><div class='property-desc'>".strip_tags($item->desc)."</div>";
				$item->desc		.= "<div class='clearfix'></div>";
				$item->desc		.= "<a title='".$item->title."' href='".$item->imglink."' class='btn propertydetailsbtn'>".Text::_('MOD_OSPSLIDESHOW_MORE_DETAILS')."&nbsp;<i class='fa fa-angle-right'></i></a>";
			}
		}
		return $items;
	}

	public static function getLanguageSuffix(){
		$languages = OSPHelper::getLanguages();
		$translatable = Multilanguage::isEnabled() && count($languages);
		if($translatable){
			$lang_suffix = OSPHelper::getFieldSuffix();
		}
		return $lang_suffix;
	}

	/**
	 * Create the list of all modules published as Object
	 *
	 * $file string the image path
	 * $x integer the new image width
	 * $y integer the new image height
	 *
	 * @return Boolean True on Success
	 */
	static function resizeImage($file, $x, $y = '', $thumbpath = 'th', $thumbsuffix = '_th') {

		if (!$file)
			return;

		$params = self::$slideshowparams;
		if (!$params->get('autocreatethumbs','1'))
			return;
			
		$thumbext = explode(".", $file);
		$thumbext = end($thumbext);
		$thumbfile = str_replace(File::getName($file), $thumbpath . "/" . File::getName($file), $file);
		$thumbfile = str_replace("." . $thumbext, $thumbsuffix . "." . $thumbext, $thumbfile);
		
		$filetmp = JPATH_ROOT . '/' . $file;
		$filetmp = str_replace("%20", " ", $filetmp);
		if (!Jfile::exists($filetmp))
			return;
		$size = getimagesize($filetmp);

		if ($size[0] > $size[1]) // paysage
		{
			$y = $x * $size[1] / $size[0];
		} else 
		{
//			$tmpx = $x;
//			$x = $y;
//			$y = $tmpx * $size[0] / $size[1];
			$x = $y * $size[0] / $size[1];
		}

		
		if ($size) {
			if (File::exists($thumbfile)) {
				return $thumbfile;
				// $thumbsize = getimagesize(JPATH_ROOT . '/' . $thumbfile);
				// if ($thumbsize[0] == $x || $thumbsuffix == '') {
					// return $thumbfile;
				// }
			}

			$thumbfolder = str_replace(File::getName($file), $thumbpath . "/", $filetmp);
			if (!Folder::exists($thumbfolder)) { 
				Folder::create($thumbfolder);
				File::copy(JPATH_ROOT . '/modules/mod_slideshowck/index.html', $thumbfolder . 'index.html' );
			}

			if ($size['mime'] == 'image/jpeg') {
				$img_big = imagecreatefromjpeg($filetmp); # On ouvre l'image d'origine
				$img_new = imagecreate($x, $y);
				# création de la miniature
				$img_mini = imagecreatetruecolor($x, $y) or $img_mini = imagecreate($x, $y);
				// copie de l'image, avec le redimensionnement.
				imagecopyresized($img_mini, $img_big, 0, 0, 0, 0, $x, $y, $size[0], $size[1]);

				imagejpeg($img_mini, JPATH_ROOT . '/' . $thumbfile);
			} elseif ($size['mime'] == 'image/png') {
				$img_big = imagecreatefrompng($filetmp); # On ouvre l'image d'origine
				$img_new = imagecreate($x, $y);
				# création de la miniature
				$img_mini = imagecreatetruecolor($x, $y) or $img_mini = imagecreate($x, $y);
				// copie de l'image, avec le redimensionnement.
				imagecopyresized($img_mini, $img_big, 0, 0, 0, 0, $x, $y, $size[0], $size[1]);

				imagepng($img_mini, JPATH_ROOT . '/' . $thumbfile);
			} elseif ($size['mime'] == 'image/gif') {
				$img_big = imagecreatefromgif($filetmp); # On ouvre l'image d'origine
				$img_new = imagecreate($x, $y);
				# création de la miniature
				$img_mini = imagecreatetruecolor($x, $y) or $img_mini = imagecreate($x, $y);
				// copie de l'image, avec le redimensionnement.
				imagecopyresized($img_mini, $img_big, 0, 0, 0, 0, $x, $y, $size[0], $size[1]);

				imagegif($img_mini, JPATH_ROOT . '/' . $thumbfile);
			}
			//echo 'Image redimensionnée !';
		}

		return $thumbfile;
	}

	/**
	 * Create the css
	 *
	 * $params JRegistry the module params
	 * $prefix integer the prefix of the params
	 *
	 * @return Array of css
	 */
	static function createCss($params, $prefix = 'menu') {
		$css = Array();
		$csspaddingtop = ($params->get($prefix . 'paddingtop') AND $params->get($prefix . 'usemargin')) ? 'padding-top: ' . self::testUnit($params->get($prefix . 'paddingtop', '0')) . ';' : '';
		$csspaddingright = ($params->get($prefix . 'paddingright') AND $params->get($prefix . 'usemargin')) ? 'padding-right: ' . self::testUnit($params->get($prefix . 'paddingright', '0')) . ';' : '';
		$csspaddingbottom = ($params->get($prefix . 'paddingbottom') AND $params->get($prefix . 'usemargin') ) ? 'padding-bottom: ' . self::testUnit($params->get($prefix . 'paddingbottom', '0')) . ';' : '';
		$csspaddingleft = ($params->get($prefix . 'paddingleft') AND $params->get($prefix . 'usemargin')) ? 'padding-left: ' . self::testUnit($params->get($prefix . 'paddingleft', '0')) . ';' : '';
		$css['padding'] = $csspaddingtop . $csspaddingright . $csspaddingbottom . $csspaddingleft;
		$cssmargintop = ($params->get($prefix . 'margintop') AND $params->get($prefix . 'usemargin')) ? 'margin-top: ' . self::testUnit($params->get($prefix . 'margintop', '0')) . ';' : '';
		$cssmarginright = ($params->get($prefix . 'marginright') AND $params->get($prefix . 'usemargin')) ? 'margin-right: ' . self::testUnit($params->get($prefix . 'marginright', '0')) . ';' : '';
		$cssmarginbottom = ($params->get($prefix . 'marginbottom') AND $params->get($prefix . 'usemargin')) ? 'margin-bottom: ' . self::testUnit($params->get($prefix . 'marginbottom', '0')) . ';' : '';
		$cssmarginleft = ($params->get($prefix . 'marginleft') AND $params->get($prefix . 'usemargin')) ? 'margin-left: ' . self::testUnit($params->get($prefix . 'marginleft', '0')) . ';' : '';
		$css['margin'] = $cssmargintop . $cssmarginright . $cssmarginbottom . $cssmarginleft;
		$bgcolor1 = ($params->get($prefix . 'bgcolor1') && $params->get($prefix . 'bgopacity')) ? self::hex2RGB($params->get($prefix . 'bgcolor1'), $params->get($prefix . 'bgopacity')) : $params->get($prefix . 'bgcolor1');
		$css['background'] = ($params->get($prefix . 'bgcolor1') AND $params->get($prefix . 'usebackground')) ? 'background: ' . $bgcolor1 . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage') AND $params->get($prefix . 'usebackground')) ? 'background-image: url("' . JURI::ROOT() . $params->get($prefix . 'bgimage') . '");' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage') AND $params->get($prefix . 'usebackground')) ? 'background-repeat: ' . $params->get($prefix . 'bgimagerepeat') . ';' : '';
		$css['background'] .= ( $params->get($prefix . 'bgimage') AND $params->get($prefix . 'usebackground')) ? 'background-position: ' . $params->get($prefix . 'bgpositionx') . ' ' . $params->get($prefix . 'bgpositiony') . ';' : '';
		$css['gradient'] = ($css['background'] AND $params->get($prefix . 'bgcolor2') AND $params->get($prefix . 'usegradient')) ?
				"background: -moz-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%, " . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: -webkit-gradient(linear, left top, left bottom, color-stop(0%," . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "), color-stop(100%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . ")); "
				. "background: -webkit-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: -o-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: -ms-linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%);"
				. "background: linear-gradient(top,  " . $params->get($prefix . 'bgcolor1', '#f0f0f0') . " 0%," . $params->get($prefix . 'bgcolor2', '#e3e3e3') . " 100%); "
				. "filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='" . $params->get($prefix . 'bgcolor1', '#f0f0f0') . "', endColorstr='" . $params->get($prefix . 'bgcolor2', '#e3e3e3') . "',GradientType=0 );" : '';
		$css['borderradius'] = ($params->get($prefix . 'useroundedcorners')) ?
				'-moz-border-radius: ' . $params->get($prefix . 'roundedcornerstl', '0') . 'px ' . $params->get($prefix . 'roundedcornerstr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbl', '0') . 'px;'
				. '-webkit-border-radius: ' . $params->get($prefix . 'roundedcornerstl', '0') . 'px ' . $params->get($prefix . 'roundedcornerstr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbl', '0') . 'px;'
				. 'border-radius: ' . $params->get($prefix . 'roundedcornerstl', '0') . 'px ' . $params->get($prefix . 'roundedcornerstr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbr', '0') . 'px ' . $params->get($prefix . 'roundedcornersbl', '0') . 'px;' : '';
		$shadowinset = $params->get($prefix . 'shadowinset', 0) ? 'inset ' : '';
		$css['shadow'] = ($params->get($prefix . 'shadowcolor') AND $params->get($prefix . 'shadowblur') AND $params->get($prefix . 'useshadow')) ?
				'-moz-box-shadow: ' . $shadowinset . $params->get($prefix . 'shadowoffsetx', '0') . 'px ' . $params->get($prefix . 'shadowoffsety', '0') . 'px ' . $params->get($prefix . 'shadowblur', '') . 'px ' . $params->get($prefix . 'shadowspread', '0') . 'px ' . $params->get($prefix . 'shadowcolor', '') . ';'
				. '-webkit-box-shadow: ' . $shadowinset . $params->get($prefix . 'shadowoffsetx', '0') . 'px ' . $params->get($prefix . 'shadowoffsety', '0') . 'px ' . $params->get($prefix . 'shadowblur', '') . 'px ' . $params->get($prefix . 'shadowspread', '0') . 'px ' . $params->get($prefix . 'shadowcolor', '') . ';'
				. 'box-shadow: ' . $shadowinset . $params->get($prefix . 'shadowoffsetx', '0') . 'px ' . $params->get($prefix . 'shadowoffsety', '0') . 'px ' . $params->get($prefix . 'shadowblur', '') . 'px ' . $params->get($prefix . 'shadowspread', '0') . 'px ' . $params->get($prefix . 'shadowcolor', '') . ';' : '';
		$css['border'] = ($params->get($prefix . 'bordercolor') AND $params->get($prefix . 'borderwidth') AND $params->get($prefix . 'useborders')) ?
				'border: ' . $params->get($prefix . 'bordercolor', '#efefef') . ' ' . $params->get($prefix . 'borderwidth', '1') . 'px solid;' : '';
		$css['fontsize'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontsize')) ?
				'font-size: ' . $params->get($prefix . 'fontsize') . ';' : '';
		$css['fontcolor'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolor')) ?
				'color: ' . $params->get($prefix . 'fontcolor') . ';' : '';
		$css['fontweight'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontweight')) ?
				'font-weight: ' . $params->get($prefix . 'fontweight') . ';' : '';
		/* $css['fontcolorhover'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'fontcolorhover')) ?
		  'color: ' . $params->get($prefix . 'fontcolorhover') . ';' : ''; */
		$css['descfontsize'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'descfontsize')) ?
				'font-size: ' . $params->get($prefix . 'descfontsize') . ';' : '';
		$css['descfontcolor'] = ($params->get($prefix . 'usefont') AND $params->get($prefix . 'descfontcolor')) ?
				'color: ' . $params->get($prefix . 'descfontcolor') . ';' : '';
		return $css;
	}

	/**
	 * Truncates text blocks over the specified character limit and closes
	 * all open HTML tags. The method will optionally not truncate an individual
	 * word, it will find the first space that is within the limit and
	 * truncate at that point. This method is UTF-8 safe.
	 *
	 * @param   string   $text       The text to truncate.
	 * @param   integer  $length     The maximum length of the text.
	 * @param   boolean  $noSplit    Don't split a word if that is where the cutoff occurs (default: true).
	 * @param   boolean  $allowHtml  Allow HTML tags in the output, and close any open tags (default: true).
	 *
	 * @return  string   The truncated text.
	 *
	 * @since   11.1
	 */
	public static function truncate($text, $length = 0, $noSplit = true, $allowHtml = true) {
		// Check if HTML tags are allowed.
		if (!$allowHtml) {
			// Deal with spacing issues in the input.
			$text = str_replace('>', '> ', $text);
			$text = str_replace(array('&nbsp;', '&#160;'), ' ', $text);
			$text = JString::trim(preg_replace('#\s+#mui', ' ', $text));

			// Strip the tags from the input and decode entities.
			$text = strip_tags($text);
			$text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

			// Remove remaining extra spaces.
			$text = str_replace('&nbsp;', ' ', $text);
			$text = JString::trim(preg_replace('#\s+#mui', ' ', $text));
		}

		// Truncate the item text if it is too long.
		if ($length > 0 && JString::strlen($text) > $length) {
			// Find the first space within the allowed length.
			$tmp = JString::substr($text, 0, $length);

			if ($noSplit) {
				$offset = JString::strrpos($tmp, ' ');
				if (JString::strrpos($tmp, '<') > JString::strrpos($tmp, '>')) {
					$offset = JString::strrpos($tmp, '<');
				}
				$tmp = JString::substr($tmp, 0, $offset);

				// If we don't have 3 characters of room, go to the second space within the limit.
				if (JString::strlen($tmp) > $length - 3) {
					$tmp = JString::substr($tmp, 0, JString::strrpos($tmp, ' '));
				}
			}

			if ($allowHtml) {
				// Put all opened tags into an array
				preg_match_all("#<([a-z][a-z0-9]*)\b.*?(?!/)>#i", $tmp, $result);
				$openedTags = $result[1];
				$openedTags = array_diff($openedTags, array("img", "hr", "br"));
				$openedTags = array_values($openedTags);

				// Put all closed tags into an array
				preg_match_all("#</([a-z]+)>#iU", $tmp, $result);
				$closedTags = $result[1];

				$numOpened = count($openedTags);

				// All tags are closed
				if (count($closedTags) == $numOpened) {
					return $tmp . '...';
				}
				$tmp .= '...';
				$openedTags = array_reverse($openedTags);

				// Close tags
				for ($i = 0; $i < $numOpened; $i++) {
					if (!in_array($openedTags[$i], $closedTags)) {
						$tmp .= "</" . $openedTags[$i] . ">";
					} else {
						unset($closedTags[array_search($openedTags[$i], $closedTags)]);
					}
				}
			}

			$text = $tmp;
		}

		return $text;
	}
	
	/**
	 * Convert a hexa decimal color code to its RGB equivalent
	 *
	 * @param string $hexStr (hexadecimal color value)
	 * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
	 * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
	 * @return array or string (depending on second parameter. Returns False if invalid hex color value)
	 */
	static function hex2RGB($hexStr, $opacity) {
		if (!stristr($opacity, '.'))
			$opacity = $opacity / 100;
		$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
		$rgbArray = array();
		if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$colorVal = hexdec($hexStr);
			$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
			$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
			$rgbArray['blue'] = 0xFF & $colorVal;
		} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
			$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
			$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
			$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
		} else {
			return false; //Invalid hex color code
		}
		$rgbacolor = "rgba(" . $rgbArray['red'] . "," . $rgbArray['green'] . "," . $rgbArray['blue'] . "," . $opacity . ")";

		return $rgbacolor;
	}

	/**
	 * Test if there is already a unit, else add the px
	 *
	 * @param string $value
	 * @return string
	 */
	static function testUnit($value) {
		if ((stristr($value, 'px')) OR (stristr($value, 'em')) OR (stristr($value, '%'))) {
			return $value;
		}

		if ($value == '') {
			$value = 0;
		}

		return $value . 'px';
	}

}
