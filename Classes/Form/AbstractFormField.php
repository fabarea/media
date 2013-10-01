<?php
namespace TYPO3\CMS\Media\Form;

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
 * A class to render a field.
 */
abstract class AbstractFormField implements \TYPO3\CMS\Media\Form\FormFieldInterface {

	/**
	 * @var string
	 */
	protected $template = '';

	/**
	 * @var string
	 */
	protected $value = '';

	/**
	 * @var string
	 */
	protected $name = '';

	/**
	 * @var string
	 */
	protected $id = '';

	/**
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * @var string
	 */
	protected $label = '';

	/**
	 * Store what child element will contain this element.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Attributes in sense of DOM attribute, e.g. class, style, etc...
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @return string
	 */
	public function render() {
		return $this->template;
	}

	/**
	 * Render the label if possible. Otherwise return an empty string.
	 *
	 * @return string
	 */
	public function renderLabel() {
		$result = '';
		if ($this->label) {
			$template = '<label class="control-label" for="%s">%s</label>';

			if (strpos($this->label, 'LLL:') === 0) {
				$this->label = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($this->label, '');
			}

			$result = sprintf($template,
				$this->getId(),
				$this->label
			);
		}
		return $result;
	}

	/**
	 * Render additional attribute for this DOM element.
	 *
	 * @return string
	 */
	public function renderAttributes() {
		$result = '';
		if (!empty($this->attributes)) {
			foreach ($this->attributes as $attribute => $value) {
				$result .= sprintf('%s="%s" ',
					htmlspecialchars($attribute),
					htmlspecialchars($value)
				);
			}
		}
		return $result;
	}

	/**
	 * Add an additional (DOM) attribute to be added to this template.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidStringException
	 * @param array $attribute associative array that contains attribute => value
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function addAttribute(array $attribute) {
		if (!empty($attribute)) {
			reset($attribute);
			$key = key($attribute);
			if (!is_string($key)) {
				throw new \TYPO3\CMS\Media\Exception\InvalidStringException('Not an associative array. Is not a key: ' . $key, 1356478742);
			}

			$this->attributes[$key] = $attribute[$key];
		}
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * @param string $template
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setTemplate($template) {
		$this->template = $template;
	}

	/**
	 * @return string
	 */
	public function getLabel() {
		return $this->label;
	}

	/**
	 * @param string $label
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	/**
	 * ViewHelper Variable Container
	 *
	 * @var \TYPO3\CMS\Fluid\Core\ViewHelper\ViewHelperVariableContainer
	 * @api
	 */
	protected $viewHelperVariableContainer;

	/**
	 * @param \TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext
	 * @return void
	 */
	public function setRenderingContext(\TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface $renderingContext) {
		$this->renderingContext = $renderingContext;
		$this->templateVariableContainer = $renderingContext->getTemplateVariableContainer();
		if ($renderingContext->getControllerContext() !== NULL) {
			$this->controllerContext = $renderingContext->getControllerContext();
		}
		$this->viewHelperVariableContainer = $renderingContext->getViewHelperVariableContainer();
	}

	/**
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setPrefix($prefix) {
		$this->prefix = $prefix;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @param array $items
	 */
	public function setItems($items) {
		$this->items = $items;
	}

	/**
	 * @return string
	 */
	public function getValue() {
		return htmlspecialchars($this->value);
	}

	/**
	 * @param string $value
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getName() {
		$result = $this->name;
		if ($this->getPrefix()) {
			$result = sprintf('%s[%s]', $this->getPrefix(), $this->name);
		}
		return $result;
	}

	/**
	 * @param string $name
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getId() {
		if ($this->id === '') {
			$this->id = \TYPO3\CMS\Media\Utility\DomElement::getInstance()->formatId($this->getName());
		}
		return $this->id;
	}

	/**
	 * @param string $id
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * @param array $attributes
	 * @return \TYPO3\CMS\Media\Form\AbstractFormField
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}
}
?>