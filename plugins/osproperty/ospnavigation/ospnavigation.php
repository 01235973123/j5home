<?php
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
/**
 * @package 	plugin_OspNavigation - Navigation plugin for OS Property
 * @version		1.0
 * @created		April 2014

 * @author		Dang Thuc Dam
 * @email		damdt@joomservices.com
 * @website		http://joomdonation.com
 * @copyright	Copyright (C) 2014 Joomdonation. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die;

class plgOspropertyOspnavigation extends CMSPlugin
{
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	public function onTopPropertyDetails($item)
	{
		global $mainframe,$bootstrapHelper;
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/bootstrap.php';
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
		OSPHelper::generateBoostrapVariables();
        $session                    = Factory::getSession();
        $url                        = $session->get('advurl','');
		$position					= $this->params->get('nav_position','middle');
		$borderColor				= $this->params->get('borderColor','#f7931d');
		if(($position == "top") and (($item->prev > 0) or ($item->next > 0))){
			?>
			<style>
			.osnavigation-right:after{
				border-color:turquoise transparent transparent <?php echo $borderColor;?> !important;
				border-left:4px solid <?php echo $borderColor;?> !important;
			}
			.osnavigation-right{
				border-right:4px solid <?php echo $borderColor;?> !important;
			}
			.osnavigation-left{
				border-left:4px solid <?php echo $borderColor;?> !important;
			}
			</style>
			<div class="osnavigation-body <?php echo $bootstrapHelper->getClassMapping('row-fluid');?>">
				<?php 
				if($item->prev > 0){
				?>
				<div class="osnavigation-left <?php echo $bootstrapHelper->getClassMapping('span5');?>">
					<div class="osnavigation-direction-text">
		            	<?php echo Text::_('OS_PREVIOUS');?>
		            </div>
		        	<div class="osnavigation-osheader-text">
		            	<h4><a href="<?php echo $item->prev_link?>"><?php echo $item->prev_property?></a></h4>
		            </div>
		            <div class="osnavigation-ostype_name">
		            	<p><?php echo $item->prev_type?></p>
		            </div>
		        </div>
			     <?php } else{
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span5');?>"></div>
			     <?php 
			     }
			     ?>
			     <?php
			     require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';
			     $needs = array();
			     $needs[] = "property_advsearch";
			     $needs[] = "ladvsearch";
			     $itemid  = OSPRoute::getItemid($needs);
                 if($url == ""){
                     $url =Route::_('index.php?option=com_osproperty&task=property_advsearch&Itemid='.$itemid);
                 }
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span2');?> hidden-phone" style="text-align:center;margin-top:20px;">
			     	<a href="<?php echo $url;?>" title="<?php echo Text::_('OS_ADV_SEARCH');?>">
			     		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-grid-3x3-gap-fill" viewBox="0 0 16 16">
						  <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zM1 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V7zM1 12a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2z"/>
						</svg>
			     	</a>
			     </div>
			     <?php 
			     if($item->next > 0){
			     ?>
		        <div class="osnavigation-right <?php echo $bootstrapHelper->getClassMapping('span5');?>">
					<div class="osnavigation-direction-text">
		            	<?php echo Text::_('OS_NEXT');?>
		            </div>
		        	<div class="osnavigation-osheader-text">
		            	<h4><a href="<?php echo $item->next_link?>"><?php echo $item->next_property?></a></h4>
		            </div>
		            <div class="osnavigation-ostype_name">
		            	<p><?php echo $item->next_type?></p>
		            </div>
		        </div>
		        <?php } else{
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span5');?>"></div>
			     <?php 
			     }
			     ?>
			</div>  
			<?php 
		}
		
	}
	
	public function onMiddlePropertyDetails($item)
	{
		global $mainframe,$bootstrapHelper;
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/bootstrap.php';
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
		OSPHelper::generateBoostrapVariables();
        $session                    = Factory::getSession();
        $url                        = $session->get('advurl','');
		$position					= $this->params->get('nav_position','middle');
		$borderColor				= $this->params->get('borderColor','#f7931d');
		ob_start();
		
		if(($position == "middle") and (($item->prev > 0) or ($item->next > 0))){
			?>
			<style>
			.osnavigation-right:after{
				border-color:turquoise transparent transparent <?php echo $borderColor;?> !important;
				border-left:4px solid <?php echo $borderColor;?> !important;
			}
			.osnavigation-right{
				border-right:4px solid <?php echo $borderColor;?> !important;
			}
			.osnavigation-left{
				border-left:4px solid <?php echo $borderColor;?> !important;
			}
			</style>
			<div class="osnavigation-body <?php echo $bootstrapHelper->getClassMapping('row-fluid');?>">
				<?php 
				if($item->prev > 0){
				?>
				<div class="osnavigation-left <?php echo $bootstrapHelper->getClassMapping('span5');?>">
					<div class="osnavigation-direction-text">
		            	<?php echo Text::_('OS_PREVIOUS');?>
		            </div>
		        	<div class="osnavigation-osheader-text">
		            	<h4><a href="<?php echo $item->prev_link?>"><?php echo $item->prev_property?></a></h4>
		            </div>
		            <div class="osnavigation-ostype_name">
		            	<p><?php echo $item->prev_type?></p>
		            </div>
		        </div>
			     <?php } else{
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span5');?>"></div>
			     <?php 
			     }
			     ?>
			     <?php
			     require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';
			     $needs = array();
			     $needs[] = "property_advsearch";
			     $needs[] = "ladvsearch";
			     $itemid  = OSPRoute::getItemid($needs);
                 if($url == ""){
                     $url =Route::_('index.php?option=com_osproperty&task=property_advsearch&Itemid='.$itemid);
                 }
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span2');?> hidden-phone" style="text-align:center;margin-top:20px;">
			     	<a href="<?php echo $url;?>" title="<?php echo Text::_('OS_ADV_SEARCH');?>">
			     		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-grid-3x3-gap-fill" viewBox="0 0 16 16">
						  <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zM1 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V7zM1 12a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2z"/>
						</svg>
			     	</a>
			     </div>
			     <?php 
			     if($item->next > 0){
			     ?>
		        <div class="osnavigation-right <?php echo $bootstrapHelper->getClassMapping('span5');?>">
					<div class="osnavigation-direction-text">
		            	<?php echo Text::_('OS_NEXT');?>
		            </div>
		        	<div class="osnavigation-osheader-text">
		            	<h4><a href="<?php echo $item->next_link?>"><?php echo $item->next_property?></a></h4>
		            </div>
		            <div class="osnavigation-ostype_name">
		            	<p><?php echo $item->next_type?></p>
		            </div>
		        </div>
		        <?php } else{
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span5');?>"></div>
			     <?php 
			     }
			     ?>
			</div>   
			<?php 
		}
		$body = ob_get_contents();
		ob_end_clean();
		return $body;
	}
	
	
	function onBottomPropertyDetails($item)
	{
		global $mainframe,$bootstrapHelper;
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/bootstrap.php';
		require_once JPATH_ROOT.'/components/com_osproperty/helpers/helper.php';
		OSPHelper::generateBoostrapVariables();
        $session                    = Factory::getSession();
        $url                        = $session->get('advurl','');
		$position					= $this->params->get('nav_position','middle');
		$borderColor				= $this->params->get('borderColor','#f7931d');
		ob_start();
		
		if(($position == "bottom") and (($item->prev > 0) or ($item->next > 0))){
			?>
			<style>
			.osnavigation-right:after{
				border-color:turquoise transparent transparent <?php echo $borderColor;?> !important;
				border-left:4px solid <?php echo $borderColor;?> !important;
			}
			.osnavigation-right{
				border-right:4px solid <?php echo $borderColor;?> !important;
			}
			.osnavigation-left{
				border-left:4px solid <?php echo $borderColor;?> !important;
			}
			</style>
			<div class="osnavigation-body <?php echo $bootstrapHelper->getClassMapping('row-fluid');?>">
				<?php 
				if($item->prev > 0){
				?>
				<div class="osnavigation-left <?php echo $bootstrapHelper->getClassMapping('span5');?>">
					<div class="osnavigation-direction-text">
		            	<?php echo Text::_('OS_PREVIOUS');?>
		            </div>
		        	<div class="osnavigation-osheader-text">
		            	<h4><a href="<?php echo $item->prev_link?>"><?php echo $item->prev_property?></a></h4>
		            </div>
		            <div class="osnavigation-ostype_name">
		            	<p><?php echo $item->prev_type?></p>
		            </div>
		        </div>
			     <?php } else{
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span5');?>"></div>
			     <?php 
			     }
			     ?>
			     <?php
			     require_once JPATH_ROOT.'/components/com_osproperty/helpers/route.php';
			     $needs = array();
			     $needs[] = "property_advsearch";
			     $needs[] = "ladvsearch";
			     $itemid  = OSPRoute::getItemid($needs);
                 if($url == ""){
                     $url =Route::_('index.php?option=com_osproperty&task=property_advsearch&Itemid='.$itemid);
                 }
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span2');?> hidden-phone" style="text-align:center;margin-top:20px;">
			     	<a href="<?php echo $url;?>" title="<?php echo Text::_('OS_ADV_SEARCH');?>">
			     		<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-grid-3x3-gap-fill" viewBox="0 0 16 16">
						  <path d="M1 2a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V2zM1 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1V7zM1 12a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1v-2zm5 0a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-2a1 1 0 0 1-1-1v-2z"/>
						</svg>
			     	</a>
			     </div>
			     <?php 
			     if($item->next > 0){
			     ?>
		        <div class="osnavigation-right <?php echo $bootstrapHelper->getClassMapping('span5');?>">
					<div class="osnavigation-direction-text">
		            	<?php echo Text::_('OS_NEXT');?>
		            </div>
		        	<div class="osnavigation-osheader-text">
		            	<h4><a href="<?php echo $item->next_link?>"><?php echo $item->next_property?></a></h4>
		            </div>
		            <div class="osnavigation-ostype_name">
		            	<p><?php echo $item->next_type?></p>
		            </div>
		        </div>
		        <?php } else{
			     ?>
			     <div class="<?php echo $bootstrapHelper->getClassMapping('span5');?>"></div>
			     <?php 
			     }
			     ?>
			</div>  
			<?php 
		}
		$body = ob_get_contents();
		ob_end_clean();
		return $body;
	}
}
