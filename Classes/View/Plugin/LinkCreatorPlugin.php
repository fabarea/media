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
            $result = sprintf('<script type="text/javascript" src="%s"></script>
			<a href="%s" id="btn-linkCreator-current" class="btn btn-linkCreator" style="display: none"></a>',
                Path::getRelativePath('JavaScript/Media.Plugin.LinkCreator.js'),
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
}
