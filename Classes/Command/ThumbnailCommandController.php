<?php
namespace Fab\Media\Command;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\CommandController;

/**
 * Command Controller which handles CLI action related to Thumbnails.
 */
class ThumbnailCommandController extends CommandController
{

    /**
     * Generate a bunch of thumbnails in advance to speed up the output of the Media BE module.
     *
     * @param int $limit where to stop in the batch processing.
     * @param int $offset where to start in the batch processing.
     * @param array $configuration override the default thumbnail configuration.
     * @param bool $verbose will output a detail result of the thumbnail generation.
     * @return void
     */
    public function generateCommand($limit = 0, $offset = 0, $configuration = array(), $verbose = FALSE)
    {

        $this->checkEnvironment();

        foreach ($this->getStorageRepository()->findAll() as $storage) {

            // TODO: Make me more flexible by passing thumbnail configuration. For now it will only generate thumbnails for the BE module.

            $this->outputLine();
            $this->outputLine(sprintf('%s (%s)', $storage->getName(), $storage->getUid()));
            $this->outputLine('--------------------------------------------');
            $this->outputLine();

            if ($storage->isOnline()) {

                $thumbnailGenerator = $this->getThumbnailGenerator();
                $thumbnailGenerator
                    ->setStorage($storage)
                    ->setConfiguration($configuration)
                    ->generate($limit, $offset);

                if ($verbose) {
                    $resultSet = $thumbnailGenerator->getResultSet();
                    foreach ($resultSet as $result) {
                        $message = sprintf('* File "%s": %s %s',
                            $result['fileUid'],
                            $result['fileIdentifier'],
                            empty($result['thumbnailUri']) ? '' : ' -> ' . $result['thumbnailUri']
                        );
                        $this->outputLine($message);
                    }
                    $this->outputLine();
                }

                $message = sprintf('Done! New generated %s thumbnail(s) from %s traversed file(s) of a total of %s files.',
                    $thumbnailGenerator->getNumberOfProcessedFiles(),
                    $thumbnailGenerator->getNumberOfTraversedFiles(),
                    $thumbnailGenerator->getTotalNumberOfFiles()
                );
                $this->outputLine($message);

                // Add warning message if missing files were found along the way.
                if ($thumbnailGenerator->getNumberOfMissingFiles() > 0) {

                    $message = sprintf('ATTENTION! %s missing file(s) detected.',
                        $thumbnailGenerator->getNumberOfMissingFiles()
                    );
                    $this->outputLine($message);
                }
            } else {
                $this->outputLine('Storage is offline!');
            }
        }
    }

    /**
     * @return void
     */
    protected function checkEnvironment()
    {
        $user = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'be_users', 'username = "_cli_lowlevel" AND password != ""');

        if (empty($user)) {
            $this->outputLine('Missing User "_cli_lowlevel" and / or its password.');
            $this->sendAndExit(1);
        }
        $user = $this->getDatabaseConnection()->exec_SELECTgetSingleRow('*', 'be_users', 'username = "_cli_scheduler" AND password != ""');

        if (empty($user)) {
            $this->outputLine('Missing User "_cli_scheduler" and / or its password.');
            $this->sendAndExit(1);
        }
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
}
