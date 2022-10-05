<?php

namespace Fab\Media\Thumbnail;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Service\DataService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Domain\Model\Selection;

/**
 * Thumbnail Generator for generating thumbnails in batch.
 */
class ThumbnailGenerator
{
    protected int $numberOfTraversedFiles = 0;

    protected int $numberOfProcessedFiles = 0;

    protected int $numberOfMissingFiles = 0;

    protected array $configuration = [];

    protected ?ResourceStorage $storage = null;

    protected ?Selection $selection = null;

    protected array $resultSet = [];

    protected array $newProcessedFileIdentifiers = [];

    protected int $lastInsertedProcessedFile = 0;

    protected int $limit = 0;

    protected int $offset = 0;

    public function generate(int $limit = 0, int $offset = 0): void
    {
        $this->limit = $limit;
        $this->offset = $offset;

        // Compute a possible limit and offset for the query.
        $rows = $this->getDataService()
            ->getRecords(
                'sys_file',
                [
                    'storage' => $this->storage->getUid()
                ],
                $limit,
                $offset
            );

        foreach ($rows as $row) {


            $file = $this->getResourceFactory()->getFileObject($row['uid'], $row);

            if ($file->exists()) {

                $thumbnailUri = $this->getThumbnailService($file)
                    ->setOutputType(ThumbnailInterface::OUTPUT_URI)
                    ->setConfiguration($this->configuration)
                    ->create();

                $this->resultSet[$file->getUid()] = [
                    'fileUid' => $file->getUid(),
                    'fileIdentifier' => $file->getIdentifier(),
                    'thumbnailUri' => strpos($thumbnailUri, '_processed_') > 0 ? $thumbnailUri : '', // only returns the thumbnail uri if a processed file has been created.
                ];

                if ($this->isNewProcessedFile()) {
                    $this->incrementNumberOfProcessedFiles();
                    $this->newProcessedFileIdentifiers[$file->getUid()] = $this->lastInsertedProcessedFile;
                }

                $this->incrementNumberOfTraversedFiles();
            } else {
                $this->incrementNumberOfMissingFiles();
            }
        }
    }

    protected function isNewProcessedFile(): bool
    {
        $isNewProcessedFile = false;

        $tableName = 'sys_file_processedfile';
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $databaseConnection = $connectionPool->getConnectionForTable($tableName);
        $lastInsertedId = (int)$databaseConnection->lastInsertId($tableName);

        if ($lastInsertedId > 0 && $lastInsertedId !== $this->lastInsertedProcessedFile) {
            $this->lastInsertedProcessedFile = $lastInsertedId;
            $isNewProcessedFile = true;
        }
        return $isNewProcessedFile;
    }

    public function getNumberOfTraversedFiles(): int
    {
        return $this->numberOfTraversedFiles;
    }

    public function getNumberOfProcessedFiles(): int
    {
        return $this->numberOfProcessedFiles;
    }

    public function getTotalNumberOfFiles(): int
    {

        return $this->getDataService()
            ->count(
                'sys_file',
                [
                    'storage' => $this->storage->getUid()
                ],
                $this->limit,
                $this->offset
            );
    }

    /**
     * @return array
     */
    public function getResultSet()
    {
        return $this->resultSet;
    }

    /**
     * @return array
     */
    public function getNewProcessedFileIdentifiers()
    {
        return $this->newProcessedFileIdentifiers;
    }

    /**
     * @return int
     */
    public function getNumberOfMissingFiles()
    {
        return $this->numberOfMissingFiles;
    }

    public function setStorage(ResourceStorage $storage): ThumbnailGenerator
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @param Selection $selection
     */
    public function setSelection($selection): ThumbnailGenerator
    {
        $this->selection = $selection;
        return $this;
    }

    public function setConfiguration(array $configuration): ThumbnailGenerator
    {
        $this->configuration = $configuration;
        return $this;
    }

    protected function getThumbnailService(File $file): ThumbnailService
    {
        return GeneralUtility::makeInstance(ThumbnailService::class, $file);
    }

    protected function incrementNumberOfTraversedFiles()
    {
        $this->numberOfTraversedFiles++;
    }

    protected function incrementNumberOfMissingFiles()
    {
        $this->numberOfMissingFiles++;
    }

    protected function incrementNumberOfProcessedFiles()
    {
        $this->numberOfProcessedFiles++;
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    protected function getResourceFactory(): ResourceFactory
    {
        return GeneralUtility::makeInstance(ResourceFactory::class);
    }
}
