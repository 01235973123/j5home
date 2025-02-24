<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$rowfluidClass	= $bootstrapHelper->getClassMapping('row-fluid');
$span12Class	= $bootstrapHelper->getClassMapping('span12');
?>
<form method="POST" action="<?php echo Route::_('index.php?option=com_osproperty&task=category_listing&Itemid='.Factory::getApplication()->input->getInt('Itemid',0))?>" name="ftForm">
<div class="<?php echo $rowfluidClass; ?>" id="categoriesListing">
	<div class="<?php echo $span12Class; ?>">
		<?php
		OSPHelper::generateHeading(2,Text::_('OS_LIST_CATEGORIES'));
		$number_column = $configClass['category_layout'];
		$widthcount	   = round(12/$number_column);
		$spanClass	   = $bootstrapHelper->getClassMapping('span'.$widthcount);
		?>
		<div class="<?php echo $rowfluidClass; ?>">
		<?php
		$j = 0;
		for($i=0;$i<count($rows);$i++)
		{
			$j++;
			$row = $rows[$i];
			$link = Route::_('index.php?option=com_osproperty&task=category_details&id='.$row->id.'&Itemid='.Factory::getApplication()->input->getInt('Itemid',0));
			$category_name = OSPHelper::getLanguageFieldValue($row,'category_name');
			$category_description = OSPHelper::getLanguageFieldValue($row,'category_description');
			?>
			<div class="<?php echo $spanClass;?>">
				<div class="categoryelement">
					<div class="<?php echo $rowfluidClass; ?>">
						<div class="<?php echo $span12Class;?> categoryelementdata center">
							<span class="categoryelementpicture">
								<a href="<?php echo $link?>" title="<?php echo $category_name?>">
									<?php
									if($row->category_image == "")
									{
										?>
										<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/noimage.png" class="categoryelementpicture" alt="<?php echo $category_name?>" />
										<?php
									}
									else
									{
										?>
										<img src="<?php echo Uri::root(true)?>/images/osproperty/category/thumbnail/<?php echo $row->category_image?>" class="categoryelementpicture" alt="<?php echo $category_name?>" />
										
										<?php
									}
									?>
								</a>
							</span>
						</div>
					</div>
					<div class="<?php echo $rowfluidClass; ?>">
						<div class="<?php echo $span12Class;?> categoryelementtitle center">
							<a href="<?php echo $link?>" title="<?php  echo $category_name;?>">
								<?php echo $category_name?> (<?php echo $row->nlisting?>)
							</a>
							<?php
							if($configClass['active_rss'] == 1)
								{
								?>
								<a href="<?php echo Uri::root()?>index.php?option=com_osproperty&task=property_exportrss&category_id=<?php echo $row->id?>&format=feed" target="_blank" title="<?php echo Text::_('OS_RSS_FEED_OF_THIS_CATEGORY');?>">
									<img src="<?php echo Uri::root()?>media/com_osproperty/assets/images/feed.png" width="12" border="0" class="rssFeedIcon"/>
								</a>
								<?php
							}
							?>
						</div>
					</div>
					
					<?php
					if($configClass['categories_show_description'] == 1)
					{
						?>
						<div class="<?php echo $rowfluidClass; ?>">
							<div class="<?php echo $span12Class;?> categoryelementdescription">
							<?php
							$desc = strip_tags(stripslashes($category_description));
							$descArr = explode(" ",$desc);
							if(count($descArr) > 20)
							{
								for($k=0;$k<20;$k++)
								{
									echo $descArr[$k]." ";
								}
								echo "...";
							}
							else
							{
								echo $desc;
							}
							?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<?php
			if($j == $number_column)
			{
				?>
				</div><div class="<?php echo $rowfluidClass; ?>">
				<?php 
				$j = 0;
			}
		}
		?>
		</div>
		<?php
		if($pageNav->total > $pageNav->limit){
		?>
		<div class="clearfix"></div>
		<div class="<?php echo $rowfluidClass; ?>">
			<div class="<?php echo $span12Class; ?>">
				<?php
					echo $pageNav->getListFooter();
				?>
			</div>
		</div>
		<?php
		}
		?>
	</div>
</div>
</form>