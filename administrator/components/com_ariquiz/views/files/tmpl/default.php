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

$files = $this->files;
$folders = $this->folders;
$currentFolder = $this->params['folder'];
$folderUri = $this->params['folderUri'];
$folderId = $this->params['folderId'];
$isRootFolder = ($currentFolder->level == 1);
$path = $this->params['path'];
?>

<?php
	if (J3_0): 
?>
<style type="text/css">
BODY {padding: 0 5px;}
</style>
<?php
	endif; 
?>
<div class="fix-float">
	<fieldset class="fix-float">
		<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_UPLOADFILE'); ?></legend>
		<fieldset class="actions">
			<div>
				<div class="leftPos">
					<input type="file" id="fileUpload" name="fileUpload" size="50" />
					<input type="button" id="file-upload-submit" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_STARTUPLOAD'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('fileUpload'); return false;" />
					<input type="checkbox" name="fileOverwrite" id="fileOverwrite" value="1" /><label class="fix-label" for="fileOverwrite"><?php echo JText::_('COM_ARIQUIZ_LABEL_OVERWRITE'); ?></label>
				</div>
				<div class="textRight">
					<input type="button" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('delete');" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_DELETE'); ?>" />
				</div>
			</div>
		</fieldset>
	</fieldset>
	<fieldset>
		<legend><?php echo JText::_('COM_ARIQUIZ_LABEL_FILES'); ?></legend>
		<fieldset>
			<?php
				if ($path):
					foreach ($path as $pathElement):
						if ($pathElement->level == 0)
							continue ;
			?>
				<?php
					if ($pathElement->id != $folderId): 
				?>
					<a href="#" title="<?php echo $pathElement->title; ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('doChangeFolder', {'newFolderId': '<?php echo $pathElement->id; ?>'}); return false;"><?php echo $pathElement->title; ?></a>
				<?php
					else:
				?>
					<?php echo $pathElement->title; ?>
				<?php
					endif; 
				?> /
			<?php
					endforeach;
				endif; 
			?>
			<input type="text" size="50" name="newFolder" id="tbxNewFolder" />&nbsp;<input type="button" class="button" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CREATEFOLDER'); ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('newFolder'); return false;" />
		</fieldset>
	
		<div class="ari-files" id="filesContainer">
		<?php
			if (!$isRootFolder): 
		?>
			<div class="ari-folder">
				<div class="ari-folder-thumb ari-folderup-thumb">
				</div>
				<div class="ari-folder-info">
					<div class="leftPos"><input type="checkbox" disabled="disabled" /></div>
					<div class="ari-folder-name"><a href="#" title=".." onclick="YAHOO.ARISoft.page.pageManager.triggerAction('doChangeFolder', {'newFolderId': '<?php echo $currentFolder->parent_id; ?>'}); return false;">..</a></div>
				</div>
			</div>
		<?php
			endif;
			if (is_array($folders) && count($folders) > 0):
				foreach ($folders as $folder):
		?>
			<div class="ari-folder">
				<div class="ari-folder-thumb">
				</div>
				<div class="ari-folder-info">
					<div class="leftPos"><input type="checkbox" class="ari-folder-chk" name="FolderId[]" value="<?php echo $folder->id; ?>" /></div>
					<div class="ari-folder-name"><a href="#" title="<?php echo $folder->title; ?>" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('doChangeFolder', {'newFolderId': '<?php echo $folder->id; ?>'}); return false;"><?php echo $folder->title; ?></a></div>
				</div>
			</div>
		<?php 
				endforeach;
			endif;
		?>
		<?php
			if (is_array($files) && count($files) > 0):
				foreach ($files as $file): 
		?>
			<div class="ari-file" id="ariFile_<?php echo $file->FileId; ?>">
				<?php
					$imgModalId = uniqid('modal-');
					if (J4) {
						$modalParams = array(
							'title' => JText::_('COM_ARIQUIZ_LABEL_IMAGEPREVIEW'),
							'url' => $folderUri . $file->FileName,
						);
						AriJoomlaHtmlHelper::renderModal($imgModalId, $modalParams);
					}
				?>
				<div class="ari-file-thumb">
					<?php
						if (J4):
					?>
						<button type="button" href="<?php echo $folderUri . $file->FileName; ?>"<?php echo AriHtmlHelper::getAttrStr(AriJoomlaHtmlHelper::modalLinkAttrs($imgModalId)); ?>><img src="<?php echo $folderUri . $file->FileName; ?>" width="75" height="75" /></button>
					<?php
						else:
					?>
						<a href="<?php echo $folderUri . $file->FileName; ?>" class="modal" target="_blank"><img src="<?php echo $folderUri . $file->FileName; ?>" width="75" height="75" /></a>
					<?php
						endif;
					?>
				</div>
				<div class="ari-file-controls"></div>
				<div class="ari-file-info">
					<div class="leftPos"><input type="checkbox" class="ari-file-chk" name="FileId[]" value="<?php echo $file->FileId; ?>" /></div>
					<div class="ari-file-name"><a href="#" onclick="YAHOO.ARISoft.page.pageManager.fileManager.fileSelected(<?php echo $file->FileId; ?>, '<?php echo $file->OriginalName; ?>', '<?php echo $folderUri . $file->FileName; ?>'); return false;" title="<?php echo $file->OriginalName; ?>"><?php echo $file->OriginalName; ?></a></div>
				</div>
			</div>
		<?php
				endforeach;
			endif; 
		?>
		</div>
	</fieldset>
	
	<input type="hidden" name="folderId" id="folderId" value="<?php echo $folderId; ?>" />
	<?php
		if (JRequest::getString('tmpl') == 'component'):
	?>
	<input type="hidden" name="tmpl" value="component" />
	<?php
		endif; 
	?>
</div>

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom;

	pageManager.fileManager = {
		fileSelectedEvent: new YAHOO.util.CustomEvent('fileSelected'),
			
		fileSelected: function(fileId, originalName, fileUrl) {
			this.fileSelectedEvent.fire({'FileId': fileId, 'Name': originalName, 'FileUrl': fileUrl});
		}
	};

	pageManager.registerActionGroup('filesAction', {
		query: {"view": "<?php echo $this->getCtrlName(); ?>"},
		onAction: page.actionHandlers.simpleAjaxAction,
		containerEl: 'filesContainer',
		enableValidation: true,
		errorMessage: '<?php echo JText::_('COM_ARIQUIZ_LABEL_ACTIONFAIL', true); ?>',
		completeMessage: "",
		loadingMessage: '<div class="ari-loading"><?php echo JText::_('COM_ARIQUIZ_LABEL_LOADING', true); ?></div>'
	});
	
	pageManager.registerAction('fileUpload', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['upload'])) 
				return ;

			pageManager.triggerAction('upload');
		}
	});
	pageManager.registerAction('newFolder', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['newFolder'])) 
				return ;

			pageManager.triggerAction('createFolder');
		}
	});

	pageManager.registerAction('doChangeFolder', {
		onAction: function(action, config) {
			var folderId = config['newFolderId'];
			Dom.get('folderId').value = folderId;

			pageManager.triggerAction('changeFolder');
		}
	});

	pageManager.registerAction('delete', {
		onAction: function() {
			if (!YAHOO.ARISoft.validators.alertSummaryValidators.validate(['delete'])) 
				return ;

			pageManager.triggerAction('ajaxDelete');
		}
	});

	pageManager.registerAction('ajaxDelete', {
		group: 'filesAction',
		completeMessage: '<?php echo JText::_('COM_ARIQUIZ_COMPLETE_FILEDELETE', true); ?>',
		onComplete: function() {
			Dom.getElementsByClassName('ari-file', null, 'filesContainer', function(fileContainer) {
				Dom.getElementsByClassName('ari-file-chk', 'INPUT', fileContainer, function(chk) {
					if (chk.checked)
						fileContainer.parentNode.removeChild(fileContainer);
				});
			});

			Dom.getElementsByClassName('ari-folder', null, 'filesContainer', function(fileContainer) {
				Dom.getElementsByClassName('ari-folder-chk', 'INPUT', fileContainer, function(chk) {
					if (chk.checked)
						fileContainer.parentNode.removeChild(fileContainer);
				});
			});
		}
	});
});
YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.customValidator(
		'',
		function(val) {
			var cnt = 0;
			Dom.getElementsByClassName('ari-file', null, 'filesContainer', function(fileContainer) {
				Dom.getElementsByClassName('ari-file-chk', 'INPUT', fileContainer, function(chk) {
					if (chk.checked)
						cnt++;
				});
			});

			if (cnt == 0)
				Dom.getElementsByClassName('ari-folder', null, 'filesContainer', function(fileContainer) {
					Dom.getElementsByClassName('ari-folder-chk', 'INPUT', fileContainer, function(chk) {
						if (chk.checked)
							cnt++;
					});
				});

			return (cnt > 0);
		}, {
			emptyValidate: true,
			errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_FILEDELETE', true); ?>',
			validationGroups: ['delete']
		}
	)
);
YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.requiredValidator(
		'fileUpload', {
			errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_SELECTFILE', true); ?>',
			validationGroups: ['upload']
		}
	)
);
YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.requiredValidator(
		'tbxNewFolder', {
			errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_FOLDERNAME', true); ?>',
			validationGroups: ['newFolder']
		}
	)
);
YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.regexpValidator(
		'tbxNewFolder', 
		/^[-\_0-9A-z]+$/i, {
			errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_FOLDERNAME', true); ?>',
			validationGroups: ['newFolder']
		}
	)
);
</script>