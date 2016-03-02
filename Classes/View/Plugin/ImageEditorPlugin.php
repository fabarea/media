<?php
namespace Fab\Media\View\Plugin;

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

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Media\Utility\Path;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * View which renders content for image editor plugin.
 */
class ImageEditorPlugin extends AbstractComponentView
{

    /**
     * Renders a hidden link for image editor.
     *
     * @return string
     */
    public function render()
    {

        $result = '';
        if ($this->getModuleLoader()->hasPlugin('imageEditor')) {

            // Load Require JS code
            $this->loadRequireJsCode();

            $result = sprintf('<a href="%s" id="btn-imageEditor-current" class="btn btn-imageEditor" style="display: none"></a>',
                $this->getImageEditorUri()
            );
        };
        return $result;
    }

    /**
     * @return string
     */
    protected function getImageEditorUri()
    {
        $urlParameters = array(
            MediaModule::getParameterPrefix() => array(
                'controller' => 'ImageEditor',
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
        $pageRenderer->loadRequireJsModule('Fab/Media/PluginImageEditor');
    }

}
