$(document).ready(function () {

	var prefix = '%s';
	var recordUid = '%s';

	$('#%s').fineUploader({
		multiple: true,
		debug: true,
		request: {
			endpoint: '/typo3/mod.php',
			// backward compatibility for fine upload to have parameters as GET params.
			// Otherwise use "setEndpoint" over "setParam" in submit event
			forceMultipart: true, // when IE9 will be support octet stream upload change me to true
			paramsInBody: false
		},
		validation: {
			allowedExtensions: ['%s'],
			sizeLimit: '%s' // bytes
		},
		text: {
			uploadButton: '<i class="icon-upload icon-white"></i> ' + Media.translate('upload_files')
		},
		// Note: main template adapted for Twitter Bootstrap
		template: '<div class="qq-uploader span8">' +
			'<pre class="qq-upload-drop-area span8"><span>{dragZoneText}</span></pre>' +
			'<div class="qq-upload-button btn btn-success" style="width: auto;">{uploadButtonText}</div>' +
			'<span class="qq-max-size qq-vertical-align">' + Media.translate('max_upload_size') +'</span>' +
			'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
			'<ul class="qq-upload-list"></ul>' +
			'</div>',
		// Note: fileTemplate content is put under list .qq-upload-list
		fileTemplate: '<li class="alert">' +
			'<div class="qq-progress-bar"></div>' +
			'<span class="qq-upload-spinner"></span>' +
			'<span class="qq-upload-finished"></span>' +
			'<span class="qq-upload-file"></span>' +
			'<span class="qq-upload-size"></span>' +
			'<a class="qq-upload-cancel" href="#">{cancelButtonText}</a>' +
			'<a class="qq-upload-retry" href="#">{retryButtonText}</a>' +
			'<a class="qq-upload-delete hide" href="#">{deleteButtonText}</a>' +
			'<span class="qq-upload-status-text">{statusText}</span>' +
			'</li>',
		classes: {
			success: 'alert alert-success',
			fail: 'alert alert-error'
		}
	}).on('submit', function (event, id, fileName) {
			var params = {};
			params[prefix + '[action]'] = 'upload';
			params[prefix + '[controller]'] = 'Asset';
			params[prefix + '[asset][uid]'] = $('#asset-uid').length > 0 ? $('#asset-uid').val() : '';
			params['M'] = 'user_MediaM1'; // @todo Make me configurable... for FE plugin for instance.
			$(this).fineUploader('setParams', params);
		})
		.on('cancel', function (event, id, fileName) {
		})
		.on('complete', function (event, id, fileName, responseJSON) {

			// Code injected below by the server.
			//%s
		});
});


