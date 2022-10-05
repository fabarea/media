<?php

namespace Fab\Media\Command;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Cache\CacheService;
use Fab\Vidi\Service\DataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileCacheFlushProcessedFilesCommand extends Command
{
    protected SymfonyStyle $io;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * Flush all processed files to be used for debugging mainly.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->getStorageRepository()->findAll() as $storage) {
            // This only works for local driver
            if ($storage->getDriverType() === 'Local' && $storage->isOnline()) {
                $processedDirectoryPath = Environment::getPublicPath() . $this->getProcessingFolderPath($storage);
                $this->io->info(sprintf('Removing files from %s for storage "%s" (%s)', $processedDirectoryPath, $storage->getName(), $storage->getUid()));

                $this->clearProcessedFiles($storage->getUid());

                GeneralUtility::rmdir($processedDirectoryPath, true);
                GeneralUtility::mkdir($processedDirectoryPath); // recreate the directory.

                $message = sprintf('Done! Removed all processed files from storage %s.', $storage->getUid());
                $this->io->info($message);
            }
        }

        return 0;
    }

    public function clearProcessedFiles(int $storageUid): int
    {
        $repository = GeneralUtility::makeInstance(ProcessedFileRepository::class);
        return $repository->removeAll($storageUid);
    }

    protected function getProcessingFolderPath(ResourceStorage $storage): string
    {
        $storageConfiguration = $storage->getConfiguration();
        $storageBasePath = rtrim($storageConfiguration['basePath'], '/');
        return '/' . $storageBasePath . $storage->getProcessingFolder()->getIdentifier();
    }

    protected function getCacheService(): CacheService
    {
        return GeneralUtility::makeInstance(CacheService::class);
    }

    protected function getStorageRepository(): StorageRepository
    {
        return GeneralUtility::makeInstance(StorageRepository::class);
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    protected function getConnection(string $tableName): Connection
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getConnectionForTable($tableName);
    }
}
