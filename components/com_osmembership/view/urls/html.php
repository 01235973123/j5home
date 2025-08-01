<?php
/**
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2025 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

class OSMembershipViewUrlsHtml extends MPFViewList
{
	/**
	 * Prepare view data before displaying
	 *
	 * @throws Exception
	 */
	public function prepareView()
	{
		$this->requestLogin();

		parent::prepareView();
	}
}
