<?php

namespace Fab\Media\Grid;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Media\Resource\FileReferenceService;
use Fab\Media\TypeConverter\ContentToFileConverter;
use Fab\Vidi\Grid\ColumnRendererAbstract;
use Fab\Vidi\Service\DataService;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use Fab\Vidi\Utility\BackendUtility;
use TYPO3\CMS\Core\Imaging\Icon;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tca\Tca;

/**
 * Class rendering usage of an asset in the grid.
 */
class UsageRenderer extends ColumnRendererAbstract
{
    /**
     * Render usage of an asset in the grid.
     *
     * @return string
     */
    public function render(): string
    {
        $file = $this->getFileConverter()->convert($this->object);

        $result = '';

        // Add number of references on the top!
        if ($this->object['number_of_references'] > 1) {
            $result .= sprintf(
                '<div><strong>%s (%s)</strong></div>',
                $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:references'),
                $this->object['number_of_references']
            );
        }

        // Render File usage
        $fileReferences = $this->getFileReferenceService()->findFileReferences($file);
        if (!empty($fileReferences)) {
            // Finalize file references assembling.
            $result .= sprintf(
                $this->getWrappingTemplate(),
                $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:file_reference'),
                $this->assembleOutput($fileReferences, array('referenceIdentifier' => 'uid_foreign', 'tableName' => 'tablenames'))
            );
        }

        // Render link usage in RTE
        $linkSoftReferences = $this->getFileReferenceService()->findSoftLinkReferences($file);
        if (!empty($linkSoftReferences)) {
            // Finalize link references assembling.
            $result .= sprintf(
                $this->getWrappingTemplate(),
                $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:link_references_in_rte'),
                $this->assembleOutput($linkSoftReferences, array('referenceIdentifier' => 'recuid', 'tableName' => 'tablename'))
            );
        }

        // Render image usage in RTE
        $imageSoftReferences = $this->getFileReferenceService()->findSoftImageReferences($file);
        if (!empty($imageSoftReferences)) {
            // Finalize image references assembling.
            $result .= sprintf(
                $this->getWrappingTemplate(),
                $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:image_references_in_rte'),
                $this->assembleOutput($imageSoftReferences, array('referenceIdentifier' => 'recuid', 'tableName' => 'tablename'))
            );
        }

        // Render related index references
        $contentIndexReferences = $this->getFileReferenceService()->findContentIndexReferences($file);
        if (!empty($contentIndexReferences)) {
            // Finalize image references assembling.
            $result .= sprintf(
                $this->getWrappingTemplate(),
                $this->getLanguageService()->sL('LLL:EXT:media/Resources/Private/Language/locallang.xlf:content_reference'),
                $this->assembleOutput($contentIndexReferences, array('referenceIdentifier' => 'recuid', 'tableName' => 'tablename'))
            );
        }

        return $result;
    }

    /**
     * Assemble output reference.
     */
    protected function assembleOutput(array $references, array $mapping): string
    {
        $result = '';
        foreach ($references as $reference) {
            $button = $this->makeLinkButton()
                ->setHref($this->getEditUri($reference, $mapping))
                ->setClasses('btn-edit-reference')
                ->setIcon($this->getIconFactory()->getIcon('actions-document-open', Icon::SIZE_SMALL))
                ->render();

            $tableName = $reference[$mapping['tableName']];
            $identifier = (int)$reference[$mapping['referenceIdentifier']];

            $result .= sprintf(
                '<li title="">%s %s</li>',
                $button,
                $this->computeTitle($tableName, $identifier)
            );
        }

        return $result;
    }

    /**
     * @param int $identifier
     */
    protected function computeTitle(string $tableName, $identifier): string
    {
        $title = '';
        if (!empty($GLOBALS['TCA'][$tableName])) {
            $title = $this->getRecordTitle($tableName, $identifier);
            if (!$title) {
                $title = Tca::table($tableName)->getTitle();
            }
        }
        return $title;
    }

    protected function makeLinkButton(): LinkButton
    {
        return GeneralUtility::makeInstance(LinkButton::class);
    }

    protected function getEditUri(array $reference, array $mapping): string
    {
        $parameterName = sprintf('edit[%s][%s]', $reference[$mapping['tableName']], $reference[$mapping['referenceIdentifier']]);
        $uri = BackendUtility::getModuleUrl(
            'record_edit',
            array(
                $parameterName => 'edit',
                'returnUrl' => $this->getModuleUrl()
            )
        );
        return $uri;
    }

    protected function getModuleUrl(): string
    {
        $additionalParameters = [];
        if (GeneralUtility::_GP('id')) {
            $additionalParameters = array(
                'id' => urldecode(GeneralUtility::_GP('id')),
            );
        }
        return BackendUtility::getModuleUrl(GeneralUtility::_GP('route'), $additionalParameters);
    }

    /**
     * Return the title given a table name and an identifier.
     *
     * @param string $identifier
     */
    protected function getRecordTitle(string $tableName, $identifier): string
    {
        $result = '';
        if ($tableName && (int)$identifier > 0) {
            $labelField = Tca::table($tableName)->getLabelField();

            // Get the title of the record.
            $record = $this->getDataService()
                ->getRecord($tableName, ['uid' => $identifier,]);

            if (!empty($record[$labelField])) {
                $result = $record[$labelField];
            }
        }

        return $result;
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    /**
     * Return the wrapping HTML template.
     */
    protected function getWrappingTemplate(): string
    {
        return '<div style="text-decoration: underline; margin-top: 10px; margin-bottom: 10px">%s</div><ul class="usage-list">%s</ul>';
    }

    protected function getFileReferenceService(): FileReferenceService
    {
        return GeneralUtility::makeInstance(FileReferenceService::class);
    }

    protected function getFileConverter(): ContentToFileConverter
    {
        return GeneralUtility::makeInstance(ContentToFileConverter::class);
    }
}
