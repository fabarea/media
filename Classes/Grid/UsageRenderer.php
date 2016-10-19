<?php
namespace Fab\Media\Grid;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Grid\ColumnRendererAbstract;
use TYPO3\CMS\Backend\Template\Components\Buttons\LinkButton;
use TYPO3\CMS\Backend\Utility\BackendUtility;
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
    public function render()
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

        return $result;
    }

    /**
     * Assemble output reference.
     *
     * @param array $references
     * @param array $mapping
     * @return string
     */
    protected function assembleOutput(array $references, array $mapping)
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
     * @param string $tableName
     * @param int $identifier
     * @return string
     */
    protected function computeTitle($tableName, $identifier)
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

    /**
     * @return LinkButton
     */
    protected function makeLinkButton()
    {
        return GeneralUtility::makeInstance(LinkButton::class);
    }

    /**
     * @param array $reference
     * @param array $mapping
     * @return string
     */
    protected function getEditUri(array $reference, array $mapping)
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

    /**
     * @return string
     */
    protected function getModuleUrl()
    {

        $additionalParameters = [];
        if (GeneralUtility::_GP('id')) {
            $additionalParameters = array(
                'id' => urldecode(GeneralUtility::_GP('id')),
            );
        }
        return BackendUtility::getModuleUrl(GeneralUtility::_GP('M'), $additionalParameters);
    }

    /**
     * Return the title given a table name and an identifier.
     *
     * @param string $tableName
     * @param string $identifier
     * @return string
     */
    protected function getRecordTitle($tableName, $identifier)
    {

        $result = '';
        if ($tableName && (int)$identifier > 0) {

            $labelField = Tca::table($tableName)->getLabelField();

            // Get the title of the record.

            /** @var array $record */
            $record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
                $labelField,
                $tableName,
                'uid = ' . $identifier
            );

            if (!empty($record[$labelField])) {
                $result = $record[$labelField];
            }
        }

        return $result;
    }

    /**
     * Return a pointer to the database.
     *
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    protected function getDatabaseConnection()
    {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * Return the wrapping HTML template.
     *
     * @return string
     */
    protected function getWrappingTemplate()
    {
        return '<div style="text-decoration: underline; margin-top: 10px; margin-bottom: 10px">%s</div><ul class="usage-list">%s</ul>';
    }

    /**
     * @return \Fab\Media\Resource\FileReferenceService
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }
}
