<?php
/*
 *
 * @package		ARI Quiz
 * @author		ARI Soft
 * @copyright	Copyright (c) 2011 www.ari-soft.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 * 
 */

defined('_JEXEC') or die;

$adminPath = dirname(__FILE__) . '/';
require_once $adminPath . 'kernel/class.AriKernel.php';
require_once $adminPath . 'defines.php';
require_once $adminPath . 'helper.php';

AriKernel::import('Install.Installer');
AriKernel::import('Joomla.Models.Model');
AriKernel::import('Joomla.Tables.Table');
AriKernel::import('Utils.DateUtility');

jimport('joomla.application.component.model');
jimport('joomla.database.table');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

function com_install() 
{
	$installer = new AriQuizInstall(
		array(
			'option' => 'com_ariquiz'
		)
	);

	$res = $installer->install();
	$isSuccess = $installer->isSuccess();

	if (!$isSuccess)
		echo nl2br(trim($res));

	return $isSuccess;	
}

class AriQuizInstall extends AriInstallerBase
{
	var $_tblPrefix = 'AriQuizTable';
	var $_modelPrefix = 'AriQuizModel';
	
	function __construct($options)
	{	
		parent::__construct($options);
		
		AriModel::addIncludePath($this->adminPath . 'models');
		AriTable::addIncludePath($this->adminPath . 'tables');
		
		$lang = JFactory::getLanguage();
		$lang->load('com_ariquiz');
	}
	
	function installSteps()
	{
		// if (!$this->isDbSupportUtf8())
		// {
		// 	throw new Exception(
		// 		'The database does not support UTF8 encoding',
		// 		500
		// 	);
		// 	return false;
		// }
		
		$this->_executeSqlScript();

		$path = dirname(__FILE__) . DS;

		$this->installPlugin($path . 'install/addons/plg_system_ariextensions.zip');

		$isSafeMode = ini_get('safe_mode');
		if (!$isSafeMode)
		{
			$this->setPermissions(
				array(
					$this->adminPath . 'files' . DS . 'images' => '0775',
				)
			);
		}

		$currentVersion = $this->_getCurrentVersion();
		
		$this->_initFolders();
		$this->_applyUpdates($currentVersion);
		$this->_createDefaultCategories();
		
		$this->_updateAssets();
		
		$this->_updateConfig(ARIQUIZ_VERSION, $currentVersion);

		return true;
	}
		
	function _createDefaultCategories()
	{
		$defaultCategoryId = AriQuizHelper::getDefaultCategoryId();
		$defaultBankCategoryId = AriQuizHelper::getDefaultBankCategoryId();
		
		if ($defaultBankCategoryId > 0 && $defaultCategoryId > 0)
			return;

		$database = $this->_db;
		$cfg = AriQuizHelper::getConfig();
		if ($defaultBankCategoryId == 0)
		{
			require_once dirname(__FILE__) . DS . 'models' . DS . 'bankcategory.php';
			require_once dirname(__FILE__) . DS . 'tables' . DS . 'bankcategory.php';
			
			$bankCategoryModel = AriModel::getInstance('Bankcategory', 'AriQuizModel');
			$bankCategory = $bankCategoryModel->saveCategory(
				array(
					'CategoryName' => 'Uncategorised'
				)
			);
		
			if ($bankCategory && $bankCategory->CategoryId)
			{
				$cfg->set('DefaultBankCategoryId', $bankCategory->CategoryId);
				
				$query = sprintf('UPDATE #__ariquizquestion SET QuestionCategoryId = %d WHERE QuizId = 0 AND QuestionCategoryId = 0',
					$bankCategory->CategoryId
				);
				$database->setQuery($query);
				try {
					$database->execute();
				} catch (Exception $ex) {
					trigger_error(sprintf(ARI_INSTALL_ERROR_EXECUTEQUERY, $ex->getMessage()), E_USER_ERROR);
				}
			}
		}
		
		if ($defaultCategoryId == 0)
		{
			$now = AriDateUtility::getDbUtcDate();
			$userId = JFactory::getUser()->get('id');

			require_once dirname(__FILE__) . DS . 'models' . DS . 'category.php';
			require_once dirname(__FILE__) . DS . 'tables' . DS . 'category.php';

			$categoryTable = AriTable::getInstance('category', $this->_tblPrefix);
			$rootCategoryId = $categoryTable->addRoot(array(
				'CategoryName' => $database->quote(''),
				'Description' => $database->quote(''),
				'Created' => $database->quote($now),
				'CreatedBy' => $userId,
				'Metadata' => $database->quote(''),
			));
			if ($rootCategoryId === false)
				trigger_error('Couldn\'t not create root category.', E_USER_ERROR);
				
			if ($rootCategoryId)
			{
				$database->setQuery('SELECT id FROM #__assets WHERE name = "com_ariquiz" LIMIT 0,1');
				$ariQuizAssetId = $database->loadResult();
				if ($ariQuizAssetId)
				{
					$assetName = 'com_ariquiz.category.' . $rootCategoryId;
					$database->setQuery(
						sprintf(
							'SELECT id FROM #__assets WHERE name = %1$s LIMIT 0,1',
							$database->Quote($assetName)
						)
					);
					$rootCatAssetId = $database->loadResult();
					if (empty($rootCatAssetId))
					{
						jimport('joomla.database.table');
						
						$asset = AriTable::getInstance('Asset');
				        $asset->name = $assetName;
				        $asset->parent_id = $ariQuizAssetId;
				        $asset->rules = '{}';
				        $asset->title = 'com_ariquiz.category.' . $rootCategoryId;
				        $asset->setLocation($ariQuizAssetId, 'last-child');
				        $asset->store();
					}
				}
			}
			
			$categoryModel = AriModel::getInstance('category', 'AriQuizModel');
			$category = $categoryModel->saveCategory(
				array(
					'parent_id' => $rootCategoryId,
					'CategoryName' => 'Uncategorised'
				)
			);
			$category->rebuild();

			if ($category && $category->CategoryId)
			{
				$cfg->set('DefaultCategoryId', $category->CategoryId);
				
				$database->setQuery('DELETE FROM #__ariquizquizcategory WHERE CategoryId = 0');
				try {
					$database->execute();
				} catch (Exception $ex) {
					trigger_error(sprintf(ARI_INSTALL_ERROR_EXECUTEQUERY, $ex->getMessage()), E_USER_ERROR);
				}
				
				$query = sprintf('SELECT Q.QuizId FROM #__ariquiz Q LEFT JOIN #__ariquizquizcategory QQC ON Q.QuizId = QQC.QuizId WHERE ISNULL(QQC.QuizId)');
				$database->setQuery($query);
				$quizIdList = $database->loadColumn();
				
				if (is_array($quizIdList) && count($quizIdList) > 0)
				{
					$query = array();
					foreach ($quizIdList as $quizId)
					{
						$query[] = sprintf('(%d,%d)',
							$quizId,
							$category->CategoryId
						);
					}
					
					$query = 'INSERT INTO #__ariquizquizcategory (QuizId,CategoryId) VALUES ' . join(',', $query);
					$database->setQuery($query);
					try {
						$database->execute();
					} catch (Exception $ex) {
						trigger_error(sprintf(ARI_INSTALL_ERROR_EXECUTEQUERY, $ex->getMessage()), E_USER_ERROR);
					}	
				}
			}
		}
		
		$cfg->save();
	}
	
	function _updateAssets()
	{
		/*
		 * 
ASSETS
		 quiz category
		 quiz
		 question category
		 quiz question

		 bank category
		 bank question
		  		 */
		$db = $this->_db;
	
		require_once dirname(__FILE__) . DS . 'models' . DS . 'category.php';
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'category.php';
		
		$categoryModel = AriModel::getInstance('category', 'AriQuizModel');
		$query = $db->setQuery('SELECT CategoryId FROM #__ariquizcategory WHERE asset_id = 0');
		$idList = J3_0 ? $db->loadColumn() : $db->loadResultArray();
		foreach ($idList as $categoryId)
		{
			$category = $categoryModel->getCategory($categoryId);
			if ($category)
				$category->store();
		}
		
		require_once dirname(__FILE__) . DS . 'models' . DS . 'quiz.php';
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'quiz.php';
		
		$quizModel = AriModel::getInstance('quiz', 'AriQuizModel');
		
		$query = $db->setQuery('SELECT QuizId FROM #__ariquiz WHERE asset_id = 0');
		$idList = $db->loadColumn();
		foreach ($idList as $quizId)
		{
			$quiz = $quizModel->getQuiz($quizId);
			if ($quiz)
				$quiz->store(false, false, false);
		}
		
		require_once dirname(__FILE__) . DS . 'models' . DS . 'questioncategory.php';
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'questioncategory.php';
		
		$categoryModel = AriModel::getInstance('questioncategory', 'AriQuizModel');
		$query = $db->setQuery('SELECT QuestionCategoryId FROM #__ariquizquestioncategory WHERE asset_id = 0');
		$idList = J3_0 ? $db->loadColumn() : $db->loadResultArray();
		foreach ($idList as $categoryId)
		{
			$category = $categoryModel->getCategory($categoryId);
			if ($category)
				$category->store();
		}
		
		require_once dirname(__FILE__) . DS . 'models' . DS . 'quizquestion.php';
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'quizquestion.php';
		
		$questionModel = AriModel::getInstance('quizquestion', 'AriQuizModel');
		$query = $db->setQuery('SELECT QuestionId FROM #__ariquizquestion WHERE QuizId > 0 AND asset_id = 0');
		$idList = J3_0 ? $db->loadColumn() : $db->loadResultArray();
		foreach ($idList as $questionId)
		{
			$question = $questionModel->getQuestion($questionId);
			if ($question)
				$question->update(array());
		}

		require_once dirname(__FILE__) . DS . 'models' . DS . 'bankcategory.php';
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'bankcategory.php';
		
		$categoryModel = AriModel::getInstance('bankcategory', 'AriQuizModel');
		$query = $db->setQuery('SELECT CategoryId FROM #__ariquizbankcategory WHERE asset_id = 0');
		$idList = J3_0 ? $db->loadColumn() : $db->loadResultArray();
		foreach ($idList as $categoryId)
		{
			$category = $categoryModel->getCategory($categoryId);
			if ($category)
				$category->store();
		}
		
		require_once dirname(__FILE__) . DS . 'models' . DS . 'bankquestion.php';
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'bankquestion.php';
		
		$questionModel = AriModel::getInstance('bankquestion', 'AriQuizModel');
		$query = $db->setQuery('SELECT QuestionId FROM #__ariquizquestion WHERE QuizId = 0 AND asset_id = 0');
		$idList = J3_0 ? $db->loadColumn() : $db->loadResultArray();
		foreach ($idList as $questionId)
		{
			$question = $questionModel->getQuestion($questionId);
			if ($question)
				$question->update(array());
		}
	}
	
	function _executeSqlScript()
	{
		$scriptPath = dirname(__FILE__) . DS . 'install' . DS . 'sql' . DS . 'mysql' . DS . 'install.sql';
		$database = $this->_db;
		
		$sql = file_get_contents($scriptPath);
		
		$queries = $database->splitSql($sql);
		if (is_array($queries))
		{
			foreach ($queries as $query)
			{
				$this->executeQuery($query);
			}
		}
		
		return true;
	}

	function _updateConfig($version, $prevVersion)
	{
		$cfg = AriQuizHelper::getConfig();
		$version = $cfg->set('Version', $version);

        if ($prevVersion != $version)
            $cfg->set('PrevVersion', $prevVersion);
        
		$cfg->save();
	}

	function _initFolders()
	{
		$db = $this->_db;

		require_once dirname(__FILE__) . DS . 'tables' . DS . 'folder.php';
		require_once dirname(__FILE__) . DS . 'models' . DS . 'folders.php';
	
		$folderTable = AriTable::getInstance('folder', $this->_tblPrefix);
		$rootFolderId = $folderTable->addRoot(array(
			'Status' => 1,
			'Group' => $db->quote(''),
		));
		if ($rootFolderId === false)
			trigger_error('Couldn\'t not create root folder.', E_USER_ERROR);

		$imagesFoldersModel = AriModel::getInstance('folders', $this->_modelPrefix, array('group' => ARIQUIZ_FOLDER_IMAGES));
		$imagesRootFolder = $imagesFoldersModel->getRootFolder();
		if (empty($imagesRootFolder))
		{
			$imagesTable = AriTable::getInstance('folder', $this->_tblPrefix);
			$imagesTable->setLocation($rootFolderId, 'first-child');
			$imagesTable->bind(
				array(
					'title' => ARIQUIZ_FOLDER_IMAGES, 
					'alias' => ARIQUIZ_FOLDER_IMAGES, 
					'Group' => ARIQUIZ_FOLDER_IMAGES,
					'parent_id' => $rootFolderId,
				)
			);
			if ($imagesTable->check())
				$imagesTable->store();
			else 
				trigger_error('Couldn\'t not create "' . ARIQUIZ_FOLDER_IMAGES . '" folder.', E_USER_ERROR);
		}
	}
	
	function _getCurrentVersion()
	{
		$db = $this->_db;
		$db->setQuery('SELECT ParamValue FROM #__ariquizconfig WHERE ParamName = "Version" LIMIT 0,1');
		$version = $db->loadResult();
		if (empty($version))
		{
			$version = ARIQUIZ_VERSION;
		}
		
		if (version_compare($version, '2.0.0', '<'))
			$version = '1.0.0';
		
		return $version;
	}

	protected function executeQuery($query) {
		$database = $this->_db;

		$database->setQuery($query);
		try {
			$database->execute();
		} catch (Exception $e) {
			trigger_error(sprintf(ARI_INSTALL_ERROR_EXECUTEQUERY, $e->getMessage()), E_USER_ERROR);
		}
	}

	function _updateTo_1_2_0()
	{
		if (!$this->_isColumnExists('#__ariquizquestionversion', 'BankQuestionId'))
		{
			$query = 'ALTER TABLE #__ariquizquestionversion ADD COLUMN `BankQuestionId` int(10) unsigned default NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatistics', 'BankVersionId'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `BankVersionId` bigint(20) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizquestion', 'BankQuestionId'))
		{
			$query = 'ALTER TABLE #__ariquizquestion ADD COLUMN `BankQuestionId` int(10) unsigned default NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizquestion', 'QuestionTypeId'))
		{
			$query = 'ALTER TABLE #__ariquizquestion ADD COLUMN `QuestionTypeId` int(11) unsigned NOT NULL';
			$this->executeQuery($query);
			
			$query = sprintf('UPDATE #__ariquizquestion QQ INNER JOIN #__ariquizquestionversion QQV' .
				'	ON QQ.QuestionVersionId = QQV.QuestionVersionId' .
				' SET QQ.QuestionTypeId = QQV.QuestionTypeId');
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizquestion', 'QuestionCategoryId'))
		{
			$query = 'ALTER TABLE #__ariquizquestion ADD COLUMN `QuestionCategoryId` int(10) unsigned default NULL';
			$this->executeQuery($query);
			
			$query = sprintf('UPDATE #__ariquizquestion QQ INNER JOIN #__ariquizquestionversion QQV' .
				'	ON QQ.QuestionVersionId = QQV.QuestionVersionId' .
				' SET QQ.QuestionCategoryId = QQV.QuestionCategoryId');
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_0_2()
	{
		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'ExtraData'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `ExtraData` text';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_1_1()
	{
		$database = $this->_db;

		if (!$this->_isColumnExists('#__ariquizquestionversion', 'Note'))
		{
			$query = 'ALTER TABLE #__ariquizquestionversion ADD COLUMN `Note` text';
			$this->executeQuery($query);
		}
			
		$codeName = 'com_ariquiz';
		$textTemplateTable = '#__arigenerictemplate';
		$mailTemplateTable = '#__ariquizmailtemplate';
		$textTemplates = array(
			'Successful' => 'QuizSuccessful',
			'Failed' => 'QuizFailed',
			'SuccessfulEmail' => 'QuizSuccessfulEmail',
			'FailedEmail' => 'QuizFailedEmail',
			'SuccessfulPrint' => 'QuizSuccessfulPrint',
			'FailedPrint' => 'QuizFailedPrint',
			'AdminEmail' => 'QuizAdminEmail'
		);
		$mailTextTemplate = array(
			$database->Quote($textTemplates['AdminEmail']), 
			$database->Quote($textTemplates['FailedEmail']),
			$database->Quote($textTemplates['SuccessfulEmail']));
		$sMailTextTemplate = join(',', $mailTextTemplate);
		
		// migrate assining text template for mail to mail templates
		$query = sprintf('SELECT GT.TemplateId,GT.TemplateName,GT.Value,GT.CreatedBy' .
			' FROM %1$s GT INNER JOIN %1$sentitymap GTEM' .
			'	ON GT.TemplateId = GTEM.TemplateId' .
			' LEFT JOIN %2$s MT' .
			'	ON GT.TemplateId = MT.TextTemplateId' .
			' WHERE MT.MailTemplateId IS NULL AND GTEM.TemplateType IN (%3$s) AND GT.BaseTemplateId = 1' .
			' GROUP BY GT.TemplateId',
			$textTemplateTable,
			$mailTemplateTable,
			$sMailTextTemplate);
		$database->setQuery($query);
		$result = $database->loadAssocList();
		if (is_null($result))
		{ 
			return false;
		}
		
		if (!empty($result))
		{
			$query = sprintf('INSERT INTO %1$s (TemplateId,BaseTemplateId,TemplateName,Value,Created,CreatedBy,Modified,ModifiedBy) VALUES(NULL,2,%%s,%%s,NOW(),%%d,NULL,NULL)',
					$textTemplateTable);
			$mapTable = array();
			foreach ($result as $item)
			{		
				$itemQuery = sprintf($query,
					$database->Quote($item['TemplateName']),
					$database->Quote($item['Value']),
					$item['CreatedBy']);
				$this->executeQuery($itemQuery);
				
				$id = $database->insertid();
				$itemQuery = sprintf('UPDATE %sentitymap SET TemplateId=%d WHERE TemplateId=%d AND TemplateType IN(%s)',
					$textTemplateTable,
					$id,
					$item['TemplateId'],
					$sMailTextTemplate);
				$this->executeQuery($itemQuery);
				
				$itemQuery = sprintf('INSERT INTO %1$s (`MailTemplateId`,`Subject`,`TextTemplateId`,`FromName`,`AllowHtml`,`From`) VALUES(NULL,NULL,%2$d,NULL,1,NULL)',
					$mailTemplateTable,
					$id);
				$this->executeQuery($itemQuery);
			}
		}
	}
	
	function _updateTo_2_1_2()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'ResultScaleId'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `ResultScaleId` int(11) unsigned default NULL';
			$this->executeQuery($query);
		}
	}

	function _updateTo_2_1_9()
	{
		$database = $this->_db;

		if (!$this->_isColumnExists('#__ariquiz', 'ParsePluginTag'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `ParsePluginTag` tinyint(1) unsigned NOT NULL default \'1\'';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_4_0()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'CanStop'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `CanStop` tinyint(1) unsigned NOT NULL default \'0\'';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'CurrentStatisticsId'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `CurrentStatisticsId` bigint(20) unsigned default NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'ModifiedDate'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `ModifiedDate` datetime default NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'ResumeDate'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `ResumeDate` datetime default NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'UsedTime'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `UsedTime` int(11) unsigned NOT NULL default \'0\'';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatistics', 'InitData'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `InitData` longtext';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatistics', 'AttemptCount'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `AttemptCount` int(11) unsigned NOT NULL default \'0\'';
			$this->executeQuery($query);
		}
		
		$query = 'ALTER TABLE #__ariquizstatisticsinfo CHANGE `Status` `Status` SET ("Prepare", "Process", "Finished", "Pause") NOT NULL DEFAULT "Process"';
		$this->executeQuery($query);
		
		if ($this->_isIndexExists('#__ariquizquizcategory', 'SSCUniquePair'))
		{
			$this->executeQuery('ALTER TABLE #__ariquizquizcategory DROP INDEX `SSCUniquePair`');
		}
	}
	
	function _updateTo_2_5_0()
	{
		$database = $this->_db;
		
		if (!$this->_isColumnExists('#__ariquiz', 'QuestionOrderType'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `QuestionOrderType` set(\'Numeric\',\'AlphaLower\',\'AlphaUpper\') NOT NULL default \'Numeric\'';
			$this->executeQuery($query);
		}
		
		$query = 'ALTER TABLE #__ariquizstatistics_attempt CHANGE `StatisticsId` `StatisticsId` bigint(20) unsigned NOT NULL';
		$this->executeQuery($query);
		
		// Fix hotspot questions
		$query = 'SELECT QV.QuestionVersionId,QV.Data,QV.QuestionId' .
			' FROM #__ariquizquestionversion QV LEFT JOIN #__ariquizquestiontype QT' .
			'	ON QV.QuestionTypeId = QT.QuestionTypeId' .
			' WHERE QT.ClassName="HotSpotQuestion"';
		$database->setQuery($query);
		$data = $database->loadAssocList();
		if (is_array($data))
		{
			$insertData = array();
			foreach ($data as $dataItem)
			{
				$questionData = $dataItem['Data'];
				if (empty($questionData)) continue;

				$fileId = 0;
				
				$matches = array();
				preg_match('/imgid="(\d+)"/i', $questionData, $matches);
				if (count($matches) > 1) $fileId = @intval($matches[1], 10);

				if ($fileId < 1) continue;

				$insertData[] = sprintf('(%d,%d,"hotspot_image",%d)',
					$fileId,
					$dataItem['QuestionVersionId'],
					$dataItem['QuestionId']);
			}
			
			if (count($insertData) > 0)
			{
				$query = 'INSERT INTO #__ariquiz_question_version_files (FileId,QuestionVersionId,`Alias`,QuestionId) VALUES ' . join(',', $insertData) . ' ON DUPLICATE KEY UPDATE FileId=FileId';
				$this->executeQuery($query);
			}
		}
	}

	function _updateTo_2_7_0()
	{
		if (!$this->_isColumnExists('#__ariquizstatistics', 'QuestionId'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `QuestionId` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatistics', 'BankQuestionId'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `BankQuestionId` int(10) unsigned NOT NULL default \'0\'';
			$this->executeQuery($query);
		}
		
		$query = 'UPDATE #__ariquizstatistics QS INNER JOIN #__ariquizquestionversion QQV' .
			'	ON QS.QuestionVersionId = QQV.QuestionVersionId' .
			' LEFT JOIN #__ariquizquestionversion QQV2' .
			'	ON QS.BankVersionId = QQV2.QuestionVersionId' .
			' SET' .
 			' QS.QuestionId = QQV.QuestionId,QS.BankQuestionId = QQV2.QuestionId';
		$this->executeQuery($query);

		if ($this->_isColumnExists('#__ariquizquestionversion', 'ShowAsImage'))
		{
			$query = 'ALTER TABLE #__ariquizquestionversion DROP COLUMN `ShowAsImage`';
			$this->executeQuery($query);
		}
		
		$modifyQueries = array(
			'ALTER TABLE #__arigenerictemplate CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestion CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestion CHANGE `QuestionCategoryId` `QuestionCategoryId` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestion CHANGE `BankQuestionId` `BankQuestionId` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestion CHANGE `QuestionIndex` `QuestionIndex` int(11) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizcategory CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestioncategory CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestioncategory CHANGE `QuestionCount` `QuestionCount` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestioncategory CHANGE `QuestionTime` `QuestionTime` int(10) unsigned NOT NULL default "0"',		
			'ALTER TABLE #__ariquiz CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquiz CHANGE `QuestionCount` `QuestionCount` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquiz CHANGE `QuestionTime` `QuestionTime` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquiz CHANGE `ResultScaleId` `ResultScaleId` int(11) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizbankcategory CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestiontemplate CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',			
			'ALTER TABLE #__ariquizquestionversion CHANGE `QuestionCategoryId` `QuestionCategoryId` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestionversion CHANGE `QuestionTime` `QuestionTime` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizquestionversion CHANGE `BankQuestionId` `BankQuestionId` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizstatistics CHANGE `QuestionId` `QuestionId` int(10) unsigned NOT NULL',
			'ALTER TABLE #__ariquizstatistics CHANGE `BankQuestionId` `BankQuestionId` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizstatistics CHANGE `QuestionTime` `QuestionTime` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizstatisticsinfo CHANGE `TotalTime` `TotalTime` int(10) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquizfile CHANGE `ModifiedBy` `ModifiedBy` int(11) unsigned NOT NULL default "0"',
			'ALTER TABLE #__ariquiz_result_scale CHANGE `ModifiedBy` `ModifiedBy` int(10) unsigned NOT NULL default "0"',
		);
		
		foreach ($modifyQueries as $modifyQuery)
		{
			$this->executeQuery($modifyQuery);
		}
		
		if ($this->_isIndexExists('#__ariquiz_question_version_files', 'Alias'))
		{
			$this->executeQuery('ALTER TABLE #__ariquiz_question_version_files DROP INDEX `Alias`');
		}
		
		$indexesInfo = array(
			array(
				'Table' => '#__arigenerictemplate',
				'Index' => 'BaseTemplateId',
				'Query' => 'ALTER TABLE #__arigenerictemplate ADD INDEX `BaseTemplateId` (`BaseTemplateId`)'
			),
			array(
				'Table' => '#__arigenerictemplatebase',
				'Index' => 'Group',
				'Query' => 'ALTER TABLE #__arigenerictemplatebase ADD INDEX `Group` (`Group`)'
			),
			array(
				'Table' => '#__arigenerictemplateentitymap',
				'Index' => 'TemplateEntityMap',
				'Query' => 'ALTER TABLE #__arigenerictemplateentitymap ADD UNIQUE INDEX `TemplateEntityMap` (`EntityName`(50),`EntityId`,`TemplateType`(50))'
			),
			array(
				'Table' => '#__arigenerictemplateentitymap',
				'Index' => 'TemplateId',
				'Query' => 'ALTER TABLE #__arigenerictemplateentitymap ADD INDEX `TemplateId` (`TemplateId`)'
			),
			array(
				'Table' => '#__arigenerictemplateparam',
				'Index' => 'BaseTemplateId',
				'Query' => 'ALTER TABLE #__arigenerictemplateparam ADD INDEX `BaseTemplateId` (`BaseTemplateId`)'
			),
			array(
				'Table' => '#__ariquizmailtemplate',
				'Index' => 'TextTemplateId',
				'Query' => 'ALTER TABLE #__ariquizmailtemplate ADD INDEX `TextTemplateId` (`TextTemplateId`)'
			),
			array(
				'Table' => '#__ariquizquestion',
				'Index' => 'QuestionVersionId',
				'Query' => 'ALTER TABLE #__ariquizquestion ADD UNIQUE INDEX `QuestionVersionId` (`QuestionVersionId`)'
			),
			array(
				'Table' => '#__ariquizquestion',
				'Index' => 'Sorting_QuestionIndex',
				'Query' => 'ALTER TABLE #__ariquizquestion ADD INDEX `Sorting_QuestionIndex` (`QuizId`,`Status`,`QuestionIndex`)'
			),
			array(
				'Table' => '#__ariquizquestion',
				'Index' => 'Status',
				'Query' => 'ALTER TABLE #__ariquizquestion ADD INDEX `Status` (`Status`)'
			),
			array(
				'Table' => '#__ariquizquestion',
				'Index' => 'BankQuestionId',
				'Query' => 'ALTER TABLE #__ariquizquestion ADD INDEX `BankQuestionId` (`BankQuestionId`)'
			),
			array(
				'Table' => '#__ariquizquestion',
				'Index' => 'QuestionTypeId',
				'Query' => 'ALTER TABLE #__ariquizquestion ADD INDEX `QuestionTypeId` (`QuestionTypeId`)'
			),
			array(
				'Table' => '#__ariquizquestion',
				'Index' => 'QuestionCategoryId',
				'Query' => 'ALTER TABLE #__ariquizquestion ADD INDEX `QuestionCategoryId` (`QuestionCategoryId`)'
			),
			array(
				'Table' => '#__ariquizquestioncategory',
				'Index' => 'QuizId',
				'Query' => 'ALTER TABLE #__ariquizquestioncategory ADD INDEX `QuizId` (`QuizId`)'
			),
			array(
				'Table' => '#__ariquizquestioncategory',
				'Index' => 'Status',
				'Query' => 'ALTER TABLE #__ariquizquestioncategory ADD INDEX `Status` (`Status`)'
			),
			array(
				'Table' => '#__ariquiz',
				'Index' => 'CssTemplateId',
				'Query' => 'ALTER TABLE #__ariquiz ADD INDEX `CssTemplateId` (`CssTemplateId`)'
			),
			array(
				'Table' => '#__ariquizquestiontemplate',
				'Index' => 'QuestionTypeId',
				'Query' => 'ALTER TABLE #__ariquizquestiontemplate ADD INDEX `QuestionTypeId` (`QuestionTypeId`)'
			),
			array(
				'Table' => '#__ariquizquestiontemplate',
				'Index' => 'TemplateName',
				'Query' => 'ALTER TABLE #__ariquizquestiontemplate ADD INDEX `TemplateName` (`TemplateName`)'
			),
			array(
				'Table' => '#__ariquizquestionversion',
				'Index' => 'QuestionId',
				'Query' => 'ALTER TABLE #__ariquizquestionversion ADD INDEX `QuestionId` (`QuestionId`)'
			),
			array(
				'Table' => '#__ariquizquestionversion',
				'Index' => 'QuestionCategoryId',
				'Query' => 'ALTER TABLE #__ariquizquestionversion ADD INDEX `QuestionCategoryId` (`QuestionCategoryId`)'
			),
			array(
				'Table' => '#__ariquizquestionversion',
				'Index' => 'QuestionTypeId',
				'Query' => 'ALTER TABLE #__ariquizquestionversion ADD INDEX `QuestionTypeId` (`QuestionTypeId`)'
			),
			array(
				'Table' => '#__ariquizquestionversion',
				'Index' => 'BankQuestionId',
				'Query' => 'ALTER TABLE #__ariquizquestionversion ADD INDEX `BankQuestionId` (`BankQuestionId`)'
			),
			array(
				'Table' => '#__ariquizstatistics',
				'Index' => 'QuestionVersionId',
				'Query' => 'ALTER TABLE #__ariquizstatistics ADD INDEX `QuestionVersionId` (`QuestionVersionId`)'
			),
			array(
				'Table' => '#__ariquizstatistics',
				'Index' => 'StatisticsInfoId',
				'Query' => 'ALTER TABLE #__ariquizstatistics ADD INDEX `StatisticsInfoId` (`StatisticsInfoId`)'
			),
			array(
				'Table' => '#__ariquizstatistics',
				'Index' => 'QuestionCategoryId',
				'Query' => 'ALTER TABLE #__ariquizstatistics ADD INDEX `QuestionCategoryId` (`QuestionCategoryId`)'
			),
			array(
				'Table' => '#__ariquizstatistics',
				'Index' => 'BankVersionId',
				'Query' => 'ALTER TABLE #__ariquizstatistics ADD INDEX `BankVersionId` (`BankVersionId`)'
			),
			array(
				'Table' => '#__ariquizstatisticsinfo',
				'Index' => 'TicketId',
				'Query' => 'ALTER TABLE #__ariquizstatisticsinfo ADD UNIQUE INDEX `TicketId` (`TicketId`)'
			),
			array(
				'Table' => '#__ariquizstatisticsinfo',
				'Index' => 'CurrentStatisticsId',
				'Query' => 'ALTER TABLE #__ariquizstatisticsinfo ADD UNIQUE INDEX `CurrentStatisticsId` (`CurrentStatisticsId`)'
			),
			array(
				'Table' => '#__ariquizstatisticsinfo',
				'Index' => 'QuizId',
				'Query' => 'ALTER TABLE #__ariquizstatisticsinfo ADD INDEX `QuizId` (`QuizId`)'
			),
			array(
				'Table' => '#__ariquizstatisticsinfo',
				'Index' => 'UserId',
				'Query' => 'ALTER TABLE #__ariquizstatisticsinfo ADD INDEX `UserId` (`UserId`)'
			),
			array(
				'Table' => '#__ariquizstatisticsinfo',
				'Index' => 'Status',
				'Query' => 'ALTER TABLE #__ariquizstatisticsinfo ADD INDEX `Status` (`Status`)'
			),
			array(
				'Table' => '#__ariquizfile',
				'Index' => 'Group',
				'Query' => 'ALTER TABLE #__ariquizfile ADD INDEX `Group` (`Group`)'
			),
			array(
				'Table' => '#__ariquizfile',
				'Index' => 'Sorting_ShortDescription',
				'Query' => 'ALTER TABLE #__ariquizfile ADD INDEX `Sorting_ShortDescription` (`Group`(20),`ShortDescription`)'
			),
			
			array(
				'Table' => '#__ariquiz_result_scale',
				'Index' => 'ScaleName',
				'Query' => 'ALTER TABLE #__ariquiz_result_scale ADD INDEX `ScaleName` (`ScaleName`)'
			),
			array(
				'Table' => '#__ariquiz_result_scale_item',
				'Index' => 'ScaleId',
				'Query' => 'ALTER TABLE #__ariquiz_result_scale_item ADD INDEX `ScaleId` (`ScaleId`)'
			),
			array(
				'Table' => '#__ariquiz_result_scale_item',
				'Index' => 'TextTemplateId',
				'Query' => 'ALTER TABLE #__ariquiz_result_scale_item ADD INDEX `TextTemplateId` (`TextTemplateId`)'
			),
			array(
				'Table' => '#__ariquiz_result_scale_item',
				'Index' => 'MailTemplateId',
				'Query' => 'ALTER TABLE #__ariquiz_result_scale_item ADD INDEX `MailTemplateId` (`MailTemplateId`)'
			),
			array(
				'Table' => '#__ariquiz_result_scale_item',
				'Index' => 'PrintTemplateId',
				'Query' => 'ALTER TABLE #__ariquiz_result_scale_item ADD INDEX `PrintTemplateId` (`PrintTemplateId`)'
			),
			array(
				'Table' => '#__ariquizquestiontype',
				'Index' => 'QuestionType',
				'Query' => 'ALTER TABLE #__ariquizquestiontype ADD UNIQUE INDEX `QuestionType` (`QuestionType`)'
			),
			
			array(
				'Table' => '#__ariquiz_question_version_files',
				'Index' => 'QuestionVersionId',
				'Query' => 'ALTER TABLE #__ariquiz_question_version_files ADD UNIQUE INDEX `QuestionVersionId` (`QuestionVersionId`, `Alias`)'
			),
			array(
				'Table' => '#__ariquiz_question_version_files',
				'Index' => 'QuestionId',
				'Query' => 'ALTER TABLE #__ariquiz_question_version_files ADD INDEX `QuestionId` (`QuestionId`)'
			),
			array(
				'Table' => '#__ariquiz_question_version_files',
				'Index' => 'FileId',
				'Query' => 'ALTER TABLE #__ariquiz_question_version_files ADD INDEX `FileId` (`FileId`)'
			),
		);
		
		foreach ($indexesInfo as $indexInfo)
		{
			if (!$this->_isIndexExists($indexInfo['Table'], $indexInfo['Index']))
			{
				$this->executeQuery($indexInfo['Query']);
			}
		}
	}
	
	function _updateTo_2_8_0()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'ShowCorrectAnswer'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `ShowCorrectAnswer` tinyint(1) unsigned NOT NULL default \'0\'';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquiz', 'ShowExplanation'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `ShowExplanation` tinyint(1) unsigned NOT NULL default \'0\'';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquiz', 'Anonymous'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `Anonymous` SET("Yes","No","ByUser") NOT NULL default "Yes"';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquiz', 'FullStatistics'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `FullStatistics` SET("Never","Always","OnLastAttempt") NOT NULL default "Never"';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_9_4()
	{
		$query = 'ALTER TABLE #__ariquiz CHANGE `FullStatistics` `FullStatistics` SET("Never","Always","OnLastAttempt","OnSuccess","OnFail") NOT NULL default "Never"';
		$this->executeQuery($query);
	}
	
	function _updateTo_2_9_6()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'MailGroupList'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `MailGroupList` VARCHAR(255) default NULL';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_9_7()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'AutoMailToUser'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `AutoMailToUser` tinyint(1) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_9_9()
	{
		if (!$this->_isColumnExists('#__ariquizquestionversion', 'OnlyCorrectAnswer'))
		{
			$query = 'ALTER TABLE #__ariquizquestionversion ADD COLUMN `OnlyCorrectAnswer` tinyint(1) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_2_9_10()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'StartDate'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `StartDate` datetime default NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquiz', 'EndDate'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `EndDate` datetime default NULL';
			$this->executeQuery($query);
		}	
	}
	
	function _updateTo_3_0_0()
	{
		$database = $this->_db;
		
		// modify and add new columns
		$newColumns = array(
			array(
				'table' => '#__ariquiz',
				'column' => 'AdminMailTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `AdminMailTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'MailFailedTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `MailFailedTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'MailPassedTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `MailPassedTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'PrintFailedTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `PrintFailedTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'PrintPassedTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `PrintPassedTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'FailedTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `FailedTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'PassedTemplateId',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `PassedTemplateId` int(10) unsigned NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'ResultTemplateType',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `ResultTemplateType` enum("manual","scale") NOT NULL'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'ExtraParams',
				'query' => 'ALTER TABLE #__ariquiz ADD COLUMN `ExtraParams` text NOT NULL'
			),
			array(
				'table' => '#__ariquizquestionversion',
				'column' => 'HasFiles',
				'query' => 'ALTER TABLE #__ariquizquestionversion ADD COLUMN `HasFiles` tinyint(1) unsigned NOT NULL'
			)
		);
		foreach ($newColumns as $newColumn)
		{
			if (!$this->_isColumnExists($newColumn['table'], $newColumn['column']))
			{
				$this->executeQuery($newColumn['query']);
			}
		}
		
		$queries = array(		
			'ALTER TABLE #__ariquiz CHANGE `PassedScore` `PassedScore` decimal(5,2) unsigned NOT NULL default "0.00"',
			'ALTER TABLE #__ariquizquestionversion CHANGE `Score` `Score` decimal(5,2) unsigned NOT NULL',
			'ALTER TABLE #__ariquizstatisticsinfo CHANGE `PassedScore` `PassedScore` decimal(5,2) unsigned NOT NULL default "0.00"',
			'ALTER TABLE #__ariquizstatisticsinfo CHANGE `UserScore` `UserScore` decimal(5,2) unsigned NOT NULL default "0.00"',
			'ALTER TABLE #__ariquizstatisticsinfo CHANGE `MaxScore` `MaxScore` decimal(5,2) unsigned NOT NULL default "0.00"',
			'ALTER TABLE #__ariquiz_result_scale_item CHANGE `BeginPoint` `BeginPoint` decimal(5,2) unsigned NOT NULL default "0.00"',
			'ALTER TABLE #__ariquiz_result_scale_item CHANGE `EndPoint` `EndPoint` decimal(5,2) unsigned NOT NULL default "0.00"'
		);
		
		foreach ($queries as $queryItem)
		{
			$this->executeQuery($queryItem);
		}
		
		// convert text and mail templates
		$queries = array(
			'UPDATE #__ariquiz SET ResultTemplateType = "manual" WHERE ResultScaleId = 0',
			'UPDATE #__ariquiz SET ResultTemplateType = "scale" WHERE ResultScaleId > 0',
		);
		foreach ($queries as $queryItem)
		{
			$this->executeQuery($queryItem);
		}
		
		$database->setQuery(
			'SELECT T.*,TB.Group AS TemplateGroup FROM #__arigenerictemplate T INNER JOIN #__arigenerictemplatebase TB ON T.BaseTemplateId = TB.BaseTemplateId'
		);
		$templates = $database->loadObjectList();
		
		if (!empty($templates))
		{
			require_once dirname(__FILE__) . DS . 'models' . DS . 'resulttemplate.php';
			require_once dirname(__FILE__) . DS . 'models' . DS . 'texttemplate.php';

			require_once dirname(__FILE__) . DS . 'tables' . DS . 'texttemplate.php';
			require_once dirname(__FILE__) . DS . 'tables' . DS . 'resulttemplate.php';
			require_once dirname(__FILE__) . DS . 'tables' . DS . 'mailtemplate.php';
		
			$resultTemplatesMapping = array();
			$mailTemplatesMapping = array();
			$resultTemplateModel = AriModel::getInstance('resulttemplate', $this->_modelPrefix);
			$mailTemplateModel = AriModel::getInstance('texttemplate', $this->_modelPrefix, array('group' => 'QuizMailResult'));
			foreach ($templates as $template)
			{
				$resultTemplate = null;
				if ($template->TemplateGroup == 'QuizResult')
					$resultTemplate = $resultTemplateModel->saveTemplate(
						array(
							'TemplateName' => $template->TemplateName,
							'Value' => $template->Value
						)
					);
				else if ($template->TemplateGroup == 'QuizMailResult')
					$resultTemplate = $mailTemplateModel->saveTemplate(
						array(
							'TemplateName' => $template->TemplateName,
							'Value' => $template->Value
						)
					);

				if ($resultTemplate)
				{
					if ($template->TemplateGroup == 'QuizResult')
						$resultTemplatesMapping[$template->TemplateId] = $resultTemplate->TemplateId;
					else if ($template->TemplateGroup == 'QuizMailResult')
						$mailTemplatesMapping[$template->TemplateId] = $resultTemplate->TemplateId;
				}
			}
			
			if (count($mailTemplatesMapping) > 0)
			{
				$queries = array();
				foreach ($mailTemplatesMapping as $oldTemplateId => $newTemplateId)
				{
					$queries[] = sprintf(
						'UPDATE #__ariquizmailtemplate SET TextTemplateId = %1$d WHERE TextTemplateId = %2$d',
						$newTemplateId,
						$oldTemplateId
					);
				}
				
				foreach ($queries as $queryItem)
				{
					$this->executeQuery($queryItem);
				}
			}
			
			$database->setQuery('SELECT TemplateId,TemplateType,EntityId FROM #__arigenerictemplateentitymap WHERE EntityName = "AriQuiz"');
			$entityMapping = $database->loadAssocList();

			if (is_array($entityMapping) && count($entityMapping) > 0)
			{
				$queries = array();
				foreach ($entityMapping as $mappingItem)
				{
					$field = '';
					switch ($mappingItem['TemplateType'])
					{
						case 'QuizSuccessful':
							$field = 'PassedTemplateId';
							break;
						
						case 'QuizFailed':
							$field = 'FailedTemplateId';
							break;
							
						case 'QuizSuccessfulEmail':
							$field = 'MailPassedTemplateId';
							break;
							
						case 'QuizFailedEmail':
							$field = 'MailFailedTemplateId';
							break;
							
						case 'QuizSuccessfulPrint':
							$field = 'PrintPassedTemplateId';
							break;
							
						case 'QuizFailedPrint':
							$field = 'PrintFailedTemplateId';
							break;
							
						case 'QuizAdminEmail':
							$field = 'AdminMailTemplateId';
							break;
					}
					
					if (empty($field) || empty($resultTemplatesMapping[$mappingItem['TemplateId']]))
						continue ;
						
					$queries[] = sprintf('UPDATE #__ariquiz SET %1$s = %2$d WHERE QuizId = %3$d',
						$field,
						$resultTemplatesMapping[$mappingItem['TemplateId']],
						$mappingItem['EntityId']
					);
				}
				
				if (count($queries) > 0)
				{
					foreach ($queries as $queryItem)
					{
						$this->executeQuery($queryItem);
					}
				}
			}
		}

		// convert files
		$this->executeQuery(
			'UPDATE `#__ariquizquestionversion` SET HasFiles = 1 WHERE QuestionVersionId IN (SELECT QuestionVersionId FROM #__ariquiz_question_version_files)'
		);

		$database->setQuery(
			'SELECT FileId,Content,FileName FROM #__ariquizfile WHERE `Group` = "hotspot"'
		);
		$images = $database->loadObjectList();
		
		if (is_array($images) && count($images) > 0)
		{
			require_once dirname(__FILE__) . DS . 'models' . DS . 'files.php';
			require_once dirname(__FILE__) . DS . 'models' . DS . 'folders.php';
			require_once dirname(__FILE__) . DS . 'tables' . DS . 'folder.php';
			require_once dirname(__FILE__) . DS . 'tables' . DS . 'file.php';
			
			$imagesFoldersModel = AriModel::getInstance('folders', $this->_modelPrefix, array('group' => ARIQUIZ_FOLDER_IMAGES));
			$filesModel = AriModel::getInstance('Files', $this->_modelPrefix);
			$imagesRootFolder = $imagesFoldersModel->getRootFolder();
			
			$jConfig = JFactory::getConfig();
			$tmpFolder = $jConfig->get('config.tmp_path', JPATH_ROOT . DS . 'tmp');
			$filesMapping = array();
			$newFileIds = array();
			$rootDir = AriQuizHelper::getFilesDir(ARIQUIZ_FOLDER_IMAGES);
			foreach ($images as $image)
			{
				$tmpFilePath = $tmpFolder . DS . $image->FileName;
				if (!JFile::write($tmpFilePath, $image->Content))
				{
					trigger_error('Old hotspot image "' . $image->FileName . '" couldn\'t be converted to new version.', E_USER_ERROR);
					continue ;
				}
				
				$fileVersion = $filesModel->saveFileVersion(
					array(
						'FileSize' => filesize($tmpFilePath)
					)
				);
				
				if (empty($fileVersion))
				{
					trigger_error('Old hotspot image "' . $image->FileName . '" couldn\'t be converted to new version.', E_USER_ERROR);
					continue ;
				}
				
				$pathInfo = pathinfo($image->FileName);
				$versionFileName = $pathInfo['filename'] . '_' . $fileVersion->FileVersionId;
				if (!empty($pathInfo['extension']))
					$versionFileName .= '.' . $pathInfo['extension'];

				$filePath = $rootDir . DS . $versionFileName;
				$fileVersion->FileName = $versionFileName;
				
				$imageInfo = @getimagesize($tmpFilePath);
				$mimeType = '';
				if (isset($imageInfo['mime']))
					$mimeType = $imageInfo['mime'];
				
				if (!JFile::move($tmpFilePath, $filePath))
				{
					trigger_error('Old hotspot image "' . $image->FileName . '" couldn\'t be converted to new version.', E_USER_ERROR);
					continue ;
				}
								
				$fileData = array(
					'MimeType' => $mimeType,
					'OriginalName' => $image->FileName,
					'Group' => ARIQUIZ_FOLDER_IMAGES,
					'FolderId' => $imagesRootFolder->id,
					'FileVersion' => $fileVersion
				);
				$file = $filesModel->saveFile($fileData);
								
				$filesMapping[$image->FileId] = array(
					'FileId' => $file->FileId,
					'FileVersionId' => $file->FileVersionId
				);
				$newFileIds[] = $file->FileId;
			}
			
			if (count($filesMapping) > 0)
			{
				$queries = array();
				foreach ($filesMapping as $oldFileId => $fileMapping)
				{
					$queries[] = sprintf(
						'UPDATE #__ariquiz_question_version_files SET FileId = %1$d WHERE FileId = %2$d',
						$fileMapping['FileId'],
						$oldFileId
					);
				}
				
				$queries[] = sprintf(
					'INSERT INTO #__ariquizstatistics_files (FileVersionId,Alias,StatisticsInfoId,QuestionId,StatisticsId) ' .
					'SELECT F.FileVersionId,"hotspot_image" AS Alias,S.StatisticsInfoId,S.QuestionId,S.StatisticsId ' .
					'FROM #__ariquizstatistics S INNER JOIN #__ariquiz_question_version_files QVF ON IF(S.BankVersionId>0,S.BankVersionId,S.QuestionVersionId) = QVF.QuestionVersionId ' .
					'INNER JOIN #__ariquiz_file F ON QVF.FileId = F.FileId ' .
					'WHERE F.FileId IN (' . join(',', $newFileIds) . ')'
				);

				foreach ($queries as $queryItem)
				{
					$this->executeQuery($queryItem);
				}
			}
		}
		
		// convert quiz properties
		$database->setQuery('SELECT * FROM #__ariquiz');
		$quizzes = $database->loadObjectList();
		
		if (!empty($quizzes))
		{
			$queries = array();
			foreach ($quizzes as $quiz)
			{
				$quizParams = array(
					'CanSkip' => $quiz->CanSkip,
					'CanStop' => $quiz->CanStop,
					'UseCalculator' => isset($quiz->UseCalculator) ? $quiz->UseCalculator : '0',
					'ParsePluginTag' => $quiz->ParsePluginTag,
					'ShowCorrectAnswer' => $quiz->ShowCorrectAnswer,
					'ShowExplanation' => $quiz->ShowExplanation,
					'AnswersOrderType' => $quiz->QuestionOrderType,
				);
				
				$queries[] = sprintf(
					'UPDATE #__ariquiz SET ExtraParams = %1$s WHERE QuizId = %2$d',
					$database->quote(json_encode($quizParams)),
					$quiz->QuizId
				);
			}
			
			foreach ($queries as $queryItem)
			{
				$this->executeQuery($queryItem);
			}
		}
		
		// delete old tables
		$query = 'DROP TABLE IF EXISTS #__arigenerictemplate, #__arigenerictemplatebase, #__arigenerictemplateentitymap, #__arigenerictemplateparam, #__ariquizfile, #__ariquiz_persistance, #__ariquiz_property, #__ariquiz_property_value';
		$this->executeQuery($query);
		
		// drop unused columns
		$dropColumns = array(
			array(
				'table' => '#__ariquiz',
				'column' => 'CanSkip'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'CanStop'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'UseCalculator'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'ParsePluginTag'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'ShowCorrectAnswer'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'ShowExplanation'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'QuestionOrderType'
			),
			array(
				'table' => '#__ariquiz',
				'column' => 'CssTemplateId'
			)
		);
		
		foreach ($dropColumns as $dropColumn)
		{
			if ($this->_isColumnExists($dropColumn['table'], $dropColumn['column']))
			{
				$query = sprintf('ALTER TABLE %1$s DROP COLUMN `%2$s`',
					$dropColumn['table'],
					$dropColumn['column']
				);
				$this->executeQuery($query);
			}
		}
		
		// delete unused folders and files
		$folders = array(
			$this->adminPath . 'install' . DS . 'css',
			$this->adminPath . 'kernel' . DS . 'Cache',
			$this->adminPath . 'kernel' . DS . 'Components',
			$this->adminPath . 'kernel' . DS . 'Config',
			$this->adminPath . 'kernel' . DS . 'Constants',
			$this->adminPath . 'kernel' . DS . 'Controllers',
			$this->adminPath . 'kernel' . DS . 'Core',
			$this->adminPath . 'kernel' . DS . 'Data' . DS . 'Export',
			$this->adminPath . 'kernel' . DS . 'Data' . DS . 'Import',
			$this->adminPath . 'kernel' . DS . 'Date',
			$this->adminPath . 'kernel' . DS . 'Entity',
			$this->adminPath . 'kernel' . DS . 'Event',
			$this->adminPath . 'kernel' . DS . 'File',
			$this->adminPath . 'kernel' . DS . 'GlobalPrefs',
			$this->adminPath . 'kernel' . DS . 'I18N',
			$this->adminPath . 'kernel' . DS . 'MailTemplates',
			$this->adminPath . 'kernel' . DS . 'Persistance',
			$this->adminPath . 'kernel' . DS . 'PHPCompat',
			$this->adminPath . 'kernel' . DS . 'Remote',
			$this->adminPath . 'kernel' . DS . 'Security',
			$this->adminPath . 'kernel' . DS . 'SecurityRules',
			$this->adminPath . 'kernel' . DS . 'System',
			$this->adminPath . 'kernel' . DS . 'Text',
			$this->adminPath . 'kernel' . DS . 'TextTemplates',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Page',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Utils',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'Toolbar',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'Validators',
			$this->adminPath . 'kernel' . DS . 'Xml' . DS . '_Templates',
			$this->basePath . 'views' . DS . 'results' 
		);
		foreach ($folders as $folder)
			if (@file_exists($folder) && @is_dir($folder))
				JFolder::delete($folder);
		
		$files = array(
			$this->adminPath . 'kernel' . DS . 'Install' . DS . 'class.XmlInstaller.php',
			$this->adminPath . 'kernel' . DS . 'Joomla' . DS . 'class.JoomlaHelper.php',
			$this->adminPath . 'kernel' . DS . 'Joomla' . DS . 'class.JoomlaBridge.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'class.TaskManager.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'class.WebControl.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'class.TextBox.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'class.ListBox.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'class.Editor.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'class.ControlFactory.php',
			$this->adminPath . 'kernel' . DS . 'Web' . DS . 'Controls' . DS . 'class.CheckBox.php',
			$this->adminPath . 'kernel' . DS . 'Xml' . DS . 'class.SimpleXmlHelper.php',
			$this->adminPath . 'kernel' . DS . 'Xml' . DS . 'class.SimpleXml.php',
			$this->adminPath . DS . 'elements' . DS . 'category.php',
			$this->adminPath . DS . 'fields' . DS . 'category.php',
			$this->adminPath . DS . 'install' . DS . 'description.xml'
		);
		foreach ($files as $file)
			if (@file_exists($file) && @is_file($file))
				JFile::delete($file);
	}

	function _updateTo_3_1_0()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'asset_id'))
		{
			$this->executeQuery('ALTER TABLE #__ariquiz ADD COLUMN `asset_id` int(10) unsigned NOT NULL default "0"');
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'asset_id'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `asset_id` int(10) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizquestion', 'asset_id'))
		{
			$query = 'ALTER TABLE #__ariquizquestion ADD COLUMN `asset_id` int(10) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizquestioncategory', 'asset_id'))
		{
			$query = 'ALTER TABLE #__ariquizquestioncategory ADD COLUMN `asset_id` int(10) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizbankcategory', 'asset_id'))
		{
			$query = 'ALTER TABLE #__ariquizbankcategory ADD COLUMN `asset_id` int(10) unsigned NOT NULL default "0"';
			$this->executeQuery($query);
		}		
	}
	
	function _updateTo_3_1_1()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'Metadata'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `Metadata` TEXT NOT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquizcategory', 'Metadata'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `Metadata` TEXT NOT NULL';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_3_1_2()
	{
		if (!$this->_isColumnExists('#__ariquizquestionversion', 'Penalty'))
		{
			$query = 'ALTER TABLE #__ariquizquestionversion ADD COLUMN `Penalty` decimal(5,2) NOT NULL';
			$this->executeQuery($query);
		}
		
		$query = 'ALTER TABLE #__ariquizstatistics CHANGE `Score` `Score` decimal(5,2) NOT NULL';
		$this->executeQuery($query);
	}

	function _updateTo_3_2_0()
	{
		$database = $this->_db;
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'parent_id'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `parent_id` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'lft'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `lft` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'rgt'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `rgt` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'level'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `level` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'title'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `title` varchar(255) NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'alias'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `alias` varchar(255) NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'access'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `access` tinyint(3) NOT NULL';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizcategory', 'path'))
		{
			$query = 'ALTER TABLE #__ariquizcategory ADD COLUMN `path` varchar(255) NOT NULL';
			$this->executeQuery($query);
		}

		$indexesInfo = array(
			array(
				'Table' => '#__ariquizcategory',
				'Index' => 'idx',
				'Query' => 'ALTER TABLE #__ariquizcategory ADD INDEX `idx` (`lft`,`rgt`)'
			),
			array(
				'Table' => '#__ariquizcategory',
				'Index' => 'idx_lft',
				'Query' => 'ALTER TABLE #__ariquizcategory ADD INDEX `idx_lft` (`lft`)'
			)
		);

		foreach ($indexesInfo as $indexInfo)
		{
			if (!$this->_isIndexExists($indexInfo['Table'], $indexInfo['Index']))
			{
				$this->executeQuery($indexInfo['Query']);
			}
		}
		
		// update cat		
		require_once dirname(__FILE__) . DS . 'tables' . DS . 'category.php';

		$categoryTable = AriTable::getInstance('category', $this->_tblPrefix);
		$rootCategoryId = $categoryTable->addRoot(array(
			'CategoryName' => $database->quote(''),
		));
		if ($rootCategoryId === false)
			trigger_error('Couldn\'t not create root category.', E_USER_ERROR);	

		$database->setQuery(
			sprintf(
				'UPDATE #__ariquizcategory SET parent_id = %1$d WHERE parent_id = 0 AND CategoryId <> %1$d',
				$rootCategoryId
			)
		);
		$database->execute();
		$categoryTable->rebuild();		
		// end updated cat
	}
	
	function _updateTo_3_3_2()
	{
		if (!$this->_isColumnExists('#__ariquizquestionversion', 'AttemptCount'))
		{
			$query = 'ALTER TABLE #__ariquizquestionversion ADD COLUMN `AttemptCount` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_3_3_4()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'FullStatisticsOnSuccess'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `FullStatisticsOnSuccess` SET("None","All","OnlyCorrect","OnlyIncorrect") NOT NULL default "All"';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquiz', 'FullStatisticsOnFail'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `FullStatisticsOnFail` SET("None","All","OnlyCorrect","OnlyIncorrect") NOT NULL default "All"';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_3_3_11()
	{
		$queries = array(
			'ALTER TABLE #__ariquizstatisticsinfo CHANGE `UserScore` `UserScore` decimal(7,2) unsigned NOT NULL default "0.00"',
			'ALTER TABLE #__ariquizstatisticsinfo CHANGE `MaxScore` `MaxScore` decimal(7,2) unsigned NOT NULL default "0.00"',
		);
		
		foreach ($queries as $queryItem)
		{
			$this->executeQuery($queryItem);
		}
	}
	
	function _updateTo_3_4_4()
	{
		$database = $this->_db;
		
		$queries = array(
			'ALTER TABLE #__ariquizcategory CHANGE `lft` `lft` int(11) NOT NULL default "0"',
			'ALTER TABLE #__ariquizcategory CHANGE `rgt` `rgt` int(11) NOT NULL default "0"'
		);
		
		foreach ($queries as $queryItem)
		{
			$this->executeQuery($queryItem);
		}
	}

	function _updateTo_3_4_11()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'AttemptPeriod'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `AttemptPeriod` text NOT NULL';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_3_4_12()
	{
		$database = $this->_db;
		
		$database->setQuery(
			'SELECT StatisticsInfoId,ExtraData FROM #__ariquizstatisticsinfo WHERE UserId = 0 AND LENGTH(ExtraData) > 0'
		);
		$data = $database->loadAssocList();
		if (is_null($data))
		{
			return ;
		}
		
		if (empty($data))
			return ;
			
		$limit = 50;
		$itemIdx = 1;
		$queryList = array();
		$query = 'INSERT INTO #__ariquiz_statistics_extradata (StatisticsInfoId,Name,Value) VALUES %s ON DUPLICATE KEY UPDATE Value=Value';
		$values = array();
		
		require_once dirname(__FILE__) . '/tables/userquiz.php';

		$statisticsInfo = AriTable::getInstance('userquiz', $this->_tblPrefix);
		foreach ($data as $dataItem)
		{
			$extraData = $statisticsInfo->parseExtraDataXml($dataItem['ExtraData']);
			if (!is_array($extraData) || count($extraData) == 0)
				continue ;
			
			foreach ($extraData as $name => $value)
			{
				$values[] = sprintf('(%d,%s,%s)',
					$dataItem['StatisticsInfoId'],
					$database->Quote($name),
					$database->Quote($value)
				);
			}

			if ($itemIdx % $limit == 0)
			{
				$queryList[] = sprintf($query, join(',', $values));
				$values = array();
				$itemIdx = 0;
			}

			++$itemIdx;
		}
		
		if (count($values) > 0)
			$queryList[] = sprintf($query, join(',', $values));
		
		foreach ($queryList as $queryItem)
		{
			$this->executeQuery($queryItem);
		}
	}
	
	function _updateTo_3_5_0()
	{
		// #__ariquizstatistics
		if (!$this->_isColumnExists('#__ariquizstatistics', 'PageNumber'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `PageNumber` mediumint(8) unsigned NOT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquizstatistics', 'PageId'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `PageId` bigint(20) DEFAULT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquizstatistics', 'Completed'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `Completed` tinyint(1) unsigned NOT NULL DEFAULT "0"';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquizstatistics', 'ElapsedTime'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics ADD COLUMN `ElapsedTime` mediumint(9) DEFAULT NULL';
			$this->executeQuery($query);
		}
		
		// copy data from #__ariquizstatistics table to #__ariquizstatistics_pages table
		$query = 'INSERT INTO #__ariquizstatistics_pages (
			StatisticsInfoId,
			PageNumber,
			QuestionCount,
			StartDate,
			EndDate,
			SkipDate,
			SkipCount,
			UsedTime,
			IpAddress,
			PageTime,
			PageIndex
		) 
		SELECT
			StatisticsInfoId,
			QuestionIndex,
			1,
			StartDate,
			EndDate,
			SkipDate,
			SkipCount,
			UsedTime,
			IpAddress,
			QuestionTime,
			QuestionIndex
		FROM
			#__ariquizstatistics';
		$this->executeQuery($query);

		$query = 'UPDATE #__ariquizstatistics S INNER JOIN #__ariquizstatistics_pages P ON S.StatisticsInfoId = P.StatisticsInfoId AND P.PageNumber = S.QuestionIndex SET S.PageId = P.PageId,S.PageNumber = P.PageNumber WHERE S.PageId IS NULL';
		$this->executeQuery($query);

		$query = 'UPDATE #__ariquizstatistics S INNER JOIN #__ariquizstatisticsinfo SI ON S.StatisticsInfoId = SI.StatisticsInfoId SET S.Completed = 1 WHERE (SI.Status = "Finished" OR S.EndDate IS NOT NULL)';
		$this->executeQuery($query);

		$query = 
		'UPDATE 
			#__ariquizstatistics S INNER JOIN #__ariquizstatisticsinfo SI 
				ON S.StatisticsInfoId = SI.StatisticsInfoId 
		SET 
			S.ElapsedTime = 
			CASE 
				WHEN 
					S.EndDate IS NOT NULL 
				THEN 
					UNIX_TIMESTAMP(S.EndDate) - UNIX_TIMESTAMP(S.StartDate) + S.UsedTime

				WHEN
					SI.Status = "Finished"
				THEN
					IF(
						S.QuestionTime > 0,
						LEAST(
							S.QuestionTime,
							IF(
								S.StartDate IS NOT NULL,
								UNIX_TIMESTAMP(SI.EndDate) - UNIX_TIMESTAMP(S.StartDate) + S.UsedTime,
								S.UsedTime 
							)
						),
						IF(
							S.StartDate IS NOT NULL,
							UNIX_TIMESTAMP(SI.EndDate) - UNIX_TIMESTAMP(S.StartDate) + S.UsedTime,
							S.UsedTime 
						)
					)
			END';
		$this->executeQuery($query);
		
		// remove obsolete columns from #__ariquizstatistics table
		if ($this->_isColumnExists('#__ariquizstatistics', 'StartDate'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics DROP COLUMN `StartDate`';
			$this->executeQuery($query);
		}
		
		if ($this->_isColumnExists('#__ariquizstatistics', 'SkipDate'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics DROP COLUMN `SkipDate`';
			$this->executeQuery($query);
		}

		if ($this->_isColumnExists('#__ariquizstatistics', 'SkipCount'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics DROP COLUMN `SkipCount`';
			$this->executeQuery($query);
		}
		
		if ($this->_isColumnExists('#__ariquizstatistics', 'UsedTime'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics DROP COLUMN `UsedTime`';
			$this->executeQuery($query);
		}

		if ($this->_isColumnExists('#__ariquizstatistics', 'QuestionTime'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics DROP COLUMN `QuestionTime`';
			$this->executeQuery($query);
		}

		if ($this->_isColumnExists('#__ariquizstatistics', 'IpAddress'))
		{
			$query = 'ALTER TABLE #__ariquizstatistics DROP COLUMN `IpAddress`';
			$this->executeQuery($query);
		}
		
		// #__ariquizstatisticsinfo table
		if ($this->_isColumnExists('#__ariquizstatisticsinfo', 'CurrentStatisticsId'))
		{
			if ($this->_isIndexExists('#__ariquizstatisticsinfo', 'CurrentStatisticsId'))
			{
				$this->executeQuery('ALTER TABLE #__ariquizstatisticsinfo DROP INDEX `CurrentStatisticsId`');
			}
			
			$query = 'ALTER TABLE #__ariquizstatisticsinfo DROP COLUMN `CurrentStatisticsId`';
			$this->executeQuery($query);
		}
		
		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'PageCount'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `PageCount` mediumint(8) unsigned NOT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'UserScorePercent'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `UserScorePercent` decimal(5,2) unsigned NOT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquizstatisticsinfo', 'ElapsedTime'))
		{
			$query = 'ALTER TABLE #__ariquizstatisticsinfo ADD COLUMN `ElapsedTime` mediumint(9) NOT NULL DEFAULT "0"';
			$this->executeQuery($query);
		}
		
		$query = 'UPDATE #__ariquizstatisticsinfo SET UserScorePercent = IF(MaxScore > 0, FORMAT(100 * UserScore/MaxScore, 2), 0.00),ElapsedTime = UNIX_TIMESTAMP(EndDate) - UNIX_TIMESTAMP(StartDate) + UsedTime WHERE Status = "Finished"';
		$this->executeQuery($query);

		$query = 'UPDATE #__ariquizstatisticsinfo SET PageCount = QuestionCount WHERE PageCount = 0';
		$this->executeQuery($query);
	}
	
	function _updateTo_3_5_1()
	{
		$database = $this->_db;
		
		if (!$this->_isColumnExists('#__ariquiz', 'StartImmediately'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `StartImmediately` tinyint(1) unsigned NOT NULL DEFAULT "0"';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_3_5_2()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'CertificateFailedTemplateId'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `CertificateFailedTemplateId` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquiz', 'CertificatePassedTemplateId'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `CertificatePassedTemplateId` int(10) unsigned NOT NULL';
			$this->executeQuery($query);
		}

		if (!$this->_isColumnExists('#__ariquiz_result_scale_item', 'CertificateTemplateId'))
		{
			$query = 'ALTER TABLE #__ariquiz_result_scale_item ADD COLUMN `CertificateTemplateId` int(11) unsigned NOT NULL';
			$this->executeQuery($query);
		}
	}
	
	function _updateTo_3_5_9()
	{		
		if (!$this->_isColumnExists('#__ariquiz', 'HideCorrectAnswers'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `HideCorrectAnswers` tinyint(1) unsigned NOT NULL DEFAULT "0"';
			$this->executeQuery($query);
		}
	}

	function _updateTo_3_5_10()
	{
		$query = 'UPDATE #__ariquizstatistics S LEFT JOIN (SELECT COUNT(*) AS Count,StatisticsId FROM #__ariquizstatistics_attempt GROUP BY StatisticsId) A ON S.StatisticsId = A.StatisticsId SET S.AttemptCount = IFNULL(A.Count, 0) + S.Completed';
		$this->executeQuery($query);
	}
	
	function _updateTo_3_6_0()
	{
		if (!$this->_isColumnExists('#__ariquiz', 'Access'))
		{
			$query = 'ALTER TABLE #__ariquiz ADD COLUMN `Access` tinyint(3) NOT NULL';
			$this->executeQuery($query);
		}

		$query = 'UPDATE #__ariquiz SET `Access` = 1';
		$this->executeQuery($query);
				
		if (!J1_6)
		{
			$query = 'UPDATE #__ariquiz Q LEFT JOIN #__ariquizaccess S ON Q.QuizId = S.QuizId SET Q.`Access` = S.GroupId';
			$this->executeQuery($query);
		}

		$this->executeQuery('DROP TABLE IF EXISTS #__ariquizaccess');
	}
	
	function _updateTo_3_6_16()
	{
		if (!$this->_isIndexExists('#__ariquizstatistics', 'PageId'))
		{
			$this->executeQuery('ALTER TABLE #__ariquizstatistics ADD INDEX `PageId` (`PageId`)');
		}
	}

    function _updateTo_3_7_0()
    {
        if (!$this->_isColumnExists('#__ariquiz_result_scale', 'ScaleType'))
        {
            $query = 'ALTER TABLE #__ariquiz_result_scale ADD COLUMN `ScaleType` enum("Percent","Score") NOT NULL DEFAULT "Percent"';
			$this->executeQuery($query);

        }
    }

    function _updateTo_3_7_4()
    {
        if (!$this->_isColumnExists('#__ariquiz', 'ShareResults'))
        {
            $query = 'ALTER TABLE #__ariquiz ADD COLUMN `ShareResults` tinyint(1) unsigned NOT NULL DEFAULT "0"';
			$this->executeQuery($query);
        }
    }

    function _updateTo_3_8_0()
    {
        if (!$this->_isColumnExists('#__ariquiz', 'PrevQuizId'))
        {
            $query = 'ALTER TABLE #__ariquiz ADD COLUMN `PrevQuizId` int(10) unsigned NOT NULL DEFAULT "0"';
			$this->executeQuery($query);
        }
    }

	function _updateTo_3_9_21()
    {
		$query = 'ALTER TABLE #__ariquiz CHANGE `Access` `Access` int(10) NOT NULL';
		$this->executeQuery($query);
	}

	function _updateTo_3_10_9()
	{
		$query = 'ALTER TABLE #__ariquizstatistics CHANGE `ElapsedTime` `ElapsedTime` bigint(20) DEFAULT NULL';
		$this->executeQuery($query);

		$query = 'ALTER TABLE #__ariquizstatisticsinfo CHANGE `ElapsedTime` `ElapsedTime` bigint(20) NOT NULL DEFAULT "0"';
		$this->executeQuery($query);
	}
}