<?php

namespace Fab\Media\Resource;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Service\DataService;
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
     * @return array
     */
    public function findFileReferences($file)
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
     * @return array
     */
    public function findSoftImageReferences($file)
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

    /**
     * Return link image references.
     *
     * @param File|int $file
     * @return array
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
     * Count all references found in sys_file_reference.
     *
     * @param File|int $file
     * @return int
     */
    public function countFileReferences($file)
    {
        $fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

        return $this->getDataService()
            ->count(
                'sys_file_reference',
                [
                    'uid_local' => $fileIdentifier
                ]
            );
    }

    /**
     * Count soft image references.
     *
     * @param File|int $file
     * @return int
     */
    public function countSoftImageReferences($file)
    {
        $fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

        return $this->getDataService()
            ->count(
                'sys_refindex',
                [
                    'softref_key' => 'rtehtmlarea_images',
                    'ref_table' => 'sys_file',
                    'ref_uid' => $fileIdentifier
                ]
            );
    }

    /**
     * Count link image references.
     *
     * @param File|int $file
     * @return int
     */
    public function countSoftLinkReferences($file)
    {
        $fileIdentifier = $file instanceof File ? $file->getUid() : (int)$file;

        return $this->getDataService()
            ->count(
                'sys_refindex',
                [
                    'softref_key' => 'typolink_tag',
                    'ref_table' => 'sys_file',
                    'ref_uid' => $fileIdentifier
                ]
            );
    }

    /**
     * Count total reference.
     *
     * @param File|int $file
     * @return int
     */
    public function countTotalReferences($file)
    {
        $numberOfReferences = $this->countFileReferences($file);
        $numberOfReferences += $this->countSoftImageReferences($file);
        $numberOfReferences += $this->countSoftLinkReferences($file);

        return $numberOfReferences;
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

    /**
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }
}
