<?php

namespace Fab\Media\Command;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Cache\CacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class FileCacheWarmUpCommand extends Command
{
    protected SymfonyStyle $io;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * Warm up the cache. Update some caching columns such as "number_of_references" to speed up the search.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $numberOfEntries = $this->getCacheService()->warmUp();
        $message = sprintf('Done! Processed %s entries', $numberOfEntries);
        $this->io->info($message);

        return 0;
    }

    protected function getCacheService(): CacheService
    {
        return GeneralUtility::makeInstance(CacheService::class);
    }
}
