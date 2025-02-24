<?php
/*------------------------------------------------------------------------
# upload.html.php - Ossolution Property
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# copyright Copyright (C) 2023 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
class HTML_OspropertyUpload
{
	static function ajaxUploadForm($property,$chunkSize,$fileSizeLimit)
	{
		global $mainframe,$configClass;
		HTMLHelper::_('behavior.formvalidation');
		
		?>
		<div id="j-main-container">
		  <div class="row-fluid">
		    <div class="span6 well">
		     <div class="legend"><?php echo Text::_('OS_COMMON_IMAGE_SELECTION'); ?></div>
		      <div id="triggerClearUploadList" class="btn btn-info pull-right hidden">
		        <i class="icon-list icon-black"></i> <?php echo Text::_('OS_AJAXUPLOAD_CLEAR_UPLOAD_LIST'); ?>
		      </div>
		      <div id="fine-uploader"></div>
		      <script>
		        jQuery(document).ready(function() {
		          var uploader = new qq.FineUploader({
		            element: jQuery('#fine-uploader')[0],
		            request: {
		              endpoint: 'index.php?option=com_osproperty&task=upload_doajaxupload&tmpl=component&id=<?php echo $property->id; ?>',
		              paramsInBody: true
		            },
		            chunking: {
		              enabled: true,
		              partSize: <?php echo $chunkSize; ?>
		            },
		            autoUpload: false,
		            display: {
		              fileSizeOnSubmit: true
		            },
		            text: {
		              uploadButton: '<i class="icon-plus icon-plus"></i> <?php echo Text::_('OS_AJAXUPLOAD_SELECT_IMAGES', true); ?>',
		              cancelButton: '<?php echo Text::_('OS_CANCEL', true); ?>',
		              retryButton: '<?php echo Text::_('OS_RETRY', true); ?>',
		              failUpload: '<?php echo Text::_('OS_AJAXUPLOAD_UPLOAD_FAILED', true); ?>',
		              dragZone: '<?php echo Text::_('OS_AJAXUPLOAD_DRAGZONETEXT', true); ?>',
		              dropProcessing:'<?php echo Text::_('OS_AJAXUPLOAD_DROPPROCESSINGTEXT', true); ?>',
		              formatProgress: '{percent}% ' + '<?php echo Text::_('OS_OF', true); ?>' +'  {total_size}',
		              waitingForResponse: '<?php echo Text::_('OS_AJAXUPLOAD_PROCESSING', true); ?>'
		            },
		            failedUploadTextDisplay: {
		              mode: 'custom'
		            },
		            dragAndDrop: {
		              extraDropzones: [],
		              hideDropzones: true,
		              disableDefaultDropzone: false
		            },
		            fileTemplate: '<li class="alert">' +
		                            '<div class="qq-progress-bar"></div>' +
		                            '<span class="qq-upload-spinner"></span>' +
		                            '<span class="qq-upload-finished"></span>' +
		                            '<span class="qq-upload-file"></span>' +
		                            '<span class="qq-upload-size badge"></span>' +
		                            '<a class="qq-upload-cancel btn btn-mini" href="#">{cancelButtonText}</a>' +
		                            '<a class="qq-upload-retry" href="#">{retryButtonText}</a>' +
		                            '<span class="qq-upload-status-text">{statusText}</span>' +
		                            '<span class="qq-upload-debug-text"></span>' +
		                          '</li>',
		            template: '<div class="qq-uploader span12">' +
		                        '<div class="qq-upload-drop-area span12"><span>{dragZoneText}</span></div>' +
		                        '<div class="qq-upload-button btn btn-large btn-success">{uploadButtonText}</div>' +
		                        '<div class="small">' + '<?php echo Text::_('OS_AJAXUPLOAD_DRAGNDROPHINT'); ?>' + '</div>'+
		                        '<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
		                        '<ul class="qq-upload-list"></ul>' +
		                      '</div>',
		            classes: {
		                success: 'alert-success',
		                fail: 'alert-error',
		                debugText: 'qq-upload-debug-text'
		            },
		            validation: {
		              allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		              acceptFiles: 'image/*',
		              sizeLimit: <?php echo $fileSizeLimit; ?>
		            },
		            messages: {
		              typeError: '{file}: ' + '<?php echo Text::_('OS_ALERT_WRONG_EXTENSION', true); ?>',
		              sizeError: '{file}: ' + '<?php echo Text::sprintf('OS_UPLOAD_OUTPUT_MAX_ALLOWED_FILESIZE', $fileSizeLimit, array(true)) ?>',
		              fileNameError: '{file}: ' + '<?php echo Text::_('OS_ALERT_WRONG_FILENAME', true); ?>',
		              fileNameDouble: '{file}: ' + '<?php echo Text::_('OS_AJAXUPLOAD_ALERT_FILENAME_DOUBLE', true); ?>',
		              minSizeError: '{file}: ' + '<?php echo Text::_('OS_AJAXUPLOAD_ALERT_FILE_TOO_SMALL', true); ?>' + ' {minSizeLimit}.',
		              emptyError: '{file} : '  + '<?php echo Text::_('OS_AJAXUPLOAD_ALERT_FILE_EMPTY', true); ?>',
		              noFilesError: '<?php echo Text::_('OS_AJAXUPLOAD_ALERT_NO_FILES', true); ?>',
		              onLeave: '<?php echo Text::_('OS_AJAXUPLOAD_ALERT_ON_LEAVE', true); ?>'
		            },
		            debug: true,
		            maxConnections: 1,
		            disableCancelForFormUploads: true,
		            callbacks: {
		              onComplete: function(id, fileName, responseJSON) {
		                if(responseJSON.debug_output) {
		                  var item = this.getItemByFileId(id);
		                  var element = this._find(item, 'debugText');
		                  element.innerHTML = responseJSON.debug_output;
		                }
		                if(this.requestParams.hasOwnProperty("filecounter")) {
		                  this.requestParams.filecounter =  this.requestParams.filecounter + 1;
		                  this.setParams(this.requestParams);
		                }
		              },
		              onValidate: function(fileData) {
		                if(!jg_filenamewithjs) {
		                  var searchwrongchars = /[^a-zA-Z0-9_-]/;
		                  if(searchwrongchars.test(fileData.name)) {
		                    this._itemError('fileNameError', fileData.name);
		                    return false;
		                  }
		                }
		                for (var i = 0; i < this._storedIds.length; i++) {
		                  var fileName = this._handler.getName(this._storedIds[i]);
		                  if(fileName && fileName == fileData.name) {
		                    this._itemError('fileNameDouble', fileData.name);
		                    return false;
		                  }
		                }
		              }
		            }
		          });
		          jQuery('#triggerClearUploadList').click(function() {
		            uploader.reset();
		            jQuery('#triggerClearUploadList').addClass('hidden');
		          });
		          jQuery('#triggerUpload').click(function() {
		            if(uploader._storedIds.length == 0) {
		              alert('<?php echo Text::_('OS_ALERT_YOU_MUST_SELECT_ONE_IMAGE', true); ?>');
		              return false;
		            }
		            var form = document.id('adminForm');
		            if(!document.formvalidator.isValid(form)) {
		              var msg = new Array();
		              msg.push('<?php echo Text::_('JGLOBAL_VALIDATION_FORM_FAILED', true);?>');
		              if(form.imgtitle.hasClass('invalid')) {
		                  msg.push('<?php echo Text::_("OS_COMMON_ALERT_IMAGE_MUST_HAVE_TITLE", true);?>');
		              }
		              if(form.catid.hasClass('invalid')) {
		                msg.push('<?php echo Text::_("OS_ALERT_YOU_MUST_SELECT_CATEGORY", true);?>');
		              }
		              alert(msg.join('\n'));
		              return false;
		            }
		
		            // Prepare request parameters
		            uploader.requestParams = new Object();
		            uploader.requestParams.catid = jQuery('#catid').val();
		            if(jQuery('#imgtitle').length > 0) {
		              uploader.requestParams.imgtitle = jQuery('#imgtitle').val();
		            }
		            if(jQuery('#filecounter').length > 0) {
		              var filecounter = parseInt(jQuery('#filecounter').val());
		              if(!isNaN(filecounter)) {
		                uploader.requestParams.filecounter = filecounter;
		              }
		            }
		            uploader.requestParams.imgtext = jQuery('#imgtext').val();
		            uploader.requestParams.imgauthor = jQuery('#imgauthor').val();
		            uploader.requestParams.published = jQuery('#published0').prop('checked') ? 0 : 1;
		            uploader.requestParams.access = jQuery('#access').val();
		            if(jQuery('#original_delete').length > 0) {
		              uploader.requestParams.original_delete = jQuery('#original_delete').prop('checked') ? 1 : 0;
		            }
		            uploader.requestParams.create_special_gif = jQuery('#create_special_gif').prop('checked') ? 1 : 0;
		            uploader.requestParams.debug = jQuery('#debug').prop('checked') ? 1 : 0;
		            uploader.setParams(uploader.requestParams);
		            uploader.uploadStoredFiles();
		            jQuery('#triggerClearUploadList').removeClass('hidden');
		          });
		        });
		      </script>
		    </div>
		    <div class="span6 well">
		      <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-validate form-horizontal" onsubmit="">
		        <div class="control-group">
			          <h3>
		              	<?php
		              	if(($property->ref != "") and ($configClass['show_ref'] == 1)){
		              		echo $property->ref.", ";
		              	}
		              	echo $property->pro_name;
		              	?>
		              </h3>
		              <BR />
		              <?php echo $property->pro_small_desc;?>
	              <div class="clearfix"></div>
		          <div class="controls">
		            <div id="triggerUpload" class="btn btn-large btn-primary">
		              <i class="icon-upload icon-white"></i> <?php echo Text::_('OS_UPLOAD'); ?>
		            </div>
		          </div>
		        </div>
		        <input type="hidden" name="option" value="com_osproperty" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="id" value="<?php echo $property->id?>" />
		      </form>
		    </div>
		  </div>
		<?php
	}
}
?>