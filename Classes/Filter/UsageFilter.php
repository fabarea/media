<?php
namespace TYPO3\CMS\Media\Filter;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Media\ObjectFactory;
use TYPO3\CMS\Vidi\Domain\Repository\ContentRepositoryFactory;
use TYPO3\CMS\Vidi\Signal\AfterFindContentObjectsSignalArguments;

/**
 * Additional filter by File Usage.
 */
class UsageFilter {

	/**
	 * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
	 * @inject
	 */
	protected $configurationManager;

	/**
	 * Filter according to Usage.
	 *
	 * @param AfterFindContentObjectsSignalArguments $signalArguments
	 * @return array
	 */
	public function filter($signalArguments) {
		if ($signalArguments->getDataType() === 'sys_file') {

			// Retrieve whether the User wants to filter by number of usage.
			$numberOfUsage = $this->getNumberOfUsage();
			if ($numberOfUsage !== NULL) {

				// Retrieve objects from the signal arguments container
				$objects = $signalArguments->getContentObjects();

				// If a limit has been set by the User we must remove it at first!
				if ($signalArguments->getLimit() > 0) {
					// Query one more time but **without** limit.
					$matcher = $signalArguments->getMatcher();
					$objects = ContentRepositoryFactory::getInstance()->findBy($matcher);
				}

				$filteredObjects = $this->filterByUsage($objects, $numberOfUsage);

				// Less efficient to count by PHP but the only way...
				$numberOfObjects = count($filteredObjects);
				$signalArguments->setNumberOfObjects($numberOfObjects);

				// Only return the equivalence of "limit" if one limit has been defined.
				if ($signalArguments->getLimit() > 0) {
					$filteredAndLimitedObjects = array_slice($filteredObjects, $signalArguments->getOffset(), $signalArguments->getLimit());

					// Reset Content objects with new set.
					$signalArguments->setContentObjects($filteredAndLimitedObjects);
				} else {
					// Reset Content objects with new set.
					$signalArguments->setContentObjects($filteredObjects);
				}

				$signalArguments->setHasBeenProcessed(TRUE);
			}
		}

		return array($signalArguments);
	}

	/**
	 * Retrieve the number of "Usage" the User wants to filter.
	 *
	 * @return int|NULL
	 */
	protected function getNumberOfUsage() {

		$numberOfUsage = NULL;

		// Retrieve a possible search term from GP.
		$searchTerm = GeneralUtility::_GP('sSearch'); // @todo 'sSearch' should come from an ENUM object DataTable/Parameter::SEARCH

		if (strlen($searchTerm) > 0) {

			// Parse the json query coming from the Visual Search.
			$searchTerm = rawurldecode($searchTerm);
			$terms = json_decode($searchTerm, TRUE);

			if (is_array($terms)) {
				foreach ($terms as $term) {
					$fieldNameAndPath = key($term);

					if ($fieldNameAndPath === 'usage') {
						$numberOfUsage = (int)current($term);
						break;
					}
				}
			}
		}

		return $numberOfUsage;
	}

	/**
	 * @param array $objects
	 * @param int $numberOfUsage
	 * @return array
	 */
	protected function filterByUsage($objects, $numberOfUsage) {
		$filteredObjects = array();

		foreach ($objects as $index => $object) {

			// Add a stop to the loop for performance sake.
			if ($index > $this->getPerformanceLimit()) {
				break;
			}

			$asset = ObjectFactory::getInstance()->convertContentObjectToAsset($object);

			// We could use countTotalReferences and retrieve $numberOfReferences in one method.
			// However we must spare server resources and avoid mass querying the database if not necessary.
			// So go step by step...
			$numberOfReferences = $this->getFileReferenceService()->countFileReferences($asset);
			if ($numberOfReferences <= $numberOfUsage) {

				$numberOfReferences += $this->getFileReferenceService()->countSoftImageReferences($asset);

				if ($numberOfReferences <= $numberOfUsage) {

					$numberOfReferences += $this->getFileReferenceService()->countSoftLinkReferences($asset);
					if ($numberOfUsage === $numberOfReferences) {
						$filteredObjects[] = $object;
					}
				}
			}
		}
		return $filteredObjects;
	}

	/**
	 * @throws \Exception
	 * @return int
	 */
	protected function getPerformanceLimit() {
		$configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		if (!isset($configuration['module.']['tx_media.']['settings.']['filter.']['usage.']['performanceLimit'])) {
			throw new \Exception('I could not find a performance limit for usage. Not loaded TS configuration?', 1407923759);
		}
		$performanceLimit = (int)$configuration['module.']['tx_media.']['settings.']['filter.']['usage.']['performanceLimit'];

		return $performanceLimit;
	}

	/**
	 * @return \TYPO3\CMS\Media\FileReference\FileReferenceService
	 */
	protected function getFileReferenceService() {
		return GeneralUtility::makeInstance('TYPO3\CMS\Media\FileReference\FileReferenceService');
	}

}
