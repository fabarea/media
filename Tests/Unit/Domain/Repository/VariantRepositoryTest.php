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
 * Test case for class \TYPO3\CMS\Media\Domain\Repository\VariantRepository.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class VariantRepositoryTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var \Tx_Phpunit_Framework
	 */
	private $testingFramework;

	/**
	 * @var int
	 */
	private $lastInsertedUid = 0;

	/**
	 * @var int
	 */
	private $fakeFileType = 0;

	/**
	 * @var int
	 */
	private $fakeOriginalUid = 0;

	/**
	 * @var int
	 */
	private $fakeVariantUid = 0;

	/**
	 * @var int
	 */
	private $numberOfFakeRecords = 3;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 */
	private $fixture;

	public function setUp() {
		$this->testingFramework = new \Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Repository\VariantRepository();

		$this->fakeFileType = rand(100, 200);

		// Populate the database with records
		$this->populateFileTable();
		$this->populateVariantTable();

		// Disable permission
		\TYPO3\CMS\Media\Utility\Setting::getInstance()->set('permission', FALSE);
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework);
	}

	/**
	 * @test
	 */
	public function findAllMethodReturnAtLeastTheNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, count($this->fixture->findAll()));
	}

	/**
	 * @test
	 */
	public function findAllMethodWithRawResultReturnAtLeastTheNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, count($this->fixture->setRawResult(TRUE)->findAll()));
	}

	/**
	 * @test
	 */
	public function countAllMethodReturnAtLeastTheNumberOfFakeRecords() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeRecords, $this->fixture->countAll());
	}

	/**
	 * @test
	 */
	public function findByUidReturnsAVariantObject() {
		$this->assertInstanceOf('\TYPO3\CMS\Media\Domain\Model\Variant', $this->fixture->findByUid($this->lastInsertedUid));
	}

	/**
	 * @test
	 */
	public function findByUidWithSetRawResultReturnsAnArray() {
		$this->assertTrue(is_array($this->fixture->setRawResult(TRUE)->findByUid($this->lastInsertedUid)));
	}

	/**
	 * @test
	 */
	public function RemoveByUidAndFindByUidReturnsFalseAsValue() {
		$this->fixture->removeByUid($this->lastInsertedUid);
		$this->assertFalse($this->fixture->findByUid($this->lastInsertedUid));
	}

	/**
	 * @test
	 */
	public function createAVariantAndCallMethodAddToInsertIntoTheRepository() {
		$variant = new \TYPO3\CMS\Media\Domain\Model\Variant();
		$variant = $this->fixture->add($variant);
		$this->assertGreaterThan($this->lastInsertedUid, $variant->getUid());
		$this->fixture->removeByUid($variant->getUid());
	}

	/**
	 * @test
	 */
	public function findByOriginalUidReturnsEqualsTheNumberOfFakeRecords() {
		$this->assertEquals($this->numberOfFakeRecords, count($this->fixture->findByOriginal($this->fakeOriginalUid)));
	}

	/**
	 * @test
	 */
	public function findByOriginalReturnsEqualsTheNumberOfFakeRecords() {
		$fileRepository = new \TYPO3\CMS\Core\Resource\FileRepository();
		$originalFile = $fileRepository->findByUid($this->fakeOriginalUid);
		$this->assertEquals($this->numberOfFakeRecords, count($this->fixture->findByOriginal($originalFile)));
	}

	/**
	 * @test
	 */
	public function countByOriginalReturnsEqualsTheNumberOfFakeRecords() {
		$this->assertEquals($this->numberOfFakeRecords, $this->fixture->countByOriginal($this->fakeOriginalUid));
	}

	/**
	 * @test
	 */
	public function findOneByOriginalUidReturnsOneVariant() {
		$fileRepository = new \TYPO3\CMS\Core\Resource\FileRepository();
		$originalFile = $fileRepository->findByUid($this->fakeOriginalUid);
		$this->assertEquals(1, count($this->fixture->findOneByOriginal($originalFile->getUid())));
	}

	/**
	 * @test
	 */
	public function findOneByOriginalReturnsOneVariant() {
		$fileRepository = new \TYPO3\CMS\Core\Resource\FileRepository();
		$originalFile = $fileRepository->findByUid($this->fakeOriginalUid);
		$this->assertEquals(1, count($this->fixture->findOneByOriginal($originalFile)));
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
	public function updateLastInsertedVariant() {
		$expected = uniqid();
		$dataSet = array(
			'uid' => $this->lastInsertedUid,
			'variation' => $expected,
		);
		$variant = new \TYPO3\CMS\Media\Domain\Model\Variant($dataSet);
		$this->fixture->update($variant);
		$this->assertSame($expected, $this->fixture->findByUid($variant->getUid())->getVariation());
	}

	/**
	 * Populate DB with default records for sys_file
	 */
	private function populateFileTable() {

		$uids = array();
		for ($index = 0; $index < 2; $index++) {
			$uids[] = $this->testingFramework->createRecord(
				'sys_file',
				array(
					'identifier' => uniqid(),
					'type' => $this->fakeFileType,
					'title' => $this->fakeTitle,
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

		for ($index = 0; $index < $this->numberOfFakeRecords; $index++) {
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
}
?>