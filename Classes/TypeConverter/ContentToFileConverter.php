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
use TYPO3\CMS\Core\SingletonInterface;
use Fab\Vidi\Domain\Model\Content;

/**
 * Convert a Content Object to File
 */
class ContentToFileConverter implements SingletonInterface
{

    /**
     * Convert a file representation to File Resource.
     *
     * @param Content|int $fileRepresentation
     * @throws \RuntimeException
     * @return File
     */
    public function convert($fileRepresentation)
    {

        if ($fileRepresentation instanceof Content) {

            $fileData = $fileRepresentation->toArray();
            $fileData['modification_date'] = $fileData['tstamp'];

            if (!isset($fileData['storage']) && $fileData['storage'] === null) {
                throw new \RuntimeException('Storage identifier can not be null.', 1379946981);
            }

            $fileUid = $fileData['uid'];
        } else {
            $fileData = [];
            $fileUid = (int)$fileRepresentation;
        }
        return ResourceFactory::getInstance()->getFileObject($fileUid, $fileData);
    }
}