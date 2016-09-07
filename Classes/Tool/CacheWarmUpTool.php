<?php
namespace Fab\Media\Tool;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tool\AbstractTool;

/**
 * Cache warm up tool for the Media module.
 */
class CacheWarmUpTool extends AbstractTool
{

    /**
     * Display the title of the tool on the welcome screen.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Cache warm up';
    }

    /**
     * Display the description of the tool in the welcome screen.
     *
     * @return string
     */
    public function getDescription()
    {
        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/CacheWarmUp/Launcher.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);
        $view->assign('sitePath', PATH_site);
        return $view->render();
    }

    /**
     * Do the job: warm up the cache.
     *
     * @param array $arguments
     * @return string
     */
    public function work(array $arguments = [])
    {

        $templateNameAndPath = 'EXT:media/Resources/Private/Standalone/Tool/CacheWarmUp/WorkResult.html';
        $view = $this->initializeStandaloneView($templateNameAndPath);

        $numberOfEntries = $this->getCacheService()->warmUp();
        $view->assign('numberOfEntries', $numberOfEntries);
        touch($this->getWarmUpSemaphorFile());

        return $view->render();
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
     * @return string
     */
    protected function getWarmUpSemaphorFile()
    {
        return PATH_site . 'typo3temp/.media_cache_warmed_up';
    }

    /**
     * @return \Fab\Media\Cache\CacheService
     */
    protected function getCacheService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Cache\CacheService');
    }

}

