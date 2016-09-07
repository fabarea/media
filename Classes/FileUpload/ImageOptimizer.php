<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class that optimize an image according to some settings.
 */
class ImageOptimizer implements SingletonInterface
{

    /**
     * @var array
     */
    protected $optimizers = [];

    /**
     * @var \TYPO3\CMS\Core\Resource\ResourceStorage
     */
    protected $storage;

    /**
     * Returns a class instance.
     *
     * @return ImageOptimizer
     * @throws \InvalidArgumentException
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     */
    static public function getInstance($storage = null)
    {
        return GeneralUtility::makeInstance(self::class, $storage);
    }

    /**
     * Constructor
     *
     * @return ImageOptimizer
     * @param \TYPO3\CMS\Core\Resource\ResourceStorage $storage
     */
    public function __construct($storage = null)
    {
        $this->storage = $storage;
        $this->add('Fab\Media\FileUpload\Optimizer\Resize');
        $this->add('Fab\Media\FileUpload\Optimizer\Rotate');
    }

    /**
     * Register a new optimizer
     *
     * @param string $className
     * @return void
     */
    public function add($className)
    {
        $this->optimizers[] = $className;
    }

    /**
     * Un-register a new optimizer
     *
     * @param string $className
     * @return void
     */
    public function remove($className)
    {
        if (in_array($className, $this->optimizers)) {
            $key = array_search($className, $this->optimizers);
            unset($this->optimizers[$key]);
        }
    }

    /**
     * Optimize an image
     *
     * @param UploadedFileInterface $uploadedFile
     * @return UploadedFileInterface
     * @throws \InvalidArgumentException
     */
    public function optimize(UploadedFileInterface $uploadedFile)
    {

        foreach ($this->optimizers as $optimizer) {

            /** @var $optimizer \Fab\Media\FileUpload\ImageOptimizerInterface */
            $optimizer = GeneralUtility::makeInstance($optimizer, $this->storage);
            $uploadedFile = $optimizer->optimize($uploadedFile);
        }

        return $uploadedFile;
    }
}
