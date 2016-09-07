<?php
namespace Fab\Media\Domain\Validator;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
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

        $allowedStorageIdentifiers = [];
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
