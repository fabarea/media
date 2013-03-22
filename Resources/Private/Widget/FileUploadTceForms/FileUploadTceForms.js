(function ($) {
	$(function () {


		var prefix = '%s';
		var recordUid = '%s';

		$('#%s').fineUploader({
			request: {
				endpoint: '/typo3/mod.php'
			},
			validation: {
				allowedExtensions: ['%s'],
				sizeLimit: '%s' // bytes
			},
			multiple: false, // no multiple upload in a regular form

			template: '<div class="qq-uploader">' +
				'<pre class="qq-upload-drop-area"><span>{dragZoneText}</span></pre>' +
				'<div class="qq-upload-button" style="width: 105px;">{uploadButtonText}</div>' +
				'<span class="qq-max-size qq-vertical-align">%s</span>' +
				'<span class="qq-drop-processing"><span>{dropProcessingText}</span><span class="qq-drop-processing-spinner"></span></span>' +
				'<ul class="qq-upload-list"></ul>' +
				'</div>',
			// Templating for Twitter Bootstrap
			debug: true
		}).on('submit', function (event, id, fileName) {
				var params = new Object();
				params[prefix + '[action]'] = 'upload';
				params[prefix + '[controller]'] = 'Asset';
				params[prefix + '[asset][uid]'] = recordUid;
				params['M'] = 'user_MediaM1';
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

				if (responseJSON.thumbnail) {
					var decoded = $("<div/>").html(responseJSON.thumbnail).text();
					$('.container-thumbnail').html(decoded);
				}

				// Code injected below by the server.
				//%s
			});
	});
})(TYPO3.jQuery);