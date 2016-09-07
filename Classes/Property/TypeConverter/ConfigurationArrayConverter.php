<?php
namespace Fab\Media\Property\TypeConverter;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface;
use TYPO3\CMS\Extbase\Property\TypeConverter\ArrayConverter;

/**
 * Class ConfigurationArrayConverter
 */
class ConfigurationArrayConverter extends ArrayConverter
{

    /**
     * Match configuration options (to parse actual options)
     * @var string
     */
    const PATTERN_MATCH_CONFIGURATIONOPTIONS = '/
			\s*
			(?P<optionName>[a-z0-9]+)
			\s*=\s*
			(?P<optionValue>
				"(?:\\\\"|[^"])*"
				|\'(?:\\\\\'|[^\'])*\'
				|(?:\s|[^,"\']*)
			)
		/ixS';

    /**
     * We can only convert empty strings to array or array to array.
     *
     * @param mixed $source
     * @param string $targetType
     * @return boolean
     */
    public function canConvertFrom($source, $targetType)
    {
        return is_string($source) || is_array($source);
    }

    /**
     * Convert from $source to $targetType, a noop if the source is an array.
     * If it is an empty string it will be converted to an empty array.
     *
     * @param string|array $source
     * @param string $targetType
     * @param array $convertedChildProperties
     * @param PropertyMappingConfigurationInterface $configuration
     * @return array
     * @api
     */
    public function convertFrom($source, $targetType, array $convertedChildProperties = [], PropertyMappingConfigurationInterface $configuration = null)
    {
        if (is_string($source)) {
            if ($source === '') {
                $target = [];
            } else {
                $target = $this->parseConfigurationOptions($source);
            }
        }

        if (is_array($source)) {
            $target = $source;
        }

        return $target;
    }

    /**
     * Parses $rawValidatorOptions not containing quoted option values.
     * $rawValidatorOptions will be an empty string afterwards (pass by ref!).
     *
     * @param string $rawValidatorOptions
     * @return array An array of optionName/optionValue pairs
     */
    protected function parseConfigurationOptions($rawConfigurationOptions)
    {
        $configurationOptions = [];
        $parsedConfigurationOptions = [];
        preg_match_all(self::PATTERN_MATCH_CONFIGURATIONOPTIONS, $rawConfigurationOptions, $configurationOptions, PREG_SET_ORDER);
        foreach ($configurationOptions as $configurationOption) {
            $parsedConfigurationOptions[trim($configurationOption['optionName'])] = trim($configurationOption['optionValue']);
        }
        array_walk($parsedConfigurationOptions, array($this, 'unquoteString'));
        return $parsedConfigurationOptions;
    }

    /**
     * Removes escapings from a given argument string and trims the outermost
     * quotes.
     *
     * This method is meant as a helper for regular expression results.
     *
     * @param string &$quotedValue Value to unquote
     * @return void
     */
    protected function unquoteString(&$quotedValue)
    {
        switch ($quotedValue[0]) {
            case '"':
                $quotedValue = str_replace('\\"', '"', trim($quotedValue, '"'));
                break;
            case '\'':
                $quotedValue = str_replace('\\\'', '\'', trim($quotedValue, '\''));
                break;
        }
        $quotedValue = str_replace('\\\\', '\\', $quotedValue);
    }
}