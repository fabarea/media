<?php
namespace Fab\Media\Grid;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Test case for class \Fab\Media\Grid\CategoryRenderer.
 */
class CategoryRendererTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {

	/**
	 * @var \Fab\Media\Grid\CategoryRenderer
	 */
	private $fixture;

	public function setUp() {
		$this->fixture = new \Fab\Media\Grid\CategoryRenderer();
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function fixtureIsOfTypeGridRendererCategoryRenderer() {
		$this->assertInstanceOf('Fab\Media\Grid\CategoryRenderer', $this->fixture);
	}
}