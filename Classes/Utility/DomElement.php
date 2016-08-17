<?php
namespace Fab\Media\Utility;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\SingletonInterface;

/**
 * A class to handle a attribute
 */
class DomElement implements SingletonInterface
{

    /**
     * Returns a class instance
     *
     * @return \Fab\Media\Utility\DomElement
     */
    static public function getInstance()
    {
        return \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Fab\Media\Utility\DomElement');
    }

    /**
     * Format an id given an input string
     *
     * @param string $input
     * @return string
     */
    public function formatId($input)
    {

        // remove the capital letter from the id
        $segments = preg_split('/(?=[A-Z])/', $input);
        $segments = array_map('strtolower', $segments);
        if ($segments[0] == '') {
            array_shift($segments);
        }
        $result = implode('-', $segments);

        return $this->sanitizeId($result);
    }

    /**
     * Sanitize an id
     *
     * @param string $input
     * @return mixed
     */
    protected function sanitizeId($input)
    {

        // remove the track of possible namespace
        $searches[] = ' ';
        $searches[] = '_';
        $searches[] = '--';
        $searches[] = '[';
        $searches[] = ']';
        $replaces[] = '-';
        $replaces[] = '-';
        $replaces[] = '-';
        $replaces[] = '-';
        $replaces[] = '';
        return str_replace($searches, $replaces, strtolower($input));
    }

}
