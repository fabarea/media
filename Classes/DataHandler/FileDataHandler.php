<?php
namespace TYPO3\CMS\Media\DataHandler;

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

use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Vidi\DataHandler\AbstractDataHandler;
use TYPO3\CMS\Vidi\Domain\Model\Content;

/**
 * Special Data Handler for file
 */
class FileDataHandler extends AbstractDataHandler {

	/**
	 * Process File with action "update".
	 *
	 * @param Content $content
	 * @throws \Exception
	 * @return bool
	 */
	public function processUpdate(Content $content) {
		throw new \Exception('Not yet implemented', 1409988673);
	}

	/**
	 * Process File with action "remove".
	 *
	 * @param Content $content
	 * @return bool
	 */
	public function processRemove(Content $content) {
		$file = ResourceFactory::getInstance()->getFileObject($content->getUid());

		$numberOfReferences = $this->getFileReferenceService()->countTotalReferences($file);
		if ($numberOfReferences === 0) {
			$file->delete();
		} else {
			$message = sprintf('I could not delete file "%s" as it is has %s reference(s).', $file->getUid(), $numberOfReferences);
			$this->errorMessages = $message;
		}
	}

	/**
	 * Process File with action "copy".
	 *
	 * @param Content $content
	 * @param string $target
	 * @throws \Exception
	 * @return bool
	 */
	public function processCopy(Content $content, $target) {
		throw new \Exception('Not yet implemented', 1409988674);
	}

	/**
	 * Process File with action "move".
	 *
	 * @param Content $content
	 * @param string $target
	 * @throws \Exception
	 * @return bool
	 */
	public function processMove(Content $content, $target) {

		$file = ResourceFactory::getInstance()->getFileObject($content->getUid());

		// Only process if the storage is different.
		if ((int)$file->getStorage()->getUid() !== (int)$target) {

			$targetStorage = ResourceFactory::getInstance()->getStorageObject((int)$target);

			// Retrieve target directory in the new storage. The folder will only be returned if the User has the correct permission.
			$targetFolder = ObjectFactory::getInstance()->getTargetFolder($targetStorage, $file);

			try {
				// Move file
				$file->moveTo($targetFolder, $file->getName(), 'renameNewFile');
			} catch(\Exception $e) {
				$this->errorMessages = $e->getMessage();
			}
		}
	}

	/**
	 * @return \TYPO3\CMS\Media\Resource\FileReferenceService
	 */
	protected function getFileReferenceService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\Resource\FileReferenceService');
	}
}
