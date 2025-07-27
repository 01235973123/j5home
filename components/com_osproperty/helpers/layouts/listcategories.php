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

<?php
OSPHelper::generateHeading(2,Text::_('OS_LIST_CATEGORIES'));
?>
<section class="category-list" id="categoriesListing">
	<style>
	<?php
	$number_column = $configClass['category_layout'];
	if($number_column == 2)
	{
		?>
		.category-list{grid-template-columns: repeat(2, 1fr);}
		<?php
	}
	elseif($number_column == 3)
	{
		?>
		.category-list{grid-template-columns: repeat(3, 1fr);}
		<?php
	}

	?>
	</style>
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
		<div class="category-item">
			<a href="<?php echo $link?>" title="<?php echo $category_name?>" class="category-link-img">
                <?php
				if($row->category_image == "")
				{
					?>
					<img src="<?php echo Uri::root(true)?>/media/com_osproperty/assets/images/noimage.png" alt="<?php echo $category_name?>" />
					<?php
				}
				else
				{
					?>
					<img src="<?php echo Uri::root(true)?>/images/osproperty/category/thumbnail/<?php echo $row->category_image?>" alt="<?php echo $category_name?>" />
					<?php
				}
				?>
            </a>
            <a href="<?php echo $link?>" title="<?php echo $category_name?>" class="category-link-title">
                <h2><?php echo $category_name?> (<?php echo $row->nlisting?>)</h2>
            </a>
            <p><?php
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
			?></p>
		</div>
		<?php	
	}
	?>
</section>
<?php

if($pageNav->total > $pageNav->limit)
{
?>
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
</form>