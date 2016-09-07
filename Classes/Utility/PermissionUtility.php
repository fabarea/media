<?php
namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class for handling permission
 */
class PermissionUtility implements SingletonInterface
{

    /**
     * Returns a class instance.
     *
     * @return PermissionUtility
     * @throws \InvalidArgumentException
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(PermissionUtility::class);
    }

    /**
     * Returns allowed extensions given a possible storage.
     *
     * @param null|int|ResourceStorage $storage
     * @return array
     * @throws \InvalidArgumentException
     */
    public function getAllowedExtensions($storage = null)
    {

        $fieldNames = array(
            'extension_allowed_file_type_1',
            'extension_allowed_file_type_2',
            'extension_allowed_file_type_3',
            'extension_allowed_file_type_4',
            'extension_allowed_file_type_5',
        );

        if (!is_null($storage)) {
            if (!$storage instanceof ResourceStorage) {
                $storage = ResourceFactory::getInstance()->getStorageObject((int)$storage);
            }
        } else {
            $storage = $this->getMediaModule()->getCurrentStorage();
        }

        $storageRecord = $storage->getStorageRecord();
        $allowedExtensions = [];
        foreach ($fieldNames as $fieldName) {
            $_allowedExtensions = GeneralUtility::trimExplode(',', $storageRecord[$fieldName], true);
            $allowedExtensions = array_merge($allowedExtensions, $_allowedExtensions);
        }

        $uniqueAllowedExtensions = array_unique($allowedExtensions);
        return array_filter($uniqueAllowedExtensions, 'strlen');
    }

    /**
     * Returns allowed extensions list.
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function getAllowedExtensionList()
    {
        return implode(',', $this->getAllowedExtensions());
    }

    /**
     * @return MediaModule
     * @throws \InvalidArgumentException
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }

}
