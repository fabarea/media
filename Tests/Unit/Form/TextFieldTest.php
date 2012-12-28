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
 * Test case for class \TYPO3\CMS\Media\Form\TextField.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class TextFieldTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Form\TextField
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
		$this->fixture = new \TYPO3\CMS\Media\Form\TextField();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
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
	public function renderTextFieldWithRandomNameContainsInputString() {
		$actual = $this->fixture->setName($this->fakeName)->render();
		$this->assertContains('<input', $actual);
	}
}
?>