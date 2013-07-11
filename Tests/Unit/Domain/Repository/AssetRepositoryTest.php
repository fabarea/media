<?php
namespace TYPO3\CMS\Media\Domain\Repository;

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
 * Test case for class \TYPO3\CMS\Media\Domain\Repository\AssetRepository.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class AssetRepositoryTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \Tx_Phpunit_Framework
	 */
	private $testingFramework;

	/**
	 * @var int
	 */
	private $lastInsertedUid = 0;

	/**
	 * @var string
	 */
	private $lastInsertedIdentifier = '';

	/**
	 * @var int
	 */
	private $fakeStorage = 0;

	/**
	 * @var int
	 */
	private $fakeFileType = 0;

	/**
	 * @var int
	 */
	private $numberOfFakeRecords = 3;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\AssetRepository
	 */
	private $fixture;

	public function setUp() {
		$this->testingFramework = new \Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Repository\AssetRepository();

		$this->fakeStorage = rand(100, 200);
		$this->fakeFileType = rand(100, 200);
		// Populate the database with records
		$this->populateFileTable();

		// Disable permission
		\TYPO3\CMS\Media\Utility\Setting::getInstance()->set('permission', FALSE);
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework);
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Media\Exception\MissingUidException
	 */
	public function updateAssetReturnsException() {
		$this->fixture->updateAsset(array());
	}

	/**
	 * @test
	 */
	public function findAllReturnsGreaterOrEqualNumberOfRecordsAsNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, count($this->fixture->findAll()));
	}

	/**
	 * @test
	 */
	public function findByReturnsSameNumberOfRecordsAsNumberOfFakeRecords() {
		$matcher = new \TYPO3\CMS\Media\QueryElement\Matcher();
		$matcher->addMatch('tx_phpunit_is_dummy_record', '1');
		$this->assertEquals($this->numberOfFakeRecords, count($this->fixture->findBy($matcher)));
	}

	/**
	 * @test
	 */
	public function countByReturnsSameNumberOfRecordsAsNumberOfFakeRecords() {
		$matcher = new \TYPO3\CMS\Media\QueryElement\Matcher();
		$matcher->addMatch('tx_phpunit_is_dummy_record', '1');
		$this->assertEquals($this->numberOfFakeRecords, $this->fixture->countBy($matcher));
	}

	/**
	 * @test
	 */
	public function findByUidCanReturnTheLastInsertedMedia() {
		$actual = $this->fixture->findByUid($this->lastInsertedUid);
		$this->assertTrue($actual instanceof \TYPO3\CMS\Media\Domain\Model\Asset);
	}

	/**
	 * @test
	 */
	public function findByTypeReturnsTheSameNumberAsFakeRecords() {
		$this->assertEquals($this->numberOfFakeRecords, count($this->fixture->findByType($this->fakeFileType)));
	}

	/**
	 * @test
	 */
	public function findOneByTypeReturnsOneRecord() {
		$this->assertEquals(1, count($this->fixture->findOneByType($this->fakeFileType)));
	}

	/**
	 * @test
	 */
	public function countByTypeReturnsTheSameNumberAsFakeRecords() {
		$this->assertEquals($this->numberOfFakeRecords, $this->fixture->countByType($this->fakeFileType));
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException
	 */
	public function randomMagicMethodReturnsException() {
		$this->fixture->ASDFEFGByType();
	}

	/**
	 * @test
	 */
	public function methodGetFileMethodReturnsValueZeroForFileObjectFoo() {

		$method = new \ReflectionMethod(
			'TYPO3\CMS\Media\Domain\Repository\AssetRepository', 'getFileType'
		);
		$method->setAccessible(TRUE);

		$expected = 0;
		$this->assertSame($expected, $method->invokeArgs($this->fixture, array('foo')));
	}

	/**
	 * @test
	 */
	public function methodGetFileMethodReturnsValueTwoForFileObjectImage() {

		$method = new \ReflectionMethod(
			'TYPO3\CMS\Media\Domain\Repository\AssetRepository', 'getFileType'
		);
		$method->setAccessible(TRUE);

		$expected = 2;
		$this->assertSame($expected, $method->invokeArgs($this->fixture, array('TYPO3\CMS\Media\Domain\Model\Image')));
	}

	/**
	 * Populate DB with default records
	 */
	private function populateFileTable() {

		for ($index = 0; $index < $this->numberOfFakeRecords; $index++) {
			$this->lastInsertedIdentifier = uniqid();
			$this->lastInsertedUid = $this->testingFramework->createRecord(
				'sys_file',
				array(
					'identifier' => $this->lastInsertedIdentifier,
					'storage' => \TYPO3\CMS\Media\Utility\Setting::getInstance()->get('storage'),
					'type' => $this->fakeFileType,
					'title' => uniqid(),
					'pid' => 0,
				)
			);
		}
	}
}
?>