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
 * A class to render a text field
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Checkbox extends \TYPO3\CMS\Media\Form\AbstractFormField  {

	/**
	 * @return \TYPO3\CMS\Media\Form\TextField
	 */
	public function __construct() {
		$this->template = <<<EOF

<div class="control-group">
	%s

	<div class="controls">
		<input type="hidden" name="%s" value="0"/>
		<input id="%s" type="checkbox" name="%s" value="1" %s %s/>
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
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Missing value for property "name" for check box', 1356217714);
		}

		$result = sprintf($this->template,
			$this->renderLabel(),
			$this->getName(),
			$this->getId(),
			$this->getName(),
			$this->renderAttributes(),
			$this->getValue() > 0 ? 'checked="checked"' : ''
		);
		return $result;
	}
}
?>