<?php
namespace Fab\Media\Controller\Backend;

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
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Processed File.
 */
class ProcessedFileController extends ActionController {

	/**
	 * Initializes the controller before invoking an action method.
	 */
	public function initializeAction() {

		// Configure property mapping to retrieve the file object.
		if ($this->arguments->hasArgument('file')) {

			/** @var \Fab\Media\TypeConverter\FileConverter $typeConverter */
			$typeConverter = $this->objectManager->get('Fab\Media\TypeConverter\FileConverter');

			$propertyMappingConfiguration = $this->arguments->getArgument('file')->getPropertyMappingConfiguration();
			$propertyMappingConfiguration->setTypeConverter($typeConverter);
		}
	}

	/**
	 * Create a processed file according to some configuration.
	 *
	 * @param File $file
	 * @param array $processingConfiguration
	 * @return string
	 */
	public function createAction(File $file, array $processingConfiguration = array()) {
		$processedFile = $file->process(ProcessedFile::CONTEXT_IMAGECROPSCALEMASK, $processingConfiguration);

		$response = array(
			'success' => TRUE,
			'original' => $file->getUid(),
			'title' => $file->getProperty('title') ? $file->getProperty('title') : $file->getName(),
			'publicUrl' => $processedFile->getPublicUrl(),
			'width' => $processedFile->getProperty('width'),
			'height' => $processedFile->getProperty('height'),
		);

		header("Content-Type: text/json");
		return htmlspecialchars(json_encode($response), ENT_NOQUOTES);
	}

}
