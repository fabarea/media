<?php
namespace Fab\Media\Property\TypeConverter;

/*                                                                        *
 * This script belongs to the Extbase framework                           *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License as published by the *
 * Free Software Foundation, either version 3 of the License, or (at your *
 * option) any later version.                                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser       *
 * General Public License for more details.                               *
 *                                                                        *
 * You should have received a copy of the GNU Lesser General Public       *
 * License along with the script.                                         *
 * If not, see http://www.gnu.org/licenses/lgpl.html                      *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                          
 *                               */
/**
 * Class ConfigurationArrayConverter
 *
 * @package Fab\Media\Property\TypeConverter
 */
class ConfigurationArrayConverter extends \TYPO3\CMS\Extbase\Property\TypeConverter\ArrayConverter {

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
	public function canConvertFrom($source, $targetType) {
		return is_string($source) || is_array($source);
	}

	/**
	 * Convert from $source to $targetType, a noop if the source is an array.
	 * If it is an empty string it will be converted to an empty array.
	 *
	 * @param string|array $source
	 * @param string $targetType
	 * @param array $convertedChildProperties
	 * @param \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration
	 * @return array
	 * @api
	 */
	public function convertFrom($source, $targetType, array $convertedChildProperties = array(), \TYPO3\CMS\Extbase\Property\PropertyMappingConfigurationInterface $configuration = NULL) {
		if (is_string($source)) {
			if ($source === '') {
				$target = array();
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
	protected function parseConfigurationOptions($rawConfigurationOptions) {
		$configurationOptions = array();
		$parsedConfigurationOptions = array();
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
	protected function unquoteString(&$quotedValue) {
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
