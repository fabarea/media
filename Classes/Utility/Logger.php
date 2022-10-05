<?php

namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Log\LogManager;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * A class for handling logger
 */
class Logger implements SingletonInterface
{
    /**
     * Returns a logger class instance.
     *
     * @param mixed $instance
     * @return \TYPO3\CMS\Core\Log\Logger
     */
    public static function getInstance($instance)
    {
        /** @var $loggerManager \TYPO3\CMS\Core\Log\LogManager */
        $loggerManager = GeneralUtility::makeInstance(LogManager::class);

        /** @var $logger \TYPO3\CMS\Core\Log\Logger */
        return $loggerManager->getLogger(get_class($instance));
    }
}
