<?php
namespace TYPO3\CMS\Media\Domain\Model;

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
 * Test case for class \TYPO3\CMS\Media\Domain\Model\Asset.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class AssetTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \TYPO3\CMS\Media\Domain\Model\Asset
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Domain\Model\Asset();
		$this->fixture->setIndexable(FALSE);
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function setARandomPropertyAndCheckTheValueIsTheSame() {
		$property = uniqid();
		$value = uniqid();
		$this->fixture->setProperty($property, $value);
		$this->assertSame($value, $this->fixture->getProperty($property));
	}

	/**
	 * @test
	 * @dataProvider propertyProvider
	 */
	public function testProperty($propertyName, $value) {
		$setter = 'set' . ucfirst($propertyName);
		$getter = 'get' . ucfirst($propertyName);
		call_user_func_array(array($this->fixture, $setter), array($value));
		$this->assertEquals($value, call_user_func(array($this->fixture, $getter)));
	}

	/**
	 * Provider
	 */
	public function propertyProvider() {
		return array(
			array('title', uniqid('foo')),
			array('alternative', uniqid('foo')),
			array('caption', uniqid('foo')),
			array('colorSpace', uniqid('foo')),
			array('creationDate', uniqid('foo')),
			array('modificationDate', uniqid('foo')),
			array('creator', uniqid('foo')),
			array('creatorTool', uniqid('foo')),
			array('description', uniqid('foo')),
			array('downloadName', uniqid('foo')),
			array('duration', uniqid('foo')),
			array('height', uniqid('foo')),
			array('keywords', uniqid('foo')),
			array('language', uniqid('foo')),
			array('latitude', uniqid('foo')),
			array('locationCity', uniqid('foo')),
			array('locationCountry', uniqid('foo')),
			array('locationRegion', uniqid('foo')),
			array('longitude', uniqid('foo')),
			array('mimeType', uniqid('foo')),
			array('note', uniqid('foo')),
			array('pages', uniqid('foo')),
			array('publisher', uniqid('foo')),
			array('ranking', uniqid('foo')),
			array('source', uniqid('foo')),
			array('status', uniqid('foo')),
			array('type', rand(1,5)),
			array('unit', uniqid('foo')),
			array('width', uniqid('foo')),
		);
	}
}
?>