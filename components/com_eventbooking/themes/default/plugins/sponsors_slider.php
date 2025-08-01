<?php
/**
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2024 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die ;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Layout variables
 *
 * @var array                     $sponsors
 * @var \Joomla\Registry\Registry $params
 */

$rootUri  = Uri::root(true);
$document = Factory::getApplication()->getDocument()
	->addStyleSheet($rootUri . '/media/com_eventbooking/assets/js/splide/css/themes/' . $params->get('theme', 'splide-default.min.css'))
	->addStyleSheet($rootUri . '/media/mod_eb_advslider/assets/css/styles.css')
	->addScript($rootUri . '/media/com_eventbooking/assets/js/splide/js/splide.min.js');

$numberSponsors        = count($sponsors);
$sliderSettings        = EventbookingHelperSlider::getSliderSettings($params, $numberSponsors);
$sliderContainerId     = 'eb-sponsors-slider-' . time() . '-' . mt_rand(1, 1000);
$sponsorContainerClass = 'eb-sponsor-container';
?>
<div class="eb-speakers-slider-container splide" id="<?php echo $sliderContainerId; ?>">
	<div class="splide__track">
		<ul class="splide__list">
			<?php
			foreach ($sponsors as $sponsor)
			{
			?>
				<li class="splide__slide">
					<?php
						echo EventbookingHelperHtml::loadCommonLayout('plugins/sponsor_item.php', ['speaker' => $sponsor, 'speakerContainerClass' => $sponsorContainerClass]);
					?>
				</li>
			<?php
			}
			?>
		</ul>
	</div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		var splide = new Splide('#<?php echo $sliderContainerId; ?>', <?php echo json_encode($sliderSettings) ?>);
		splide.mount();
	});
</script>