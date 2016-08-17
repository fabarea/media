<?php
namespace Fab\Media\Hook;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtility;
use TYPO3\CMS\Core\Utility\File\ExtendedFileUtilityProcessDataHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Extracts metadata after uploading a file.
 */
class FileUploadHook implements ExtendedFileUtilityProcessDataHookInterface
{

    /**
     * @param string $action The action
     * @param array $cmdArr The parameter sent to the action handler
     * @param array $result The results of all calls to the action handler
     * @param ExtendedFileUtility $pObj The parent object
     * @return void
     */
    public function processData_postProcessAction($action, array $cmdArr, array $result, ExtendedFileUtility $pObj)
    {
        if ($action === 'upload') {
            /** @var \TYPO3\CMS\Core\Resource\File[] $files */
            $files = array_pop($result);
            if (!is_array($files)) {
                return;
            }

            foreach ($files as $file) {
                // Run the indexer for extracting metadata.
                $this->getMediaIndexer($file->getStorage())
                    ->extractMetadata($file)
                    ->applyDefaultCategories($file);
            }
        }
    }

    /**
     * Get the instance of the Indexer service to update the metadata of the file.
     *
     * @param ResourceStorage $storage
     * @return \Fab\Media\Index\MediaIndexer
     */
    protected function getMediaIndexer($storage)
    {
        return GeneralUtility::makeInstance('Fab\Media\Index\MediaIndexer', $storage);
    }

}
