<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2024 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

/**
 * Class OSMembershipTableField
 *
 * @property $id
 * @property $name
 * @property $title
 * @property $description
 * @property $multiple
 * @property $values
 * @property $default_values
 * @property $fee_field
 * @property $fee_values
 * @property $fee_formula
 * @property $quantity_field
 * @property $quantity_values
 * @property $depend_on_field_id
 * @property $depend_on_options
 * @property $is_core
 * @property $required
 * @property $min
 * @property $max
 * @property $step
 * @property $place_holder
 * @property $max_length
 * @property $size
 * @property $rows
 * @property $cols
 * @property $css_class
 * @property $extra
 * @property $validation_rules
 * @property $validation_error_message
 */

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class OSMembershipTableField extends Table
{
	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  Database connector object
	 */
	public function __construct($db)
	{
		parent::__construct('#__osmembership_fields', 'id', $db);
	}
}
