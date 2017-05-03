<?php
namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;
use Fab\Vidi\Domain\Model\Content;

/**
 * View which renders a "hide" button to be placed in the grid.
 */
class HideButton extends AbstractComponentView
{

    /**
     * Renders a "hide" button to be placed in the grid.
     *
     * @param Content $object
     * @return string
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function render(Content $object = null)
    {
        $button = '';

        if (isset($object->metadata) && isset($object->metadata->visible) && (bool)$object->metadata->visible === false) {
            $buttonIcon = 'actions-edit-unhide';
            $label = 'unHide';
        } else {
            $buttonIcon = 'actions-edit-hide';
            $label = 'hide';
        }
        $file = $this->getFileConverter()->convert($object);

        // Only display the hide icon if the file has no reference?
        if ($this->getFileReferenceService()->countTotalReferences($object->getUid()) === 0 && $file->checkActionPermission('write')) {

            $button = $this->makeLinkButton()
                ->setHref($this->getHideUri($object))
                ->setDataAttributes([
                    'uid' => $object->getUid(),
                    'toggle' => 'tooltip',
                    'label' => $file->getProperty('title'),
                ])
                ->setClasses('btn-visibility-toggle')
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:lang/locallang_mod_web_list.xlf:' . $label))
                ->setIcon($this->getIconFactory()->getIcon($buttonIcon, Icon::SIZE_SMALL))
                ->render();
        }

        return $button;
    }

    /**
     * @param Content $object
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getHideUri(Content $object)
    {
        $newVisibility = (isset($object->metadata) && isset($object->metadata->visible)) ? (int)!$object->metadata->visible : 0;
        $additionalParameters = [
            $this->getModuleLoader()->getParameterPrefix() => [
                'controller' => 'Content',
                'action' => 'update',
                'format' => 'json',
                'fieldNameAndPath' => 'metadata.visible',
                'content' => [
                    'visible' => $newVisibility
                ],
                'matches' => [
                    'uid' => $object->getUid(),
                ],
            ],
        ];
        return $this->getModuleLoader()->getModuleUrl($additionalParameters);
    }

    /**
     * @return \Fab\Media\Resource\FileReferenceService
     * @throws \InvalidArgumentException
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     * @throws \InvalidArgumentException
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }

}
