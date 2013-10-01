<?php
namespace TYPO3\CMS\Media\FileUpload;

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
 * Test case for class \TYPO3\CMS\Media\FileUpload\ImageOptimizer.
 */
class ImageOptimizerTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \TYPO3\CMS\Media\FileUpload\ImageOptimizer
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \TYPO3\CMS\Media\FileUpload\ImageOptimizer();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function checkOptimizersPropertyContainsDefaultValues() {
		$this->assertAttributeContains('TYPO3\CMS\Media\FileUpload\Optimizer\Resize', 'optimizers', $this->fixture);
		$this->assertAttributeContains('TYPO3\CMS\Media\FileUpload\Optimizer\Rotate', 'optimizers', $this->fixture);
	}

	/**
	 * @test
	 */
	public function addNewRandomOptimizer() {
		$optimizer = uniqid();
		$this->fixture->add($optimizer);
		$this->assertAttributeContains($optimizer, 'optimizers', $this->fixture);
	}

	/**
	 * @test
	 */
	public function addNewRandomAndRemoveOptimizer() {
		$optimizer = uniqid();
		$this->fixture->add($optimizer);
		$this->fixture->remove($optimizer);
		$this->assertAttributeNotContains($optimizer, 'optimizers', $this->fixture);
	}
}
?>