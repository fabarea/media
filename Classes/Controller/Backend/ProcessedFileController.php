<?php
namespace TYPO3\CMS\Media\Controller\Backend;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Resource\ProcessedFile;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Controller which handles actions related to Processed File.
 */
class ProcessedFileController extends ActionController {

	/**
	 * Create a processed file according to some configuration.
	 *
	 * @param int $file
	 * @param array $processingConfiguration
	 * @return string
	 */
	public function createAction($file, array $processingConfiguration = array()) {

		$processingConfiguration = array(
			'additionalParameters' => '-crop 300x300+400+400 -resize 100x100 -modulate 120,90',
//			'additionalParameters' => '-rotate 90 -negate -crop 100x100+100+100'
		);

		$file = ResourceFactory::getInstance()->getFileObject($file);
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
