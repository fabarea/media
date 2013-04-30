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
 * Test case for class \TYPO3\CMS\Media\Service\Variant.
 *
 * @author Fabien Udriot <fabien.udriot@typo3.org>
 * @package TYPO3
 * @subpackage media
 */
class VariantTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var Tx_Phpunit_Framework
	 */
	private $testingFramework;

	/**
	 * @var \TYPO3\CMS\Media\Service\Variant
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $sourcePath;

	/**
	 * @var \TYPO3\CMS\Media\Domain\Repository\VariantRepository
	 */
	protected $variantRepository;

	/**
	 * @var \TYPO3\CMS\Core\Resource\File
	 */
	private $file;


	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\Service\Variant();
		$this->testingFramework = new Tx_Phpunit_Framework('sys_file');
		$this->variantRepository = new TYPO3\CMS\Media\Domain\Repository\VariantRepository();
		$this->sourcePath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('media') . 'Tests/Resources';
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function createVariantFromBigImageReturnsVariantObject() {

		// Copy the original resource since addFile method will move file
		$sourceFileNameAndPath = $this->sourcePath . '/sample01.jpg';
		$targetFileNameAndPath = $this->sourcePath . '/sample01_517fd09f56799.jpg';
		copy($sourceFileNameAndPath, $targetFileNameAndPath);

		$storage = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getCurrentStorage();
		$file = $storage->addFile($targetFileNameAndPath, $storage->getRootLevelFolder(), NULL, 'changeName');

		$configuration = array('height' => 300, 'width' => 300);
		$actual = $this->fixture->create($file, $configuration);
		$this->assertInstanceOf('\TYPO3\CMS\Media\Domain\Model\Variant', $actual);

		// Clean up environment
		$this->variantRepository->remove($actual);
		$file->delete();
	}

	/**
	 * @test
	 */
	public function createVariantFromSmallImageReturnsNull() {

		// Copy the original resource since addFile method will move file
		$sourceFileNameAndPath = $this->sourcePath . '/sample02.jpg';
		$targetFileNameAndPath = $this->sourcePath . '/sample02_517fd09f56701.jpg';
		copy($sourceFileNameAndPath, $targetFileNameAndPath);

		$storage = \TYPO3\CMS\Media\ObjectFactory::getInstance()->getCurrentStorage();
		$file = $storage->addFile($targetFileNameAndPath, $storage->getRootLevelFolder(), NULL, 'changeName');

		$configuration = array('height' => 300, 'width' => 300);
		$actual = $this->fixture->create($file, $configuration);
		$this->assertNull($actual);

		// Clean up environment
		$file->delete();
	}
}
?>