<?php
namespace TYPO3\CMS\Media\Utility;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;

/**
 * A class for handling variants settings
 */
class VariantUtility implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * @var array
	 */
	protected $variations = array();

	/**
	 * @var int
	 */
	protected $storageIdentifier;

	/**
	 * Returns a class instance.
	 *
	 * @param int $storageIdentifier
	 * @return \TYPO3\CMS\Media\Utility\VariantUtility
	 */
	static public function getInstance($storageIdentifier = NULL) {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Utility\VariantUtility', $storageIdentifier);
	}

	/**
	 * @param $storageIdentifier
	 */
	public function __construct($storageIdentifier) {
		$this->storageIdentifier = $storageIdentifier;
	}

	/**
	 * @return array
	 */
	public function getVariations() {
		if (empty($this->variations)) {
			$storage = ObjectFactory::getInstance()->getStorage($this->storageIdentifier);
			$storageRecord = $storage->getStorageRecord();
			if (strlen($storageRecord['default_variations']) > 0) {
				$variations = GeneralUtility::trimExplode(',', $storageRecord['default_variations']);
				foreach ($variations as $variation) {

					/** @var \TYPO3\CMS\Media\Dimension $dimension */
					$dimension = GeneralUtility::makeInstance('TYPO3\CMS\Media\Dimension', $variation);
					$this->variations[] = $dimension;
				}
			}
		}
		return $this->variations;
	}
}
?>
