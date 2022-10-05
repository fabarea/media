<?php

namespace Fab\Media\FileUpload;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

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
 * Test case for class \Fab\Media\FileUpload\UploadManager.
 */
class UploadManagerTest extends UnitTestCase
{
    /**
     * @var UploadManager
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

    public function setUp()
    {
        $this->fixture = new UploadManager();
        $this->fakeName = uniqid('name');
        $this->fakePrefix= uniqid('prefix');
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     * @dataProvider propertyProvider
     */
    public function testProperty($propertyName, $value)
    {
        $setter = 'set' . ucfirst($propertyName);
        $getter = 'get' . ucfirst($propertyName);
        call_user_func_array(array($this->fixture, $setter), array($value));
        $this->assertEquals($value, call_user_func(array($this->fixture, $getter)));
    }

    /**
     * Provider
     */
    public function propertyProvider()
    {
        return array(
            array('uploadFolder', uniqid()),
            array('inputName', uniqid()),
            array('sizeLimit', rand(10, 100)),
        );
    }

    /**
     * @test
     * @dataProvider fileNameProvider
     */
    public function sanitizeFileNameTest($actual, $expected)
    {
        $this->assertSame($expected, $this->fixture->sanitizeFileName($actual));
    }

    /**
     * Provider
     */
    public function fileNameProvider()
    {
        return array(
            array('éléphant', 'elephant'),
            array('foo bar', 'foo-bar'),
            array('Foo Bar', 'Foo-Bar'),
            array('foo_bar', 'foo_bar'),
            array('foo&bar', 'foo-bar'),
            array('foo!bar', 'foo-bar'),
            array('foo~bar', 'foo-bar'),
        );
    }
}
