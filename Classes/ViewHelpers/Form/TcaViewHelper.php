<?php
namespace TYPO3\CMS\Media\ViewHelpers\Form;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * View helper rendering a form based on its TCA
 *
 * Example <m:form.tca object="{asset}" />
 * @todo View Helper is not used anymore, remove me! It was used when https://github.com/Ecodev/swiftform was in Media
 *
 * @category    ViewHelpers
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class TcaViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * Render a form according to a given object.
	 *
	 * @param \TYPO3\CMS\Media\Domain\Model\Asset $object Object to use for the form. Use in conjunction with the "property" attribute on the sub tags
	 * @param string $prefix prefix the field name with a namespace
	 * @return string
	 */
	public function render(\TYPO3\CMS\Media\Domain\Model\Asset $object = NULL, $prefix = NULL) {

		$this->prefix = $prefix;
		if (empty($this->prefix)) {
			$this->prefix = $this->getObjectName($object);
		}

		/** @var $tabPanel \TYPO3\CMS\Media\FormContainer\TabPanel */
		$tabPanel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FormContainer\TabPanel');

		/** @var $panel \TYPO3\CMS\Media\FormContainer\Panel */
		$panel = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FormContainer\Panel');
		$panel->createPanel(12)->addItem($tabPanel);

		/** @var $paragraph \TYPO3\CMS\Media\Form\Paragraph */
		#$paragraph = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\Paragraph');
		#$paragraph->setValue('');
		#$panel->createPanel(4)->addItem($paragraph);

		$fieldStructure = \TYPO3\CMS\Media\Utility\TcaForm::getService()->getFieldStructure($object->getType());

		$panels = array_keys($fieldStructure);

		while ($fields = array_shift($fieldStructure)) {
			$panelTitle = array_shift($panels);

			$tabPanel->createPanel($panelTitle);

			foreach ($fields as $fieldName) {

				// Get the field type
				$fieldType = \TYPO3\CMS\Media\Utility\TcaField::getService()->getFieldType($fieldName);

				// Get the value by calling the getter method if existing.
				$getter = 'get' . ucfirst($fieldName);
				if (method_exists($object, $getter)) {
					$value = call_user_func(array($object, $getter));
				} elseif ($fieldType == 'palette' || $fieldType == 'widget') {
					$value = $object->getUid();
				} else {
					$value = call_user_func_array(array($object, 'getProperty'), array($fieldName));
				}

				/** @var $fieldFactory \TYPO3\CMS\Media\FormFactory\FieldFactory */
				$fieldFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\FormFactory\FieldFactory');

				/** @var $fieldObject \TYPO3\CMS\Media\Form\FormFieldInterface */
				$fieldObject = $fieldFactory->setFieldName($fieldName)
					->setValue($value)
					->setPrefix($this->getPrefix())
					->get();

				if (is_object($fieldObject)) {
					$tabPanel->addItem($fieldObject);
				}
			}
		}

		// Call hook functions for additional constraints
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXT']['media']['ViewHelpers/Form/TcaViewHelper.php']['render'])) {
			$params = array(
				'panel' => &$panel,
			);
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXT']['media']['Domain/Repository/AbstractDemandedRepository.php']['render'] as $reference) {
				t3lib_div::callUserFunction($reference, $params, $this);
			}
		}

		return $panel->render();
	}

	/**
	 * Get object type for TCA
	 *
	 * @param object $object
	 * @return string
	 */
	public function getObjectName($object){
		$parts = explode('\\', get_class($object));
		return strtolower(array_pop($parts));
	}

	/**
	 * Prefixes / namespaces the given name with the form field prefix
	 *
	 * @return string
	 */
	protected function getPrefix() {
		$prefix = (string) $this->viewHelperVariableContainer->get('TYPO3\\CMS\\Fluid\\ViewHelpers\\FormViewHelper', 'fieldNamePrefix');

		if (!empty($this->prefix)) {
			$prefix = sprintf('%s[%s]', $prefix, $this->prefix);
		}
		return $prefix;
	}
}

?>