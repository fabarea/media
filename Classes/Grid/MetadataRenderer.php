<?php
namespace Fab\Media\Grid;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Vidi\Grid\ColumnRendererAbstract;
use Fab\Vidi\Tca\FieldType;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tca\Tca;

/**
 * Class for rendering a configurable metadata property of a file in the Grid.
 */
class MetadataRenderer extends ColumnRendererAbstract
{

    /**
     * Renders a configurable metadata property of a file in the Grid.
     *
     * @throws \Exception
     * @return string
     */
    public function render()
    {

        if (empty($this->gridRendererConfiguration['property'])) {
            throw new \Exception('Missing property value for Grid Renderer Metadata', 1390391042);
        }

        $file = $this->getFileConverter()->convert($this->object);
        $propertyName = $this->gridRendererConfiguration['property'];

        if ($propertyName === 'uid') {
            $metadata = $file->_getMetaData();
            $result = $metadata['uid']; // make an exception here to retrieve the uid of the metadata.
        } else {
            $result = $file->getProperty($propertyName);
        }

        // Avoid bad surprise, converts characters to HTML.
        $fieldType = Tca::table('sys_file_metadata')->field($propertyName)->getType();
        if ($fieldType !== FieldType::TEXTAREA) {
            $result = htmlentities($result);
        } elseif ($fieldType === FieldType::TEXTAREA && !$this->isClean($result)) {
            $result = htmlentities($result);
        } elseif ($fieldType === FieldType::TEXTAREA && !$this->hasHtml($result)) {
            $result = nl2br($result);
        }

        return $result;
    }

    /**
     * Check whether a string contains HTML tags.
     *
     * @param string $content the content to be analyzed
     * @return boolean
     */
    protected function hasHtml($content)
    {
        $result = false;

        // We compare the length of the string with html tags and without html tags.
        if (strlen($content) != strlen(strip_tags($content))) {
            $result = true;
        }
        return $result;
    }

    /**
     * Check whether a string contains potential XSS
     *
     * @param string $content the content to be analyzed
     * @return boolean
     */
    protected function isClean($content)
    {

        // @todo implement me!
        $result = true;
        return $result;
    }

    /**
     * @return \Fab\Media\TypeConverter\ContentToFileConverter
     */
    protected function getFileConverter()
    {
        return GeneralUtility::makeInstance('Fab\Media\TypeConverter\ContentToFileConverter');
    }
}
