<?php
namespace TYPO3\CMS\Media\SignalSlot;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class which handle signal slot for Vidi Content controller
 */
class ContentController {

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var \TYPO3\CMS\Vidi\ModuleLoader
	 * @inject
	 */
	protected $moduleLoader;

	/**
	 * Post process the matcher object.
	 *
	 * @param \TYPO3\CMS\Vidi\Persistence\Matcher $matcherObject
	 * @param string $dataType
	 * @return void
	 */
	public function postProcessMatcherObject(\TYPO3\CMS\Vidi\Persistence\Matcher $matcherObject, $dataType) {
		if ($dataType === 'sys_file') {

			$parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET($this->moduleLoader->getParameterPrefix());

			if (!empty($parameters['storage']) && (int) $parameters['storage'] > 0) {
				$storageIdentifier = (int) $parameters['storage'];
			} else {
				$storage = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorage();
				$storageIdentifier = $storage->getUid();
			}

			$matcherObject->equals('storage', $storageIdentifier);
			$matcherObject->equals('is_variant', 0);
		}
	}
}

?>