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
 * Test case for class \TYPO3\CMS\Media\Domain\Repository\ApplicationRepository.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class ApplicationRepositoryTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

	/**
	 * @var Tx_Phpunit_Framework
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
	 * @var string
	 */
	private $fakeTitle = '';

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
	 * @var int
	 */
	private $numberOfFakeApplications = 4;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\ApplicationRepository
	 */
	private $fixture;

	public function setUp() {
		$this->testingFramework = new Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Repository\ApplicationRepository();

		$this->fakeStorage = rand(100, 200);
		$this->fakeFileType = rand(100, 200);
		$this->fakeTitle = uniqid();

		// Populate the database with records
		$this->populateFileTable();
		$this->populateFileTableWithApplications();
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework);
	}

	/**
	 * @test
	 */
	public function findAllApplicationReturnsTheGreaterOrEqualThanNumberOfFakeApplications() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeApplications, count($this->fixture->findAll()));
	}

	/**
	 * @test
	 */
	public function findFilteredReturnsSameNumberOfRecordsAsNumberOfFakeRecords() {
		$filter = new \TYPO3\CMS\Media\QueryElement\Filter();
		$filter->addConstraint('tx_phpunit_is_dummy_record', '1');
		$this->assertEquals($this->numberOfFakeApplications, count($this->fixture->findFiltered($filter)));
	}

	/**
	 * @test
	 */
	public function countFilteredReturnsSameNumberOfRecordsAsNumberOfFakeRecords() {
		$filter = new \TYPO3\CMS\Media\QueryElement\Filter();
		$filter->addConstraint('tx_phpunit_is_dummy_record', '1');
		$this->assertEquals($this->numberOfFakeApplications, $this->fixture->countFiltered($filter));
	}

	/**
	 * @test
	 */
	public function findByUidCanReturnTheLastInsertedApplication() {
		$actual = $this->fixture->findByUid($this->lastInsertedUid);
		$this->assertTrue($actual instanceof \TYPO3\CMS\Media\Domain\Model\Application);
	}

	/**
	 * @test
	 */
	public function findByTitleReturnsTheSameNumberAsFakeApplicationRecords() {
		$this->assertEquals($this->numberOfFakeApplications, count($this->fixture->findByTitle($this->fakeTitle)));
	}

	/**
	 * @test
	 */
	public function findOneByTitleReturnsOneRecord() {
		$actual = $this->fixture->findOneByTitle($this->fakeTitle);
		$this->assertEquals(1, count($actual));
		$this->assertTrue($actual instanceof \TYPO3\CMS\Media\Domain\Model\Application);
	}

	/**
	 * @test
	 */
	public function countByTypeReturnsTheSameNumberAsFakeRecords() {
		$this->assertEquals($this->numberOfFakeApplications, $this->fixture->countByTitle($this->fakeTitle));
	}

	/**
	 * @test
	 * @expectedException \TYPO3\CMS\Extbase\Persistence\Generic\Exception\UnsupportedMethodException
	 */
	public function randomMagicMethodReturnsException() {
		$this->fixture->ASDFEFGByType();
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
					'type' => $this->fakeFileType,
					'title' => $this->fakeTitle,
					'pid' => 0,
				)
			);
		}
	}

	/**
	 * Populate DB with default records
	 */
	private function populateFileTableWithApplications() {

		for ($index = 0; $index < $this->numberOfFakeApplications; $index++) {
			$this->lastInsertedIdentifier = uniqid();
			$this->lastInsertedUid = $this->testingFramework->createRecord(
				'sys_file',
				array(
					'identifier' => $this->lastInsertedIdentifier,
					'type' => \TYPO3\CMS\Core\Resource\File::FILETYPE_SOFTWARE,
					'title' => $this->fakeTitle,
					'pid' => 0,
				)
			);
		}
	}
}
?>