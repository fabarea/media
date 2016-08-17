<?php
namespace Fab\Media\View\Button;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a button for uploading assets.
 */
class UploadButton extends AbstractComponentView
{

    /**
     * Renders a button for uploading assets.
     *
     * @return string
     */
    public function render()
    {

        /** @var $fileUpload \Fab\Media\Form\FileUpload */
        $fileUpload = GeneralUtility::makeInstance('Fab\Media\Form\FileUpload');
        return $fileUpload->setPrefix(MediaModule::getParameterPrefix())->render();
    }
}
