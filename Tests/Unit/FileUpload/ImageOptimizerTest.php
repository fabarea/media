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
 * Test case for class \Fab\Media\FileUpload\ImageOptimizer.
 */
class ImageOptimizerTest extends UnitTestCase
{
    /**
     * @var ImageOptimizer
     */
    private $fixture;

    public function setUp()
    {
        $this->fixture = new ImageOptimizer();
    }

    public function tearDown()
    {
        unset($this->fixture);
    }

    /**
     * @test
     */
    public function checkOptimizersPropertyContainsDefaultValues()
    {
        $this->assertAttributeContains('Fab\Media\FileUpload\Optimizer\Resize', 'optimizers', $this->fixture);
        $this->assertAttributeContains('Fab\Media\FileUpload\Optimizer\Rotate', 'optimizers', $this->fixture);
    }

    /**
     * @test
     */
    public function addNewRandomOptimizer()
    {
        $optimizer = uniqid();
        $this->fixture->add($optimizer);
        $this->assertAttributeContains($optimizer, 'optimizers', $this->fixture);
    }

    /**
     * @test
     */
    public function addNewRandomAndRemoveOptimizer()
    {
        $optimizer = uniqid();
        $this->fixture->add($optimizer);
        $this->fixture->remove($optimizer);
        $this->assertAttributeNotContains($optimizer, 'optimizers', $this->fixture);
    }
}
