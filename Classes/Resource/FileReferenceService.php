<?php

namespace Fab\Media\Resource;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Service\DataService;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * File Reference Service.
 */
class FileReferenceService
{
    /**
     * Return all references found in sys_file_reference.
     *
     * @param File|int $file
     */
    public function findFileReferences($file): array
    {
        $fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

        // Get the file references of the file.
        return $this->getDataService()->getRecords(
            'sys_file_reference',
            [
                'uid_local' => $fileIdentifier,
            ]
        );
    }

    /**
     * Return soft image references.
     *
     * @param File|int $file
     */
    public function findSoftImageReferences($file): array
    {
        $fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

        // Get the file references of the file in the RTE.
        $softReferences = $this->getDataService()->getRecords(
            'sys_refindex',
            [
                'softref_key' => 'rtehtmlarea_images',
                'ref_table' => 'sys_file',
                'ref_uid' => $fileIdentifier,
            ]
        );
        return $softReferences;
    }

    protected function findFileIndexReferences(File $file): array
    {
        $queryBuilder = $this->getQueryBuilder('sys_refindex');
        return $queryBuilder
            ->select('*')
            ->from('sys_refindex')
            ->where(
                $queryBuilder->expr()->eq(
                    'ref_table',
                    $queryBuilder->createNamedParameter('sys_file')
                ),
                $queryBuilder->expr()->eq(
                    'ref_uid',
                    $queryBuilder->createNamedParameter($file->getUid(), Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    'tablename',
                    $queryBuilder->createNamedParameter('sys_file_metadata')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    public function findContentIndexReferences(File $file): array
    {
        $queryBuilder = $this->getQueryBuilder('sys_refindex');
        $relatedIndexReferences = [];
        foreach ($this->findFileIndexReferences($file) as $fileIndexReference) {
            $relatedIndexReference = $queryBuilder
                ->select('*')
                ->from('sys_refindex')
                ->where(
                    $queryBuilder->expr()->eq(
                        'ref_table',
                        $queryBuilder->createNamedParameter('sys_file_reference')
                    ),
                    $queryBuilder->expr()->eq(
                        'ref_uid',
                        $queryBuilder->createNamedParameter($fileIndexReference['recuid'], Connection::PARAM_INT)
                    ),
                    $queryBuilder->expr()->neq(
                        'tablename',
                        $queryBuilder->createNamedParameter('sys_file_metadata')
                    )
                )
                ->executeQuery()
                ->fetchAssociative();

            if ($relatedIndexReference) {
                $relatedIndexReferences[] = $relatedIndexReference;
            }
        }
        return $relatedIndexReferences;
    }

    /**
     * Return link image references.
     *
     * @param File|int $file
     */
    public function findSoftLinkReferences($file)
    {
        $fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

        // Get the link references of the file.
        $softReferences = $this->getDataService()->getRecords(
            'sys_refindex',
            [
                'softref_key' => 'typolink_tag',
                'ref_table' => 'sys_file',
                'ref_uid' => $fileIdentifier,
            ]
        );
        return $softReferences;
    }

    /**
     * @param File|int $file
     */
    public function countTotalReferences($file): int
    {

        $fileUid = $file instanceof File
            ? $file->getUid()
            : $file;

        $numberOfReferences = $this->countFileReferences($fileUid);
        $numberOfReferences += $this->countSoftImageReferences($fileUid);
        $numberOfReferences += $this->countSoftLinkReferences($fileUid);
        $numberOfReferences += $this->countFileIndexReferences($fileUid);

        return $numberOfReferences;
    }

    /**
     * Count all references found in sys_file_reference.
     */
    protected function countFileReferences(int $fileUid): int
    {
        return $this->getDataService()
            ->count(
                'sys_file_reference',
                [
                    'uid_local' => $fileUid
                ]
            );
    }

    /**
     * Count soft image references.
     */
    protected function countSoftImageReferences(int $fileUid): int
    {
        return $this->getDataService()
            ->count(
                'sys_refindex',
                [
                    'softref_key' => 'rtehtmlarea_images',
                    'ref_table' => 'sys_file',
                    'ref_uid' => $fileUid
                ]
            );
    }

    /**
     * Count link image references.
     */
    protected function countSoftLinkReferences(int $fileUid): int
    {
        return $this->getDataService()
            ->count(
                'sys_refindex',
                [
                    'softref_key' => 'typolink_tag',
                    'ref_table' => 'sys_file',
                    'ref_uid' => $fileUid
                ]
            );
    }

    protected function countFileIndexReferences(int $fileUid): int
    {
        $queryBuilder = $this->getQueryBuilder('sys_refindex');
        return (int)$queryBuilder
            ->count('*')
            ->from('sys_refindex')
            ->where(
                $queryBuilder->expr()->eq(
                    'ref_table',
                    $queryBuilder->createNamedParameter('sys_file')
                ),
                $queryBuilder->expr()->eq(
                    'ref_uid',
                    $queryBuilder->createNamedParameter($fileUid, Connection::PARAM_INT)
                ),
                $queryBuilder->expr()->neq(
                    'tablename',
                    $queryBuilder->createNamedParameter('sys_file_metadata')
                )
            )
            ->executeQuery()
            ->fetchOne();
    }

    protected function getQueryBuilder(string $tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($tableName);
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }
}
