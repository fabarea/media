<?php
namespace Fab\Media\Form;

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

use Fab\Media\Tests\Functional\AbstractFunctionalTestCase;

require_once dirname(dirname(__FILE__)) . '/AbstractFunctionalTestCase.php';

/**
 * Test case for class \Fab\Media\Form\FileUpload.
 */
class FileUploadTest extends AbstractFunctionalTestCase {

	/**
	 * @var \Fab\Media\Form\FileUpload
	 */
	private $fixture;

	/**
	 * @var string
	 */
	private $fakeName = '';

	/**
	 * @var string
	 */
	private $fakePrefix = '';

	public function setUp() {
		parent::setUp();

        $this->fixture = $this->getMock('Fab\Media\Form\FileUpload', array('addLanguage'));
		$this->fakeName = uniqid('name');
		$this->fakePrefix= uniqid('prefix');
	}

	public function tearDown() {
		unset($this->fixture);
	}

	/**
	 * @test
	 */
	public function getJavaScriptMethodReturnNotEmptyContent() {
		$method = new \ReflectionMethod(
			'Fab\Media\Form\FileUpload', 'getJavaScript'
		);

		$method->setAccessible(TRUE);
		#$actual = $method->invoke($this->fixture);
		$this->markTestIncomplete('Fix test by mocking storage');
		#$this->assertNotEmpty($actual);
	}

	/**
	 * @test
	 */
	public function renderFileUploadIsNotEmptyByDefault() {
		$this->markTestIncomplete('Fix test by mocking storage');
		#$this->assertNotEmpty($this->fixture->render());
	}

	/**
	 * @test
	 */
	public function getBasePrefixStripsTheSquareBraquets() {
		$method = new \ReflectionMethod(
			'Fab\Media\Form\FileUpload', 'getBasePrefix'
		);

		$method->setAccessible(TRUE);

		$basePart = uniqid();
		$fakePrefix = $basePart . '[foo]';
		$actual = $method->invokeArgs($this->fixture, array($fakePrefix));
		$this->assertSame($basePart, $actual);
	}

}