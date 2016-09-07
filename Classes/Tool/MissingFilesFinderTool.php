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
 * Index analyser tool for the Media module.
 */
class MissingFilesFinderTool extends AbstractTool
{

    /**
     * Display the title of the tool on the welcome screen.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Find missing files';
    }

    /**
     * Display the description of the tool in the welcome screen.
     *
     * @return string
     */
    public function getDescription()
    {
        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/MissingFilesFinder/Launcher.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);
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
        if (!empty($arguments['deleteMissingFiles'])) {
            $this->deleteMissingFilesAction($arguments['files']);
        }

        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/MissingFilesFinder/WorkResult.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);

        $missingReports = [];
        foreach ($this->getStorageRepository()->findAll() as $storage) {

            if ($storage->isOnline()) {
                $missingFiles = $this->getIndexAnalyser()->searchForMissingFiles($storage);

                $missingReports[] = array(
                    'storage' => $storage,
                    'missingFiles' => $missingFiles,
                    'numberOfMissingFiles' => count($missingFiles),
                );
            }
        }

        $view->assign('missingReports', $missingReports);

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
                if ($file) {
                    // The case is special as we have a missing file in the file system
                    // As a result, we can't use $fileObject->delete(); which will
                    // raise exception "Error while fetching permissions"
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
        return $this->getBackendUser()->isAdmin();
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

