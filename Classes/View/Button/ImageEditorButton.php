<?php
namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a "image-editor" button to be placed in the grid.
 */
class ImageEditorButton extends AbstractComponentView
{

    /**
     * Renders a "image-editor" button to be placed in the grid.
     *
     * @param Content $object
     * @return string
     */
    public function render(Content $object = null)
    {
        $button = '';
        if ($this->getModuleLoader()->hasPlugin('imageEditor')) {
            $button = $this->makeLinkButton()
                ->setHref($this->getImageEditorUri($object))
                ->setDataAttributes([
                    'uid' => $object->getUid(),
                    'toggle' => 'tooltip',
                ])
                ->setClasses('btn-imageEditor')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:edit_image'))
                ->setIcon($this->getIconFactory()->getIcon('extensions-media-image-edit', Icon::SIZE_SMALL))
                ->render();
        }
        return $button;
    }

    /**
     * @param Content $object
     * @return string
     */
    protected function getImageEditorUri(Content $object)
    {
        $urlParameters = array(
            MediaModule::getParameterPrefix() => array(
                'controller' => 'ImageEditor',
                'action' => 'show',
                'file' => $object->getUid(),
            ),
        );
        return BackendUtility::getModuleUrl(MediaModule::getSignature(), $urlParameters);
    }
}
