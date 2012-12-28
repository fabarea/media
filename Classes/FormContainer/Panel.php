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
 * A class to render a panel
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class Panel extends \TYPO3\CMS\Media\FormContainer\AbstractContainer {

	/**
	 * @var string
	 */
	protected $template = '';

	/**
	 * @var int
	 */
	protected $selectedPanel = -1;

	/**
	 * @var array
	 */
	protected $panels = array();

	/**
	 * Attributes in sense of DOM attribute, e.g. class, style, etc...
	 *
	 * @var array
	 */
	protected $attributes = array(
		'class' => 'container-fluid'
	);

	/**
	 * @return \TYPO3\CMS\Media\FormContainer\Panel
	 */
	public function __construct() {
		$this->template = <<<EOF

<div %s>
	<div class="row-fluid">
	    %s
	</div>
</div>
EOF;
	}

	/**
	 * Render a panel.
	 *
	 * @return string
	 */
	public function render() {
		$result = '';
		if (! empty($this->panels)) {
			$result = sprintf($this->template,
				$this->renderAttributes(),
				$this->renderPanels()
			);
		}
		return $result;
	}

	/**
	 * Render items of the panel.
	 *
	 * @return string
	 */
	public function renderPanels() {
		$template = '<div class="%s">%s</div>';
		$result = '';
		foreach ($this->panels as $panel) {

			$itemsResult = '';

			foreach ($panel['items'] as $item) {
				/** @var $item \TYPO3\CMS\Media\Form\FormFieldInterface */
				$itemsResult .= $item->render();
			}

			$result .= sprintf($template . PHP_EOL,
				'span' . $panel['size'],
				$itemsResult
			);
		}
		return $result;
	}

	/**
	 * Create a new panel and increase panel pointer.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidIntegerRangeException
	 * @param int|string $size should be a value between 1 and 12
	 * @return \TYPO3\CMS\Media\FormContainer\Panel
	 */
	public function createPanel($size = 12) {
		$validationOptions = array(
			'options' => array(
				'min_range' => 1,
				'max_range' => 12
			)
		);
		if (!filter_var( $size, FILTER_VALIDATE_INT, $validationOptions)) {
			throw new \TYPO3\CMS\Media\Exception\InvalidIntegerRangeException('Invalid size given which should be between 1 and 12: ' . $size, 1357081032);
		}

		$this->selectedPanel ++;
		$this->panels[$this->selectedPanel] = array(
			'size' => $size,
			'items' => array()
		);
		return $this;
	}

	/**
	 * Select a panel.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @param int $index
	 * @return void
	 */
	public function selectPanel($index) {
		if (empty($this->panels[$index])) {
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('Given key does not exist: ' . $index, 1357083303);
		}
		$this->selectedPanel = $index;
	}

	/**
	 * Add a new item into the current panel
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @param \TYPO3\CMS\Media\Form\FormFieldInterface $item
	 * @return \TYPO3\CMS\Media\FormContainer\Panel
	 */
	public function addItem(\TYPO3\CMS\Media\Form\FormFieldInterface $item) {
		if (empty($this->panels[$this->selectedPanel])) {
		    throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('Can not add an item on current panel. Have you created one?', 1357083302);
		}

		$this->panels[$this->selectedPanel]['items'][] = $item;
		return $this;
	}

	/**
	 * Render additional attribute for this DOM element
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
	 * @return array
	 */
	public function getPanels() {
		return $this->panels;
	}

	/**
	 * @param array $panels
	 * @return \TYPO3\CMS\Media\FormContainer\Panel
	 */
	public function setPanels($panels) {
		$this->panels = $panels;
		return $this;
	}

	/**
	 * @return int
	 */
	public function getSelectedPanel() {
		return $this->selectedPanel;
	}
}
?>