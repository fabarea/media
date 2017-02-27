(function ($) {
	$(function () {

		var parameterPrefix = '%s';

		$('#%s').fineUploader({
			debug: true,
			multiple: false, // no multiple upload in a regular form
			request: {
				endpoint: '%s',
				// backward compatibility for fine upload to have parameters as GET params.
				// Otherwise use "setEndpoint" over "setParam" in submit event
				paramsInBody: false
			},
			validation: {
				allowedExtensions: ['%s'],
				sizeLimit: '%s' // bytes
			},
			text: {
				failUpload: TYPO3.lang['media_file_upload.uploadFailed'],
				formatProgress: "{percent}%% " + TYPO3.lang['media_file_upload.formatProgressOf'] +" {total_size}",
				waitingForResponse: TYPO3.lang['media_file_upload.waitingForResponse']
			},
			messages: {
				tooManyFilesError: TYPO3.lang['media_file_upload.tooManyFilesError'],
				typeError: TYPO3.lang['media_file_upload.typeError'],
				sizeError: TYPO3.lang['media_file_upload.sizeError'],
				minSizeError: TYPO3.lang['media_file_upload.minSizeError'],
				emptyError: TYPO3.lang['media_file_upload.emptyError'],
				noFilesError: TYPO3.lang['media_file_upload.noFilesError'],
				tooManyItemsError: TYPO3.lang['media_file_upload.tooManyItemsError'],
				retryFailTooManyItems: TYPO3.lang['media_file_upload.retryFailTooManyItems'],
				onLeave: TYPO3.lang['media_file_upload.onLeave']
			},
			template: 'file-upload-template',
			classes: {
				success: 'alert alert-success',
				fail: 'alert alert-danger'
			}
		}).on('submit', function (event, id, fileName) {
				var params = {};
				params[parameterPrefix + '[action]'] = 'update';
				params[parameterPrefix + '[controller]'] = 'Asset';
				params[parameterPrefix + '[file]'] = '%s';
				$(this).fineUploader('setParams', params);

				// Hide the size message
				$('.qq-max-size').toggle();
				$('.qq-upload-list').show();

			})
			.on('cancel', function (event, id, fileName) {
			})
			.on('complete', function (event, id, fileName, responseJSON) {

				if (responseJSON.thumbnail) {

					$('.qq-max-size').toggle();
					$('.qq-upload-list').hide();
					$('.qq-upload-list', this).html(''); // remove progress bar

					// Replace thumbnail by new one.
					var decoded = $("<div/>").html(responseJSON.thumbnail).text();
					$('.container-thumbnail').html(decoded);
				}

				if (responseJSON.fileInfo) {
					$('.container-fileInfo').html(responseJSON.fileInfo);
				}
			});
	});
})(TYPO3.jQuery);