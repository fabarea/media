<?php
namespace TYPO3\CMS\Media\Form;

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
 * A class to render a select field
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Select extends \TYPO3\CMS\Media\Form\AbstractFormField  {

	/**
	 * @var array
	 */
	protected $options = array();

	/**
	 * @return \TYPO3\CMS\Media\Form\Select
	 */
	public function __construct() {
		$this->template = <<<EOF

<div class="control-group">
	%s
	<div class="controls">
		<select id="%s" type="text" name="%s" %s >
		%s
		</select>
	</div>
</div>
EOF;
	}

	/**
	 * Render a text field
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return string
	 */
	public function render() {

		if (! $this->getName()) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Missing value for property "name" for text field', 1356217712);
		}

		$result = sprintf($this->template,
			$this->renderLabel(),
			$this->getId(),
			$this->getName(),
			$this->renderAttributes(),
			$this->renderOptions()
		);
		return $result;
	}

	/**
	 * Render "options" tags of a select box
	 *
	 * @return string
	 */
	public function renderOptions(){
		$result = '';
		$template = '<option value="%s" %s %s>%s</option>' . PHP_EOL;
		foreach ($this->options as $value => $option) {

			$result .= sprintf($template,
				$value,
				(string) $value === $this->getValue() ? 'selected="selected"' : '',
				$this->renderIcon($option),
				$option['value']
			);
		}
		return $result;
	}

	/**
	 * Render the icon.
	 *
	 * @param $option
	 * @return string
	 */
	public function renderIcon($option) {
		$icon = '';
		if (! empty($option['icon'])) {
			$style = 'style="background-image: url(%s); padding-left: 20px; background-repeat: no-repeat;"';
			$icon = sprintf($style, $option['icon']);
		}
		return $icon;
	}

	/**
	 * @return array
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * @param array $options
	 *              + value contains the value
	 *              + icon contains a possible path to the icon (mandatory)
	 * @return \TYPO3\CMS\Media\Form\Select
	 */
	public function setOptions($options) {
		$this->validateOptions($options);
		$this->options = $options;
		return $this;
	}

	/**
	 * Validate options.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\MissingKeyInArrayException
	 * @param array $options
	 * @return void
	 */
	public function validateOptions($options){
		foreach ($options as $option) {
			if (!isset($option['value'])) {
				throw new \TYPO3\CMS\Media\Exception\MissingKeyInArrayException('Key "value" does not exist in "options" array.', 1356972608);
			}
		}
	}
}
?>