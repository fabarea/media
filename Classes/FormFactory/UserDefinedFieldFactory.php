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
 * User defined object factory.
 *
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class UserDefinedFieldFactory extends \TYPO3\CMS\Media\FormFactory\FieldFactory {

	/**
	 * Get a user defined object.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingKeyInArrayException
	 * @throws \TYPO3\CMS\Media\Exception\NotExistingClassException
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return \TYPO3\CMS\Media\Form\TextField
	 */
	public function get() {

		if (empty($this->fieldName)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Missing value for property "fieldName".', 1357402956);
		}

		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($this->fieldName);

		if (empty($configuration['renderer'])) {
			throw new \TYPO3\CMS\Media\Exception\MissingKeyInArrayException('Missing "renderer" key in config', 1357403187);
		}

		if (! class_exists($configuration['renderer'])) {
			throw new \TYPO3\CMS\Media\Exception\NotExistingClassException('Class does not exist: ' . $configuration['renderer'], 1357403187);
		}

		/** @var $fieldObject \TYPO3\CMS\Media\Form\TextField */
		$fieldObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($configuration['renderer']);

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