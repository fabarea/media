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
 * Test case for class \TYPO3\CMS\Media\FormContainer\Panel.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class PanelTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\FormContainer\Panel
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
		$this->fixture = new \TYPO3\CMS\Media\FormContainer\Panel();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function newPanelIsCorrectlyStoredInPropertyPanels() {
		$size = 6;
		$this->fixture->createPanel($size);
		$panels = $this->fixture->getPanels();
		$this->assertEquals($size, $panels[0]['size']);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidIntegerRangeException
	 */
	public function newPanelWithOutOfRangeSizeRaisesAnException() {
		$size = 13;
		$this->fixture->createPanel($size);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidIntegerRangeException
	 */
	public function newPanelWithStringForSizeParameterRaisesAnException() {
		$size = uniqid();
		$this->fixture->createPanel($size);
	}

	/**
	 * @test
	 */
	public function createAndCheckWhatPanelIsSelected() {
		$this->fixture->createPanel();
		$this->assertEquals(0, $this->fixture->getSelectedPanel());
	}

	/**
	 * @test
	 */
	public function createAFewPanelsAndSelectTheFirstOne() {
		foreach (array(6, 6) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}
		$this->fixture->selectPanel(0);
		$this->assertEquals(0, $this->fixture->getSelectedPanel());
	}

	/**
	 * @test
	 */
	public function createTwoPanelsCreateAndAddTwoTextFieldsAndRender() {
		$sizeOne = 6;
		$sizeTwo = 6;
		foreach (array($sizeOne, $sizeTwo) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}

		$actual = $this->fixture->render();

		$this->assertEquals(1, preg_match_all('/<div class="container-fluid"/isU', $actual, $matches));
	}

	/**
	 * @test
	 */
	public function createTwoPanelsAndRenderThePanels() {
		$sizeOne = 6;
		$sizeTwo = 6;
		foreach (array($sizeOne, $sizeTwo) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}
		$actual = $this->fixture->renderPanels();

		$this->assertEquals(2, preg_match_all('/<div class="span6">/isU', $actual, $matches));
	}

	/**
	 * @test
	 */
	public function createTwoPanelsCreateAndAddTwoTextFieldsAsItemAndRenderThePanels() {
		$sizeOne = 6;
		$sizeTwo = 6;
		foreach (array($sizeOne, $sizeTwo) as $panelTitle) {
			$this->fixture->createPanel($panelTitle);
		}

		$textFieldOne = new \TYPO3\CMS\Media\Form\TextField();
		$textFieldOne->setName(uniqid('name'));

		$textFieldTwo = new \TYPO3\CMS\Media\Form\TextField();
		$textFieldTwo->setName(uniqid('name'));

		$actual = $this->fixture->addItem($textFieldOne)
			->addItem($textFieldTwo)
			->renderPanels();

		$this->assertEquals(2, preg_match_all('/<div class="control-group">/isU', $actual, $matches));
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
		$this->fixture->createPanel();

		$fakeName = uniqid('name');
		$textField = new \TYPO3\CMS\Media\Form\TextField();
		$textField->setName($fakeName);

		$this->fixture->addItem($textField);
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