<?php
namespace TYPO3\CMS\Media\FormContainer;

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
 * A abstract class for a container
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
abstract class AbstractContainer implements
	\TYPO3\CMS\Media\FormContainer\ContainerInterface,
	\TYPO3\CMS\Media\Form\FormFieldInterface {

	/**
	 * @var string
	 */
	protected $template = '';

	/**
	 * Attributes in sense of DOM attribute, e.g. class, style, etc...
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Add an additional (DOM) attribute to be added to this template.
	 *
	 * @param array $attribute associative array that contains attribute => value
	 * @throws \TYPO3\CMS\Media\Exception\InvalidStringException
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	public function addAttribute(array $attribute) {
		reset($attribute);
		$key = key($attribute);
		if (!is_string($key)) {
			throw new \TYPO3\CMS\Media\Exception\InvalidStringException('Not an associative array. Is not a key: ' . $key, 1356478742);
		}

		$this->attributes[$key] = $attribute[$key];
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
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	public function setTemplate($template) {
		$this->template = $template;
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
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
		return $this;
	}
}
?>