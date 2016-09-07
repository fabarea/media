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
class DuplicateRecordsFinderTool extends AbstractTool
{

    /**
     * Display the title of the tool on the welcome screen.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Find duplicate Records';
    }

    /**
     * Display the description of the tool in the welcome screen.
     *
     * @return string
     */
    public function getDescription()
    {
        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/DuplicateRecordsFinder/Launcher.html';
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

        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/DuplicateRecordsFinder/WorkResult.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);

        $duplicateRecordsReports = [];
        foreach ($this->getStorageRepository()->findAll() as $storage) {

            if ($storage->isOnline()) {
                $duplicateFiles = $this->getIndexAnalyser()->searchForDuplicateIdentifiers($storage);
                $duplicateRecordsReports[] = array(
                    'storage' => $storage,
                    'duplicateFiles' => $duplicateFiles,
                    'numberOfDuplicateFiles' => count($duplicateFiles),
                );
            }
        }

        $view->assign('duplicateRecordsReports', $duplicateRecordsReports);

        return $view->render();
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

