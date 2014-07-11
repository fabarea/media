<?php
namespace TYPO3\CMS\Media\Hooks;

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

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtility;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Extracts metadata after uploading a file.
 */
class FileUploadHook implements ExtendedFileUtilityProcessDataHookInterface {

	/**
	 * @param string $action The action
	 * @param array $cmdArr The parameter sent to the action handler
	 * @param array $result The results of all calls to the action handler
	 * @param ExtendedFileUtility $pObj The parent object
	 * @return void
	 */
	public function processData_postProcessAction($action, array $cmdArr, array $result, ExtendedFileUtility $pObj) {
		if ($action === 'upload') {
			/** @var \TYPO3\CMS\Core\Resource\File[] $files */
			$files = array_pop($result);
			if (!is_array($files)) {
				return;
			}

			foreach ($files as $file) {
				// Run the indexer for extracting metadata.
				$this->getIndexer($file->getStorage())
					->extractMetadata($file)
					->applyDefaultCategories($file);
			}
		}
	}

	/**
	 * @param ResourceStorage $storage
	 * @return \TYPO3\CMS\Media\Index\Indexer
	 */
	protected function getIndexer($storage) {
		// Call the indexer service for updating the metadata of the file.
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Index\Indexer', $storage);
	}

}
