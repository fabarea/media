<?php
namespace Fab\Media\Command;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Service\DataService;
use FilesystemIterator;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
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

                $count = $this->getDataService()
                    ->count(
                        'sys_file_processedfile',
                        [
                            'storage' => $storage->getUid()
                        ]
                    );

                // Remove the record as well.
                $this->getDataService()->delete('sys_file_processedfile', ['storage' => $storage->getUid()]);

                $message = sprintf('Done! Removed %s records from "sys_file_processedfile".', $count);
                $this->outputLine($message);
            }

        }

        // Remove possible remaining "sys_file_processedfile"
        $this
            ->getConnection('sys_file_processedfile')
            ->query('TRUNCATE sys_file_processedfile');
    }

    /**
     * @return \Fab\Media\Cache\CacheService|object
     */
    protected function getCacheService()
    {
        return GeneralUtility::makeInstance(\Fab\Media\Cache\CacheService::class);
    }

    /**
     * @return StorageRepository|object
     */
    protected function getStorageRepository()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Resource\StorageRepository::class);
    }

    /**
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    /**
     * @param string $tableName
     * @return object|Connection
     */
    protected function getConnection($tableName): Connection
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getConnectionForTable($tableName);
    }

}
