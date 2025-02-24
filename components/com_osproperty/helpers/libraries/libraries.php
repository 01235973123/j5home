<?php
/*------------------------------------------------------------------------
# libraries.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Filesystem\Folder;

class OSLibraries{
	public static function checkMembership(){
		global $mainframe;
		$db = Factory::getDbo();
		jimport('joomla.filesystem.folder');
		if(!Folder::exists(JPATH_ROOT.DS."components".DS."com_osmembership")){
			$db->setQuery("Select count(id) from #__osrs_configuration where fieldname like 'integrate_membership'");
			$count = $db->loadResult();
			if($count > 0){
				$db->setQuery("UPDATE #__osrs_configuration SET fieldvalue = '0' WHERE fieldname LIKE 'integrate_membership'");
				$db->execute();
			}
		}
	}
	
	/**
	 * Show Address function
	 *
	 * @param unknown_type $show_address
	 * @return unknown
	 */
	function loadSelectYesNofield($fieldname,$fieldvalue){
		$optionArr[] = HTMLHelper::_('select.option','0',Text::_('OS_NO'));
		$optionArr[] = HTMLHelper::_('select.option','1',Text::_('OS_YES'));
		return HTMLHelper::_('select.genericlist',$optionArr,$fieldname,'class="inputbox"','value','text',$fieldvalue);
	}
	
	/**
	 * Conversion
	 *
	 * @param unknown_type $cur_from
	 * @param unknown_type $cur_to
	 * @return unknown
	 */
	function get_conversion($cur_from,$cur_to){
		if(strlen($cur_from)==0){
			$cur_from = "USD";
		}
		if(strlen($cur_to)==0){
			$cur_from = "PHP";
		}
		$host="download.finance.yahoo.com";
		$fp = @fsockopen($host, 80, $errno, $errstr, 30);
		if (!$fp)
		{
			$errorstr="$errstr ($errno)<br />\n";
			return false;
		}
		else
		{
			$file="/d/quotes.csv";
			$str = "?s=".$cur_from.$cur_to."=X&f=sl1d1t1ba&e=.csv";
			$out = "GET ".$file.$str." HTTP/1.0\r\n";
		    $out .= "Host: download.finance.yahoo.com\r\n";
			$out .= "Connection: Close\r\n\r\n";
			@fputs($fp, $out);
			while (!@feof($fp))
			{
				$data .= @fgets($fp, 128);
			}
			@fclose($fp);
			@preg_match("/^(.*?)\r?\n\r?\n(.*)/s", $data, $match);
			$data =$match[2];
			$search = array ("'<script[^>]*?>.*?</script>'si","'<[\/\!]*?[^<>]*?>'si","'([\r\n])[\s]+'","'&(quot|#34);'i","'&(amp|#38);'i","'&(lt|#60);'i","'&(gt|#62);'i","'&(nbsp|#160);'i","'&(iexcl|#161);'i","'&(cent|#162);'i","'&(pound|#163);'i","'&(copy|#169);'i","'&#(\d+);'e");
			$replace = array ("","","\\1","\"","&","<",">"," ",chr(161),chr(162),chr(163),chr(169),"chr(\\1)");
			$data = @preg_replace($search, $replace, $data);
			$result = split(",",$data);
			return $result[1];
		}//else
	}//end get_conversion
}
?> 