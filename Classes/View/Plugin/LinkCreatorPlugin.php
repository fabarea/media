<?php
namespace Fab\Media\View\Plugin;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Media\Utility\Path;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View which renders content for link creator plugin.
 */
class LinkCreatorPlugin extends AbstractComponentView
{

    /**
     * Renders a hidden link for link creator.
     *
     * @return string
     */
    public function render()
    {
        $result = '';
        if ($this->getModuleLoader()->hasPlugin('linkCreator')) {

            // Load Require JS code
            $this->loadRequireJsCode();

            $result = sprintf('<a href="%s" id="btn-linkCreator-current" class="btn btn-linkCreator" style="display: none"></a>',
                $this->getLinkCreatorUri()
            );
        };
        return $result;
    }

    /**
     * @return string
     */
    protected function getLinkCreatorUri()
    {
        $urlParameters = array(
            MediaModule::getParameterPrefix() => array(
                'controller' => 'LinkCreator',
                'action' => 'show',
            ),
        );
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }

    /**
     * @return void
     */
    protected function loadRequireJsCode()
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);

        $configuration['paths']['Fab/Media'] = '../typo3conf/ext/media/Resources/Public/JavaScript';
        $pageRenderer->addRequireJsConfiguration($configuration);
        $pageRenderer->loadRequireJsModule('Fab/Media/PluginLinkCreator');
    }

}
