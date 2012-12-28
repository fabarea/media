<?php
namespace TYPO3\CMS\Media\Utility;

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
 * A class to handle media type.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class MediaType implements \TYPO3\CMS\Core\SingletonInterface {

	const UNKNOWN = 0;

	const TEXT = 1;

	const IMAGE = 2;

	const AUDIO = 3;

	const VIDEO = 4;

	const SOFTWARE = 5;

	/**
	 * Store a equivalence type integer => type name
	 *
	 * @var array
	 */
	static protected $values = array(
		1 => 'text',
		2 => 'image',
		3 => 'audio',
		4 => 'video',
		5 => 'software',
	);

	/**
	 * Convert an integer type to a name.
	 *
	 * @param int $mediaType
	 * @return string
	 */
	static public function toName($mediaType = 0) {

		// just makes sure it can be converted
		$mediaTypeConverted = (int) $mediaType;
		if ($mediaType > 0) {
			$mediaType = $mediaTypeConverted;
		}

		if (is_string($mediaType)) {
			return $mediaType;
		}
		$result = 'unknown';
		if (isset(self::$values[$mediaType])) {
			$result = self::$values[$mediaType];
		}
		return $result;
	}

	/**
	 * Convert an integer type to a name.
	 *
	 * @param int $mediaName
	 * @return string
	 */
	static public function toInteger($mediaName = 'unknown') {
		if (is_int($mediaName)) {
			return $mediaName;
		}
		$result = 0;
		$key = array_search($mediaName, self::$values);
		if ($key !== FALSE) {
			$result = $key;
		}
		return $result;
	}

	/**
	 * Returns a set of data related to media types.
	 *
	 * @return array
	 */
	static public function getTypes() {
		$mediaTypes = array();

		$typeFilter = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(
			',',
			\TYPO3\CMS\Media\Utility\Configuration::get('visible_media_type_in_new_menu')
		);

		$types = \TYPO3\CMS\Media\Tca\ServiceFactory::getFormService('sys_file')->getTypes();
		foreach ($types as $type) {
			if (in_array($type, $typeFilter)) {
				$mediaTypes[] = array(
					'type' => $type,
					'label' => \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('type_' . $type, 'media'),
				);
			}
		}
		return $mediaTypes;
	}
}
?>