<?php
use Joomla\CMS\Router\Route;
/**
 * @package 	mod_featuredagents - Featured agents
 * @version		1.0
 * @created		July 2013

 * @author		Dang Thuc Dam
 * @email		damdt@joomservices.com
 * @website		http://joomdonation.com
 * @copyright	Copyright (C) 2013 Joomdonation. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 */

// no direct access
defined('_JEXEC') or die;
?>

<?php
if(count($items) > 0){
?>
	<div id="featuredagentsmodule" style="" class="featured-agents<?php echo $params->get('moduleclass_sfx');?>">
	<?php
	for($i=0;$i<count($items);$i++)
	{
		$item = $items[$i];

		?>
		<div class="agent clearfix">
			<div class="image">
				<a href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$item->id.'&Itemid='.OSPRoute::getAgentItemid($item->id))?>" title="<?php echo $item->name;?>">
					<?php
					if($item->photo != ""){
						if(file_exists(JPATH_ROOT.'/images/osproperty/agent/thumbnail/'.$item->photo)){
							?>
							<img class="" width="140" alt="<?php echo $item->name;?>" src="<?php echo JURI::root().'/images/osproperty/agent/thumbnail/'.$item->photo?>" /> 
							<?php
						}else{
							?>
							<img class="" width="140" alt="<?php echo $item->name;?>" src="<?php echo JURI::root();?>media/com_osproperty/assets/images/noimage.jpg" />
							<?php
						}
					}else{
						?>
						<img class="" width="140" alt="<?php echo $item->name;?>" src="<?php echo JURI::root();?>media/com_osproperty/assets/images/noimage.jpg" />
						<?php
					}
					?>
				</a>
			</div>
			<div class="name">
				<a href="<?php echo Route::_('index.php?option=com_osproperty&task=agent_info&id='.$item->id.'&Itemid='.OSPRoute::getAgentItemid($item->id))?>" title="<?php echo $item->name;?>"><?php echo $item->name;?></a>
			</div>
			<?php
			if($item->phone != ""){
			?>
			<div class="phone"><?php echo $item->phone;?></div>
			<?php
			}
			?>
			<?php
			if($item->email != ""){
			?>
			<div class="email">
				<a href="mailto:<?php echo $item->email;?>"><?php echo $item->email;?></a>
			</div>
			<?php
			}
			?>
		</div>
	<?php
	}
	?>
	</div>
<?php
}
?>

