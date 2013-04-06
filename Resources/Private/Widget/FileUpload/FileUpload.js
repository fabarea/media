$(document).ready(function () {

	var prefix = '%s';
	var recordUid = '%s';

	$('#%s').fineUploader({
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
			uploadButton: '<i class="icon-upload icon-white"></i>' + Media.translate('upload_file')
		},
		template: '<div class="qq-uploader span12">' +
			'<pre class="qq-upload-drop-area span12"><span>{dragZoneText}</span></pre>' +
			'<div class="qq-upload-button btn btn-success" style="width: auto;">{uploadButtonText}</div>' +
			'<span class="qq-max-size qq-vertical-align">' + Media.translate('max_upload_size') +'</span>' +
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
			params[prefix + '[controller]'] = 'Asset';
			params[prefix + '[asset][uid]'] = $('#asset-uid').length > 0 ? $('#asset-uid').val() : '';
			params['M'] = 'user_MediaM1'; // @todo Make me configurable... for FE plugin for instance.
			$(this).fineUploader('setParams', params);

			// Hide the size message
			$('.qq-max-size').toggle();
		})
		.on('cancel', function (event, id, fileName) {
		})
		.on('complete', function (event, id, fileName, responseJSON) {

			$('.qq-max-size').toggle();

			// Code injected below by the server.
			//%s
		});
});


