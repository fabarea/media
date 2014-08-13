# Module configuration
module.tx_media {
	settings {
		filter {
			usage {
				# Add a performance limit
				performanceLimit = {$module.tx_media.settings.filter.usage.performanceLimit}
			}
		}
	}
	view {
		templateRootPath = {$module.tx_media.view.templateRootPath}
		partialRootPath = {$module.tx_media.view.partialRootPath}
		layoutRootPath = {$module.tx_media.view.layoutRootPath}
	}
}