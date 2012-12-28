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
 * Test case for class \TYPO3\CMS\Media\Form\Select.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class SelectTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Form\Select
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $fakeName = '';

	/**
	 * @var array
	 */
	private $fakeOptions = array();

	/**
	 * @var string
	 */
	private $fakePrefix = '';

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Form\Select();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
		$this->fakeOptions = array(
			'0' => array(
				'value' => uniqid('item'),
			),
			'1' => array(
				'value' => uniqid('item'),
			),
			'2' => array(
				'icon' => uniqid('icon'),
				'value' => uniqid('item'),
			),
		);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 */
	public function exceptionMissingPropertyIsRaisedIfNoNameIsDefined() {
		$this->fixture->render();
	}

	/**
	 * @test
	 */
	public function setFakeNameShouldBeReturnedByRendered() {
		$field = $this->fixture->setName($this->fakeName)->render();
		$needle = sprintf('name="%s"', $this->fakeName);
		$this->assertContains($needle, $field);
	}

	/**
	 * @test
	 */
	public function setFakeNameWithPrefixShouldBeReturnedByRendered() {
		$field = $this->fixture
			->setName($this->fakeName)
			->setPrefix($this->fakePrefix)
			->render();
		$needle = sprintf('name="%s[%s]"', $this->fakePrefix, $this->fakeName);
		$this->assertContains($needle, $field);
	}

	/**
	 * @test
	 */
	public function renderWithFakeNameSetWillSetId() {
		$field = $this->fixture
			->setName($this->fakeName)
			->setLabel(uniqid('foo'))
			->setPrefix($this->fakePrefix)
			->render();

		$needle = sprintf('id="%s-%s"', $this->fakePrefix, $this->fakeName);
		$this->assertContains($needle, $field);
		$needle = sprintf('for="%s-%s"', $this->fakePrefix, $this->fakeName);
		$this->assertContains($needle, $field);
	}

	/**
	 * @test
	 */
	public function renderSelectWithRandomNameContainsInputString() {
		$actual = $this->fixture->setName($this->fakeName)->render();
		$this->assertContains('<select', $actual);
	}

	/**
	 * @test
	 */
	public function setOptionsAndTestWhetherGetOptionsReturnsTheSameSetOfData() {
		$this->fixture->setOptions($this->fakeOptions);
		$this->assertEquals($this->fakeOptions, $this->fixture->getOptions());
	}

	/**
	 * @test
	 */
	public function setOptionsAndCheckOptionsTagAreRendered() {
		$actual = $this->fixture
			->setOptions($this->fakeOptions)
			->setName($this->fakeName)
			->render();

		$this->assertEquals(3, preg_match_all('/<option/isU', $actual, $matches));
		$this->assertEquals(0, preg_match_all('/selected/isU', $actual, $matches));
		$this->assertEquals(1, preg_match_all('/background-image/isU', $actual, $matches));
	}

	/**
	 * @test
	 */
	public function setOptionsAndSelectSecondOptions() {
		$actual = $this->fixture
			->setOptions($this->fakeOptions)
			->setName($this->fakeName)
			->setValue(1)
			->render();

		$this->assertContains('<option value="1" selected="selected"', $actual);
	}

	/**
	 * @test
	 */
	public function validateOptionsWithCorrectValue() {
		$this->fixture->validateOptions($this->fakeOptions);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\MissingKeyInArrayException
	 */
	public function validateOptionsWithValueNull() {
		$options = array(
			array(
			'value' => NULL,
			)
		);
		$this->fixture->validateOptions($options);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\MissingKeyInArrayException
	 */
	public function validateOptionsWithMissingValueCell() {
		$options = array(
			'0' => array(),
		);
		$this->fixture->validateOptions($options);
	}

	/**
	 * @test
	 */
	public function createConfiguration() {
		$option['icon'] = uniqid('icon');
		$actual = $this->fixture->renderIcon($option);
		$this->assertContains($option['icon'], $actual);
	}
}
?>