<?php

namespace Fab\Media\Command;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Thumbnail\ThumbnailGenerator;
use Fab\Vidi\Service\DataService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Resource\StorageRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ThumbnailCommand extends Command
{
    protected SymfonyStyle $io;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function configure()
    {
        $this
            ->addOption(
                'limit',
                '',
                InputOption::VALUE_REQUIRED,
                'where to stop in the batch processing.',
                0
            )
            ->addOption(
                'offset',
                '',
                InputOption::VALUE_REQUIRED,
                'where to start in the batch processing.',
                0
            )
            ->addOption(
                'configuration',
                '',
                InputOption::VALUE_REQUIRED,
                'override the default thumbnail configuration.',
                []
            );
    }

    /**
     * Generate a bunch of thumbnails in advance to speed up the output of the Media BE module.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $limit = $input->getOption('limit');
        $offset = $input->getOption('offset');
        $configuration = $input->getOption('configuration');
        $verbose = $input->getOption('verbose');

        foreach ($this->getStorageRepository()->findAll() as $storage) {
            $this->io->info(sprintf('Processing files from storage %s (%s)', $storage->getName(), $storage->getUid()));

            if ($storage->isOnline()) {
                // For the CLI cause.
                $storage->setEvaluatePermissions(false);

                $thumbnailGenerator = $this->getThumbnailGenerator();
                $thumbnailGenerator
                    ->setStorage($storage)
                    ->setConfiguration($configuration)
                    ->generate($limit, $offset);

                if ($verbose) {
                    $resultSet = $thumbnailGenerator->getResultSet();
                    foreach ($resultSet as $result) {
                        $message = sprintf(
                            '* File "%s": %s %s',
                            $result['fileUid'],
                            $result['fileIdentifier'],
                            empty($result['thumbnailUri']) ? '' : ' -> ' . $result['thumbnailUri']
                        );
                        $this->io->info($message);
                    }
                }

                $message = sprintf(
                    'Done! New generated %s thumbnail(s) from %s traversed file(s) of a total of %s files.',
                    $thumbnailGenerator->getNumberOfProcessedFiles(),
                    $thumbnailGenerator->getNumberOfTraversedFiles(),
                    $thumbnailGenerator->getTotalNumberOfFiles()
                );
                $this->io->info($message);

                // Add warning message if missing files were found along the way.
                if ($thumbnailGenerator->getNumberOfMissingFiles() > 0) {
                    $message = sprintf(
                        'ATTENTION! %s missing file(s) detected.',
                        $thumbnailGenerator->getNumberOfMissingFiles()
                    );
                    $this->io->warning($message);
                }
            } else {
                $this->io->info('Storage is offline!');
            }
        }
        return 0;
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    protected function getThumbnailGenerator(): ThumbnailGenerator
    {
        return GeneralUtility::makeInstance(ThumbnailGenerator::class);
    }

    protected function getStorageRepository(): StorageRepository
    {
        return GeneralUtility::makeInstance(StorageRepository::class);
    }
}
