<?php
namespace Fab\Media\View\Info;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\Utility\ListUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a text telling what folder is selected.
 */
class SelectedFolderInfo extends AbstractComponentView
{

    /**
     * @var array
     */
    public $notAllowedMountPoints = [];

    /**
     * Renders a button for uploading assets.
     *
     * @return string
     */
    public function render()
    {

        $result = '';
        if ($this->getMediaModule()->hasFolderTree()) {

            $folder = $this->getMediaModule()->getCurrentFolder();
            $result = sprintf('<h1>%s</h1>', $this->getFolderName($folder));
        }

        return $result;
    }

    /**
     * Get main headline based on active folder or storage for backend module
     *
     * Folder names are resolved to their special names like done in the tree view.
     *
     * @param Folder $folder
     * @return string
     */
    protected function getFolderName(Folder $folder)
    {
        $name = $folder->getName();
        if ($name === '') {
            // Show storage name on storage root
            if ($folder->getIdentifier() === '/') {
                $name = $folder->getStorage()->getName();
            }
        } else {
            $name = key(ListUtility::resolveSpecialFolderNames(
                array($name => $folder)
            ));
        }
        return $name;
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

}
