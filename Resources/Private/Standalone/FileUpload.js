(function($) {
	$(function() {

		var parameterPrefix = '%s';

		$('#%s').fineUploader({
			multiple: true,
			debug: true,
			request: {
				endpoint: '%s',
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
				failUpload: TYPO3.lang['media_file_upload.uploadFailed'],
				formatProgress: TYPO3.lang['media_file_upload.formatProgress'],
				waitingForResponse: TYPO3.lang['media_file_upload.waitingForResponse']
			},
			messages: {
				typeError: TYPO3.lang['media_file_upload.typeError'],
				sizeError: TYPO3.lang['media_file_upload.sizeError'],
				minSizeError: TYPO3.lang['media_file_upload.minSizeError'],
				emptyError: TYPO3.lang['media_file_upload.emptyError'],
				noFilesError: TYPO3.lang['media_file_upload.noFilesError'],
				tooManyItemsError: TYPO3.lang['media_file_upload.tooManyItemsError'],
				retryFailTooManyItems: TYPO3.lang['media_file_upload.retryFailTooManyItems'],
				onLeave: TYPO3.lang['media_file_upload.onLeave']
			},
			showMessage: function(message) {
				alert(message);
			},
			template: 'file-upload-template',
			classes: {
				success: 'alert alert-success',
				fail: 'alert alert-danger'
			}
		}).on('submit', function(event, id, fileName) {
			var params = {};
			params[parameterPrefix + '[action]'] = 'create';
			params[parameterPrefix + '[controller]'] = 'Asset';
			params[parameterPrefix + '[combinedIdentifier]'] = '%s';

			$(this).fineUploader('setParams', params);
		})
			.on('cancel', function(event, id, fileName) {
			})
			.on('complete', function(event, id, fileName, responseJSON) {

				// Callback action after file upload
				if (responseJSON.uid) {

					// Hide message for file upload
					$('.qq-upload-list', this).find('li:eq(' + id + ')').fadeOut(500);

					// Reset table only if all files have been uploaded
					if ($('.qq-upload-list', this).find('li').not('.alert-success').length == 0) {
						Vidi.grid.fnResetDisplay();
					}
				}
			});
	});
})(jQuery);
