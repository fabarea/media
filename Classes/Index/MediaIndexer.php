<?php
namespace Fab\Media\Index;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\ExtractorRegistry;
use TYPO3\CMS\Core\Resource\Index\FileIndexRepository;
use TYPO3\CMS\Core\Resource\Index\MetaDataRepository;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Media\Utility\ConfigurationUtility;

/**
 * Service dealing with Indexing in the context of Media.
 */
class MediaIndexer
{

    /**
     * @var ResourceStorage
     */
    protected $storage = null;

    /**
     * @param ResourceStorage $storage
     */
    public function __construct(ResourceStorage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @return $this
     */
    public function updateIndex(File $file)
    {
        $this->getCoreIndexer()->updateIndexEntry($file);
        return $this;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @return $this
     */
    public function extractMetadata(File $file)
    {

        $extractionServices = $this->getExtractorRegistry()->getExtractorsWithDriverSupport($this->storage->getDriverType());

        $newMetaData = array(
            0 => $file->_getMetaData()
        );
        foreach ($extractionServices as $service) {
            if ($service->canProcess($file)) {
                $newMetaData[$service->getPriority()] = $service->extractMetaData($file, $newMetaData);
            }
        }

        ksort($newMetaData);
        $metaData = [];
        foreach ($newMetaData as $data) {
            $metaData = array_merge($metaData, $data);
        }
        $file->_updateMetaDataProperties($metaData);
        $this->getMetaDataRepository()->update($file->getUid(), $metaData);
        $this->getFileIndexRepository()->updateIndexingTime($file->getUid());

        return $this;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @return $this
     */
    public function applyDefaultCategories(File $file)
    {

        $categoryList = ConfigurationUtility::getInstance()->get('default_categories');
        $categories = GeneralUtility::trimExplode(',', $categoryList, true);

        foreach ($categories as $category) {
            $values = array(
                'uid_local' => $category,
                'uid_foreign' => $this->getFileMetadataIdentifier($file),
                'tablenames' => 'sys_file_metadata',
                'fieldname' => 'categories',
            );
            $this->getDatabaseConnection()->exec_INSERTquery('sys_category_record_mm', $values);
        }

        $metaData['categories'] = count($categories);
        $file->_updateMetaDataProperties($metaData);
        $this->getMetaDataRepository()->update($file->getUid(), $metaData);
        $this->getFileIndexRepository()->updateIndexingTime($file->getUid());
        return $this;
    }

    /**
     * Retrieve the file metadata uid which is different from the file uid.
     *
     * @param \TYPO3\CMS\Core\Resource\File $file
     * @return int
     */
    protected function getFileMetadataIdentifier(File $file)
    {
        $metadataProperties = $file->_getMetaData();
        return isset($metadataProperties['_ORIG_uid']) ? (int)$metadataProperties['_ORIG_uid'] : (int)$metadataProperties['uid'];
    }


    /**
     * Returns an instance of the FileIndexRepository
     *
     * @return FileIndexRepository
     */
    protected function getFileIndexRepository()
    {
        return FileIndexRepository::getInstance();
    }

    /**
     * Returns an instance of the FileIndexRepository
     *
     * @return MetaDataRepository
     */
    protected function getMetaDataRepository()
    {
        return MetaDataRepository::getInstance();
    }

    /**
     * Returns an instance of the FileIndexRepository
     *
     * @return ExtractorRegistry
     */
    protected function getExtractorRegistry()
    {
        return ExtractorRegistry::getInstance();
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

    /**
     * @return \TYPO3\CMS\Core\Resource\Index\Indexer
     */
    protected function getCoreIndexer()
    {
        return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\Index\Indexer', $this->storage);
    }

}
