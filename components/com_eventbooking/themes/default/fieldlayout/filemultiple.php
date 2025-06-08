<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

Factory::getApplication()->getDocument()->addScript(Uri::root(true) . '/media/com_eventbooking/assets/js/jquery.min.js');
?>
<input type="file" id="file-upload-<?php echo $name; ?>" multiple class="form-control" />
<div id="upload-status-<?php echo $name; ?>" class="mt-2"></div>
<input type="hidden" name="<?php echo $name; ?>" id="<?php echo $name; ?>" value="<?php echo $value; ?>" />

<script>
jQuery(document).ready(function() {
    const input = jQuery('#file-upload-<?php echo $name; ?>');
    const statusBox = jQuery('#upload-status-<?php echo $name; ?>');
    const hiddenInput = jQuery('#<?php echo $name; ?>');
    let uploadedFiles = [];

    input.on('change', function() {
        const files = this.files;

        // 🧹 Reset lại trạng thái cũ
        uploadedFiles = [];
        statusBox.html('');
        hiddenInput.val('');

        const uploadUrl = siteUrl +
            'index.php?option=com_eventbooking&task=upload_file&field_id=<?php echo $row->id; ?>';

        for (let i = 0; i < files.length; i++) {
            const fileData = new FormData();
            fileData.append('file', files[i]);

            const loadingText = jQuery('<div>').text('<?php echo Text::_('EB_UPLOADING'); ?> ' + files[
                i].name + '...').appendTo(statusBox);

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
                        statusBox.append(
                            '<div><i class="fa fa-check-circle text-success"></i> ' +
                            response.file + '</div>');
                        hiddenInput.val(uploadedFiles.join(','));
                    } else {
                        statusBox.append(
                            '<div class="text-danger"><i class="fa fa-times-circle"></i> ' +
                            (response.error || 'Upload failed') + '</div>');
                    }
                },
                error: function() {
                    loadingText.remove();

                    statusBox.append(
                        '<div class="text-danger"><i class="fa fa-times-circle"></i> Error uploading ' +
                        files[i].name + '</div>');
                }
            });
        }
    });
});
</script>
