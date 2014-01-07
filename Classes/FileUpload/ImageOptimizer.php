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
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class that optimize an image according to some settings.
 */
class ImageOptimizer implements SingletonInterface {

	/**
	 * @var array
	 */
	protected $optimizers = array();

	/**
	 * @var \TYPO3\CMS\Core\Resource\ResourceStorage
	 */
	protected $storage;

	/**
	 * Returns a class instance.
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\ImageOptimizer
	 * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
	 */
	static public function getInstance($storage = NULL) {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\FileUpload\ImageOptimizer', $storage);
	}

	/**
	 * Constructor
	 *
	 * @return \TYPO3\CMS\Media\FileUpload\ImageOptimizer
	 * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
	 */
	public function __construct($storage = NULL) {
		$this->storage = $storage;
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
	 * @param UploadedFileInterface $uploadedFile
	 * @return UploadedFileInterface
	 */
	public function optimize(UploadedFileInterface $uploadedFile) {

		foreach ($this->optimizers as $optimizer) {

			/** @var $optimizer \TYPO3\CMS\Media\FileUpload\ImageOptimizerInterface */
			$optimizer = GeneralUtility::makeInstance($optimizer, $this->storage);
			$uploadedFile = $optimizer->optimize($uploadedFile);
		}

		return $uploadedFile;
	}
}

?>