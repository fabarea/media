$(document).ready(function () {

	var parameterPrefix = '%s';

	$('#%s').fineUploader({
		multiple: true,
		debug: true,
		request: {
			endpoint: 'mod.php',
			// backward compatibility for fine upload to have parameters as GET params.
			// Otherwise use "setEndpoint" over "setParam" in submit event
			forceMultipart: true, // when IE9 will be support octet stream upload change me to true
			paramsInBody: false
		},
		validation: {
			allowedExtensions: ['%s'],
			sizeLimit: '%s' // bytes
		},
		showMessage: function (message) {
			bootbox.alert(message);
		},
		text: {
			uploadButton: '<span class="t3-icon t3-icon-actions t3-icon-actions-edit t3-icon-edit-upload">&nbsp;</span>'//Media.translate('upload_files')
		},
		// Note: main template adapted for Twitter Bootstrap
		template: '<div class="qq-uploader span8">' +
			'<pre class="qq-upload-drop-area span8"><span>{dragZoneText}</span></pre>' +
			'<div class="qq-upload-button" style="display: inline-block;">{uploadButtonText}</div>' +
			'<div class="qq-upload-button" style="display: inline-block; bottom: 3px; position: relative"><span class="qq-max-size qq-vertical-align">%s</span></div>' +
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
			params[parameterPrefix + '[action]'] = 'create';
			params[parameterPrefix + '[controller]'] = 'Asset';
			params[parameterPrefix + '[storageIdentifier]'] = '%s';
			params['M'] = 'user_MediaM1';
			$(this).fineUploader('setParams', params);
		})
		.on('cancel', function (event, id, fileName) {
		})
		.on('complete', function (event, id, fileName, responseJSON) {

			// Callback action after file upload
			if (responseJSON.uid) {

				// Hide message for file upload
				$('.qq-upload-list', this).find('li:eq(' + id + ')').fadeOut(500);

				// Reset table only if all files have been uploaded
				if ($('.qq-upload-list', this).find('li').not('.alert-success').length == 0) {
					Vidi.table.fnResetDisplay();
				}
			}
		});
});
