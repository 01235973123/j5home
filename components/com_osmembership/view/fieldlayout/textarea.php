<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * Layout variables
 *
 * @var stdClass $row
 * @var string   $name
 * @var ?string  $value
 * @var string   $attributes
 */
?>
<textarea name="<?php echo $name; ?>"
          id="<?php echo $name; ?>"<?php echo $attributes; ?>><?php echo htmlspecialchars((string) $value, ENT_COMPAT, 'UTF-8'); ?></textarea>
