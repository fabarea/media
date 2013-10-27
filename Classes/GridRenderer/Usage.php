<?php
namespace TYPO3\CMS\Media\GridRenderer;
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

/**
 * Class rendering usage of an asset in the grid.
 */
class Usage extends \TYPO3\CMS\Vidi\GridRenderer\GridRendererAbstract {

	/**
	 * Render usage of an asset in the grid.
	 *
	 * @return string
	 */
	public function render() {

		$asset = \TYPO3\CMS\Media\ObjectFactory::getInstance()->convertContentObjectToAsset($this->object);

		$result = $_result = '';

		// Get the file references of the asset
		$records = $this->getDatabaseConnection()->exec_SELECTgetRows(
			'*',
			'sys_file_reference',
			'deleted = 0 AND uid_local = ' . $asset->getUid()
		);

		if (!empty($records)) {

			$_template = <<<EOF
<li title="uid: %s">
	<a href="alt_doc.php?returnUrl=mod.php?M=user_MediaM1&edit[%s][%s]=edit" class="btn-edit-reference">%s</a>
	%s
</li>
EOF;

			// assemble reference
			foreach ($records as $record) {
				$_result .= sprintf($_template,
					$record['uid_foreign'],
					$record['tablenames'],
					$record['uid_foreign'],
					\TYPO3\CMS\Backend\Utility\IconUtility::getSpriteIcon('actions-document-open'),
					$record['tablenames']
				);
			}

			// finalize reference assembling
			$_template = '<span style="text-decoration: underline">%s (%s)</span><ul style="margin: 0">%s</ul>';
			$result = sprintf($_template,
				\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('references', 'media'),
				count($records),
				$_result
			);
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
}
?>