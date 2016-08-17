<?php
namespace Fab\Media\View\MenuItem;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use Fab\Vidi\View\AbstractComponentView;
use TYPO3\CMS\Core\Imaging\Icon;

/**
 * View which renders a "file picker" menu item to be placed in the grid menu of Media.
 */
class FilePickerMenuItem extends AbstractComponentView
{

    /**
     * Renders a "file picker" menu item to be placed in the grid menu of Media.
     *
     * @return string
     */
    public function render()
    {
        $result = '';
        if ($this->getModuleLoader()->hasPlugin('filePicker')) {
            $result = sprintf('<li><a href="%s" class="mass-file-picker" data-argument="assets">%s Insert files</a>',
                $this->getMassDeleteUri(),
                $this->getIconFactory()->getIcon('extensions-media-image-export', Icon::SIZE_SMALL)
            );
        }
        return $result;
    }

    /**
     * Render a mass delete URI.
     *
     * @return string
     */
    protected function getMassDeleteUri()
    {
        $urlParameters = array(
            MediaModule::getParameterPrefix() => array(
                'controller' => 'Asset',
                'action' => '',
                'format' => 'json',
            ),
        );
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }

}
