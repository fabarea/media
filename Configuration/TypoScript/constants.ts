module.tx_media {
	settings {
		filter {
			usage {
				# cat=plugin.tx_media/a; type=int+; label=Performance Limit for filter Usage:Filter by Usage requires a lot of server resource and therefore the scope must be limited.
				performanceLimit = 300
			}
		}
	}
	view {
		# cat=module.tx_media/file; type=string; label=Path to template root (BE)
		templateRootPath = EXT:media/Resources/Private/Backend/Templates/
		# cat=module.tx_media/file; type=string; label=Path to template partials (BE)
		partialRootPath = EXT:media/Resources/Private/Partials/
		# cat=module.tx_media/file; type=string; label=Path to template layouts (BE)
		layoutRootPath = EXT:media/Resources/Private/Backend/Layouts/
	}
}