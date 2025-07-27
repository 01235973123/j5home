<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$isMultiple = $row->multiple_files;

Factory::getApplication()
	->getDocument()
	->getWebAssetManager()
	->registerAndUseScript('com_eventbooking.ajaxupload', 'media/com_eventbooking/assets/js/ajaxupload.min.js');
?>

<?php if ($isMultiple): ?>
	<input type="file" id="file-upload-<?php echo $name; ?>" multiple class="form-control" />
	<div id="upload-status-<?php echo $name; ?>" class="mt-2">
		<?php
		if (!empty($value)) {
			$files = explode(',', $value);
			foreach ($files as $file) {
				$file = trim($file);
				if (file_exists(JPATH_ROOT . '/media/com_eventbooking/files/' . $file)) {
					$fileUrl = Route::_('index.php?option=com_eventbooking&task=controller.download_file&file_name=' . $file);
					echo '<div><a href="' . $fileUrl . '" target="_blank"><i class="fa fa-download"></i> ' . htmlspecialchars($file) . '</a></div>';
				}
			}
		}
		?>
	</div>
	<input type="hidden" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>" />

	<script>
		jQuery(document).ready(function() {
			const input = jQuery('#file-upload-<?php echo $name; ?>');
			const statusBox = jQuery('#upload-status-<?php echo $name; ?>');
			const hiddenInput = jQuery('#<?php echo $name; ?>');
			let uploadedFiles = hiddenInput.val() ? hiddenInput.val().split(',') : [];

			input.on('change', function() {
				const files = this.files;

				// Reset
				uploadedFiles = [];
				statusBox.html('');
				hiddenInput.val('');

				const uploadUrl = siteUrl + 'index.php?option=com_eventbooking&task=upload_file&field_id=<?php echo $row->id; ?>';

				for (let i = 0; i < files.length; i++) {
					const fileData = new FormData();
					fileData.append('file', files[i]);

					const loadingText = jQuery('<div>').text('<?php echo Text::_('EB_UPLOADING'); ?> ' + files[i].name + '...').appendTo(statusBox);

					jQuery.ajax({
						url: uploadUrl,
						type: 'POST',
						data: fileData,
						contentType: false,
						processData: false,
						dataType: 'json',
						success: function(response) {
							loadingText.remove();
							if (response.success) {
								uploadedFiles.push(response.file);
								const link = '<div><i class="fa fa-check-circle text-success"></i> ' +
									'<a href="' + siteUrl + 'index.php?option=com_eventbooking&task=controller.download_file&file_name=' + response.file + '" target="_blank">' +
									response.file + '</a></div>';
								statusBox.append(link);
								hiddenInput.val(uploadedFiles.join(','));
							} else {
								statusBox.append('<div class="text-danger"><i class="fa fa-times-circle"></i> ' + (response.error || 'Upload failed') + '</div>');
							}
						},
						error: function() {
							loadingText.remove();
							statusBox.append('<div class="text-danger"><i class="fa fa-times-circle"></i> Error uploading ' + files[i].name + '</div>');
						}
					});
				}
			});
		});
	</script>

<?php else: ?>
	<input type="button" value="<?php echo Text::_('EB_SELECT_FILE'); ?>" id="button-file-<?php echo $name; ?>" class="btn btn-primary" />
	<span class="eb-uploaded-file" id="uploaded-file-<?php echo $name; ?>">
		<?php if ($value && file_exists(JPATH_ROOT . '/media/com_eventbooking/files/' . $value)) : ?>
			<a href="<?php echo Route::_('index.php?option=com_eventbooking&task=controller.download_file&file_name=' . $value); ?>" target="_blank">
				<i class="fa fa-download"></i><strong><?php echo $value; ?></strong>
			</a>
		<?php endif; ?>
	</span>
	<input type="hidden" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />

	<script>
		new AjaxUpload('#button-file-<?php echo $name; ?>', {
			action: siteUrl + 'index.php?option=com_eventbooking&task=upload_file&field_id=<?php echo $row->id; ?>',
			name: 'file',
			autoSubmit: true,
			responseType: 'json',
			onSubmit: function(file, extension) {
				jQuery('#button-file-<?php echo $name; ?>').after('<span class="wait">&nbsp;<img src="<?php echo Uri::root(true); ?>/media/com_eventbooking/ajax-loadding-animation.gif" alt="" /></span>');
				jQuery('#button-file-<?php echo $name; ?>').attr('disabled', true);
			},
			onComplete: function(file, json) {
				jQuery('#button-file-<?php echo $name; ?>').attr('disabled', false);
				jQuery('.error').remove();
				if (json['success']) {
					jQuery('#uploaded-file-<?php echo $name; ?>').html(
						'<a href="' + siteUrl + 'index.php?option=com_eventbooking&task=controller.download_file&file_name=' + json.file + '" target="_blank"><i class="fa fa-download"></i><strong>' + json.file + '</strong></a>'
					);
					jQuery('input[name="<?php echo $name; ?>"]').val(json.file);
				}
				if (json['error']) {
					jQuery('#button-file-<?php echo $name; ?>').after('<span class="error">' + json['error'] + '</span>');
				}
				jQuery('.wait').remove();
			}
		});
	</script>
<?php endif; ?>