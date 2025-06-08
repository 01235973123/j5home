<?php

/**
 * @package        Joomla
 * @subpackage     OS Property
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2012 - 2023 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Filesystem\File;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;

class Pkg_OspropertyInstallerScript
{
	protected $installType;
	const MIN_PHP_VERSION = '7.2.0';
	const MIN_JOOMLA_VERSION = '3.9.0';
	public static $languageFiles = array('en-GB','de-DE','el-GR','fr-FR','es-ES','pt-PT','nl-NL','tr-TR','ru-RU','it-IT');
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	public function install($parent)
	{
		$this->installType = 'install';
	}

	public function update($parent)
	{
		$this->installType = 'update';
	}

	function preflight($type, $parent)
	{
		if (!version_compare(JVERSION, '3.4.0', 'ge'))
		{
			Factory::getApplication()->enqueueMessage(
				'Cannot install OS Property in a Joomla! release prior to ' . self::MIN_JOOMLA_VERSION,
				'error'
			);
		}

		//Backup the old language file
		foreach (self::$languageFiles as $languageFile)
		{
			$module_language_file = array($languageFile.'.mod_ospropertysearch.ini',$languageFile.'.mod_ospropertyloancal.ini',$languageFile.'.mod_ospropertymortgage.ini',$languageFile.'.mod_ospslideshow.ini',$languageFile.'.mod_osquicksearchrealhomes.ini',$languageFile.'.mod_ospropertyrandom.ini');
			foreach($module_language_file as $filename){
				if (File::exists(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename))
				{
					File::copy(JPATH_ROOT . '/language/'.$languageFile.'/' . $filename, JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename);
				}
			}
		}			
	}	

	public function postflight($type, $parent)
	{
		//Restore the modified language strings by merging to language files
		foreach (self::$languageFiles as $languageFile)
		{
			$registry = new Registry();
			$module_language_file = array($languageFile.'.mod_ospropertysearch.ini',$languageFile.'.mod_ospropertyloancal.ini',$languageFile.'.mod_ospropertymortgage.ini',$languageFile.'.mod_ospslideshow.ini',$languageFile.'.mod_osquicksearchrealhomes.ini',$languageFile.'.mod_ospropertyrandom.ini');
			foreach($module_language_file as $filename){
				$registry = new Registry();
				$backupFile  = JPATH_ROOT . '/language/'.$languageFile.'/bak.' . $filename;
				$currentFile = JPATH_ROOT . '/language/'.$languageFile.'/' . $filename;
				if (File::exists($currentFile) && File::exists($backupFile))
				{
					$registry->loadFile($currentFile, 'INI');
					$currentItems = $registry->toArray();
					$registry->loadFile($backupFile, 'INI');
					$backupItems = $registry->toArray();
					$items       = array_merge($currentItems, $backupItems);
					$content     = "";
					foreach ($items as $key => $value)
					{
						$content .= "$key=\"$value\"\n";
					}
					File::write($currentFile, $content);
				}
			}
		}

		ob_start();
		?>
		<style>
		

		table{
			border-collapse: separate !important;
		}

		div#es-installer * {
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		div#es-installer{
			width: 100%;
		}
		div#es-installer,
		div#es-installer p,
		div#es-installer div
		{
		}

		div#es-installer .clearfix,
		div#es-installer .box-hd,
		div#es-installer .box-bd {
			clear:none;display:block;
		}
		div#es-installer .clearfix:after,
		div#es-installer .box-hd,
		div#es-installer .box-bd {
			content:"";display:table;clear:both;
		}

		div#es-installer .box
		{
			background: #F9FAFC;
			border: 1px solid #D3D3D3;
			padding: 0px;
			margin-bottom: 20px;
			color: #777;

			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
		}
		div#es-installer .box-hd {
			background: #F6F7F9;
			border-bottom: 1px solid #d3d3d3;
			width: 100%;
			padding: 8px 15px 3px;

			-webkit-border-radius: 3px 3px 0 0;
			-moz-border-radius: 3px 3px 0 0;
			border-radius: 3px 3px 0 0;
		}
		div#es-installer .box-hd .es-title {
			float: left;
		}
		div#es-installer .box-hd .es-logo {
			float: right;
			margin-right: 10px;
		}
		div#es-installer .box-hd .es-logo img {
			vertical-align: bottom;
		}
		div#es-installer .box-hd .es-social {
			float: right;
		}

		div#es-installer .box-bd {
			padding: 16px !important;
		}
		div#es-installer h1.es-title {
			font-size: 22px;
			line-height: 24px;
			color: #333;
		}

		div#es-installer .btn-install {
			font-size: 11px;
			padding: 6px 16px;

			background-color: #8AD449;
			background-image: linear-gradient(to bottom, #8AD449, #6CD107);
			background-repeat: repeat-x;
			border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			color: #FFFFFF;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		}

		div#es-installer .btn-dashboard {
			background-color: #fea364;
			background-image: linear-gradient(to bottom, #fea364, #fe7d23);
			background-repeat: repeat-x;
			border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
			color: #ffffff;
			font-size: 11px;
			padding: 6px 16px;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		}

		div#es-installer .btn-install:hover {
			background-position: 0 0;
		}

		div#es-installer .box p
		{
			font-weight: normal;
			text-align: left;
		}

		div#es-installer .box p img
		{
			padding: 0 25px 0 0;
		}

		div#es-installer .fb-like,
		div#es-installer .fb-like iframe{
			width: 85px !important;
			max-width: 85px !important;
		}
		div#es-installer .twitter-follow-button{
			margin-left: 5px;
		}

		div#es-installer .actions{
			margin-top: 30px;
			text-align: left !important;
		}

		.table {
			border: 1px solid #ddd;
			margin: 20px 0 0;
			width: 100%;
		}

		table {
			border-collapse: collapse;
			border-spacing: 0;
		}

		.text-success {
			color: #3c763d;
		}

		.alert {
		  padding: 15px;
		  margin-bottom: 18px;
		  border: 1px solid transparent;
		  border-radius: 3px;
		}

		.table thead tr td {
			background: #f5f5f5 none repeat scroll 0 0;
			font-weight: 500;
		}
		.content .table td, .content .table th {
			padding: 15px;
		}

		.table tbody > tr:nth-child(2n) > td, .content .table tbody > tr:nth-child(2n) > th {
			background: #fafafa none repeat scroll 0 0;
		}
		.content .table tbody > tr + tr > td {
			border-top: 1px solid #e5e5e5;
		}

		.table .label {
			background: #43a047 none repeat scroll 0 0;
			border-radius: 3px;
			color: #fff;
			display: inline-block;
			font-size: 11px;
			margin-right: 10px;
			padding: 3px 5px;
			position: relative;
			top: -2px;
			vertical-align: top;
		}

		.text-success {
			color: #3c763d;
		}

		.text-error {
		  color: #b00;
		}
		</style>
		<div class="row-fluid">
			<div class="span12">
				<div id="es-installer">
					<div class="box">
						<div class="box-hd">
							<div class="es-title">
								You are about to install <b>OS Property Real Estate</b>.
							</div>

							<div class="es-social socialize">
								<div id="fb-root"></div>
								<script>(function(d, s, id) {
								var js, fjs = d.getElementsByTagName(s)[0];
								if (d.getElementById(id)) return;
								js = d.createElement(s); js.id = id;
								js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=406369119482668";
								fjs.parentNode.insertBefore(js, fjs);
								}(document, 'script', 'facebook-jssdk'));</script>
								<div class="fb-like" data-href="https://www.facebook.com/ossolution" data-width="90" data-layout="button_count" data-show-faces="false" data-send="false"></div>
							</div>

							<div class="es-logo">
								Another product by <a href="https://www.joomdonation.com" target="_blank"><img src="https://www.joomdonation.com/templates/ossolution/images/logo.png" alt="" style="height:22px !important;"></a>
							</div>

						</div>
						<!-- box-hd -->
						<div class="box-bd">
							<h1 class="es-title">
								Thank you for your recent purchase of OS Property.
							</h1>
							<p>
								Thank you for your recent purchase of OS Property and congratulations on making the choice to use the Best Real Estate Extension for Joomla!
							</p>

							<div class="actions">
								<a href="index.php?option=com_osproperty&amp;task=cpanel_list" class="btn btn-success btn-install">Go to OS Property Dashboard &raquo;</a>
								&nbsp;
								<a href="index.php?option=com_osproperty&amp;task=properties_prepareinstallsample" class="btn btn-success btn-dashboard">Install Sample data &raquo;</a>
							</div>
							<div style="clear:both;"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div style="clear:both"></div>
		<?php
		$gd = function_exists( 'gd_info' );
		$curl = is_callable( 'curl_init' );

		############################################
		## MySQL info
		############################################
		$db = Factory::getDBO();
		$mysqlVersion	= $db->getVersion();

		############################################
		## PHP info
		############################################
		$phpVersion = phpversion();
		$uploadLimit = ini_get('upload_max_filesize');
		$memoryLimit = ini_get('memory_limit');
		$postSize = ini_get('post_max_size');
		//$magicQuotes = get_magic_quotes_gpc() && JVERSION > 3;
		$passThru = function_exists('passthru');

		$postSize = 4;
		$hasErrors = false;

		if (stripos($memoryLimit, 'G') !== false) {
			list($memoryLimit) = explode('G', $memoryLimit);
			$memoryLimit = $memoryLimit * 1024;
		}

		if (!$gd || !$curl) {
			$hasErrors 	= true;
		}

		?>
		<p>Thank you for your recent purchase of <a href="https://www.joomdonation.com/joomla-extensions/os-property-joomla-real-estate.html" target="_blank">OS Property</a>! Before proceeding with the extension, please ensure that these Requirement Dependencies are met. These are the Required Dependencies to ensure that OS Property runs smoothly on your site.</p>

		<?php if (!$hasErrors) { ?>
		<hr />
		<p class="text-success">Awesome! The minimum requirements are met. You may proceed with the installation process now.</p>
		<?php } ?>

		<div class="alert alert-error" data-requirements-error style="display: none;">
			Please ensure that all of the requirements below are met.
		</div>

		<div class="requirements-table" data-system-requirements>
			<table class="table table-striped mt-20 stats">
				<thead>
					<tr>
						<td width="40%">
							Settings
						</td>
						<td class="center" width="30%">
							Recommended
						</td>
						<td class="center" width="30%">
							Current
						</td>
					</tr>
				</thead>

				<tbody>
					<tr class="<?php echo version_compare( $phpVersion , '5.4.0' ) == -1 ? 'error' : '';?>">
						<td>
							<div class="clearfix">
								<span class="label label-info">PHP</span> PHP Version
								<i class="fa fa-help" data-original-title="This is the installed php version on the site currently" data-toggle="tooltip" data-placement="bottom"></i>

								<?php if( version_compare( $phpVersion , '5.4.0') == -1 ){ ?>
								<a href="http://osproperty.ext4joomla.com/documentation/getting-started/requirements" class="pull-right btn btn-es-danger btn-mini">Fix This</a>
								<?php } ?>
							</div>
						</td>
						<td class="center text-success">
							5.4.0 +
						</td>
						<td class="center text-<?php echo version_compare($phpVersion , '5.4.0' ) == -1 ? 'error' : 'success';?>">
							<?php echo $phpVersion;?>
						</td>
					</tr>
					<tr class="<?php echo !$gd ? 'error' : '';?>">
						<td>
							<div class="clearfix">
								<span class="label label-info">PHP</span> GD Library
								<i class="fa fa-help" data-original-title="GD Library is used to manipulate images. Without GD library, EasySocial will not be able to manipulate uploaded images" data-toggle="tooltip" data-placement="bottom"></i>

								<?php if( !$gd ){ ?>
								<a href="http://osproperty.ext4joomla.com/documentation/getting-started/requirements" target="_blank" class="pull-right btn btn-es-danger btn-mini">Fix this</a>
								<?php } ?>
							</div>
						</td>
						<td class="center text-success">
							<i class="icon-ok"></i>
						</td>
						<?php if( $gd ){ ?>
						<td class="center text-success">
							<i class="icon-ok"></i>
						</td>
						<?php } else { ?>
						<td class="center text-error">
							<i class="icon-remove"></i>
						</td>
						<?php } ?>
					</tr>

					<tr class="<?php echo !$curl ? 'error' : '';?>">
						<td>
							<div class="clearfix">
								<span class="label label-info">PHP</span> CURL Library
								<i class="fa fa-help" data-original-title="CURL library is used to perform outgoing connections to an external site to retrieve information. This is required to fetch stories that contains URL" data-toggle="tooltip" data-placement="bottom"></i>
								<?php if( !$curl ){ ?>
								<a href="http://osproperty.ext4joomla.com/documentation/getting-started/requirements" target="_blank" class="pull-right btn btn-es-danger btn-mini">Fix this</a>
								<?php } ?>
							</div>
						</td>
						<td class="center text-success">
							<i class="icon-ok"></i>
						</td>
						<?php if( $curl ){ ?>
						<td class="center text-success">
							<i class="icon-ok"></i>
						</td>
						<?php } else { ?>
						<td class="center text-error">
							<i class="icon-remove"></i>
						</td>
						<?php } ?>
					</tr>
					
					<tr class="<?php echo $memoryLimit < 256 ? 'error' : '';?>">
						<td>
							<span class="label label-info">PHP</span> memory_limit
							<i class="fa fa-help" data-original-title="Memory Limit determines how much memory can PHP utilize per request on the server. On a normal site, 64MB should be more than sufficient but on a busier site, it's best to set it to 256MB" data-toggle="tooltip" data-placement="bottom"></i>
						</td>
						<td class="center text-success">
							128 <?php echo Text::_( 'M' );?>
						</td>
						<td class="center text-<?php echo $memoryLimit < 256 ? 'error' : 'success';?>">
							<?php echo $memoryLimit; ?>
						</td>
					</tr>
					<tr>
						<td>
							<span class="label label-success">MySQL</span> MySQL Version
							<i class="fa fa-help" data-original-title="This is the installed mysql server version on the site currently" data-toggle="tooltip" data-placement="bottom"></i>
						</td>
						<td class="center text-success">
							5.0.4
						</td>
						<td class="center text-<?php echo !$mysqlVersion || version_compare( $mysqlVersion , '5.0.4' ) == -1 ? 'error' : 'success'; ?>">
							<?php echo !$mysqlVersion ? 'N/A' : $mysqlVersion;?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<BR />
		<BR />
		<?php
		$contents 	= ob_get_contents();
		ob_end_clean();

		echo $contents;
	}
}