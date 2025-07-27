<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

$language = Factory::getApplication()->getLanguage();

/**
 * Layout variables
 *
 * @var string $id
 * @var string $link
 * @var string $image
 * @var string $text
 *
 */
?>
<div style="float:<?php
	echo ($language->isRTL()) ? 'right' : 'left'; ?>;" <?php if ($id) {echo 'id="' . $id . '"';} ?>>
	<div class="icon">
		<a class="lh-1" href="<?php echo $link; ?>">
			<?php echo HTMLHelper::_('image', 'administrator/components/com_osmembership/assets/icons/' . $image, $text); ?>
			<span><?php echo $text; ?></span>
		</a>
	</div>
</div>