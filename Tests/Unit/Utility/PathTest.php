<?php
namespace Fab\Media\Utility;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use TYPO3\CMS\Core\Core\Environment;

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
 * Test case for class \Fab\Media\Utility\Path.
 */
class PathTest extends UnitTestCase {

	public function setUp() {
	}

	public function tearDown() {
	}

	/**
	 * @test
	 */
	public function canResolvesAPath() {
		$resourceName = uniqid('resource');
		$expected = 'media/Resources/Public/' . $resourceName;
		$actual = Path::resolvePath($resourceName);

		$this->assertTrue(strpos($actual, $expected) > 0);
		$this->assertEquals(0, strpos(Environment::getPublicPath() . '/', $expected));
	}

	/**
	 * @test
	 */
	public function canReturnsAPublicPath() {

		$resourceName = uniqid('resource');
		$expected = 'media/Resources/Public/' . $resourceName;
		$actual = Path::getRelativePath($resourceName);

		$this->assertTrue(strpos($actual, $expected) > 0);
		$this->assertFalse(strpos(Environment::getPublicPath() . '/', $expected));
	}

	/**
	 * @test
	 */
	public function methodExistsReturnTrueForFileExistingInExtensionMedia() {
		$this->assertTrue(Path::exists('Icons/MissingMimeTypeIcon.png'));
	}

	/**
	 * @test
	 */
	public function methodNotExistsReturnFalseForFileExistingInExtensionMedia() {
		$this->assertFalse(Path::notExists('Icons/MissingMimeTypeIcon.png'));
	}

	/**
	 * @test
	 */
	public function returnsCanonicalPathForPathContainingRelativeSegment() {
		$actual = '../bar/../../foo.png';
		$expected = 'bar/foo.png';
		$this->assertSame($expected, Path::canonicalPath($actual));
	}
}