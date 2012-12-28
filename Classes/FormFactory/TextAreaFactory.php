<?php
namespace TYPO3\CMS\Media\FormFactory;

/***************************************************************
*  Copyright notice
*
*  (c) 2012
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
 * Text field object
 *
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class TextAreaFactory extends \TYPO3\CMS\Media\FormFactory\FieldFactory {

	/**
	 * Get a text field object
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return \TYPO3\CMS\Media\Form\TextArea
	 */
	public function get() {

		if (empty($this->fieldName)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Missing value for property "fieldName".', 1356894537);
		}

		/** @var $fieldObject \TYPO3\CMS\Media\Form\TextArea */
		$fieldObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\TextArea');

		$additionalAttributes = array(
			'class' => sprintf('%s %s',
				\TYPO3\CMS\Media\Form\Configuration::get('width'),
				\TYPO3\CMS\Media\Utility\ClientValidation::getInstance()->get($this->fieldName)),
		);

		$label = \TYPO3\CMS\Media\Utility\TcaField::getService()->getLabel($this->fieldName);

		$fieldObject->setName($this->fieldName)
			->setLabel($label)
			->setValue($this->value)
			->setPrefix($this->prefix)
			->addAttribute($additionalAttributes);

		return $fieldObject;
	}
}

?>