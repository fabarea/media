$(document).ready(function () {

	var prefix = '%s';
	var recordUid = '%s';

	$('#jquery-wrapped-fine-uploader').fineUploader({
		request: {
			// @todo Make me configurable... for FE plugin for instance.
			endpoint: '/typo3/mod.php'
		},
		validation: {
			allowedExtensions: ['%s'],
			sizeLimit: '%s' // bytes
		},
		multiple: false, // no multiple upload in a regular form
		// Templating for Twitter Bootstrap
		text: {
			uploadButton: '<i class="icon-upload icon-white"></i> Upload a file'
		},
		template: '<div class="qq-uploader span12">' +
			'<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' +
			'<div class="qq-upload-button btn btn-success" style="width: auto;">{uploadButtonText}</div>' +
			'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
			'<ul class="qq-upload-list"></ul>' +
			'</div>',
		classes: {
			success: 'alert alert-success',
			fail: 'alert alert-error'
		},
		debug: true
	}).on('submit', function (event, id, fileName) {
			var params = new Object();
			params[prefix + '[action]'] = 'upload';
			params[prefix + '[controller]'] = 'Media';
			params[prefix + '[media][uid]'] = $('#media-uid').val();
			params['M'] = 'user_MediaTxMediaM1'; // @todo Make me configurable... for FE plugin for instance.
			$(this).fineUploader('setParams', params);
		})
		.on('cancel', function (event, id, fileName) {
		})
		.on('complete', function (event, id, fileName, responseJSON) {
			if (responseJSON.uid) {
				$('#media-uid').val(responseJSON.uid);
			}
			if (responseJSON.thumbnail) {
				// Replace thumbnail by new one.
				$(this).prev().html(Encoder.htmlDecode(responseJSON.thumbnail));
				$('.qq-upload-list', this).html('');
			}
			if (responseJSON.formAction) {
				$(this).closest('form').attr('action', (Encoder.htmlDecode(responseJSON.formAction)));
			}
		});
});


