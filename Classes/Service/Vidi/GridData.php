<?php

class Tx_Media_Service_Vidi_GridData extends Tx_Vidi_Service_GridData_TcaDataProcessingService {

	public function getRecords($parameters) {
		$mountRepository = t3lib_div::makeInstance('t3lib_file_Repository_StorageRepository');
		/** @var t3lib_file_Factory $factory */
		$factory = t3lib_div::makeInstance('t3lib_file_Factory');

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
			$fileData['id'] = $fileData['uid'];
			$fileData['icon'] = t3lib_iconWorks::getSpriteIconForFile($fileData['extension']);
			$data[] = $fileData;
		}
		return array(
			'data' => $data,
			'total' => count($data)
		);
	}

	public function getTableFields($parameters) {
		$data = array(
			array(
				'title' => 'id',
				'name' => 'id',
				'type' => 'string'
			),
			array(
				'title' => 'Bezeichnung',
				'name' => 'name',
				'type' => 'string'
			),
			array(
				'title' => 'Erweiterung',
				'name' => 'extension',
				'type' => 'string'
			),
			array(
				'title' => 'Dateigroesse',
				'name' => 'size',
				'type' => 'number'
			),
			array(
				'title' => 'Category',
				'name' => 'sys_category',
				'type' => 'relation',
				'relationTable' => 'sys_category',
				'relationTitle'	=> 'Category'
			)
		);
		return $data;
	}

	public function buildColumnConfiguration() {
		$columns = array(
			array('text' => '', 'dataIndex' => 'icon', 'hidden' => false, 'xtype' => 'iconColumn'),
			array('text' => 'Name', 'dataIndex' => 'name', 'hidable' => false),
			array('text' => '', 'xtype' => 'fileActionColumn'),
			array('text' => 'Grösse', 'dataIndex' => 'size', 'xtype' => 'byteColumn'),
			array('text' => 'Extension', 'dataIndex' => 'extension'),
			array('text' => 'Mimetype', 'dataIndex' => 'type'),
			array('text' => 'Erstellt am', 'dataIndex' => 'creationDate', 'xtype' => 'datecolumn', 'format' => 'd.m.Y H:i'),
			array('text' => 'Änderungsdatum', 'dataIndex' => 'creationDate', 'xtype' => 'datecolumn', 'format' => 'd.m.Y H:i'),
			array('text' => 'Thumbnail', 'dataIndex' => 'url', 'xtype' => 'thumbnailColumn')
		);
		return $columns;
	}

	public function buildFieldConfiguration() {
		$fields = array(
			array('name' => 'id', 'type' => 'string'),
			array('name' => 'icon', 'type' => 'string'),
			array('name' => 'name', 'type' => 'string'),
			array('name' => 'size', 'type' => 'int'),
			array('name' => 'extension', 'type' => 'string'),
			array('name' => 'type', 'type' => 'string'),
			array('name' => 'creationDate', 'type' => 'date', 'dateFormat' => 'timestamp'),
			array('name' => 'mtime', 'type' => 'date', 'dateFormat' => 'timestamp'),
			array('name' => 'permissions', 'type' => 'auto'),
			array('name' => 'indexed', 'type' => 'boolean'),
			array('name' => 'url', 'type' => 'string')
		);
		return $fields;
	}

}
