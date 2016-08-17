<?php
namespace Fab\Media\View\Plugin;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\View\AbstractComponentView;
use Fab\Media\Utility\Path;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View which renders content for file picker plugin.
 */
class FilePickerPlugin extends AbstractComponentView
{

    /**
     * Renders a hidden link for file picker.
     *
     * @return string
     */
    public function render()
    {

        if ($this->getModuleLoader()->hasPlugin('filePicker')) {
            $this->loadRequireJsCode();
        };
        return '';
    }

    /**
     * @return void
     */
    protected function loadRequireJsCode()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        $configuration['paths']['Fab/Media'] = '../typo3conf/ext/media/Resources/Public/JavaScript';
        $pageRenderer->addRequireJsConfiguration($configuration);
        $pageRenderer->loadRequireJsModule('Fab/Media/PluginFilePicker');
    }

}
