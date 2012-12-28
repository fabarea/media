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
 * Field factory
 *
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class FieldFactory implements \TYPO3\CMS\Media\FormFactory\FieldFactoryInterface {

	/**
	 * @var string
	 */
	protected $fieldName = '';

	/**
	 * @var string
	 */
	protected $value = '';

	/**
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Get a field object
	 *
	 * @return \TYPO3\CMS\Media\Form\FormFieldInterface
	 */
	public function get(){

		$className = '';
		$fieldType = \TYPO3\CMS\Media\Utility\TcaField::getService()->getFieldType($this->fieldName);
		if ($fieldType == 'input') {
			$className = 'TYPO3\CMS\Media\FormFactory\TextFieldFactory';
		} elseif ($fieldType == 'select') {
			$className = 'TYPO3\CMS\Media\FormFactory\SelectFactory';
		} elseif ($fieldType == 'check') {
			$className = 'TYPO3\CMS\Media\FormFactory\CheckboxFactory';
		} elseif ($fieldType == 'radio') {
			// @todo implement me
		} elseif ($fieldType == 'text') {
			$className = 'TYPO3\CMS\Media\FormFactory\TextAreaFactory';
		} elseif ($fieldType == 'user') {
			$className = 'TYPO3\CMS\Media\FormFactory\UserDefinedFieldFactory';
		} elseif ($fieldType == 'widget') {
			$className = 'TYPO3\CMS\Media\FormFactory\WidgetFactory';
		} elseif ($fieldType == 'inline') {
			// @todo not supported
		} elseif ($fieldType == 'palette') {
			// @todo not supported
		} else {
			/** @var \TYPO3\CMS\Core\Log\Logger $logger */
			$logger = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Log\\LogManager')->getLogger(__CLASS__);
			$logger->warning(sprintf('Unknown field type for field "%s"', $this->fieldName));
		}

		if ($className) {

			/** @var $fieldFactory \TYPO3\CMS\Media\FormFactory\FieldFactoryInterface */
			$fieldFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance($className);
			$fieldObject = $fieldFactory->setFieldName($this->fieldName)
				->setValue($this->value)
				->setPrefix($this->prefix)
				->get();
		}
		return isset($fieldObject) ? $fieldObject : NULL;
	}

	/**
	 * @return string
	 */
	public function getFieldName() {
		return $this->fieldName;
	}

	/**
	 * @param string $fieldName
	 * @return \TYPO3\CMS\Media\FormFactory\FieldFactory
	 */
	public function setFieldName($fieldName) {
		$this->fieldName = $fieldName;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @param string $value
	 * @return \TYPO3\CMS\Media\FormFactory\FieldFactory
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 * @return \TYPO3\CMS\Media\FormFactory\FieldFactory
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
		return $this;
	}
}

?>