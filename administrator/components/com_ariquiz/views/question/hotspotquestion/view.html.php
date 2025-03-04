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

require_once dirname(__FILE__) . DS . '..' . DS . 'question.php';

AriKernel::import('Web.Controls.Advanced.MultiplierControls');

class AriQuizSubViewQuestionHotspotquestion extends AriQuizSubViewQuestion 
{
	function displayView($params, $tpl = null) 
	{
		$files = AriUtils::getParam($params, 'files', array());
		$hotSpotImage = array(
			'FileId' => 0,
			'FileUrl' => ''
		);
		if (!empty($files['hotspot_image']))
		{
			$filesModel = AriModel::getInstance('Files', 'AriQuizModel');
			$file = $filesModel->getFile($files['hotspot_image']);
			if (!is_null($file))
			{
				$imageDir = AriQuizHelper::getFilesDir('images');
				$foldersModel = AriModel::getInstance(
					'Folders', 
					'AriQuizModel',
					array(
						'rootDir' => $imageDir, 
						'group' => $file->Group
					)
				);
				
				$path = join('/', $foldersModel->getSimplePath($foldersModel->getPath($file->FolderId)));

				$hotSpotImage['FileId'] = $file->FileId;
				$hotSpotImage['FileUrl'] = JURI::root(true) . '/' . AriUtils::absPath2Relative($imageDir . DS . ($path ? $path . DS : '') . $file->FileVersion->FileName);
			}
		}
		
		$this->assign('hotSpotImage', $hotSpotImage);
		
		parent::displayView($params, $tpl);
	}
	
	function getJSCropConfig($dataItem)
	{
		$cropCfg = new stdClass();
		$cropCfg->minWidth = 10;
		$cropCfg->minHeight = 10;
		
		if (!empty($dataItem))
		{
			$cropCfg->initialXY = array($dataItem[ARIQUIZ_HOTSPOTQUESTION_X1], $dataItem[ARIQUIZ_HOTSPOTQUESTION_Y1]);
			$cropCfg->initWidth = abs($dataItem[ARIQUIZ_HOTSPOTQUESTION_X2] - $dataItem[ARIQUIZ_HOTSPOTQUESTION_X1]);
			$cropCfg->initHeight = abs($dataItem[ARIQUIZ_HOTSPOTQUESTION_Y2] - $dataItem[ARIQUIZ_HOTSPOTQUESTION_Y1]);
		}
		
		return $cropCfg;
	}
}