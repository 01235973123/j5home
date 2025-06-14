<?php
/**
 * @package            Joomla
 * @subpackage         Event Booking
 * @author             Tuan Pham Ngoc
 * @copyright          Copyright (C) 2010 - 2024 Ossolution Team
 * @license            GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

trait EventbookingViewCaptcha
{
	/**
	 * The flag to determine whether captcha is shown on the view
	 *
	 * @var bool
	 */
	protected $showCaptcha = false;

	/**
	 * Name of captcha plugin used
	 *
	 * @var string
	 */
	protected $captchaPlugin;

	/**
	 * The string contain HTML code to render captcha
	 *
	 * @var string
	 */
	protected $captcha = null;

	/**
	 * Load captcha and store it into properties
	 *
	 * @param   bool  $initOnly
	 */
	protected function loadCaptcha($initOnly = false)
	{
		$user   = Factory::getApplication()->getIdentity();
		$config = EventbookingHelper::getConfig();

		if ($config->enable_captcha && !$user->authorise('core.admin', 'com_eventbooking'))
		{
			$captchaPlugin = Factory::getApplication()->get('captcha') ?: 'recaptcha';

			$plugin = PluginHelper::getPlugin('captcha', $captchaPlugin);

			if ($plugin)
			{
				$this->showCaptcha = true;

				if ($initOnly)
				{
					Captcha::getInstance($captchaPlugin)->initialise('eb_dynamic_recaptcha_1');
				}
				else
				{
					$this->captcha = Captcha::getInstance($captchaPlugin)->display('eb_dynamic_recaptcha_1', 'eb_dynamic_recaptcha_1', 'required');
				}
			}
			else
			{
				Factory::getApplication()->enqueueMessage(Text::_('EB_CAPTCHA_NOT_ACTIVATED_IN_YOUR_SITE'), 'error');
			}

			$this->captchaPlugin = $captchaPlugin;
		}
	}
}
