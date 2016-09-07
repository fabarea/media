<?php
namespace Fab\Media\Tool;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tool\AbstractTool;

/**
 * Search for duplicate files having the same "sha1" and process them.
 */
class DuplicateFilesFinderTool extends AbstractTool
{

    /**
     * Display the title of the tool on the welcome screen.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Find duplicate Files';
    }

    /**
     * Display the description of the tool in the welcome screen.
     *
     * @return string
     */
    public function getDescription()
    {
        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/DuplicateFilesFinder/Launcher.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);
        $view->assign('isAdmin', $this->getBackendUser()->isAdmin());
        $view->assign('sitePath', PATH_site);
        return $view->render();
    }

    /**
     * Do the job: analyse Index.
     *
     * @param array $arguments
     * @return string
     */
    public function work(array $arguments = [])
    {

        // Possible clean up of missing files if the User has clicked so.
        if (!empty($arguments['deleteDuplicateFiles'])) {
            $this->deleteMissingFilesAction($arguments['files']);
        }

        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/DuplicateFilesFinder/WorkResult.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);

        $duplicateFilesReports = [];

        if ($this->getBackendUser()->isAdmin()) {
            foreach ($this->getStorageRepository()->findAll() as $storage) {
                if ($storage->isOnline()) {
                    $duplicateFiles = $this->getIndexAnalyser()->searchForDuplicateSha1($storage);
                    $duplicateFilesReports[] = array(
                        'storage' => $storage,
                        'duplicateFiles' => $duplicateFiles,
                        'numberOfDuplicateFiles' => count($duplicateFiles),
                    );
                }
            }
        } else {

            $fileMounts = $this->getBackendUser()->getFileMountRecords();

            $allowedStorages = [];
            foreach ($fileMounts as $fileMount) {
                if ((bool)$fileMount['read_only']) {
                    continue;
                }

                if (!isset($allowedStorages[$fileMount['base']])) {
                    $allowedStorages[$fileMount['base']] = [];
                }
                if (!in_array($fileMount['base'], $allowedStorages)) {
                    $allowedStorages[$fileMount['base']][] = $fileMount['path'];
                }
            }

            foreach ($allowedStorages as $storageIdentifier => $allowedMountPoints) {
                $storage = ResourceFactory::getInstance()->getStorageObject($storageIdentifier);

                if ($storage->isOnline()) {

                    $duplicateFiles = $this->getIndexAnalyser()->searchForDuplicateSha1($storage);

                    // Filter duplicates files
                    foreach ($duplicateFiles as $key => $files) {

                        $filteredFiles = [];
                        foreach ($files as $file) {

                            foreach ($allowedMountPoints as $allowedMountPoint) {

                                $pattern = '%^' . $allowedMountPoint . '%isU';
                                if (preg_match($pattern, $file['identifier'])) {
                                    $filteredFiles[] = $file;
                                    break; // no need to further loop around, stop the loop.
                                }
                            }
                        }

                        // We need more than 1 files to be shown as duplicate.
                        if (count($filteredFiles) > 1) {
                            $duplicateFiles[$key] = $filteredFiles;
                        } else {
                            unset($duplicateFiles[$key]);
                        }
                    }
                    $duplicateFilesReports[] = array(
                        'storage' => $storage,
                        'duplicateFiles' => $duplicateFiles,
                        'numberOfDuplicateFiles' => count($duplicateFiles),
                    );

                }
            }
        }

        $view->assign('duplicateFilesReports', $duplicateFilesReports);

        return $view->render();
    }

    /**
     * Delete files given as parameter.
     * This is a special case as we have a missing file in the file system
     * As a result, we can't use $fileObject->delete(); which will
     * raise exception "Error while fetching permissions".
     *
     * @param array $files
     * @return void
     */
    protected function deleteMissingFilesAction(array $files = [])
    {

        foreach ($files as $fileUid) {

            /** @var \TYPO3\CMS\Core\Resource\File $file */
            try {
                $file = ResourceFactory::getInstance()->getFileObject($fileUid);
                if ($file->exists()) {

                    $numberOfReferences = $this->getFileReferenceService()->countTotalReferences($file);
                    if ($numberOfReferences === 0) {
                        $file->delete();
                    }
                } else {
                    $this->getDatabaseConnection()->exec_DELETEquery('sys_file', 'uid = ' . $file->getUid());
                }
            } catch (\Exception $e) {
                continue;
            }
        }
    }

    /**
     * Return a pointer to the database.
     *
     * @return \Fab\Media\Index\IndexAnalyser
     */
    protected function getIndexAnalyser()
    {
        return GeneralUtility::makeInstance('Fab\Media\Index\IndexAnalyser');
    }

    /**
     * @return \Fab\Media\Thumbnail\ThumbnailGenerator
     */
    protected function getThumbnailGenerator()
    {
        return GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailGenerator');
    }

    /**
     * @return StorageRepository
     */
    protected function getStorageRepository()
    {
        return GeneralUtility::makeInstance('TYPO3\CMS\Core\Resource\StorageRepository');
    }

    /**
     * Tell whether the tools should be displayed according to the context.
     *
     * @return bool
     */
    public function isShown()
    {
        return true;
    }

    /**
     * @return \Fab\Media\Resource\FileReferenceService
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
    }

    /**
     * Return a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

}

