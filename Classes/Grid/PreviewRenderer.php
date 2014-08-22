<?php
namespace TYPO3\CMS\Media\Grid;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Media\Service\ThumbnailInterface;
use TYPO3\CMS\Media\Utility\ModuleUtility;
use TYPO3\CMS\Vidi\Grid\GridRendererAbstract;
use TYPO3\CMS\Vidi\Module\ModulePlugin;

/**
 * Class rendering the preview of a media in the Grid.
 */
class PreviewRenderer extends GridRendererAbstract {

	/**
	 * Render a preview of a file in the Grid.
	 *
	 * @return string
	 */
	public function render() {

		$file = ObjectFactory::getInstance()->convertContentObjectToFile($this->object);

		$uri = FALSE;
		$appendTime = TRUE;

		// Compute image-editor or link-creator URL.
		if (ModulePlugin::getInstance()->isPluginRequired('imageEditor')) {
			$appendTime = FALSE;
			$uri = sprintf('%s&%s[asset]=%s',
				ModuleUtility::getUri('show', 'ImageEditor'),
				ModuleUtility::getParameterPrefix(),
				$this->object->getUid()
			);
		} elseif (ModulePlugin::getInstance()->isPluginRequired('linkCreator')) {
			$appendTime = FALSE;
			$uri = sprintf('%s&%s[asset]=%s',
				ModuleUtility::getUri('show', 'LinkCreator'),
				ModuleUtility::getParameterPrefix(),
				$this->object->getUid()
			);
		}

		$result = $this->getThumbnailService($file)
			->setOutputType(ThumbnailInterface::OUTPUT_IMAGE_WRAPPED)
			->setAppendTimeStamp($appendTime)
			->setTarget(ThumbnailInterface::TARGET_BLANK)
			->setAnchorUri($uri)
			->create();

		// Add file info
		$result .= sprintf('<div class="container-fileInfo" style="font-size: 7pt; color: #777;">%s</div>',
			$this->getMetadataViewHelper()->render($file)
		);
		return $result;
	}

	/**
	 * @param File $file
	 * @return \TYPO3\CMS\Media\Service\ThumbnailService
	 */
	protected function getThumbnailService(File $file) {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Service\ThumbnailService', $file);
	}

	/**
	 * @return \TYPO3\CMS\Media\ViewHelpers\MetadataViewHelper
	 */
	protected function getMetadataViewHelper() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\ViewHelpers\MetadataViewHelper');
	}
}
