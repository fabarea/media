<?php
namespace Fab\Media\Index;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Resource\File;
use TYPO3\CMS\Core\Resource\Index\ExtractorInterface;

/**
 * Service dealing with title extraction of a file.
 * As a basic metadata extractor, Media will set a title when a file is uploaded or whenever the files get indexed
 * through the Scheduler task. The title is basically derived from the file name e.g. ``my_report.pdf`` will
 * results as ``My report``. Notice, title will only be "injected" if no title exists for the file of course.
 */
class TitleMetadataExtractor implements ExtractorInterface
{

    /**
     * Returns an array of supported file types;
     * An empty array indicates all filetypes
     *
     * @return array
     */
    public function getFileTypeRestrictions()
    {
        return [];
    }

    /**
     * Get all supported DriverClasses
     * Since some extractors may only work for local files, and other extractors
     * are especially made for grabbing data from remote.
     * Returns array of string with driver names of Drivers which are supported,
     * If the driver did not register a name, it's the classname.
     * empty array indicates no restrictions
     *
     * @return array
     */
    public function getDriverRestrictions()
    {
        return [];
    }

    /**
     * Returns the data priority of the extraction Service.
     * Defines the precedence of Data if several extractors
     * extracted the same property.
     * Should be between 1 and 100, 100 is more important than 1
     *
     * @return integer
     */
    public function getPriority()
    {
        return 15;
    }

    /**
     * Returns the execution priority of the extraction Service
     * Should be between 1 and 100, 100 means runs as first service, 1 runs at last service
     *
     * @return integer
     */
    public function getExecutionPriority()
    {
        return 15;
    }

    /**
     * Checks if the given file can be processed by this Extractor
     *
     * @param File $file
     * @return boolean
     */
    public function canProcess(File $file)
    {
        return true;
    }

    /**
     * The actual processing TASK
     * Should return an array with database properties for sys_file_metadata to write
     *
     * @param File $file
     * @param array $previousExtractedData optional, contains the array of already extracted data
     * @return array
     */
    public function extractMetaData(File $file, array $previousExtractedData = [])
    {
        $metadata = [];
        $title = $file->getProperty('title');
        if (empty($title)) {
            $metadata = array('title' => $this->guessTitle($file->getName()));
        }
        return $metadata;
    }

    /**
     * Guess a title given a file name. Examples:
     * name: my-file-name.jpg -> title: My file name
     * name: myFileName.jpg -> title: My file name
     *
     * @param string $fileName
     * @return string
     */
    protected function guessTitle($fileName)
    {
        $fileNameWithoutExtension = $this->removeExtension($fileName);

        $title = $fileNameWithoutExtension;
        // first case: the name is separated by _ or -
        // second case: this is an upper camel case name
        if (preg_match('/-|_/is', $fileNameWithoutExtension)) {
            $title = preg_replace('/-|_/is', ' ', $fileNameWithoutExtension);
        } elseif (preg_match('/[A-Z]/', $fileNameWithoutExtension)) {
            $parts = preg_split('/(?=[A-Z])/', $fileNameWithoutExtension, -1, PREG_SPLIT_NO_EMPTY);
            $title = implode(' ', $parts);
        }

        // Remove double space.
        $title = preg_replace('/\s+/', ' ', $title);
        return ucfirst($title);
    }

    /**
     * Remove extension of a file.
     *
     * @param string $fileName
     * @return string
     */
    protected function removeExtension($fileName)
    {
        $parts = explode('.', $fileName);
        if (!empty($parts)) {
            array_pop($parts);
        }
        return implode('.', $parts);
    }
}
