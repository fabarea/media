<?php
namespace TYPO3\CMS\Media\Utility;

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

/**
 * A class for handling logger
 */
class Logger implements \TYPO3\CMS\Core\SingletonInterface {

	/**
	 * Returns a logger class instance.
	 *
	 * @param mixed $instance
	 * @return \TYPO3\CMS\Core\Log\Logger
	 */
	static public function getInstance($instance) {
		/** @var $loggerManager \TYPO3\CMS\Core\Log\LogManager */
		$loggerManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager');

		/** @var $logger \TYPO3\CMS\Core\Log\Logger */
		return $loggerManager->getLogger(get_class($instance));
	}
}
