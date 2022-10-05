<?php

namespace Fab\Media\Cache;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\ProcessedFileRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use Fab\Media\Resource\FileReferenceService;
use Fab\Vidi\Service\DataService;
use Fab\Vidi\Utility\BackendUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Service dealing with cache related to a File.
 */
class CacheService
{
    /**
     * Traverse all files and initialize cache values.
     */
    public function warmUp(): int
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder('sys_file');
        $rows = $queryBuilder
            ->select('*')
            ->from('sys_file')
            ->where('storage > 0')
            ->execute()
            ->fetchAllAssociative();

        $counter = 0;
        foreach ($rows as $row) {
            $fileIdentifier = $row['uid'];
            $totalNumberOfReferences = $this->getFileReferenceService()->countTotalReferences($fileIdentifier);

            $this->getDataService()->update(
                'sys_file',
                [
                    'number_of_references' => $totalNumberOfReferences
                ],
                [
                    'uid' => $fileIdentifier
                ]
            );
            $counter++;
        }

        return $counter;
    }

    /**
     * Clear all possible cache related to a file.
     * This method is useful when replacing a file for instance.
     *
     * @param File $file
     * @return void
     */
    public function clearCache(File $file)
    {
        $this->clearCachePages($file);
        $this->flushProcessedFiles($file);
    }

    /**
     * Remove all processed files that belong to the given File object.
     *
     * @param File $file
     * @return void
     */
    protected function flushProcessedFiles(File $file)
    {
        /** @var $processedFile \TYPO3\CMS\Core\Resource\ProcessedFile */
        foreach ($this->getProcessedFileRepository()->findAllByOriginalFile($file) as $processedFile) {
            if ($processedFile->exists()) {
                $processedFile->delete(true);
            }

            $this->getDataService()->delete(
                'sys_file_processedfile',
                [
                    'uid' => (int)$processedFile->getUid()
                ]
            );
        }
    }

    /**
     * Return a processed file repository
     *
     * @return ProcessedFileRepository|object
     */
    protected function getProcessedFileRepository()
    {
        return GeneralUtility::makeInstance(ProcessedFileRepository::class);
    }

    /**
     * Returns the file references.
     *
     * @param File $file
     * @return void
     */
    protected function clearCachePages($file)
    {
        /** @var $tce \TYPO3\CMS\Core\DataHandling\DataHandler */
        $tce = GeneralUtility::makeInstance(DataHandler::class);
        $tce->start([], []);

        #$pages = array_merge(
        #    $this->findPagesWithFileReferences($file),
        #    $this->findPagesWithSoftReferences($file)
        #);

        // Previous code which does not work in TYPO3 CMS 7 LTS.
        // It is adviced to use "registerPageCacheClearing" but how?
        #foreach (array_unique($pages) as $page) {
        #    $tce->clear_cache('pages', $page);
        #}
        $tce->clear_cacheCmd('pages');
    }

    /**
     * Find all pages which contains file references to the given $file.
     *
     * @param File $file
     * @return array
     */
    protected function findPagesWithFileReferences($file)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder('sys_file_reference');
        $rows = $queryBuilder
            ->select('pid')
            ->from('sys_file_reference')
            ->groupBy('pid') // no support for distinct
            ->andWhere(
                'pid > 0',
                'uid_local = ' . $file->getUid()
            )
            ->execute()
            ->fetchAllAssociative();

        foreach ($rows as $row) {
            $pages[] = $row['pid'];
        }

        return $pages;
    }

    /**
     * Find all pages which have soft references to the given $file.
     *
     * @param File $file
     * @return array
     */
    #protected function findPagesWithSoftReferences(File $file)
    #{
    #    $subClauseParts = array(
    #        'deleted = 0',
    #        '(softref_key = "rtehtmlarea_images" OR softref_key = "typolink_tag")',
    #        'ref_table = "sys_file"',
    #        'tablename = "tt_content"',
    #        'ref_uid = ' . $file->getUid(),
    #    );
    #
    #    $rows = $this->getDatabaseConnection()->exec_SELECTquery(
    #        'DISTINCT pid',
    #        'tt_content',
    #        sprintf('uid IN (SELECT recuid FROM sys_refindex WHERE %s) %s',
    #            implode(' AND ', $subClauseParts),
    #            $this->getWhereClauseForEnabledFields('tt_content')
    #        )
    #    );
    #
    #    // Compute result
    #    $pages = [];
    #    while ($affectedPage = $this->getDatabaseConnection()->sql_fetch_assoc($rows)) {
    #        $pages[] = $affectedPage['pid'];
    #    }
    #
    #    return $pages;
    #}

    /**
     * Get the WHERE clause for the enabled fields given a $tableName.
     *
     * @param string $tableName
     * @return string
     */
    protected function getWhereClauseForEnabledFields($tableName)
    {
        if ($this->isFrontendMode()) {
            // frontend context
            $whereClause = $this->getPageRepository()->deleteClause($tableName);
        } else {
            // backend context
            $whereClause = BackendUtility::deleteClause($tableName);
        }
        return $whereClause;
    }

    /**
     * Returns whether the current mode is Frontend
     *
     * @return string
     */
    protected function isFrontendMode()
    {
        return ApplicationType::fromRequest($GLOBALS['TYPO3_REQUEST'])->isFrontend();
    }

    /**
     * Returns an instance of the page repository.
     *
     * @return \TYPO3\CMS\Frontend\Page\PageRepository
     */
    protected function getPageRepository()
    {
        return $GLOBALS['TSFE']->sys_page;
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

    /**
     * @return FileReferenceService|object
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance(FileReferenceService::class);
    }
}
