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

$specificQuestion = $this->specificQuestion;
$questionData = $this->questionData;
$hotSpotImage = $this->hotSpotImage;
$isEmpty = empty($hotSpotImage['FileId']);

$dataItem = $specificQuestion->getDataFromXml($questionData, false);
$cropCfg = $this->getJSCropConfig($dataItem);
$defCropCfg = $this->getJSCropConfig(null);
?>

<div id="panelHotSpotFiles" style="visibility: hidden;">
	<div class="hd"><?php echo JText::_('COM_ARIQUIZ_LABEL_FILEMANAGER'); ?></div>  
	<div class="bd" style="text-align: center; overflow: auto;" id="tblMassSettings">
		<iframe id="frmFiles" name="frmFiles" src="about:blank" frameborder="0" width="1000" height="480"></iframe>
	</div>
	<div class="ft">
		<div class="buttons">
			<input type="button" class="button floatNone" value="<?php echo JText::_('COM_ARIQUIZ_LABEL_CANCEL'); ?>" onclick="YAHOO.ARISoft.page.panelHotSpotFiles.hide(); return false;" />
		</div>
	</div>
</div>

<a href="#" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('manageHotSpotFiles');return false;"><?php echo JText::_('COM_ARIQUIZ_LABEL_CHOOSEUPLOADIMAGE'); ?></a>
<br/><br/>

<img src="<?php echo $hotSpotImage['FileUrl']; ?>" id="imgHotSpot"<?php if (empty($hotSpotImage['FileUrl'])): ?> style="display: none;"<?php endif; ?> />
<div class="ari-hotspot-empty" id="hotSpotEmpty"<?php if (!$isEmpty):?> style="display: none;"<?php endif;?>>
	<a href="#" onclick="YAHOO.ARISoft.page.pageManager.triggerAction('manageHotSpotFiles');return false;"><?php echo JText::_('COM_ARIQUIZ_LALEL_HOTSPOTSELECTIMAGE'); ?></a>
</div>

<input type="hidden" id="hotSpotFileId" name="questionFiles[hotspot_image]" value="<?php echo $hotSpotImage['FileId']; ?>" />
<input type="hidden" id="hotSpotCoords" name="hotSpotCoords" />

<script type="text/javascript">
YAHOO.util.Event.onDOMReady(function() {
	var isFileManagerLoaded = false,
		page = YAHOO.ARISoft.page,
		pageManager = page.pageManager,
		Dom = YAHOO.util.Dom,
		aDom = YAHOO.ARISoft.DOM;

	page.panelHotSpotFiles = new YAHOO.widget.Panel("panelHotSpotFiles", { 
		width: "1030px", 
		height: "555px", 
		visible: false, 
		constraintoviewport: true, 
		modal: true, 
		fixedcenter: "contained", 
		zIndex: 1200
	});   
	page.panelHotSpotFiles.render();
	
	var hotSpotCrop = <?php if (!$isEmpty): ?>
		new YAHOO.widget.ImageCropper('imgHotSpot', <?php echo json_encode($cropCfg); ?>);
	<?php else: ?>
		null;
	<?php endif; ?>

	pageManager.registerAction('manageHotSpotFiles', {
		onAction: function() {
			if (!isFileManagerLoaded) {
				Dom.get('frmFiles').src = 'index.php?option=com_ariquiz&view=images&tmpl=component';
				isFileManagerLoaded = true;
			};
			
			page.panelHotSpotFiles.show();
		}
	});
	pageManager.subscribe('beforeAction', function(o) {
		if ((o.action == 'save' || o.action == 'apply')) {
			var hidHotSpotCoords = Dom.get('hotSpotCoords'),
				coords = hotSpotCrop.getCropCoords();

			delete coords["image"];
			hidHotSpotCoords.value = YAHOO.lang.JSON.stringify(coords);
		}
	});

	YAHOO.util.Event.on('frmFiles', 'load', function() {
		var frm = Dom.get('frmFiles');
		frm.contentWindow.YAHOO.ARISoft.page.pageManager.fileManager.fileSelectedEvent.subscribe(function(event, data) {
			var fileData = data[0],
				imgHotSpot = Dom.get('imgHotSpot');

			Dom.setStyle(imgHotSpot, 'display', 'inline');
			Dom.setStyle('hotSpotEmpty', 'display', 'none');
			Dom.get('hotSpotFileId').value = 0;
			Dom.get('hotSpotCoords').value = '';
			if (hotSpotCrop)
				hotSpotCrop.destroy();

			YAHOO.ARISoft.page.panelHotSpotFiles.hide();

			YAHOO.util.Event.removeListener(imgHotSpot, 'load'); 
			YAHOO.util.Event.on(imgHotSpot, 'load', function() {
				Dom.get('hotSpotFileId').value = fileData.FileId;
				hotSpotCrop = new YAHOO.widget.ImageCropper(imgHotSpot, <?php echo json_encode($defCropCfg); ?>);

				YAHOO.util.Event.removeListener(imgHotSpot, 'load');
			});
			imgHotSpot.src = fileData.FileUrl;
		});
	});
});

YAHOO.ARISoft.validators.validatorManager.addValidator(
	new YAHOO.ARISoft.validators.rangeValidator(
		'hotSpotFileId', 
		1,
		null,
		YAHOO.ARISoft.validators.rangeValidatorType.int, {
			emptyValidate: true, 
			errorMessage: '<?php echo JText::_('COM_ARIQUIZ_ERROR_HOTSPOTIMAGE', true); ?>'
		}
	)
);
</script>