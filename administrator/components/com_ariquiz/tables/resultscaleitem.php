<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

(defined('_JEXEC') && defined('ARI_FRAMEWORK_LOADED')) or die;

AriKernel::import('Joomla.Tables.Table');

class AriQuizTableResultscaleitem extends AriTable 
{
	var $ScaleItemId = null;
	var $ScaleId = 0;
	var $BeginPoint = 0;
	var $EndPoint = 0;
	var $TextTemplateId = null;
	var $MailTemplateId = null;
	var $PrintTemplateId = null;
	var $CertificateTemplateId = null;

	function __construct(&$db) 
	{
		parent::__construct('#__ariquiz_result_scale_item', 'ScaleItemId', $db);
	}
	
	function loadByScore($scaleId, $score)
	{
		return $this->customLoad(array(&$this, '_loadByScore'), array($scaleId, $score), 0, 1);
	}
	
	function _loadByScore($query, $queryParams, $scaleId, $score)
	{
		$tblAlias = $queryParams['tblAlias'];
		
		$query->where(sprintf('%1$s.ScaleId = %2$d',
			$tblAlias,
			$scaleId));
		$query->where(sprintf('%1$s.BeginPoint <= %2$f',
			$tblAlias,
			$score));
		$query->where(sprintf('%1$s.EndPoint >= %2$f',
			$tblAlias,
			$score));
			
		return $query;
	}
}