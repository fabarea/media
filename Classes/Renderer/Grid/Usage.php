<?php
namespace TYPO3\CMS\Media\Renderer\Grid;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012
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
 * Class rendering usage for the Grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Usage implements \TYPO3\CMS\Media\Renderer\RendererInterface {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandler;

	/**
	 * @return \TYPO3\CMS\Media\Renderer\Grid\Usage
	 */
	public function __construct() {
		$this->databaseHandler = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Render a categories for a media
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $asset = NULL) {
		return $this->getVariantOutput($asset) . $this->getReferenceOutput($asset);
	}

	/**
	 * Get the variant output.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function getVariantOutput($asset) {
		$result = $_result = '';

		// Get the file variants
		$variants = $asset->getVariants();
		if (!empty($variants)) {

			$_template = <<<EOF
<li title="uid: %s">
	- %s x %s
</li>
EOF;

			foreach ($variants as $variant) {
				$_result .= sprintf($_template,
					$variant->getVariant()->getUid(),
					$variant->getVariant()->getProperty('height'),
					$variant->getVariant()->getProperty('width')
				);
			}

			// finalize reference assembling
			$_template = '<span style="text-decoration: underline">%s (%s)</span><ul style="margin: 0 0 10px 0">%s</ul>';
			$result = sprintf($_template,
				\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('variants', 'media'),
				count($variants),
				$_result
			);
		}
		return $result;
	}

	/**
	 * Get the reference output.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function getReferenceOutput($asset) {
		$result = $_result = '';

		// Get the file references of the asset
		$records = $this->databaseHandler->exec_SELECTgetRows('*', 'sys_file_reference', 'uid_local = ' . $asset->getUid());
		if (!empty($records)) {

			$_template = <<<EOF
<li title="uid: %s">
	<a href="alt_doc.php?returnUrl=/typo3/mod.php?M=user_MediaM1&edit[%s][%s]=edit" class="btn-edit-reference">%s</a>
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
}
?>