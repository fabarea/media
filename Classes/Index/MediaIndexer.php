<?php

namespace Fab\Media\Index;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Resource\Index\Indexer;
use Fab\Vidi\Service\DataService;
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
     * @param File $file
     * @return $this
     */
    public function updateIndex(File $file)
    {
        $this->getCoreIndexer()->updateIndexEntry($file);
        return $this;
    }

    /**
     * @param File $file
     * @return $this
     */
    public function extractMetadata(File $file)
    {
        $extractionServices = $this->getExtractorRegistry()->getExtractorsWithDriverSupport($this->storage->getDriverType());

        $newMetaData = array(
            0 => $file->getMetaData()->get()
        );

        foreach ($extractionServices as $services) {
            if (is_array($services)) {
                foreach ($services as $service) {
                    if ($service->canProcess($file)) {
                        $newMetaData[$service->getPriority()] = $service->extractMetaData($file, $newMetaData);
                    }
                }
            } else {
                $service = $services;
                // We could optimise here for not repeating this bit
                if ($service->canProcess($file)) {
                    $newMetaData[$service->getPriority()] = $service->extractMetaData($file, $newMetaData);
                }
            }
        }

        ksort($newMetaData);
        $metaData = [];
        foreach ($newMetaData as $data) {
            $metaData = array_merge($metaData, $data);
        }
        $file->updateProperties($metaData);
        $this->getMetaDataRepository()->update($file->getUid(), $metaData);
        $this->getFileIndexRepository()->updateIndexingTime($file->getUid());

        return $this;
    }

    /**
     * @param File $file
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
            $this->getDataService()->insert('sys_category_record_mm', $values);
        }

        $metaData['categories'] = count($categories);
        $file->updateProperties($metaData);
        $this->getMetaDataRepository()->update($file->getUid(), $metaData);
        $this->getFileIndexRepository()->updateIndexingTime($file->getUid());
        return $this;
    }

    /**
     * Retrieve the file metadata uid which is different from the file uid.
     *
     * @param File $file
     * @return int
     */
    protected function getFileMetadataIdentifier(File $file)
    {
        $metadataProperties = $file->getMetaData()->get();
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
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    /**
     * @return Indexer|object
     */
    protected function getCoreIndexer()
    {
        return GeneralUtility::makeInstance(Indexer::class, $this->storage);
    }
}
