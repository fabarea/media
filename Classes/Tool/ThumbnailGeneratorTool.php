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
 * Thumbnail generator tool for the Media module.
 */
class ThumbnailGeneratorTool extends AbstractTool
{

    /**
     * Display the title of the tool on the welcome screen.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Generate thumbnails';
    }

    /**
     * Display the description of the tool in the welcome screen.
     *
     * @return string
     */
    public function getDescription()
    {
        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/ThumbnailGenerator/Launcher.html';
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

        $reports = [];

        $limit = 500; // default value
        $newOffset = 0;

        // Possible clean up of missing files if the User has clicked so.
        if (isset($arguments['limit']) && isset($arguments['offset'])) {

            $limit = (int)$arguments['limit'];
            $offset = (int)$arguments['offset'];

            foreach ($this->getStorageRepository()->findAll() as $storage) {

                if ($storage->isOnline()) {

                    $thumbnailGenerator = $this->getThumbnailGenerator();
                    $thumbnailGenerator
                        ->setStorage($storage)
                        ->generate($limit, $offset);

                    $formattedResultSet = [];
                    $resultSet = $thumbnailGenerator->getResultSet();
                    $processedFileIdentifiers = $thumbnailGenerator->getNewProcessedFileIdentifiers();

                    foreach ($processedFileIdentifiers as $fileIdentifier => $processedFileIdentifier) {
                        $result = $resultSet[$fileIdentifier];
                        $formattedResultSet[] = sprintf('* File "%s": %s %s',
                            $result['fileUid'],
                            $result['fileIdentifier'],
                            empty($result['thumbnailUri']) ? '' : ' -> ' . $result['thumbnailUri']
                        );
                    }

                    $reports[] = array(
                        'storage' => $storage,
                        'isStorageOnline' => true,
                        'resultSet' => $formattedResultSet,
                        'numberOfProcessedFiles' => $thumbnailGenerator->getNumberOfProcessedFiles(),
                        'numberOfTraversedFiles' => $thumbnailGenerator->getNumberOfTraversedFiles(),
                        'numberOfMissingFiles' => $thumbnailGenerator->getNumberOfMissingFiles(),
                        'totalNumberOfFiles' => $thumbnailGenerator->getTotalNumberOfFiles(),
                    );
                } else {
                    $reports[] = array(
                        'storage' => $storage,
                        'isStorageOnline' => false,
                    );
                }
            }

            $newOffset = $limit + $offset;
        }

        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/ThumbnailGenerator/WorkResult.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);

        $view->assign('limit', $limit);
        $view->assign('offset', $newOffset);
        $view->assign('reports', $reports);
        return $view->render();
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

