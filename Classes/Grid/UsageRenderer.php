<?php
namespace TYPO3\CMS\Media\Grid;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Backend\Utility\IconUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Vidi\Grid\GridRendererAbstract;
use TYPO3\CMS\Vidi\Tca\TcaService;

/**
 * Class rendering usage of an asset in the grid.
 */
class UsageRenderer extends GridRendererAbstract {

	/**
	 * Render usage of an asset in the grid.
	 *
	 * @return string
	 */
	public function render() {

		$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($this->object);

		$result = '';

		// Render File usage
		$fileReferences = $this->getFileReferences($asset);
		if (!empty($fileReferences)) {

			// Finalize file references assembling.
			$result .= sprintf($this->getWrappingTemplate(),
				LocalizationUtility::translate('references', 'media'),
				count($fileReferences),
				$this->assembleOutput($fileReferences, array('referenceIdentifier' => 'uid_foreign', 'tableName' => 'tablenames'))
			);
		}

		// Render link usage in RTE
		$linkSoftReferences = $this->getSoftLinkReferences($asset);
		if (!empty($linkSoftReferences)) {

			// Finalize link references assembling.
			$result .= sprintf($this->getWrappingTemplate(),
				LocalizationUtility::translate('link_references_in_rte', 'media'),
				count($linkSoftReferences),
				$this->assembleOutput($linkSoftReferences, array('referenceIdentifier' => 'recuid', 'tableName' => 'tablename'))
			);
		}

		// Render image usage in RTE
		$imageSoftReferences = $this->getSoftImageReferences($asset);
		if (!empty($imageSoftReferences)) {

			// Finalize image references assembling.
			$result .= sprintf($this->getWrappingTemplate(),
				LocalizationUtility::translate('image_references_in_rte', 'media'),
				count($imageSoftReferences),
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
				rawurlencode(BackendUtility::getModuleUrl('user_VidiSysFileM1')),
				$reference[$mapping['tableName']],
				$reference[$mapping['referenceIdentifier']],
				IconUtility::getSpriteIcon('actions-document-open'),
				TcaService::table($reference[$mapping['tableName']])->getTitle()
			);
		}
		return $result;
	}

	/**
	 * Return the title given a table name and an identifier.
	 *
	 * @param string $tableName
	 * @param string $identifier
	 * @return string
	 */
	public function getRecordTitle($tableName, $identifier) {

		$result = '';
		if ($tableName && (int)$identifier > 0) {

			$labelField = TcaService::table($tableName)->getLabelField();

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
	 * Return all references found in sys_file_reference.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	public function getFileReferences($asset) {

		// Get the file references of the asset.
		return $this->getDatabaseConnection()->exec_SELECTgetRows(
			'*',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $asset->getUid()
		);
	}

	/**
	 * Return soft image references.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	public function getSoftImageReferences($asset) {

		// Get the file references of the asset in the RTE.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "rtehtmlarea_images" AND ref_table = "sys_file" AND ref_uid = ' . $asset->getUid()
		);

		return $softReferences;
	}

	/**
	 * Return link image references.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return array
	 */
	public function getSoftLinkReferences($asset) {

		// Get the link references of the asset.
		$softReferences = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'recuid, tablename',
			'sys_refindex',
			'deleted = 0 AND softref_key = "typolink_tag" AND ref_table = "sys_file" AND ref_uid = ' . $asset->getUid()
		);

		return $softReferences;
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
		return '<li title="%s - %s"><a href="/typo3/alt_doc.php?returnUrl=%s&edit[%s][%s]=edit" class="btn-edit-reference">%s</a> %s</li>';
	}

	/**
	 * Return the wrapping HTML template.
	 *
	 * @return string
	 */
	protected function getWrappingTemplate() {
		return '<span style="font-weight: bold; ">%s (%s)</span><ul class="usage-list">%s</ul>';
	}
}
