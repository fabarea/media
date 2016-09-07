<?php
namespace Fab\Media\Form;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Media\Thumbnail\ThumbnailInterface;
use Fab\Media\Utility\PermissionUtility;

/**
 * A class to render a file upload widget.
 */
class FileUpload extends AbstractFormField
{

    /**
     * @var string
     */
    protected $elementId;

    /**
     * @var \TYPO3\CMS\Core\Resource\File
     */
    protected $file;

    /**
     * @var string
     */
    protected $templateFile = 'Resources/Private/Standalone/FileUploadTemplate.html';

    /**
     * @return \Fab\Media\Form\FileUpload
     */
    public function __construct()
    {
        $this->addLanguage();
        $this->elementId = 'jquery-wrapped-fine-uploader-' . uniqid();

        $this->template = <<<EOF
<div class="control-group control-group-upload" style="%s">
    <div class="container-thumbnail">%s</div>
    %s
    <div id="%s"></div>
    %s
	<script>
	    %s
	</script>
</div>

EOF;
    }

    /**
     * Add language labels for JavaScript files
     */
    protected function addLanguage()
    {
        /** @var PageRenderer $pageRenderer */
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->addInlineLanguageLabelFile(ExtensionManagementUtility::extPath('media') . 'Resources/Private/Language/locallang.xlf', 'media_file_upload');
    }

    /**
     * Render a file upload field.
     *
     * @throws \Fab\Media\Exception\EmptyPropertyException
     * @return string
     */
    public function render()
    {

        // Instantiate the file object for the whole class if possible.
        if ($this->getValue()) {
            $this->file = ResourceFactory::getInstance()->getFileObject($this->getValue());
        }

        $result = sprintf(
            $this->template,
            $this->getInlineStyle(),
            $this->getThumbnail(),
            $this->getFileInfo(),
            $this->elementId,
            $this->getJavaScriptTemplate(),
            $this->getJavaScript()
        );
        return $result;
    }

    /**
     * @return string
     */
    protected function getInlineStyle()
    {
        $style = 'float: left';
        if ($this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin()) {
            $style .= '; padding-left: 3px';
        }
        return $style;
    }

    /**
     * Get the javascript from a file and replace the markers with live variables.
     *
     * @return string
     */
    protected function getThumbnail()
    {
        $thumbnail = '';
        if ($this->file) {

            /** @var $thumbnailService \Fab\Media\Thumbnail\ThumbnailService */
            $thumbnailService = GeneralUtility::makeInstance('Fab\Media\Thumbnail\ThumbnailService', $this->file);
            $thumbnail = $thumbnailService
                ->setOutputType(ThumbnailInterface::OUTPUT_IMAGE_WRAPPED)
                ->setAppendTimeStamp(true)
                ->create();
        }
        return $thumbnail;
    }

    /**
     * Get the javascript from a file and replace the markers with live variables.
     *
     * @return string
     */
    protected function getJavaScriptTemplate()
    {
        $view = $this->getStandaloneView();
        $view->assignMultiple(
            array(
                'maximumUploadLabel' => $this->getMaximumUploadLabel(),
            )
        );
        return $view->render();
    }

    /**
     * @return \TYPO3\CMS\Fluid\View\StandaloneView
     */
    protected function getStandaloneView()
    {
        $objectManager = GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');

        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */
        $view = $objectManager->get('TYPO3\CMS\Fluid\View\StandaloneView');

        $templatePathAndFilename = ExtensionManagementUtility::extPath('media') . $this->templateFile;
        $view->setTemplatePathAndFilename($templatePathAndFilename);

        return $view;
    }

    /**
     * Get the javascript from a file and replace the markers with live variables.
     *
     * @return string
     */
    protected function getJavaScript()
    {

        // Get the base prefix
        $basePrefix = $this->getBasePrefix($this->getPrefix());

        $filePath = ExtensionManagementUtility::extPath('media') . 'Resources/Private/Standalone/FileUpload.js';

        return sprintf(
            file_get_contents($filePath),
            $basePrefix,
            $this->elementId,
            $this->getModuleUrl(),
            $this->getAllowedExtensions(),
            GeneralUtility::getMaxUploadFileSize() * 1024,
            $this->isDrivenByFolder() ?
                $this->getMediaModule()->getCurrentFolder()->getCombinedIdentifier() :
                $this->getMediaModule()->getCurrentStorage()->getUid() . ':/'

        );
    }

    /**
     * @return bool
     */
    protected function isDrivenByFolder()
    {
        return $this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin();
    }

    /**
     * @return string
     */
    protected function getModuleUrl()
    {
        $moduleSignature = MediaModule::getSignature();
        return BackendUtility::getModuleUrl($moduleSignature);
    }

    /**
     * Returns the max upload file size in Mo.
     *
     * @return string
     */
    protected function getMaximumUploadLabel()
    {
        $result = round(GeneralUtility::getMaxUploadFileSize() / 1024, 2);
        $label = LocalizationUtility::translate('max_upload_file', 'media');
        $result = sprintf($label, $result);
        return $result;
    }

    /**
     * Get allowed extension.
     *
     * @return string
     */
    protected function getAllowedExtensions()
    {
        return implode("','", PermissionUtility::getInstance()->getAllowedExtensions());
    }

    /**
     * Compute the base prefix by removing the square brackets.
     *
     * @param string $prefix
     * @return string
     */
    protected function getBasePrefix($prefix)
    {
        $parts = explode('[', $prefix);
        return empty($parts) ? '' : $parts[0];
    }

    /**
     * Returns additional file info.
     *
     * @return string
     */
    protected function getFileInfo()
    {
        return ''; // empty return here but check out Tceforms/FileUpload
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @return \Fab\Vidi\Module\ModuleLoader
     */
    protected function getModuleLoader()
    {
        return GeneralUtility::makeInstance('Fab\Vidi\Module\ModuleLoader');
    }

}
