<?php
namespace Fab\Media\ViewHelpers\Form\Select;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use Fab\Media\Module\VidiModule;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper dealing with the storage menu
 * displayed in the top right corner of the Media module.
 */
class StorageViewHelper extends AbstractViewHelper
{

    /**
     * Render a file upload field
     *
     * @param array $objects
     * @return string
     */
    public function render($objects = [])
    {

        // Check if a storages is selected
        $currentStorage = $this->getMediaModule()->getCurrentStorage();

        $template = '<select name="%s[target]">%s</select>';
        $options = [];
        foreach ($objects as $storage) {

            /** @var \TYPO3\CMS\Core\Resource\ResourceStorage $storage */
            $options[] = sprintf('<option value="%s" %s>%s %s</option>',
                $storage->getUid(),
                is_object($currentStorage) && $currentStorage->getUid() == $storage->getUid() ? 'selected="selected"' : '',
                $storage->getName(),
                !$storage->isOnline() ? '(offline)' : ''
            );
        }
        return sprintf($template,
            VidiModule::getParameterPrefix(),
            implode("\n", $options)
        );
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

}
