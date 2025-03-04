CREATE TABLE IF NOT EXISTS `#__ariquizmailtemplate` (
    `MailTemplateId` int(10) unsigned NOT NULL auto_increment,
    `TextTemplateId` int(10) unsigned NOT NULL,
    `Subject` varchar(255) default NULL,
    `From` varchar(255) default NULL,
    `FromName` varchar(255) default NULL,
	`AllowHtml` tinyint(1) unsigned NOT NULL default '1',
    PRIMARY KEY  (`MailTemplateId`),
    KEY `TextTemplateId` (`TextTemplateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz` (
  `QuizId` int(10) unsigned NOT NULL auto_increment,
  `QuizName` varchar(255) NOT NULL,
  `CreatedBy` int(10) unsigned NOT NULL,
  `Created` datetime NOT NULL,
  `ModifiedBy` int(10) unsigned NOT NULL default '0',
  `Modified` datetime default NULL,
  `AccessType` int(10) unsigned default NULL,
  `Status` int(10) unsigned NOT NULL,
  `TotalTime` int(10) unsigned default NULL,
  `PassedScore` decimal(5,2) unsigned NOT NULL default '0.00',				  
  `QuestionCount` int(10) unsigned NOT NULL default '0',
  `QuestionTime` int(10) unsigned NOT NULL default '0',
  `Description` longtext,
  `RandomQuestion` tinyint(1) unsigned NOT NULL default '0',
  `LagTime` int(11) unsigned NOT NULL default '0',
  `AttemptCount` int(11) unsigned NOT NULL default '0',
  `AttemptPeriod` text NOT NULL,
  `AdminEmail` text,
  `ResultScaleId` int(11) unsigned NOT NULL default '0',
  `Anonymous` SET('Yes','No','ByUser') NOT NULL default 'Yes',
  `FullStatistics` SET('Never','Always','OnLastAttempt','OnSuccess','OnFail') NOT NULL default 'Never',
  `FullStatisticsOnSuccess` SET('None','All','OnlyCorrect','OnlyIncorrect') NOT NULL DEFAULT 'All',
  `FullStatisticsOnFail` SET('None','All','OnlyCorrect','OnlyIncorrect') NOT NULL DEFAULT 'All',
  `MailGroupList` VARCHAR(255) default NULL,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `AutoMailToUser` tinyint(1) unsigned NOT NULL default '0',
  `ExtraParams` text NOT NULL,
  `ResultTemplateType` enum('manual','scale') NOT NULL,
  `PassedTemplateId` int(10) unsigned NOT NULL,
  `FailedTemplateId` int(10) unsigned NOT NULL,
  `PrintPassedTemplateId` int(10) unsigned NOT NULL,
  `PrintFailedTemplateId` int(10) unsigned NOT NULL,
  `MailPassedTemplateId` int(10) unsigned NOT NULL,
  `MailFailedTemplateId` int(10) unsigned NOT NULL,
  `CertificateFailedTemplateId` int(10) unsigned NOT NULL,
  `CertificatePassedTemplateId` int(10) unsigned NOT NULL,
  `AdminMailTemplateId` int(10) unsigned NOT NULL,
  `Metadata` text NOT NULL,
  `StartImmediately` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `HideCorrectAnswers` tinyint(1) unsigned NOT NULL default '0',
  `Access` int(10) NOT NULL,
  `ShareResults` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `PrevQuizId` int(10) unsigned NOT NULL default '0',
  `asset_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`QuizId`),
  KEY `Status` (`Status`),
  KEY `ResultScaleId` (`ResultScaleId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizcategory` (
	`CategoryId` int(10) unsigned NOT NULL auto_increment,
	`CategoryName` varchar(255) NOT NULL,
	`Description` text NOT NULL,
	`Created` datetime NOT NULL,
	`CreatedBy` int(10) unsigned NOT NULL,
	`Modified` datetime default NULL,
	`ModifiedBy` int(10) unsigned NOT NULL default '0',
	`Metadata` text NOT NULL,
	`asset_id` int(10) unsigned NOT NULL default '0',
	`parent_id` int(10) unsigned NOT NULL,
	`lft` int(11) NOT NULL default '0',
	`rgt` int(11) NOT NULL default '0',
	`level` int(10) unsigned NOT NULL,
	`title` varchar(255) NOT NULL,
	`alias` varchar(255) NOT NULL,
	`access` tinyint(3) NOT NULL,
	`path` varchar(255) NOT NULL,
	PRIMARY KEY  (`CategoryId`),
	KEY `idx` (`lft`,`rgt`),
	KEY `idx_lft` (`lft`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizconfig` (
	`ParamName` varchar(100) NOT NULL,
	`ParamValue` varchar(255) NOT NULL,
	PRIMARY KEY  (`ParamName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizquestion` (
	`QuestionId` int(10) unsigned NOT NULL auto_increment,
	`QuizId` int(10) unsigned NOT NULL,
	`QuestionVersionId` bigint(20) default NULL,
	`Created` datetime NOT NULL,
	`CreatedBy` int(10) unsigned NOT NULL,
	`Modified` datetime default NULL,
	`ModifiedBy` int(10) unsigned NOT NULL default '0',
	`Status` int(11) unsigned NOT NULL,
	`QuestionIndex` int(11) unsigned NOT NULL default '0',
    `BankQuestionId` int(10) unsigned NOT NULL default '0',
	`QuestionTypeId` int(11) unsigned NOT NULL,
	`QuestionCategoryId` int(10) unsigned NOT NULL default '0',
	`asset_id` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`QuestionId`),
	UNIQUE KEY `QuestionVersionId` (`QuestionVersionId`),
	KEY `Sorting_QuestionIndex` (`QuizId`,`Status`,`QuestionIndex`),
	KEY `Status` (`Status`),
	KEY `BankQuestionId` (`BankQuestionId`),
	KEY `QuestionTypeId` (`QuestionTypeId`),
	KEY `QuestionCategoryId` (`QuestionCategoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizquestioncategory` (
	`QuestionCategoryId` int(10) unsigned NOT NULL auto_increment,
	`QuizId` int(10) unsigned NOT NULL,
	`CategoryName` varchar(255) NOT NULL,
	`Description` text,
	`Created` datetime NOT NULL,
	`CreatedBy` int(10) unsigned NOT NULL,
	`Modified` datetime default NULL,
	`ModifiedBy` int(10) unsigned NOT NULL default '0',
	`QuestionCount` int(10) unsigned NOT NULL default '0',
	`QuestionTime` int(10) unsigned NOT NULL default '0',
	`RandomQuestion` tinyint(1) unsigned NOT NULL default '0',
	`Status` int(11) unsigned NOT NULL default '1',
	`asset_id` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`QuestionCategoryId`),
	KEY `QuizId` (`QuizId`),
	KEY `Status` (`Status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizbankcategory` (
	`CategoryId` int(10) unsigned NOT NULL auto_increment,
	`CategoryName` varchar(255) NOT NULL,
	`Description` text NOT NULL,
	`Created` datetime NOT NULL,
	`CreatedBy` int(10) unsigned NOT NULL,
	`Modified` datetime default NULL,
	`ModifiedBy` int(10) unsigned NOT NULL default '0',
	`asset_id` int(10) unsigned NOT NULL default '0',
	PRIMARY KEY  (`CategoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizquestiontemplate` (
  `TemplateId` int(10) unsigned NOT NULL auto_increment,
  `TemplateName` varchar(255) NOT NULL,
  `QuestionTypeId` int(11) NOT NULL,
  `Data` longtext,
  `Created` datetime NOT NULL,
  `CreatedBy` int(11) unsigned NOT NULL,
  `Modified` datetime default NULL,
  `ModifiedBy` int(10) unsigned NOT NULL default '0',
  `DisableValidation` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`TemplateId`),
  KEY `QuestionTypeId` (`QuestionTypeId`),
  KEY `TemplateName` (`TemplateName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;				

CREATE TABLE IF NOT EXISTS `#__ariquizquestiontype` (
  `QuestionTypeId` int(10) unsigned NOT NULL auto_increment,
  `QuestionType` varchar(255) NOT NULL,
  `ClassName` varchar(255) NOT NULL,
  `Default` tinyint(1) unsigned NOT NULL,
  `CanHaveTemplate` tinyint(1) unsigned NOT NULL default '1',
  `TypeOrder` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`QuestionTypeId`),
  UNIQUE KEY `QuestionType` (`QuestionType`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;				

CREATE TABLE IF NOT EXISTS `#__ariquizquestionversion` (
  `QuestionVersionId` bigint(20) unsigned NOT NULL auto_increment,
  `QuestionId` int(10) unsigned NOT NULL,
  `QuestionCategoryId` int(10) unsigned NOT NULL default '0',
  `QuestionTime` int(10) unsigned NOT NULL default '0',
  `QuestionTypeId` int(11) unsigned NOT NULL,
  `Question` text NOT NULL,
  `HashCode` char(32) NOT NULL,
  `Created` datetime NOT NULL,
  `CreatedBy` int(10) unsigned NOT NULL,
  `Data` longtext NOT NULL,
  `Score` decimal(5,2) unsigned NOT NULL,
  `BankQuestionId` int(10) unsigned NOT NULL default '0',
  `Note` text default NULL,
  `OnlyCorrectAnswer` tinyint(1) unsigned NOT NULL default '0',
  `HasFiles` tinyint(1) unsigned NOT NULL,
  `Penalty` decimal(5,2) NOT NULL,
  `AttemptCount` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`QuestionVersionId`),
  KEY `QuestionId` (`QuestionId`),
  KEY `QuestionCategoryId` (`QuestionCategoryId`),
  KEY `QuestionTypeId` (`QuestionTypeId`),
  KEY `BankQuestionId` (`BankQuestionId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizquizcategory` (
	`QuizId` int(10) unsigned NOT NULL,
	`CategoryId` int(10) unsigned NOT NULL,
	PRIMARY KEY  (`QuizId`,`CategoryId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizstatistics` (
	`StatisticsId` bigint(20) unsigned NOT NULL auto_increment,
	`QuestionId` int(10) unsigned NOT NULL,
	`QuestionVersionId` bigint(20) unsigned NOT NULL,
	`StatisticsInfoId` bigint(20) NOT NULL,
	`Data` longtext,
	`EndDate` datetime default NULL,
	`QuestionIndex` int(10) unsigned NOT NULL,
	`Score` decimal(5,2) NOT NULL default '0',
	`QuestionCategoryId` int(10) unsigned NOT NULL,
	`BankQuestionId` int(10) unsigned NOT NULL default '0',
	`BankVersionId` bigint(20) unsigned NOT NULL default '0',
	`InitData` longtext,
	`AttemptCount` int(11) unsigned NOT NULL default '0',
	`PageNumber` mediumint(8) unsigned NOT NULL,
	`PageId` bigint(20) DEFAULT NULL,
	`Completed` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`ElapsedTime` bigint(20) DEFAULT NULL,
	PRIMARY KEY  (`StatisticsId`),
	KEY `QuestionVersionId` (`QuestionVersionId`),
	KEY `PageId` (`PageId`),
	KEY `StatisticsInfoId` (`StatisticsInfoId`),
	KEY `QuestionCategoryId` (`QuestionCategoryId`),
	KEY `BankVersionId` (`BankVersionId`)					
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizstatistics_attempt` (
	`StatisticsId` bigint(20) unsigned NOT NULL,
	`Data` longtext,
	`CreatedDate` datetime NOT NULL,
	KEY `StatisticsId` (`StatisticsId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizstatisticsinfo` (
	`StatisticsInfoId` bigint(20) unsigned NOT NULL auto_increment,
	`QuizId` int(10) unsigned NOT NULL,
	`UserId` int(10) unsigned NOT NULL default '0',
	`Status` set('Prepare','Process','Finished','Pause') NOT NULL default 'Process',
	`TicketId` char(32) NOT NULL,
	`StartDate` datetime default NULL,
	`EndDate` datetime default NULL,
	`PassedScore` decimal(5,2) unsigned NOT NULL default '0.00',
	`UserScore` decimal(7,2) unsigned NOT NULL default '0.00',
	`MaxScore` decimal(7,2) unsigned NOT NULL default '0.00',
	`Passed` tinyint(1) unsigned NOT NULL default '0',
	`CreatedDate` datetime NOT NULL,
	`QuestionCount` int(11) unsigned NOT NULL default '0',
	`TotalTime` int(10) unsigned NOT NULL default '0',
	`ResultEmailed` tinyint(1) unsigned NOT NULL default '0',
	`ExtraData` text,
	`UsedTime` int(11) unsigned NOT NULL default '0',
	`ResumeDate` datetime default NULL,
	`ModifiedDate` datetime default NULL,
	`PageCount` mediumint(8) unsigned NOT NULL,
	`UserScorePercent` decimal(5,2) unsigned NOT NULL,
	`ElapsedTime` bigint(20) NOT NULL DEFAULT '0',
	PRIMARY KEY  (`StatisticsInfoId`),
	KEY `QuizId` (`QuizId`),
	KEY `UserId` (`UserId`),
	KEY `Status` (`Status`),
    UNIQUE KEY `TicketId` (`TicketId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizstatistics_pages` (
  `PageId` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `StatisticsInfoId` bigint(20) unsigned NOT NULL,
  `PageNumber` mediumint(8) unsigned NOT NULL,
  `QuestionCount` mediumint(8) unsigned NOT NULL,
  `StartDate` datetime DEFAULT NULL,
  `EndDate` datetime DEFAULT NULL,
  `SkipDate` datetime DEFAULT NULL,
  `SkipCount` int(11) unsigned NOT NULL,
  `UsedTime` int(11) unsigned NOT NULL,
  `IpAddress` int(10) unsigned DEFAULT NULL,
  `PageTime` int(10) unsigned NOT NULL,
  `PageIndex` mediumint(8) unsigned NOT NULL,
  `Description` longtext,
  PRIMARY KEY (`PageId`),
  UNIQUE KEY `PageKey` (`StatisticsInfoId`,`PageNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquizstatistics_files` (
  `FileVersionId` int(10) unsigned NOT NULL,
  `Alias` varchar(85) NOT NULL,
  `StatisticsInfoId` int(10) unsigned NOT NULL,
  `QuestionId` int(10) unsigned NOT NULL,
  `StatisticsId` int(10) unsigned NOT NULL,
  KEY `StatisticsInfoId` (`StatisticsInfoId`),
  KEY `StatisticsId` (`StatisticsId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_folder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) NOT NULL DEFAULT '',
  `access` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `Status` int(10) unsigned NOT NULL,
  `Group` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_left_right` (`lft`,`rgt`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_file` (
  `FileId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileVersionId` int(10) unsigned NOT NULL,
  `OriginalName` varchar(255) NOT NULL,
  `Created` datetime NOT NULL,
  `Modified` datetime DEFAULT NULL,
  `FolderId` int(11) unsigned NOT NULL,
  `Group` varchar(30) NOT NULL,
  `Status` int(10) unsigned NOT NULL,
  `CreatedBy` int(11) NOT NULL,
  `ModifiedBy` int(10) unsigned NOT NULL,
  `MimeType` varchar(100) NOT NULL,
  PRIMARY KEY (`FileId`),
  KEY `FileVersion` (`FileVersionId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_file_versions` (
  `FileVersionId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `FileId` int(11) NOT NULL,
  `Created` datetime NOT NULL,
  `FileName` varchar(50) NOT NULL,
  `FileSize` bigint(20) NOT NULL,
  `Params` text NOT NULL,
  `CreatedBy` int(10) unsigned NOT NULL,
  PRIMARY KEY (`FileVersionId`),
  KEY `File` (`FileId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_question_version_files` (
  `FileId` int(11) unsigned NOT NULL,
  `QuestionVersionId` int(11) unsigned NOT NULL,
  `Alias` varchar(85) NOT NULL DEFAULT '',
  `QuestionId` int(11) unsigned NOT NULL,
  UNIQUE KEY `Alias` (`Alias`,`QuestionVersionId`),
  KEY `QuestionId` (`QuestionId`),
  KEY `FileId` (`FileId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_result_scale` (
  `ScaleId` int(10) unsigned NOT NULL auto_increment,
  `ScaleName` varchar(255) NOT NULL,
  `Created` datetime NOT NULL,
  `CreatedBy` int(10) unsigned NOT NULL,
  `Modified` datetime default NULL,
  `ModifiedBy` int(10) unsigned NOT NULL default '0',
  `ScaleType` enum('Percent','Score') NOT NULL DEFAULT 'Percent',
  PRIMARY KEY  (`ScaleId`),
  KEY `ScaleName` (`ScaleName`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_result_scale_item` (
  `ScaleItemId` int(10) unsigned NOT NULL auto_increment,
  `ScaleId` int(10) unsigned NOT NULL,
  `BeginPoint` decimal(5,2) unsigned NOT NULL default '0.00',
  `EndPoint` decimal(5,2) unsigned NOT NULL default '0.00',
  `TextTemplateId` int(11) unsigned default NULL,
  `MailTemplateId` int(11) unsigned default NULL,
  `PrintTemplateId` int(11) unsigned default NULL,
  `CertificateTemplateId` int(11) unsigned default NULL,
  PRIMARY KEY  (`ScaleItemId`),
  KEY `ScaleId` (`ScaleId`),
  KEY `TextTemplateId` (`TextTemplateId`),
  KEY `MailTemplateId` (`MailTemplateId`),
  KEY `PrintTemplateId` (`PrintTemplateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_texttemplate` (
  `TemplateId` int(11) NOT NULL auto_increment,
  `Group` varchar(30) NOT NULL default '',
  `TemplateName` varchar(85) NOT NULL default '',
  `Value` longtext,
  `Created` datetime NOT NULL default '0000-00-00 00:00:00',
  `CreatedBy` int(11) unsigned NOT NULL default '0',
  `Modified` datetime default NULL,
  `ModifiedBy` int(11) unsigned default NULL,
  PRIMARY KEY  (`TemplateId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_quiz_questionpool` (
  `QuestionPoolId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `QuizId` int(10) unsigned NOT NULL,
  `QuestionCategoryId` int(10) unsigned NOT NULL,
  `BankCategoryId` int(10) unsigned NOT NULL,
  `QuestionCount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`QuestionPoolId`),
  UNIQUE KEY `CategoryIdx` (`QuestionCategoryId`,`BankCategoryId`,`QuizId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ariquiz_statistics_extradata` (
  `StatisticsInfoId` bigint(20) unsigned NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Value` text NOT NULL,
  UNIQUE KEY `ParamIndex` (`StatisticsInfoId`,`Name`),
  KEY `StatisticsInfoId` (`StatisticsInfoId`),
  KEY `Name` (`Name`),
  KEY `Value` (`Value`(30))
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `#__ariquizquestiontype` (`QuestionTypeId`, `QuestionType`, `ClassName`, `Default`, `CanHaveTemplate`, `TypeOrder`) VALUES 
	  (1,'Single Question','SingleQuestion',1,1,0),
	  (2,'Multiple Question','MultipleQuestion',0,1,0),
	  (3,'Correlation Question','CorrelationQuestion',0,1,0),
	  (4,'Free Text Question','FreeTextQuestion',0,1,0),
	  (5,'HotSpot Question','HotSpotQuestion',0,0,0),
	  (6,'D&D Correlation Question','CorrelationDDQuestion',0,1,0),
	  (7,'Multiple Summing Question','MultipleSummingQuestion',0,0,0),
	  (8,'Essay Question','EssayQuestion',0,0,0),
	  (9,'Multiple DropDown Question','MultipleDropdownQuestion',0,1,0),
	  (10,'Multiple Free Text Question','MultipleFreeTextQuestion',0,1,0)
	ON DUPLICATE KEY UPDATE QuestionType=QuestionType;