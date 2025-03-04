<?php 
/*
 *
 * @package		ARI Framework
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

class AriMailer
{
	static public function send(
		$from, 
		$fromName, 
		$recipient, 
		$subject, 
		$body, 
		$mode = 0, 
		$cc = null, 
		$bcc = null, 
		$attachment = null,
		$replyTo = null, 
		$replyToName = null)
	{
		// if ($subject) $subject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

		$mailer = JFactory::getMailer();
	
		$mailer->setSender($from, $fromName);
		$mailer->setSubject($subject);
		$mailer->setBody($body);
		$mailer->IsHTML($mode);

		if ($recipient)
			$recipient = explode(';', $recipient);

		$mailer->addRecipient($recipient);

		if (!is_null($cc))
			$mailer->addCC($cc);
			
		if (!is_null($bcc))
			$mailer->addBCC($bcc);
			
		if (!is_null($attachment))
			$mailer->addAttachment($attachment);
			
		if (!is_null($replyTo))
			$mailer->addReplyTo($replyTo, $replyToName);

		$rs = $mailer->Send();
		if (($rs instanceof Exception) || empty($rs)) 
			$rs = false;
		else
			$rs = true;
			
		return $rs;
	}
}