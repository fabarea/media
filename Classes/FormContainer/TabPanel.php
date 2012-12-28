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
 * A class to render a tab panel
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class TabPanel extends \TYPO3\CMS\Media\FormContainer\AbstractContainer {

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
		'class' => 'tabbable'
	);

	/**
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	public function __construct() {
		$this->template = <<<EOF

<div %s>
	<ul class="nav nav-tabs">
		%s
	</ul>
	<div class="tab-content">
		%s
	</div>
</div>
EOF;
	}

	/**
	 * Render a tab panel
	 *
	 * @return string
	 */
	public function render() {
		$result = '';
		if (! empty($this->panels)) {
			$result = sprintf($this->template,
				$this->renderAttributes(),
				$this->renderTabs(),
				$this->renderPanels()
			);
		}
		return $result;
	}

	/**
	 * Render the tabs of the tab panel.
	 *
	 * @return string
	 */
	public function renderTabs() {
		$activePanel = 'active';
		$template = '<li class="%s"><a href="#%s" data-toggle="tab">%s</a></li>';
		$result = '';
		foreach ($this->panels as $panel) {
			$result .= sprintf($template,
				$activePanel,
				$this->getTabId($panel['title']),
				$panel['title']
			);
			$activePanel = '';
		}
		return $result;
	}

	/**
	 * Render items of the tab panel.
	 *
	 * @return string
	 */
	public function renderPanels() {
		$activePanel = 'active';
		$template = '<div class="tab-pane %s" id="%s">%s</div>';
		$result = '';
		foreach ($this->panels as $panel) {

			$itemsResult = '';

			foreach ($panel['items'] as $item) {
				/** @var $item \TYPO3\CMS\Media\Form\FormFieldInterface */
				$itemsResult .= $item->render();
			}

			$result .= sprintf($template . PHP_EOL,
				$activePanel,
				$this->getTabId($panel['title']),
				$itemsResult
			);
			$activePanel = '';
		}
		return $result;
	}

	/**
	 * Create a new panel and set the current panel pointer to this latter.
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidStringException
	 * @param string $title
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	public function createPanel($title) {
		if (! is_string($title)) {
			throw new \TYPO3\CMS\Media\Exception\InvalidStringException('Invalid title given', 1356610125);
		}

		// Get translation of title
		if (strpos($title, 'LLL:') === 0) {
			$title = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($title, '');
		}

		$this->selectedPanel ++;
		$this->panels[$this->selectedPanel] = array(
			'title' => $title,
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
			throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('Key does not exist', 1356610125);
		}
		$this->selectedPanel = $index;
	}

	/**
	 * Add a new item into the current panel
	 *
	 * @throws \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 * @param \TYPO3\CMS\Media\Form\FormFieldInterface $item
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	public function addItem(\TYPO3\CMS\Media\Form\FormFieldInterface $item) {
		if (empty($this->panels[$this->selectedPanel])) {
		    throw new \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException('Can not add an item on current panel. Have you created one?', 1356645770);
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
	 * Render a tab id.
	 *
	 * @param string $panelTitle
	 * @return string
	 */
	public function getTabId($panelTitle) {
		return \TYPO3\CMS\Media\Utility\DomElement::getInstance()->formatId('tab-' . $panelTitle);
	}

	/**
	 * @return array
	 */
	public function getPanels() {
		return $this->panels;
	}

	/**
	 * @param array $panels
	 * @return \TYPO3\CMS\Media\FormContainer\TabPanel
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