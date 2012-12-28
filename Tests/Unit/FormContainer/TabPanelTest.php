<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Media development team <typo3-project-media@lists.typo3.org>
 *
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
 * Test case for class \TYPO3\CMS\Media\FormContainer\TabPanel.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class TabPanelTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\FormContainer\TabPanel
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $fakeName = '';

	/**
	 * @var string
	 */
	private $fakePrefix = '';

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\FormContainer\TabPanel();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function newPanelFooIsStoredInPropertyPanels() {
		$panelTitle = uniqid('foo');
		$this->fixture->createPanel($panelTitle);
		$panels = $this->fixture->getPanels();
		$this->assertEquals($panelTitle, $panels[0]['title']);
	}

	/**
	 * @test
	 */
	public function createAndCheckWhatPanelIsSelected() {
		$panelTitle = uniqid('foo');
		$this->fixture->createPanel($panelTitle);
		$this->assertEquals(0, $this->fixture->getSelectedPanel());
	}

	/**
	 * @test
	 */
	public function createAFewPanelsAndSelectTheFirstOne() {
		foreach (array(uniqid('foo'), uniqid('foo')) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}
		$this->fixture->selectPanel(0);
		$this->assertEquals(0, $this->fixture->getSelectedPanel());
	}

	/**
	 * @test
	 */
	public function createTwoTabsAndCheckWhetherSomeSegmentsAreFound() {
		$titleOne = uniqid('foo');
		$titleTwo = uniqid('foo');
		foreach (array($titleOne, $titleTwo) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}

		$actual = $this->fixture->renderTabs();
		$this->assertContains('href="#tab-' . $titleOne, $actual);
		$this->assertContains('href="#tab-' . $titleTwo, $actual);
		$this->assertContains('<li class="active">', $actual);
	}

	/**
	 * @test
	 */
	public function createTwoPanelsCreateAndAddTwoTextFieldsAndRenderThem() {
		$titleOne = uniqid('foo');
		$titleTwo = uniqid('foo');
		foreach (array($titleOne, $titleTwo) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}

		$fakeName = uniqid('name');
		$textFieldOne = new \TYPO3\CMS\Media\Form\TextField();
		$textFieldOne->setName($fakeName);

		$fakeName = uniqid('name');
		$textFieldTwo = new \TYPO3\CMS\Media\Form\TextField();
		$textFieldTwo->setName($fakeName);

		$actual = $this->fixture->addItem($textFieldOne)
			->addItem($textFieldTwo)
			->render();

		$this->assertEquals(1, preg_match_all('/<div class="tabbable"/isU', $actual, $matches));
		$this->assertEquals(1, preg_match_all('/<ul class="nav nav-tabs">/isU', $actual, $matches));
		$this->assertEquals(1, preg_match_all('/<div class="tab-content">/isU', $actual, $matches));
	}

	/**
	 * @test
	 */
	public function createTwoPanelsCreateAndAddTwoTextFieldsAsItemAndRenderTheItems() {
		$titleOne = uniqid('foo');
		$titleTwo = uniqid('foo');
		foreach (array($titleOne, $titleTwo) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}

		$fakeName = uniqid('name');
		$textFieldOne = new \TYPO3\CMS\Media\Form\TextField();
		$textFieldOne->setName($fakeName);

		$fakeName = uniqid('name');
		$textFieldTwo = new \TYPO3\CMS\Media\Form\TextField();
		$textFieldTwo->setName($fakeName);

		$actual = $this->fixture->addItem($textFieldOne)
			->addItem($textFieldTwo)
			->renderPanels();

		$this->assertEquals(2, preg_match_all('/<div class="control-group">/isU', $actual, $matches));
		$this->assertEquals(2, preg_match_all('/<div class="tab-pane/isU', $actual, $matches));
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidKeyInArrayException
	 */
	public function addATextFieldWithNoPanelCreatedRaisesAnException() {
		$fakeName = uniqid('name');
		$textField = new \TYPO3\CMS\Media\Form\TextField();
		$textField->setName($fakeName);

		$this->fixture->addItem($textField);
	}

	/**
	 * @test
	 */
	public function createAATextFieldObjectAndAddItAsNewItemIntoAPanel() {
		$panelTitle = uniqid('foo');
		$this->fixture->createPanel($panelTitle);

		$fakeName = uniqid('name');
		$textField = new \TYPO3\CMS\Media\Form\TextField();
		$textField->setName($fakeName);

		$this->fixture->addItem($textField);
	}

	/**
	 * @test
	 */
	public function setAPanelNameAndCheckTheExpectedReturn() {
		$name = uniqid('foo');
		$expected = 'tab-' . $name;
		$this->assertEquals($expected, $this->fixture->getTabId($name));
	}

	/**
	 * @test
	 */
	public function resetAttributesAndAddARandomOneAndCheckTheOutput() {
		$value = uniqid('foo');
		$this->fixture->setAttributes(array());
		$this->fixture->addAttribute(array('foo' => $value));
		$expected = sprintf('foo="%s" ', $value);

		$this->assertEquals($expected, $this->fixture->renderAttributes());
	}

}
?>