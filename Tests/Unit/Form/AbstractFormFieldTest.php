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
class AbstractFieldFormTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

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
		// Use a concrete object extending the abstract class.
		$this->fixture = new \TYPO3\CMS\Media\Form\TextField();
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function setANameAndCheckIfIdMatches() {
		$name = uniqid('foo');
		$this->fixture->setName($name);
		$this->assertEquals($name, $this->fixture->getId());
	}

	/**
	 * @test
	 */
	public function methodRenderLabelWithNotDefinedLabelReturnsEmptyString() {
		$this->assertEquals('', $this->fixture->renderLabel());
	}

	/**
	 * @test
	 */
	public function renderLabelForNotTranslatedLabelTitle() {
		$label = 'LLL:EXT:media/Resources/Private/Language/locallang_db.xlf:tx_media.title';
		$this->fixture->setLabel($label);

		$expected = '<label class="control-label" for="">Title</label>';
		$this->assertEquals($expected, $this->fixture->renderLabel());
	}

	/**
	 * @test
	 */
	public function renderLabelForRandomTranslatedLabel() {
		$label = uniqid('foo');
		$this->fixture->setLabel($label);

		$expected = sprintf('<label class="control-label" for="">%s</label>', $label);
		$this->assertEquals($expected, $this->fixture->renderLabel());
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\InvalidStringException
	 */
	public function raisedExceptionIfArrayGivenToAddAttributeIsNotAssociative() {
		$this->fixture->addAttribute(array(uniqid('foo')));
	}

	/**
	 * @test
	 */
	public function addAttributeReturnsOneElementWhenAddingFooElement() {
		$this->fixture->addAttribute(array('foo' => uniqid('foo')));
		$this->assertTrue(count($this->fixture->getAttributes()) == 1);
		$this->assertArrayHasKey('foo', $this->fixture->getAttributes());
	}

	/**
	 * @test
	 */
	public function propertyValueCanBeSetAndGet() {
		$value = uniqid('foo');
		$this->fixture->setValue($value);
		$this->assertEquals($value, $this->fixture->getValue());
	}

	/**
	 * @test
	 */
	public function renderAdditionalAttributeForTwoRandomAttributesGiven() {
		$value = uniqid('foo');
		$this->fixture->addAttribute(array('foo' => $value));
		$expected = sprintf('foo="%s" ', $value);

		$this->assertEquals($expected, $this->fixture->renderAttributes());

		$value = uniqid('bar');
		$this->fixture->addAttribute(array('bar' => $value));
		$expected .= sprintf('bar="%s" ', $value);
		$this->assertEquals($expected, $this->fixture->renderAttributes());
	}
}
?>