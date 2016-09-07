<?php
namespace Fab\Media\TypeConverter;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\AbstractTypeConverter;

/**
 * Convert a file uid into a File object.
 */
class FileConverter extends AbstractTypeConverter
{

    /**
     * @var array<string>
     */
    protected $sourceTypes = array('int');

    /**
     * @var string
     */
    protected $targetType = 'TYPO3\CMS\Core\Resource\File';

    /**
     * @var integer
     */
    protected $priority = 1;

    /**
     * Actually convert from $source to $targetType
     *
     * @param string $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return File
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {

        /** @var $file File */
        $file = ResourceFactory::getInstance()->getFileObject((int)$source);

        if (!$file) {
            $message = sprintf('File with identifier "%s" could not be found.', $file);
            throw new \Exception($message, 1433529796);
        }

        $file->getType(); // force to internally know its mime-type.
        return $file;
    }
}