<?php
namespace Fab\Media\Grid;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Fab\Media\Module\MediaModule;
use Fab\Media\Module\VidiModule;
use Fab\Vidi\Grid\ColumnRendererAbstract;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use Fab\Vidi\Tca\Tca;

/**
 * Class rendering usage of an asset in the grid.
 */
class UsageRenderer extends ColumnRendererAbstract {

	/**
	 * Render usage of an asset in the grid.
	 *
	 * @return string
	 */
	public function render() {

		$file = $this->getFileConverter()->convert($this->object);

		$result = '';


		// Add number of references on the top!
		if ($this->object['number_of_references'] > 1) {
			$result .= sprintf('<div><strong>%s (%s)</strong></div>',
				LocalizationUtility::translate('references', 'media'),
				$this->object['number_of_references']
			);
		}


		// Render File usage
		$fileReferences = $this->getFileReferenceService()->findFileReferences($file);
		if (!empty($fileReferences)) {

			// Finalize file references assembling.
			$result .= sprintf($this->getWrappingTemplate(),
				LocalizationUtility::translate('file_reference', 'media'),
				$this->assembleOutput($fileReferences, array('referenceIdentifier' => 'uid_foreign', 'tableName' => 'tablenames'))
			);
		}

		// Render link usage in RTE
		$linkSoftReferences = $this->getFileReferenceService()->findSoftLinkReferences($file);
		if (!empty($linkSoftReferences)) {

			// Finalize link references assembling.
			$result .= sprintf($this->getWrappingTemplate(),
				LocalizationUtility::translate('link_references_in_rte', 'media'),
				$this->assembleOutput($linkSoftReferences, array('referenceIdentifier' => 'recuid', 'tableName' => 'tablename'))
			);
		}

		// Render image usage in RTE
		$imageSoftReferences = $this->getFileReferenceService()->findSoftImageReferences($file);
		if (!empty($imageSoftReferences)) {

			// Finalize image references assembling.
			$result .= sprintf($this->getWrappingTemplate(),
				LocalizationUtility::translate('image_references_in_rte', 'media'),
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
	protected function assembleOutput(array $references, array $mapping) {

		$result = '';
		foreach ($references as $reference) {

			$result .= sprintf($this->getReferenceTemplate(),
				$reference[$mapping['referenceIdentifier']],
				$this->getRecordTitle($reference[$mapping['tableName']], $reference[$mapping['referenceIdentifier']]),
				$this->getModuleUrl(),
				$reference[$mapping['tableName']],
				$reference[$mapping['referenceIdentifier']],
				IconUtility::getSpriteIcon('actions-document-open'),
				Tca::table($reference[$mapping['tableName']])->getTitle()
			);
		}
		return $result;
	}

	/**
	 * @return string
	 */
	protected function getModuleUrl() {
		$moduleSignature = VidiModule::getSignature();
		return rawurlencode(BackendUtility::getModuleUrl($moduleSignature) . '&id=' . MediaModule::getCombinedIdentifier());
	}

	/**
	 * Return the title given a table name and an identifier.
	 *
	 * @param string $tableName
	 * @param string $identifier
	 * @return string
	 */
	protected function getRecordTitle($tableName, $identifier) {

		$result = '';
		if ($tableName && (int)$identifier > 0) {

			$labelField = Tca::table($tableName)->getLabelField();

			// Get the title of the record.
			$record = $this->getDatabaseConnection()->exec_SELECTgetSingleRow(
				$labelField,
				$tableName,
				'uid = ' . $identifier
			);

			$result = $record[$labelField];
		}

		return $result;
	}

	/**
	 * Return a pointer to the database.
	 *
	 * @return \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected function getDatabaseConnection() {
		return $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Return HTML template for the Reference case.
	 *
	 * @return string
	 */
	protected function getReferenceTemplate() {
		return '<li title="%s - %s"><a href="alt_doc.php?returnUrl=%s&edit[%s][%s]=edit" class="btn-edit-reference">%s</a> %s</li>';
	}

	/**
	 * Return the wrapping HTML template.
	 *
	 * @return string
	 */
	protected function getWrappingTemplate() {
		return '<div style="text-decoration: underline; margin-top: 10px">%s</div><ul class="usage-list">%s</ul>';
	}

	/**
	 * @return \Fab\Media\Resource\FileReferenceService
	 */
	protected function getFileReferenceService() {
		return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
	}

	/**
	 * @return \Fab\Media\TypeConverter\ContentToFileConverter
	 */
	protected function getFileConverter() {
		return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
	}
}
