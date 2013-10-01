<?php
namespace TYPO3\CMS\Media\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012-2013 Fabien Udriot <fabien.udriot@typo3.org>
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
 * Test case for class \TYPO3\CMS\Media\Domain\Model\Variant.
 */
class VariantTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \Tx_Phpunit_Framework
	 */
	private $testingFramework;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Model\Variant
	 */
	private $fixture;

	/**
	 * @var int
	 */
	private $fakeOriginalUid = 0;

	/**
	 * @var int
	 */
	private $fakeVariantUid = 0;

	public function setUp() {
		$this->testingFramework = new \Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Model\Variant();

		// Populate the database with records
		$this->populateFileTable();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getOriginalMethodReturnFileObject() {
		$dataSet = array('original' => $this->fakeOriginalUid);
		$fixture = new \TYPO3\CMS\Media\Domain\Model\Variant($dataSet);
		$this->assertInstanceOf('\TYPO3\CMS\Media\Domain\Model\Asset', $fixture->getOriginal());
	}

	/**
	 * @test
	 */
	public function getVariantMethodReturnsAssetObject() {
		$dataSet = array('variant' => $this->fakeVariantUid);
		$fixture = new \TYPO3\CMS\Media\Domain\Model\Variant($dataSet);
		$this->assertInstanceOf('\TYPO3\CMS\Media\Domain\Model\Asset', $fixture->getVariant());
	}

	/**
	 * @test
	 */
	public function createAVariantObjectAndCallToArrayMethodWhichShouldHaveAssociatedKeys() {
		foreach (array('pid', 'original', 'variant', 'variation') as $key) {
			$this->assertArrayHasKey($key, $this->fixture->toArray());
		}
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
			array('uid', rand(1, 10)),
			array('pid', rand(1, 10)),
			array('variation', uniqid()),
		);
	}

	/**
	 * Populate DB with default records for sys_file
	 */
	private function populateFileTable() {

		$storageObject = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getStorage();

		$uids = array();
		for ($index = 0; $index < 2; $index++) {
			$this->lastInsertedIdentifier = uniqid();
			$uids[] = $this->testingFramework->createRecord(
				'sys_file',
				array(
					'identifier' => $this->lastInsertedIdentifier,
					'type' => $this->fakeFileType,
					'title' => $this->fakeTitle,
					'storage' => $storageObject->getUid(),
					'pid' => 0,
				)
			);
		}
		$this->fakeOriginalUid = $uids[0];
		$this->fakeVariantUid = $uids[1];
	}

	/**
	 * Populate DB with default records for sys_file_records
	 */
	private function populateVariantTable() {
		$this->lastInsertedUid = $this->testingFramework->createRecord(
			'sys_file_variants',
			array(
				'pid' => 0,
				'role' => uniqid(),
				'original' => $this->fakeOriginalUid,
				'variant' => $this->fakeVariantUid,
				'variation' => uniqid(),
			)
		);
	}
}
?>