<?php
namespace TYPO3\CMS\Media\Domain\Repository;

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
 * Test case for class \TYPO3\CMS\Media\Domain\Repository\ImageRepository.
 */
class ImageRepositoryTest extends \TYPO3\CMS\Extbase\Tests\Unit\BaseTestCase {

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
	private $numberOfFakeImages = 4;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\ImageRepository
	 */
	private $fixture;

	public function setUp() {
		$this->testingFramework = new \Tx_Phpunit_Framework('sys_file');
		$this->fixture = new \TYPO3\CMS\Media\Domain\Repository\ImageRepository();

		$this->fakeStorage = rand(100, 200);
		$this->fakeFileType = rand(100, 200);
		$this->fakeTitle = uniqid();

		// Populate the database with records
		$this->populateFileTable();
		$this->populateFileTableWithImages();
	}

	public function tearDown() {
		$this->testingFramework->cleanUp();
		unset($this->fixture, $this->testingFramework);
	}

	/**
	 * @test
	 */
	public function findAllImageReturnsTheGreaterOrEqualThanNumberOfFakeImages() {
		$this->assertGreaterThanOrEqual($this->numberOfFakeImages, count($this->fixture->findAll()));
	}

	/**
	 * @test
	 */
	public function findByReturnsSameNumberOfRecordsAsNumberOfFakeRecords() {
		$matcher = new \TYPO3\CMS\Media\QueryElement\Matcher();
		$matcher->addMatch('tx_phpunit_is_dummy_record', '1');
		$this->assertEquals($this->numberOfFakeImages, count($this->fixture->findBy($matcher)));
	}

	/**
	 * @test
	 */
	public function countByReturnsSameNumberOfRecordsAsNumberOfFakeRecords() {
		$matcher = new \TYPO3\CMS\Media\QueryElement\Matcher();
		$matcher->addMatch('tx_phpunit_is_dummy_record', '1');
		$this->assertEquals($this->numberOfFakeImages, $this->fixture->countBy($matcher));
	}

	/**
	 * @test
	 */
	public function findByUidCanReturnTheLastInsertedImage() {
		$actual = $this->fixture->findByUid($this->lastInsertedUid);
		$this->assertTrue($actual instanceof \TYPO3\CMS\Media\Domain\Model\Image);
	}

	/**
	 * @test
	 */
	public function findByTitleReturnsTheSameNumberAsFakeImageRecords() {
		$this->assertEquals($this->numberOfFakeImages, count($this->fixture->findByTitle($this->fakeTitle)));
	}

	/**
	 * @test
	 */
	public function findOneByTitleReturnsOneRecord() {
		$actual = $this->fixture->findOneByTitle($this->fakeTitle);
		$this->assertEquals(1, count($actual));
		$this->assertTrue($actual instanceof \TYPO3\CMS\Media\Domain\Model\Image);
	}

	/**
	 * @test
	 */
	public function countByTypeReturnsTheSameNumberAsFakeRecords() {
		$this->assertEquals($this->numberOfFakeImages, $this->fixture->countByTitle($this->fakeTitle));
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
	private function populateFileTableWithImages() {

		for ($index = 0; $index < $this->numberOfFakeImages; $index++) {
			$this->lastInsertedIdentifier = uniqid();
			$this->lastInsertedUid = $this->testingFramework->createRecord(
				'sys_file',
				array(
					'identifier' => $this->lastInsertedIdentifier,
					'storage' => \TYPO3\CMS\Media\Utility\ConfigurationUtility::getInstance()->get('storages'),
					'type' => \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE,
					'title' => $this->fakeTitle,
					'pid' => 0,
				)
			);
		}
	}
}
?>