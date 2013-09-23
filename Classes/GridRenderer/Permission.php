<?php
namespace TYPO3\CMS\Media\GridRenderer;
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
 * Class rendering permission for the Grid.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Permission implements \TYPO3\CMS\Media\GridRenderer\GridRendererInterface {

	/**
	 * Render permission for the Grid.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $asset
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $asset = NULL) {

		$backendResult = '';

		// We are force to convert to array to be sure of result exists.
		// Method "isValid" from QueryResult can not be used here (returns TRUE only once?).
		$backendUserGroups = $asset->getBackendUserGroups()->toArray();
		if (!empty($backendUserGroups)) {
			$template = '<li style="list-style: disc">%s</li>';
			/** @var $backendUserGroup \TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup */
			foreach ($asset->getBackendUserGroups() as $backendUserGroup) {
				$backendResult .= sprintf($template, $backendUserGroup->getTitle());
			}
			$backendResult = sprintf('%s<ul>%s</ul>',
				'<span style="text-decoration: underline">Backend User Group</span>',
				$backendResult
			);
		}

		$frontendResult = '';

		// We are force to convert to array to be sure of result exists.
		// Method "isValid" from QueryResult can not be used here (returns TRUE only once?).
		$frontendUserGroups = $asset->getFrontendUserGroups()->toArray();
		if (!empty($frontendUserGroups)) {
			$template = '<li style="list-style: disc">%s</li>';
			/** @var $frontendUserGroup \TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup */
			foreach ($asset->getFrontendUserGroups() as $frontendUserGroup) {
				$frontendResult .= sprintf($template, $frontendUserGroup->getTitle());
			}
			$frontendResult = sprintf('%s<ul>%s</ul>',
				'<span style="text-decoration: underline">Frontend User Group</span>',
				$frontendResult
			);
		}
		return $frontendResult . $backendResult;
	}
}
?>