<?php
namespace Fab\Media\Command;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use FilesystemIterator;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command Controller which handles actions related to the Cache.
 */
class FileCacheCommandController extends CommandController
{

    /**
     * Warm up the cache. Update some caching columns such as "number_of_references" to speed up the search.
     *
     * @return void
     */
    public function warmUpCommand()
    {
        $numberOfEntries = $this->getCacheService()->warmUp();
        $message = sprintf('Done! Processed %s entries', $numberOfEntries);
        $this->outputLine($message);
    }

    /**
     * Flush all processed files to be used for debugging mainly.
     *
     * @return void
     */
    public function flushProcessedFilesCommand()
    {

        foreach ($this->getStorageRepository()->findAll() as $storage) {

            // This only works for local driver
            if ($storage->getDriverType() === 'Local') {

                $this->outputLine();
                $this->outputLine(sprintf('%s (%s)', $storage->getName(), $storage->getUid()));
                $this->outputLine('--------------------------------------------');
                $this->outputLine();

                #$storage->getProcessingFolder()->delete(true); // will not work

                // Well... not really FAL friendly but straightforward for Local drivers.
                $processedDirectoryPath = PATH_site . $storage->getProcessingFolder()->getPublicUrl();
                $fileIterator = new FilesystemIterator($processedDirectoryPath, FilesystemIterator::SKIP_DOTS);
                $numberOfProcessedFiles = iterator_count($fileIterator);

                GeneralUtility::rmdir($processedDirectoryPath, true);
                GeneralUtility::mkdir($processedDirectoryPath); // recreate the directory.

                $message = sprintf('Done! Removed %s processed file(s).', $numberOfProcessedFiles);
                $this->outputLine($message);

                // Remove the record as well.
                $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('count(*) AS numberOfProcessedFiles', 'sys_file_processedfile', 'storage = ' . $storage->getUid());
                $this->getDatabaseConnection()->exec_DELETEquery('sys_file_processedfile', 'storage = ' . $storage->getUid());

                $message = sprintf('Done! Removed %s records from "sys_file_processedfile".', $record['numberOfProcessedFiles']);
                $this->outputLine($message);
            }

        }

        // Remove possible remaining "sys_file_processedfile"
        $query = 'TRUNCATE sys_file_processedfile';
        $this->getDatabaseConnection()->sql_query($query);
    }

    /**
     * @return \Fab\Media\Cache\CacheService
     */
    protected function getCacheService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Cache\CacheService');
    }

    /**
     * @return StorageRepository
     */
    protected function getStorageRepository()
    {
        return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
    }

    /**
     * Returns a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
