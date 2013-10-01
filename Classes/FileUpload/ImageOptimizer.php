<?php
namespace TYPO3\CMS\Media\FileUpload;

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

/**
 * Class that optimize an image according to some settings.
 */
class ImageOptimizer implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $optimizers = array();

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\ImageOptimizer
	 */
	static public function getInstance() {
		return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\ImageOptimizer');
	}

	/**
	 * Constructor
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\ImageOptimizer
	 */
	public function __construct() {
		$this->add('TYPO3\CMS\Media\FileUpload\Optimizer\Resize');
		$this->add('TYPO3\CMS\Media\FileUpload\Optimizer\Rotate');
	}

	/**
	 * Register a new optimizer
	 *
	 * @param string $className
	 * @return void
	 */
	public function add($className) {
		$this->optimizers[] = $className;
	}

	/**
	 * Un-register a new optimizer
	 *
	 * @param string $className
	 * @return void
	 */
	public function remove($className) {
		if (in_array($className, $this->optimizers)) {
			$key = array_search($className, $this->optimizers);
			unset($this->optimizers[$key]);
		}
	}

	/**
	 * Optimize an image
	 *
	 * @param \TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile
	 * @return \TYPO3\CMS\Media\FileUpload\UploadedFileInterface
	 */
	public function optimize(\TYPO3\CMS\Media\FileUpload\UploadedFileInterface $uploadedFile) {

		foreach ($this->optimizers as $optimizer) {
			/** @var $optimizer \TYPO3\CMS\Media\FileUpload\ImageOptimizerInterface */
			$optimizer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($optimizer);
			$uploadedFile = $optimizer->optimize($uploadedFile);
		}

		return $uploadedFile;
	}
}
?>