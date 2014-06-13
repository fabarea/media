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
				uploadButton: TYPO3.l10n.localize('media_file_upload.replaceFile'),
				cancelButton: TYPO3.l10n.localize('media_file_upload.cancel'),
				retryButton: TYPO3.l10n.localize('media_file_upload.retry'),
				deleteButton: TYPO3.l10n.localize('media_file_upload.delete'),
				failUpload: TYPO3.l10n.localize('media_file_upload.uploadFailed'),
				dragZone: TYPO3.l10n.localize('media_file_upload.dragZone'),
				dropProcessing: TYPO3.l10n.localize('media_file_upload.dropProcessing'),
				formatProgress: "{percent}%% " + TYPO3.l10n.localize('media_file_upload.formatProgressOf') + " {total_size}",
				waitingForResponse: TYPO3.l10n.localize('media_file_upload.waitingForResponse')
			},
			messages: {
				tooManyFilesError: TYPO3.l10n.localize('media_file_upload.tooManyFilesError'),
				typeError: TYPO3.l10n.localize('media_file_upload.typeError'),
				sizeError: TYPO3.l10n.localize('media_file_upload.sizeError'),
				minSizeError: TYPO3.l10n.localize('media_file_upload.minSizeError'),
				emptyError: TYPO3.l10n.localize('media_file_upload.emptyError'),
				noFilesError: TYPO3.l10n.localize('media_file_upload.noFilesError'),
				tooManyItemsError: TYPO3.l10n.localize('media_file_upload.tooManyItemsError'),
				retryFailTooManyItems: TYPO3.l10n.localize('media_file_upload.retryFailTooManyItems'),
				onLeave: TYPO3.l10n.localize('media_file_upload.onLeave')
			},
			// Override override main template
			template: '<div class="qq-uploader">' +
				'<pre class="qq-upload-drop-area"><span>{dragZoneText}</span></pre>' +
				'<div class="qq-upload-button" style="width: 105px;">{uploadButtonText}</div>' +
				'<span class="qq-max-size qq-vertical-align">%s</span>' +
				'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
				'<ul class="qq-upload-list"></ul>' +
				'</div>'
		}).on('submit', function (event, id, fileName) {
				var params = {};
				params[parameterPrefix + '[action]'] = 'update';
				params[parameterPrefix + '[controller]'] = 'Asset';
				params[parameterPrefix + '[fileIdentifier]'] = '%s';
				$(this).fineUploader('setParams', params);

				// Hide the size message
				$('.qq-max-size').toggle();
				$('.qq-upload-list').show();

			})
			.on('cancel', function (event, id, fileName) {
			})
			.on('complete', function (event, id, fileName, responseJSON) {

				$('.qq-max-size').toggle();
				$('.qq-upload-list').hide();
				$('.qq-upload-list', this).html(''); // remove progress bar

				if (responseJSON.thumbnail) {

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