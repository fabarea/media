<?php
namespace Fab\Media\Hook;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\Exception\FileDoesNotExistException;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

/**
 * Hook Functions for the 'DataHandler'.
 */
class DataHandlerHook
{

    /**
     * Store indexed file before the Data Handler start "working".
     *
     * @var array
     */
    protected $beforeDataHandlerProcessFileIdentifiers = [];

    /**
     * Store indexed file after the Data Handler has done its job.
     *
     * @var array
     */
    protected $afterDataHandlerProcessFileIdentifiers = [];

    /**
     * Internal key for the Cache Manager.
     *
     * @var string
     */
    protected $registerKey = 'media-hook-elementsToKeepTrack';

    /**
     * First procedures to launch before all operations in DataHandler.
     *
     * Feed variable $this->beforeDataHandlerProcessFileIdentifiers
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $caller TCEMain Object
     * @return void
     */
    public function processDatamap_beforeStart(DataHandler $caller)
    {

        // Use a register to keep track of files.
        // It is required according to TCEMain behaviour which register "elements to be deleted".
        // Those element must not be forgotten.
        $this->initializeFileRegister();
        $this->registerFilesToKeepTrack();

        foreach ($caller->datamap as $tableName => $configuration) {

            $id = key($configuration);
            if (!MathUtility::canBeInterpretedAsInteger($id)) {
                continue;
            }

            /** @var $refIndexObj \TYPO3\CMS\Core\Database\ReferenceIndex */
            $refIndexObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Database\\ReferenceIndex');
            if (BackendUtility::isTableWorkspaceEnabled($tableName)) {
                $refIndexObj->setWorkspaceId($caller->BE_USER->workspace);
            }
            $indexes = $refIndexObj->updateRefIndexTable($tableName, $id);

            // Make sure $index is an array.
            if (!is_array($indexes)) {
                $indexes = [];
            }

            $fileIdentifiers = $this->lookForFiles($indexes);
            $this->addBeforeDataHandlerProcessFileIdentifiers($fileIdentifiers);
        }
    }

    /**
     * Last procedures to launch after all operations in DataHandler.
     *
     * Process field "number_of_references" which may require updates.
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $caller TCEMain Object
     * @return void
     */
    public function processDatamap_afterAllOperations(DataHandler $caller)
    {

        // First collect files which have been involved.
        foreach ($caller->datamap as $tableName => $configuration) {

            $id = key($configuration);

            /** @var $refIndexObj \TYPO3\CMS\Core\Database\ReferenceIndex */
            $refIndexObj = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Database\\ReferenceIndex');
            if (BackendUtility::isTableWorkspaceEnabled($tableName)) {
                $refIndexObj->setWorkspaceId($caller->BE_USER->workspace);
            }
            $indexes = $refIndexObj->updateRefIndexTable($tableName, $id);

            // Make sure $index is an array.
            if (!is_array($indexes)) {
                $indexes = [];
            }

            $fileIdentifiers = $this->lookForFiles($indexes);
            $this->addAfterDataHandlerProcessFileIdentifiers($fileIdentifiers);
        }

        // After collecting files, update the column "number_of_references".
        foreach ($this->getFileToProcess() as $fileIdentifier) {
            try {
                $file = ResourceFactory::getInstance()->getFileObject($fileIdentifier);
                $numberOfReferences = $this->getFileReferenceService()->countTotalReferences($file);

                $values = array(
                    'number_of_references' => $numberOfReferences
                );
                $this->getDatabaseConnection()->exec_UPDATEquery('sys_file', 'uid = ' . $file->getUid(), $values);
            } catch (FileDoesNotExistException $fileDoesNotExistException) {
                // Do nothing here. A file that does not exist needs no update.
                // See https://github.com/fabarea/media/issues/159 for more information.
            }
        }
    }

    /**
     * @return void
     */
    protected function initializeFileRegister()
    {

        $items = $this->getMemoryCache()->get($this->registerKey);
        if (!is_array($items)) {
            $this->getMemoryCache()->set($this->registerKey, []);
        }
    }

    /**
     * @return void
     */
    protected function registerFilesToKeepTrack()
    {
        $fileIdentifiers = [];
        $elementsToBeDeleted = $this->getMemoryCache()->get('core-t3lib_TCEmain-elementsToBeDeleted');
        if (is_array($elementsToBeDeleted)) {
            foreach ($elementsToBeDeleted as $tableName => $element) {

                if ($tableName === 'sys_file_reference') {

                    $fileReferenceIdentifier = key($element);
                    if ($element[$fileReferenceIdentifier] === true) {
                        $fileIdentifier = $this->findFileByFileReference($fileReferenceIdentifier);
                        $fileIdentifiers[] = $fileIdentifier;
                    }
                }
            }
        }

        // Put back in the memory cache the value.
        $items = $this->getMemoryCache()->get($this->registerKey);
        $mergedItems = array_merge($items, $fileIdentifiers);
        $this->getMemoryCache()->set($this->registerKey, $mergedItems);
    }

    /**
     * @return array
     */
    protected function getRegisteredFiles()
    {
        $files = $this->getMemoryCache()->get($this->registerKey);
        return $files;
    }

    /**
     * Look for file which are within the reference index.
     *
     * @return array
     */
    protected function getFileToProcess()
    {
        $fileIdentifiers = array_merge(
            $this->beforeDataHandlerProcessFileIdentifiers,
            $this->afterDataHandlerProcessFileIdentifiers,
            $this->getRegisteredFiles()
        );
        return array_unique($fileIdentifiers);
    }

    /**
     * @param array $fileIdentifiers
     * @return void
     */
    protected function addBeforeDataHandlerProcessFileIdentifiers(array $fileIdentifiers)
    {
        $this->beforeDataHandlerProcessFileIdentifiers = array_merge($this->beforeDataHandlerProcessFileIdentifiers, $fileIdentifiers);
    }

    /**
     * @param array $fileIdentifiers
     * @return void
     */
    protected function addAfterDataHandlerProcessFileIdentifiers(array $fileIdentifiers)
    {
        $this->afterDataHandlerProcessFileIdentifiers = array_merge($this->afterDataHandlerProcessFileIdentifiers, $fileIdentifiers);
    }

    /**
     * Look for file which are within the reference index.
     *
     * @param array $indexes
     * @return array
     */
    protected function lookForFiles(array $indexes)
    {

        $fileIdentifiers = [];
        if (isset($indexes['relations'])) {

            foreach ($indexes['relations'] as $index) {
                if (is_array($index)) {
                    if ($this->isSoftReferenceImage($index)) {
                        $fileIdentifiers[] = $index['ref_uid'];
                    } elseif ($this->isSoftReferenceLink($index)) {
                        $fileIdentifiers[] = $index['ref_uid'];
                    } elseif ($this->isFileReference($index)) {
                        $fileIdentifiers[] = $this->findFileByFileReference($index['ref_uid']);
                    }
                }
            }
        }

        return $fileIdentifiers;
    }

    /**
     * @param array $index
     * @return bool
     */
    public function isFileReference(array $index)
    {
        return $index['ref_table'] === 'sys_file_reference';
    }

    /**
     * @param array $index
     * @return bool
     */
    public function isSoftReferenceLink(array $index)
    {
        return $index['softref_key'] === 'typolink_tag' && $index['ref_table'] === 'sys_file';
    }

    /**
     * @param array $index
     * @return bool
     */
    public function isSoftReferenceImage(array $index)
    {
        return $index['softref_key'] === 'rtehtmlarea_images' && $index['ref_table'] === 'sys_file';
    }

    /**
     * Retrieve the File identifier.
     *
     * @param $fileReferenceIdentifier
     * @throws \Exception
     * @return int
     */
    protected function findFileByFileReference($fileReferenceIdentifier)
    {
        $tableName = 'sys_file_reference';
        $clause = 'uid = ' . $fileReferenceIdentifier;
        #$clause .= BackendUtility::BEenableFields($tableName); // was removed following https://forge.typo3.org/issues/62370
        $clause .= BackendUtility::deleteClause($tableName);
        $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'sys_file_reference', $clause);

        if (empty($record)) {
            throw new \Exception('There is something broken with the File References. Consider updating the Reference Index.', 1408619796);
        }

        $fileIdentifier = $record['uid_local'];
        return $fileIdentifier;
    }

    /**
     * @return \Fab\Media\Resource\FileReferenceService
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
    }

    /**
     * Gets an instance of the memory cache.
     *
     * @return \TYPO3\CMS\Core\Cache\Frontend\VariableFrontend
     */
    protected function getMemoryCache()
    {
        return $this->getCacheManager()->getCache('cache_runtime');
    }

    /**
     * Create and returns an instance of the CacheManager
     *
     * @return \TYPO3\CMS\Core\Cache\CacheManager
     */
    protected function getCacheManager()
    {
        return GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager');
    }

    /**
     * Wrapper around the global database connection object.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }
}
