<?php
/*------------------------------------------------------------------------
# xml.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');


use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

class HTML_OspropertyXml{
    static function xmlImportForm($option, $lists){
        global $mainframe;
        $document = Factory::getDocument();
        ToolBarHelper::title(Text::_('OS_IMPORT_XML'),'upload');
        ToolBarHelper::custom('xml_import','upload.png','upload.png',Text::_('OS_IMPORT'),false);
        ToolBarHelper::cancel();
        ?>
        <form method="POST" action="index.php" name="adminForm" id="adminForm" enctype="multipart/form-data">
		<div class="row-fluid">
			<div class="span2" style="margin-left:0px;"></div>
			<div class="span9" style="text-align:center;margin-left:0px;">
				<h2>
					<?php echo Text::_('OS_PHP_INFORMATION');?>
				</h2>
				<i>
					<span color="gray">
						<?php echo Text::_('OS_SERVER_NOTE');?>
					</span>
				</i>
				<table width="100%" class="img img-polaroid" style="border:1px solid #CCC;padding:20px;">
					<tr>
						<td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
							<strong><?php echo Text::_('OS_MEMORY_LIMIT'); ?></strong>
						</td>
						<td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
							<?php
                            $memory_limit = ini_get('memory_limit');
                            if($memory_limit != ""){
                                echo $memory_limit;
                                $memory_limit1 = intval(trim(str_replace("M","",$memory_limit)));
                                if($memory_limit1 <  50){
                                    if(ini_set('memory_limit','999M')){
                                        ?>
                                        &nbsp;<span style="color:green;">OS Property can change this value when we run the function</span>
                                    <?php
                                    }else{
                                        ?>
                                        &nbsp;<span style="color:red;">OS Property cannot change this value when we run the function</span>
                                    <?php
                                    }
                                }
                            }
                            ?>
						</td>
					</tr>
					<tr>
						<td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
							<strong><?php echo Text::_('OS_MAX_EXECUTION_TIME'); ?></strong>
						</td>
						<td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
							<?php
                            $max_execution_time = ini_get('max_execution_time');
                            if($max_execution_time != ""){
                                echo $max_execution_time/60;
                                echo " seconds";
                                $max_execution_time1 = intval(trim(str_replace("M","",$max_execution_time)));
                                if($max_execution_time1 <  1000){
                                    if(ini_set('max_execution_time','3000')){
                                        ?>
                                        &nbsp;<span style="color:green;"><?php echo Text::_('OS_COMPONENT_CAN_CHANGE_THIS_VALUE');?></span>
                                    <?php
                                    }else{
                                        ?>
                                        &nbsp;<span style="color:red;"><?php echo Text::_('OS_COMPONENT_CANNOT_CHANGE_THIS_VALUE');?></span>
                                    <?php
                                    }
                                }
                            }
                            ?>
						</td>
					</tr>
                    <tr>
						<td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
							<strong><?php echo Text::_('OS_CURL_ENABLED'); ?></strong>
						</td>
						<td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
							<?php
                            if(function_exists('curl_version')){
                                echo "<strong style='color:green;'>".Text::_("OS_YES")."</strong>";
                            }else{
                                echo "<strong style='color:red;'>".Text::_("OS_NO")."</strong>";
                            }
                            ?>
						</td>
					</tr>
				</table>
				<BR /><BR />
				<h2>
					<?php echo Text::_('OS_SELECT_XML_FILE');?>
				</h2>
                <table width="100%" class="img img-polaroid" style="border:1px solid #CCC;padding:20px;">
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_PUBLISH_AND_APPROVE_PROPERTIES'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo HTMLHelper::_('select.genericlist',$lists['optionArr'],'publish_properties','class="input-mini form-select imedium"','value','text');?>
                        </td>
                    </tr>
					<tr>
						<td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
							<strong><?php echo Text::_('OS_XML_FILE'); ?></strong>
						</td>
						<td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
							<input type="file" class="input-large form-control" name="xml_file" id="xml_file" />
						</td>
					</tr>
				</table>
			</div>
			<div class="span2" style="margin-left:0px;"></div>
		</div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="xml_import" />
		<input type="hidden" name="MAX_UPLOAD_FILESIZE" value="900000000" />
		<input type="hidden" name="boxchecked" value="0" id="boxchecked" />
		</form>
		<script type="text/javascript">
			function checkFile(fieldObj)
		    {
		        var FileName  = fieldObj.value;
		        var FileExt = FileName.substr(FileName.lastIndexOf('.')+1);
		        if (FileExt != "xml")
		        {
		            var error = "File type : "+ FileExt+"\n\n";
		            error += "<?php echo Text::_('OS_PLEASE_MAKE_SURE_YOUR_FILE_IS_KYERO_XML')?>";
		            alert(error);
		            return true;
		        }
		        return false;
		    }
		</script>
        <?php
    }

	static function xmlExportForm($option,$lists){
		global $mainframe,$configClass;
		HTMLHelper::_('behavior.multiselect');
		ToolBarHelper::title(Text::_('OS_SELECT_OPTIONS_TO_EXPORT_XML'),'download');
		ToolBarHelper::custom('xml_export','download.png','download.png',Text::_('OS_EXPORTPROPERTIES'),false);
		ToolBarHelper::cancel();
		ToolBarHelper::custom('cpanel_list','featured.png', 'featured_f2.png',Text::_('OS_DASHBOARD'),false);
		?>
		<form method="POST" action="index.php?option=com_osproperty" name="adminForm" id="adminForm">

        <div class="row-fluid">
            <div class="span2" style="margin-left:0px;"></div>
            <div class="span9" style="text-align:center;margin-left:0px;">
                <h2>
                    <?php echo Text::_('OS_SELECT_OPTIONS_TO_EXPORT_PROPERTIES');?>
                </h2>
                <table width="100%" class="img img-polaroid" style="border:1px solid #CCC;padding:20px;">
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_CATEGORY'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo $lists['category'];?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_TYPE'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo $lists['type'];?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_USER'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
							<div id="agent_div">
								<?php echo $lists['agent'];?>
							</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_COMPANY'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo $lists['company'];?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_COUNTRY'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;float:left;" width="70%">
                            <div style="float:left;">
                                <?php echo $lists['country'];?>
                            </div>
                            <div style="float:left;display:inline;" id="country_state">
                                <?php echo $lists['states'];?>
                            </div>
                            <div style="float:left;display:inline;" id="city_div">
                                <?php echo $lists['city'];?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_ADD_PROPERTY_IDS_INTO_XML_ELEMENTS'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo HTMLHelper::_('select.genericlist',$lists['optionArr'],'include_pids','class="input-mini form-select"','value','text');?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_ADD_AGENT_IDS_INTO_XML_ELEMENTS'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo HTMLHelper::_('select.genericlist',$lists['optionArr'],'include_aids','class="input-mini form-select"','value','text');?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" style="text-align:left;padding:10px;background-color:#efefef;border-bottom:1px solid #CCC;border-right:1px solid #CCC;" width="30%">
                            <strong><?php echo Text::_('OS_ADD_COMPANY_IDS_INTO_XML_ELEMENTS'); ?></strong>
                        </td>
                        <td style="text-align:left;padding:10px;border-bottom:1px solid #CCC;" width="70%">
                            <?php echo HTMLHelper::_('select.genericlist',$lists['optionArr'],'include_cids','class="input-mini form-select"','value','text');?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="span2" style="margin-left:0px;"></div>
        </div>
		<input type="hidden" name="option" value="com_osproperty" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="" />
		</form>
        <script type="text/javascript">
            function loadState(country_id,state_id,city_id){
                var live_site = '<?php echo Uri::root()?>';
                loadLocationInfoStateCity(country_id,state_id,city_id,'country','state',live_site);
            }
            function loadCity(state_id,city_id){
                var live_site = '<?php echo Uri::root()?>';
                loadLocationInfoCity(state_id,city_id,'state',live_site);
            }
			function loadAgents(company_id){
				var live_site = '<?php echo Uri::root()?>';
                loadAgentsAjax(company_id,live_site);
			}
        </script>
		<?php 
	}
}
?>