<?php
namespace TYPO3\CMS\Media\FormFactory;

/***************************************************************
*  Copyright notice
*
*  (c) 2012
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Text field object
 *
 * @package     TYPO3
 * @subpackage  media
 * @author      Fabien Udriot <fabien.udriot@typo3.org>
 */
class SelectFactory extends \TYPO3\CMS\Media\FormFactory\FieldFactory {

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseHandle;

	/**
	 * @return \TYPO3\CMS\Media\FormFactory\SelectFactory
	 */
	public function __construct() {
		$this->databaseHandle = $GLOBALS['TYPO3_DB'];
	}

	/**
	 * Get a text field object
	 *
	 * @throws \TYPO3\CMS\Media\Exception\EmptyPropertyException
	 * @return \TYPO3\CMS\Media\Form\Select
	 */
	public function get() {

		if (empty($this->fieldName)) {
			throw new \TYPO3\CMS\Media\Exception\EmptyPropertyException('Missing value for property "fieldName".', 1356894537);
		}

		/** @var $fieldObject \TYPO3\CMS\Media\Form\Select */
		$fieldObject = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Media\Form\Select');

		$additionalAttributes = array(
			'class' => sprintf('%s %s',
				\TYPO3\CMS\Media\Form\Configuration::get('width'),
				\TYPO3\CMS\Media\Utility\ClientValidation::getInstance()->get($this->fieldName)),
		);

		$label = \TYPO3\CMS\Media\Utility\TcaField::getService()->getLabel($this->fieldName);

		$fieldObject->setOptions($this->getOptions())
			->setName($this->fieldName)
			->setLabel($label)
			->setValue($this->value)
			->setPrefix($this->prefix)
			->addAttribute($additionalAttributes);

		return $fieldObject;
	}

	/**
	 * Get a list of options according to the field configuration
	 *
	 * @return array
	 */
	public function getOptions() {
		$configuration = \TYPO3\CMS\Media\Utility\TcaField::getService()->getConfiguration($this->fieldName);

		$items = \TYPO3\CMS\Core\Utility\GeneralUtility::array_merge(
			$this->getItemsFromDatabase($configuration),
			$this->getItemsFromTca($configuration)
		);
		return $items;
	}

	/**
	 * Get a set of items from the database from the TCA.
	 *
	 * @param array $configuration
	 * @return array
	 */
	public function getItemsFromTca (array $configuration) {
		$items = array();
		if (!empty($configuration['items']) && is_array($configuration['items'])) {
			foreach ($configuration['items'] as $item) {

				if (count($item) >= 2) {

					$key = $item[1];
					$value = $item[0];
					if (strpos($value, 'LLL:') === 0) {
						$value = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($value, '');
					}

					$items[$key] = array(
						'icon' => empty($item[2]) ? '' : $item[2],
						'value' => $value === NULL ? '' : $value,
					);
				}
			}
		}
		return $items;
	}

	/**
	 * Get a set of items from the database.
	 *
	 * @param array $configuration
	 * @return array
	 */
	public function getItemsFromDatabase($configuration){

		$records = array();

		if (!empty($configuration['foreign_table'])) {

			$tableName = $configuration['foreign_table'];

			/** @var $tableService \TYPO3\CMS\Media\Tca\TableService */
			$tableService = \TYPO3\CMS\Media\Tca\ServiceFactory::getTableService($tableName);

			$template = 'SELECT * FROM %s WHERE 1=1 %s';
			$query = sprintf($template,
				$tableName,
				empty($configuration['foreign_table_where']) ? '' : $configuration['foreign_table_where']
			);

			$resource = $this->databaseHandle->sql_query($query);
			while ($row = $this->databaseHandle->sql_fetch_assoc($resource)) {
				$records[$row['uid']] = array(
					'value' => $row[$tableService->getLabel()],
				);
			}
		}

		return $records;
	}
}

?>