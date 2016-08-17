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
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a "move" menu item to be placed in the "change storage" menu.
 */
class ChangeStorageMenuItem extends AbstractComponentView
{

    /**
     * Renders a "change storage" menu item to be placed in the grid menu of Media.
     *
     * @return string
     */
    public function render()
    {
        $output = '';
        if (!$this->getMediaModule()->hasFolderTree()) {


            $output = sprintf('<li><a href="%s" class="change-storage" >%s %s</a>',
                $this->getChangeStorageUri(),
                $this->getIconFactory()->getIcon('extensions-media-storage-change', Icon::SIZE_SMALL),
                $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:change_storage')
            );

        }
        return $output;
    }

    /**
     * @return string
     */
    protected function getChangeStorageUri()
    {
        $urlParameters = array(
            MediaModule::getParameterPrefix() => array(
                'controller' => 'Asset',
                'action' => 'editStorage',
            ),
        );
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }

}
