<?php

class Tx_Media_Service_Vidi_GridData extends Tx_Vidi_Service_GridData_FileDataProcessingService {

	public function getRecords($parameters) {
		$mountRepository = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_file_Repository_StorageRepository');
		/** @var t3lib_file_Factory $factory */
		$factory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_file_Factory');

		$data = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'*',
			$this->table,
			$this->generateWhereClauseFromQuery($parameters->query),
			'',
			$this->generateSortingClause((array)$parameters->sort),
			t3lib_utility_Math::convertToPositiveInteger($parameters->start) . ',' . t3lib_utility_Math::convertToPositiveInteger($parameters->limit)

		);

		$files = array();
		foreach ($data AS $record) {
			$files[] = $factory->getFileObject($record['uid'], $record);
		}
		$data = array();
		foreach ((array)$files as $file) {
			$fileData = $file->toArray();
			$temp = $fileData;
			$fileData['icon'] = t3lib_iconWorks::getSpriteIconForFile(strtolower($fileData['extension']));
			foreach ($fileData AS $key => $value) {
				if (is_array($GLOBALS['TCA']['sys_file']['columns'][$key])) {
					$processedValue = t3lib_BEfunc::getProcessedValue('sys_file', $key, $value);
					$fileData[$key] = $processedValue;
				}
			}
			$fileData['__raw'] = $temp;
			$data[] = $fileData;
		}
		return array(
			'data' => $data,
			'total' => count($data)
		);
	}

	/**
	 * build sorting clause out of ExtJS sorting params
	 *
	 * @param array $sorting
	 * @return string
	 */
	protected function generateSortingClause(array $sorting) {
		$sortParams = array();
		foreach((array)$sorting AS $param) {
			$sortParams[] = $GLOBALS['TYPO3_DB']->quoteStr($param->property, $this->table) . (trim($param->direction) == 'DESC' ? ' DESC' : ' ASC');
		}
		return implode(', ', $sortParams);
	}
}
