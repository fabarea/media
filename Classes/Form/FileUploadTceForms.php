<?php

namespace Fab\Media\Form;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Media\View\MetadataView;
use Fab\Media\Module\MediaModule;
use Fab\Vidi\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * A class to render a file upload widget.
 * Notice the file is very similar to FileUpload.php but integrates itself into TCEforms.
 */
class FileUploadTceForms extends FileUpload
{
    /**
     * @var string
     */
    protected string $templateFile = 'Resources/Private/Standalone/FileUploadTceFormsTemplate.html';

    /**
     * Fetch the JavaScript to be rendered and replace the markers with "live" variables.
     */
    protected function getJavaScript(): string
    {
        // Get the base prefix.
        $basePrefix = $this->getBasePrefix($this->getPrefix());
        $filePath = ExtensionManagementUtility::extPath('media') . 'Resources/Private/Standalone/FileUploadTceForms.js';

        return sprintf(
            file_get_contents($filePath),
            $basePrefix,
            $this->elementId,
            $this->getModuleUrl(),
            $this->getAllowedExtension(),
            GeneralUtility::getMaxUploadFileSize() * 1024,
            $this->getValue()
        );
    }

    protected function getModuleUrl(): string
    {
        $moduleSignature = MediaModule::getSignature();
        return BackendUtility::getModuleUrl($moduleSignature);
    }

    /**
     * Get allowed extension.
     */
    protected function getAllowedExtension(): string
    {
        return $this->file->getExtension();
    }

    /**
     * Returns additional file info.
     */
    protected function getFileInfo(): string
    {
        /** @var MetadataView $metadataView */
        $metadataView = GeneralUtility::makeInstance(MetadataView::class);

        return sprintf(
            '<div class="container-fileInfo" style="font-size: 7pt; color: #777;">%s</div>',
            $metadataView->render($this->file)
        );
    }
}
