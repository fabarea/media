<?php
namespace Fab\Media\Index;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Resource\FileReferenceService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class providing indexing service for Media/
 */
class IndexAnalyser implements SingletonInterface
{

    /**
     * Return missing file for a given storage.
     *
     * @param ResourceStorage $storage
     * @return array
     */
    public function searchForMissingFiles(ResourceStorage $storage)
    {

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file');

        $missingFiles = [];
        $statement = $queryBuilder
            ->select('*')
            ->from('sys_file')
            ->where(
                $queryBuilder->expr()->eq('storage', $storage->getUid())
            )->execute();
        while ($row = $statement->fetchAssociative()) {

            // This task is very memory consuming on large data set e.g > 20'000 records.
            // We must think of having a pagination if there is the need for such thing.
            $file = ResourceFactory::getInstance()->getFileObject($row['uid'], $row);
            if (!$file->exists()) {
                $missingFiles[] = $file;
            }
        }
        return $missingFiles;
    }

    /**
     * Deletes all missing files for a given storage.
     *
     * @param ResourceStorage $storage
     * @return array
     * @throws \InvalidArgumentException
     */
    public function deleteMissingFiles(ResourceStorage $storage)
    {
        /** @var FileReferenceService $fileReferenceService */
        $fileReferenceService = GeneralUtility::makeInstance(FileReferenceService::class);
        $missingFiles = $this->searchForMissingFiles($storage);
        $deletedFiles = [];

        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);

        /** @var \TYPO3\CMS\Core\Resource\File $missingFile */
        foreach ($missingFiles as $missingFile) {
            try {
                // if missingFile has no file references
                if ($missingFile && count($fileReferenceService->findFileReferences($missingFile)) === 0) {
                    // The case is special as we have a missing file in the file system
                    // As a result, we can't use $fileObject->delete(); which will
                    // raise exception "Error while fetching permissions"
                    $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file');
                    $queryBuilder->delete('sys_file')
                        ->where($queryBuilder->expr()->eq('uid', $missingFile->getUid()))
                        ->execute();

                    $deletedFiles[$missingFile->getUid()] = $missingFile->getIdentifier();
                }
            } catch (\Exception $e) {
                continue;
            }
        }
        return $deletedFiles;
    }

    /*
     * Return duplicates file records
     *
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     * @return array
     */
    public function searchForDuplicateIdentifiers(ResourceStorage $storage)
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file');

        $statement = $queryBuilder
            ->select('*')
            ->from('sys_file')
            ->where(
                $queryBuilder->expr()->eq('storage', $storage->getUid())
            )
            ->groupby('identifier')
            ->having('count(*) > 1')
            ->execute();

        // Detect duplicate records.
        $duplicates = [];
        while ($row = $statement->fetchAssociative()) {

            $records = $queryBuilder
                ->select('*')
                ->from('sys_file')
                ->where(
                    $queryBuilder->expr()->eq('storage', $storage->getUid())
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('identifier', $queryBuilder->createNamedParameter($row['identifier']))
                )
                ->execute();
            $records = $records->fetchAllAssociative();
            $duplicates[$row['identifier']] = $records;
        }
        return $duplicates;
    }

    /**
     * Return duplicates file records
     *
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     * @return array
     */
    public function searchForDuplicateSha1(ResourceStorage $storage)
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file');

        $statement = $queryBuilder
            ->select('*')
            ->from('sys_file')
            ->where(
                $queryBuilder->expr()->eq('storage', $storage->getUid())
            )
            ->groupby('sha1')
            ->having('count(*) > 1')
            ->execute();

        // Detect duplicate records.
        $duplicates = [];
        while ($row = $statement->fetchAssociative()) {

            $records = $queryBuilder
                ->select('*')
                ->from('sys_file')
                ->where(
                    $queryBuilder->expr()->eq('storage', $storage->getUid())
                )
                ->andWhere(
                    $queryBuilder->expr()->eq('identifier', $queryBuilder->createNamedParameter($row['sha1']))
                )
                ->execute();
            $records = $records->fetchAllAssociative();
            $duplicates[$row['sha1']] = $records;
        }
        return $duplicates;
    }

    /**
     * @param string $tableName
     * @return object|QueryBuilder
     */
    protected function getQueryBuilder($tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($tableName);
    }
}
