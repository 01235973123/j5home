<?php
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();
$needs = array();
$needs[0] = "property_details";
$needs[1] = $row->id;
$itemid = OSPRoute::getItemid($needs);
$url = Route::_("index.php?option=com_osproperty&task=property_details&id=".$id."&Itemid=".$itemid);
$url = Uri::getInstance()->toString(array('scheme', 'user', 'pass', 'host')).$url;
$slidertype = 'slidernav';
$animation = 'slide';
$slideshow = 'true';
$slideshowspeed = 5000;
$arrownav = 'true';
$controlnav = 'true' ;
$keyboardnav = 'true';
$mousewheel = 'false';
$randomize =  'false';
$animationloop =  'true';
$pauseonhover =  'true' ;
$target = 'self';
$jquery = 'noconflict';
$document->addStyleSheet(Uri::root().'media/com_osproperty/assets/css/frontend_style.css');
$document->addStyleSheet(Uri::root().'components/com_osproperty/templates/default/style/favslider.css');
if ($jquery == 1 || $jquery == 0) { $noconflict = ''; $varj = '$';}
if ($jquery == "noconflict") {$noconflict = 'jQuery.noConflict();'; $varj = 'jQuery';}
if ($slidertype == "slidernav") {
	echo '<script type="text/javascript" src="'.Uri::root().'components/com_osproperty/templates/default/js/jquery.flexslider.js"></script><script type="text/javascript">
	'.$noconflict.'
		'.$varj.'(window).load(function(){
		  '.$varj.'(\'#carousel1\').favslider({
			animation: "slide",
			controlNav: false,
			directionNav: '.$arrownav.',
			mousewheel: '.$mousewheel.',
			animationLoop: false,
			slideshow: false,
			itemWidth: 120,
			asNavFor: \'#slider1\',
			startAt:'.(int)$photo_id.'
		  });
		  
		  '.$varj.'(\'#slider1\').favslider({
			animation: "'.$animation.'",
			directionNav: '.$arrownav.',
			mousewheel: '.$mousewheel.',
			slideshow: '.$slideshow.',
			slideshowSpeed: '.$slideshowspeed.',
			randomize: '.$randomize.',
			animationLoop: '.$animationloop.',
			pauseOnHover: '.$pauseonhover.',
			controlNav: false,
			sync: "#carousel1",
			start: function(slider){
			'.$varj.'(\'body\').removeClass(\'loading\');
			},
			startAt:'.(int)$photo_id.'
		  });
		});
	</script>'; }
?>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> gallerypage osp-container">
	<div class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
		<div id="slider1" class="favslider1 margin0">
			<ul class="favs">
				<?php
				for($i=0;$i<count($photos);$i++){
					if($photos[$i]->image != ""){
						if(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/medium/'.$photos[$i]->image){
							?>
							<li>
								<img src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id;?>/medium/<?php echo $photos[$i]->image?>" alt="<?php echo $photos[$i]->image_desc;?>" title="<?php echo $photos[$i]->image_desc;?>"/>
								
									<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> flex-caption">
										<div class="leftcaption">
											<?php
											if($photos[$i]->image_desc != ""){
											?>
												<?php echo $photos[$i]->image_desc;?>
											<?php } ?>
										</div>
										<div class="rightcaption noleftmargin">
											<a class="social-button facebook" target="_blank" href="//facebook.com/sharer.php?u=<?php echo $url;?>">facebook</a>
											<a class="social-button twitter" target="_blank" href="//twitter.com/intent/tweet?text=<?php echo str_replace(" ","+",$pro_name);?>&url=<?php echo $url;?>">twitter</a>
										</div>
									</div>
							</li>
							<?php
						}
					}
				}
				?>
			</ul>
		</div>
		<?php if(count($photos) > 1){?>
			<div id="carousel1" class="favslider1">
				<ul class="favs">
					<?php
					for($i=0;$i<count($photos);$i++){
						if($photos[$i]->image != ""){
							if(JPATH_ROOT.'/images/osproperty/properties/'.$id.'/thumb/'.$photos[$i]->image){
								?>
								<li <?php if ($i>0) {?>style="margin-left: 3px;width:120px !important;"<?php }else{ ?>style="width:120px !important; "<?php } ?>><img class="detailwidth" alt="<?php echo $photos[$i]->image_desc;?>" title="<?php echo $photos[$i]->image_desc;?>" src="<?php echo Uri::root()?>images/osproperty/properties/<?php echo $id;?>/thumb/<?php echo $photos[$i]->image?>" /></li>
								<?php
							}
						}
					}
					?>
				</ul>
			</div>
		<?php } ?>
	</div>
</div>
