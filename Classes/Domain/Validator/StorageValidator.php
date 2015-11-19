<?php
namespace Fab\Media\Domain\Validator;

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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate whether "storageIdentifier" is allowed.
 */
class StorageValidator extends AbstractValidator
{

    /**
     * Check if $storageIdentifier is allowed. If it is not valid, throw an exception.
     *
     * @param int $combinedIdentifier
     * @return void
     */
    public function isValid($combinedIdentifier)
    {

        $allowedStorageIdentifiers = array();
        foreach ($this->getMediaModule()->getAllowedStorages() as $allowedStorage) {
            $allowedStorageIdentifiers[] = $allowedStorage->getUid();
        }

        $storage = ResourceFactory::getInstance()->getStorageObjectFromCombinedIdentifier($combinedIdentifier);
        if (!in_array($storage->getUid(), $allowedStorageIdentifiers)) {
            $message = sprintf('Storage identifier "%s" is not allowed or is currently off-line.', $combinedIdentifier);
            $this->addError($message, 1380813503);
        }
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }
}
