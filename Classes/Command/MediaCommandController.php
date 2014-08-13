<?php
namespace TYPO3\CMS\Media\Command;
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

use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command Controller which handles actions related to Media.
 */
class MediaCommandController extends CommandController {

	/**
	 * Index (or re-index) all files of the Media storage.
	 *
	 * @return void
	 */
	public function indexCommand() {

		$this->outputLine('Scanning Media Storages...');

		/** @var \TYPO3\CMS\Media\Service\AssetIndexerService $indexerService */
		$indexerService = $this->objectManager->get('TYPO3\CMS\Media\Service\AssetIndexerService');
		$result = $indexerService->indexStorage();

		// Format the message to output.
		$message = sprintf('* Storage "%s" contains %s file%s (variants included).',
			$result['storageName'],
			$result['fileNumber'],
			$result['fileNumber'] > 1 ? 's' : ''
		);

		$this->outputLine($message);
	}

	/**
	 * Check whether the Index is Ok. In case not, display some message.
	 *
	 * @return void
	 */
	public function checkIndexCommand() {

		$this->outputLine('Checking index of storage...');

		/** @var \TYPO3\CMS\Media\Service\AssetIndexerService $assetIndexerService */
		$assetIndexerService = $this->objectManager->get('TYPO3\CMS\Media\Service\AssetIndexerService');
		$missingResources = $assetIndexerService->getMissingResources();
		$duplicates = $assetIndexerService->getDuplicates();

		// Missing files case
		if (!empty($missingResources)) {
			$this->outputLine('');
			$this->outputLine('Missing resources:');
			/** @var \TYPO3\CMS\Core\Resource\File $missingFile */
			foreach ($missingResources as $missingFile) {
				$message = sprintf('* Missing resource for uid "%s" with identifier "%s".',
					$missingFile->getUid(),
					$missingFile->getIdentifier()
				);
				$this->outputLine($message);
			}
		}

		// Duplicate file object
		if (!empty($duplicates)) {
			$this->outputLine('');
			$this->outputLine('Duplicated identifiers detected:');
			foreach ($duplicates as $identifier => $duplicate) {

				// build temporary array
				$uids = array();
				foreach ($duplicate as $value) {
					$uids[] = $value['uid'];
				}

				$message = sprintf('* uids "%s" having same identifier %s',
					implode(',', $uids),
					$identifier
				);
				$this->outputLine($message);

			}
		}

		if (empty($missingResources) && empty($duplicates)) {
			$this->outputLine('Index is OK');
		}
	}
}
