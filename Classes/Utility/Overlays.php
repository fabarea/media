<?php
namespace TYPO3\CMS\Media\Utility;

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

/**
 * Main library of the 'overlays' extension.
 * It aims to improve on the performance of the original overlaying mechanism provided by t3lib_page
 * and to provide a more useful API for developers
 */
final class Overlays {

	static public $tableFields = array();

	/**
	 * This method is designed to get all the records from a given table, properly overlaid with versions and translations
	 * Its parameters are the same as t3lib_db::exec_SELECTquery()
	 * A small difference is that it will take only a single table
	 * The big difference is that it returns an array of properly overlaid records and not a result pointer
	 *
	 * @param    string        $selectFields: List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @param    string        $fromTable: Table from which to select. This is what comes right after "FROM ...". Required value.
	 * @param    string        $whereClause: Optional additional WHERE clauses put in the end of the query. NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself! DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param    string        $groupBy: Optional GROUP BY field(s), if none, supply blank string.
	 * @param    string        $orderBy: Optional ORDER BY field(s), if none, supply blank string.
	 * @param    string        $limit: Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return    array        Fully overlaid recordset
	 */
	public static function getAllRecordsForTable($selectFields, $fromTable, $whereClause = '', $groupBy = '', $orderBy = '', $limit = '') {
		// SQL WHERE clause is the base clause passed to the function
		$where = $whereClause;
		// Add language condition
		$condition = self::getLanguageCondition($fromTable);
		if (!empty($condition)) {
			if (!empty($where)) {
				$where .= ' AND ';
			}
			$where .= '(' . $condition . ')';
		}
		// Add enable fields condition
		$condition = self::getEnableFieldsCondition($fromTable);
		if (!empty($condition)) {
			if (!empty($where)) {
				$where .= ' AND ';
			}
			$where .= '(' . $condition . ')';
		}
		// Add workspace condition
		$condition = self::getVersioningCondition($fromTable);
		if (!empty($condition)) {
			if (!empty($where)) {
				$where .= ' AND ';
			}
			$where .= '(' . $condition . ')';
		}

		// If the language is not default, prepare for overlays
		$doOverlays = FALSE;
		if ($GLOBALS['TSFE']->sys_language_content > 0) {
			// Make sure the list of selected fields includes the necessary language fields
			// so that language overlays can be gotten properly
			try {
				$selectFields = self::selectOverlayFields($fromTable, $selectFields);
				$doOverlays = TRUE;
			} catch (Exception $e) {
				// If the language fields could not be gotten, avoid overlay process
				$doOverlays = FALSE;
			}
		}
		// If versioning preview is on, prepare for version overlays
		$doVersioning = FALSE;
		if ($GLOBALS['TSFE']->sys_page->versioningPreview) {
			try {
				$selectFields = self::selectVersioningFields($fromTable, $selectFields);
				$doVersioning = TRUE;
			} catch (Exception $e) {
				$doVersioning = FALSE;
			}
		}

		// Add base fields (uid, pid) if translations or versioning are activated
		if ($doOverlays || $doVersioning) {
			try {
				$selectFields = self::selectBaseFields($fromTable, $selectFields);
			} catch (Exception $e) {
				// Neither translations nor versioning can happen without uid and pid
				$doOverlays = FALSE;
				$doVersioning = FALSE;
			}
		}

		// Execute the query itself
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows($selectFields, $fromTable, $where, $groupBy, $orderBy, $limit);
		// Perform version overlays, if needed
		if ($doVersioning) {
			$numRecords = count($records);
			for ($i = 0; $i < $numRecords; $i++) {
				$GLOBALS['TSFE']->sys_page->versionOL($fromTable, $records[$i]);
				// The versioned record may actually be FALSE if it is meant to be deleted
				// in the workspace. To be really clean, unset it.
				if ($records[$i] === FALSE) {
					unset($records[$i]);
				}
			}
		}

		// If we have both a uid and a pid field, we can proceed with overlaying the records
		if ($doOverlays) {
			$records = self::overlayRecordSet($fromTable, $records, $GLOBALS['TSFE']->sys_language_content, $GLOBALS['TSFE']->sys_language_contentOL, $doVersioning);
		}
		return $records;
	}

	/**
	 * This method gets the SQL condition to apply for fetching the proper language
	 * depending on the localization settings in the TCA
	 *
	 * @param    string        $table: (true) name of the table to assemble the condition for
	 * @param    string        $alias: alias to use for the table instead of its true name
	 * @return    string        SQL to add to the WHERE clause (without "AND")
	 */
	public static function getLanguageCondition($table, $alias = '') {
		$languageCondition = '';
		if (empty($alias)) {
			$alias = $table;
		}

		// First check if there's actually a TCA for the given table
		if (isset($GLOBALS['TCA'][$table]['ctrl'])) {
			$tableCtrlTCA = $GLOBALS['TCA'][$table]['ctrl'];

			// Assemble language condition only if a language field is defined
			if (!empty($tableCtrlTCA['languageField'])) {
				if (isset($GLOBALS['TSFE']->sys_language_contentOL) && isset($tableCtrlTCA['transOrigPointerField'])) {
					// Default language and "all" language
					$languageCondition = $alias . '.' . $tableCtrlTCA['languageField'] . ' IN (0,-1)';

					// If current language is not default, select elements that exist only for current language
					// That means elements that exist for current language but have no parent element
					if ($GLOBALS['TSFE']->sys_language_content > 0) {
						$languageCondition .= ' OR (' . $alias . '.' . $tableCtrlTCA['languageField'] . " = '" . $GLOBALS['TSFE']->sys_language_content . "' AND " . $alias . '.' . $tableCtrlTCA['transOrigPointerField'] . " = '0')";
					}
				} else {
					$languageCondition = $alias . '.' . $tableCtrlTCA['languageField'] . " = '" . $GLOBALS['TSFE']->sys_language_content . "'";
				}
			}
		}
		return $languageCondition;
	}

	/**
	 * This method returns the condition on enable fields for the given table
	 * Basically it calls on the method provided by t3lib_page, but without the " AND " in front
	 *
	 * @param    string        $table: name of the table to build the condition for
	 * @param    boolean        $showHidden: set to TRUE to force the display of hidden records
	 * @param    array        $ignoreArray: use keys like "disabled", "starttime", "endtime", "fe_group" (i.e. keys from "enablefields" in TCA) and set values to TRUE to exclude corresponding conditions from WHERE clause
	 * @return    string        SQL to add to the WHERE clause (without "AND")
	 */
	public static function getEnableFieldsCondition($table, $showHidden = FALSE, $ignoreArray = array()) {
		$enableCondition = '';
		// First check if table has a TCA ctrl section, otherwise t3lib_page::enableFields() will die() (stupid thing!)
		// NOTE: since TYPO3 4.5, an exception is thrown, so this method could eventually be adapted
		if (isset($GLOBALS['TCA'][$table]['ctrl'])) {

			if (TYPO3_MODE == 'FE') {
				$showHidden = $showHidden ? $showHidden : ($table == 'pages' ? $GLOBALS['TSFE']->showHiddenPage : $GLOBALS['TSFE']->showHiddenRecords);
				$enableCondition = $GLOBALS['TSFE']->sys_page->enableFields($table, $showHidden, $ignoreArray);
			} elseif (TYPO3_MODE == 'BE') {
				$enableCondition = \TYPO3\CMS\Backend\Utility\BackendUtility::BEenableFields($table);
				// @fix: overlays patch for the need of Media, we don't want deleted file displayed in the BE.
				$enableCondition .= ' AND deleted = 0 ';
			}
			// If an enable clause was returned, strip the first ' AND '
			if (!empty($enableCondition)) {
				$enableCondition = substr($enableCondition, strlen(' AND '));
			}
		}
		// TODO: throw an exception if the given table has no TCA? (t3lib_page::enableFields() used a die)
		return $enableCondition;
	}

	/**
	 * This method is used to assemble the proper condition with regards to versioning/workspaces
	 * However it is now deprecated and getVersioningCondition should be used instead
	 *
	 * @param array $table Name of the table to get the condition for
	 * @return string The SQL condition
	 * @deprecated Use self::getVersioningCondition() instead. Kept for backwards-compatibility but may be removed in the future.
	 */
	public static function getWorkspaceCondition($table) {
		t3lib_div::logDeprecatedFunction();
		return self::getVersioningCondition($table);
	}

	/**
	 * This method assembles the proper condition with regards to versioning/workspaces
	 * Explanations on parameter $getOverlaysDirectly:
	 * -----------------------------------------------
	 * The base condition assembled by this method will get the placeholders for new or modified records.
	 * This is normally the right way to do things, since those records are overlaid with their workspace version afterwards.
	 * However if you want this condition as part of a more complicated query implying JOINs,
	 * selecting placeholders will not work as the relationships are normally built with the version overlays
	 * and not the placeholders. In this case it is desirable to select the overlays directly,
	 * which can be achieved by setting $getOverlaysDirectly to TRUE
	 * NOTE: if this all sounds like gibberish, try reading more about workspaces in "Core API"
	 * (there's also quite some stuff in "Inside TYPO3", but part of it is badly outdated)
	 *
	 * @param    string        $table: (true) name of the table to build the condition for
	 * @param    string        $alias: alias to use for the table instead of its true name
	 * @param    boolean        $getOverlaysDirectly: flag to choose original/placeholder records or overlays (see explanations above)
	 * @return    string        SQL to add to the WHERE clause (without "AND")
	 */
	public static function getVersioningCondition($table, $alias = '', $getOverlaysDirectly = FALSE) {
		$workspaceCondition = '';
		if (empty($alias)) {
			$alias = $table;
		}

		// If the table has some TCA definition, check workspace handling
		if (isset($GLOBALS['TCA'][$table]['ctrl']) && !empty($GLOBALS['TCA'][$table]['ctrl']['versioningWS'])) {

			// Base condition to get only live records
			// (they get overlaid afterwards in case of preview)
			$workspaceCondition .= '(' . $alias . '.t3ver_state <= 0 AND ' . $alias . '.t3ver_oid = 0)';

			// Additional conditions when previewing a workspace
			if ($GLOBALS['TSFE']->sys_page->versioningPreview) {
				$workspace = intval($GLOBALS['BE_USER']->workspace);
				// Condition for records that are unmodified but whose parent was modified
				// (when a parent record is modified, copies of its children are made that refer to the modified parent)
				$workspaceCondition .= ' OR (' . $alias . '.t3ver_state = 0 AND ' . $alias . '.t3ver_wsid = ' . $workspace . ')';
				// Choose the version state of records to select based on the $getOverlaysDirectly flag
				// (see explanations in the phpDoc comment above)
				$modificationPlaceholderState = 1;
				$movePlaceholderState = 3;
				if ($getOverlaysDirectly) {
					$modificationPlaceholderState = -1;
					$movePlaceholderState = 4;
				}
				// Select new records (which exist only in the workspace)
				// This is achieved by selecting the placeholders, which will be overlaid
				// with the actual content later when calling t3lib_page::versionOL()
				$workspaceCondition .= ' OR (' . $alias . '.t3ver_state = ' . $modificationPlaceholderState . ' AND ' . $alias . '.t3ver_wsid = ' . $workspace . ')';
				// Move-to placeholder
				$workspaceCondition .= ' OR (' . $alias . '.t3ver_state = ' . $movePlaceholderState . ' AND ' . $alias . '.t3ver_wsid = ' . $workspace . ')';
			}
		}
		return $workspaceCondition;
	}

	/**
	 * This method gets all fields for a given table and stores that list
	 * into an internal cache array
	 * It then returns the list of fields
	 *
	 * @param    string    $table: name of the table to fetch the fields for
	 * @return    array    List of fields for the given table
	 */
	public static function getAllFieldsForTable($table) {
		if (!isset(self::$tableFields[$table])) {
			self::$tableFields[$table] = $GLOBALS['TYPO3_DB']->admin_get_fields($table);
		}
		return self::$tableFields[$table];
	}

	/**
	 * This method makes sure that base fields such as uid and pid are included
	 * in the list of selected fields. These fields are absolutely necessary when
	 * translations or versioning overlays are being made.
	 * The method throws an exception if the fields are not available.
	 *
	 * @param string $table Table from which to select. This is what comes right after "FROM ...". Required value.
	 * @param string $selectFields List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @throws Exception
	 * @return string The modified SELECT string
	 */
	public static function selectBaseFields($table, $selectFields) {
		$select = $selectFields;
		// Don't bother if all fields are selected anyway
		if ($selectFields != '*') {
			$hasUidField = FALSE;
			$hasPidField = FALSE;
			// Get the list of fields for the given table
			$tableFields = self::getAllFieldsForTable($table);

			// Add the fields, if available
			// NOTE: this may add the fields twice if they are already
			// in the list of selected fields, but that doesn't hurt
			// It doesn't seem worth making a very precise parsing of the list
			// of selected fields just to avoid duplicates
			if (isset($tableFields['uid'])) {
				$select .= ', ' . $table . '.uid';
				$hasUidField = TRUE;
			}
			if (isset($tableFields['pid'])) {
				$select .= ', ' . $table . '.pid';
				$hasPidField = TRUE;
			}
			// If one of the fields is still missing after that, throw an exception
			if ($hasUidField === FALSE || $hasPidField === FALSE) {
				throw new Exception('Not all base fields are available.', 1284463019);
			}
		}
		return $select;
	}

	/**
	 * This method makes sure that all the fields necessary for proper overlaying are included
	 * in the list of selected fields and exist in the table being queried
	 * If not, it lets the exception thrown by tx_context::selectOverlayFieldsArray() bubble up
	 *
	 * @param    string        $table: Table from which to select. This is what comes right after "FROM ...". Required value.
	 * @param    string        $selectFields: List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @return    string        Possibly modified list of fields to select
	 */
	public static function selectOverlayFields($table, $selectFields) {
		$additionalFields = self::selectOverlayFieldsArray($table, $selectFields);
		if (count($additionalFields) > 0) {
			foreach ($additionalFields as $aField) {
				$selectFields .= ', ' . $table . '.' . $aField;
			}
		}
		return $selectFields;
	}

	/**
	 * This method checks which fields need to be added to the given list of SELECTed fields
	 * so that language overlays can take place properly
	 * If some information is missing, it throws an exception
	 *
	 * @param string $table Table from which to select. This is what comes right after "FROM ...". Required value.
	 * @param string $selectFields List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @throws Exception
	 * @return array List of fields to add
	 */
	public static function selectOverlayFieldsArray($table, $selectFields) {
		$additionalFields = array();

		// If all fields are selected anyway, no need to worry
		if ($selectFields != '*') {
			// Check if the table indeed has a TCA
			if (isset($GLOBALS['TCA'][$table]['ctrl'])) {

				// Continue only if table is not using foreign tables for translations
				// (in this case no additional field is needed) and has a language field
				if (empty($GLOBALS['TCA'][$table]['ctrl']['transForeignTable']) && !empty($GLOBALS['TCA'][$table]['ctrl']['languageField'])) {
					$languageField = $GLOBALS['TCA'][$table]['ctrl']['languageField'];

					// In order to be properly overlaid, a table has to have a given languageField
					$hasLanguageField = strpos($selectFields, $languageField);
					if ($hasLanguageField === FALSE) {
						// Get the list of fields for the given table
						$tableFields = self::getAllFieldsForTable($table);
						if (isset($tableFields[$languageField])) {
							$additionalFields[] = $languageField;
							$hasLanguageField = TRUE;
						}
					}
					// If language field is still missing after that, throw an exception
					if ($hasLanguageField === FALSE) {
						throw new Exception('Language field not available.', 1284463837);
					}
				}

				// The table has no TCA, throw an exception
			} else {
				throw new Exception('No TCA for table, cannot add overlay fields.', 1284474025);
			}
		}
		return $additionalFields;
	}

	/**
	 * This method makes sure that all the fields necessary for proper versioning overlays are included
	 * in the list of selected fields and exist in the table being queried
	 * If not, it lets the exception thrown by tx_context::selectVersioningFieldsArray() bubble up
	 *
	 * @param    string        $table: Table from which to select. This is what comes right after "FROM ...". Required value.
	 * @param    string        $selectFields: List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @return    string        Possibly modified list of fields to select
	 */
	public static function selectVersioningFields($table, $selectFields) {
		$additionalFields = self::selectVersioningFieldsArray($table, $selectFields);
		if (count($additionalFields) > 0) {
			foreach ($additionalFields as $aField) {
				$selectFields .= ', ' . $table . '.' . $aField;
			}
		}
		return $selectFields;
	}

	/**
	 * This method checks which fields need to be added to the given list of SELECTed fields
	 * so that versioning overlays can take place properly
	 * If some information is missing, it throws an exception
	 *
	 * @param string $table Table from which to select. This is what comes right after "FROM ...". Required value.
	 * @param string $selectFields List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @throws Exception
	 * @return array List of fields to add
	 */
	public static function selectVersioningFieldsArray($table, $selectFields) {
		$additionalFields = array();

		// If all fields are selected anyway, no need to worry
		if ($selectFields != '*') {
			// Check if the table indeed has a TCA and versioning information
			if (isset($GLOBALS['TCA'][$table]['ctrl']) && !empty($GLOBALS['TCA'][$table]['ctrl']['versioningWS'])) {

				// In order for versioning to work properly, the version state field is needed
				$stateField = 't3ver_state';
				$hasStateField = strpos($selectFields, $stateField);
				if ($hasStateField === FALSE) {
					// Get the list of fields for the given table
					$tableFields = self::getAllFieldsForTable($table);
					if (isset($tableFields[$stateField])) {
						$additionalFields[] = $stateField;
						$hasStateField = TRUE;
					}
				}
				// If state field is still missing after that, throw an exception
				if ($hasStateField === FALSE) {
					throw new Exception('Fields for versioning were not all available.', 1284473941);
				}

				// The table has no TCA, throw an exception
			} else {
				throw new Exception('No TCA for table, cannot add versioning fields.', 1284474016);
			}
		}
		return $additionalFields;
	}

	/**
	 * Creates language-overlay for records in general (where translation is found in records from the same table)
	 * This is originally copied from t3lib_page::getRecordOverlay()
	 *
	 * @param    string        $table: Table name
	 * @param    array        $recordset: Full recordset to overlay. Must containt uid, pid and $TCA[$table]['ctrl']['languageField']
	 * @param    integer        $currentLanguage: Uid of the currently selected language in the FE
	 * @param    string        $overlayMode: Overlay mode. If "hideNonTranslated" then records without translation will not be returned un-translated but removed instead.
	 * @param    boolean        $doVersioning: true if workspace preview is on
	 * @return    array        Returns the full overlaid recordset. If $overlayMode is "hideNonTranslated" then some records may be missing if no translation was found.
	 */
	public static function overlayRecordSet($table, $recordset, $currentLanguage, $overlayMode = '', $doVersioning = FALSE) {

		// Test with the first row if uid and pid fields are present
		if (!empty($recordset[0]['uid']) && !empty($recordset[0]['pid'])) {

			// Test if the table has a TCA definition
			if (isset($GLOBALS['TCA'][$table])) {
				$tableCtrl = $GLOBALS['TCA'][$table]['ctrl'];

				// Test if the TCA definition includes translation information for the same table
				if (isset($tableCtrl['languageField']) && isset($tableCtrl['transOrigPointerField'])) {

					// Test with the first row if languageField is present
					if (isset($recordset[0][$tableCtrl['languageField']])) {

						// Filter out records that are not in the default or [ALL] language, should there be any
						$filteredRecordset = array();
						foreach ($recordset as $row) {
							if ($row[$tableCtrl['languageField']] <= 0) {
								$filteredRecordset[] = $row;
							}
						}
						// Will try to overlay a record only if the sys_language_content value is larger than zero,
						// that is, it is not default or [ALL] language
						if ($currentLanguage > 0) {
							// Assemble a list of uid's for getting the overlays,
							// but only from the filtered recordset
							$uidList = array();
							foreach ($filteredRecordset as $row) {
								$uidList[] = $row['uid'];
							}

							// Get all overlay records
							$overlays = self::getLocalOverlayRecords($table, $uidList, $currentLanguage, $doVersioning);

							// Now loop on the filtered recordset and try to overlay each record
							$overlaidRecordset = array();
							foreach ($recordset as $row) {
								// If record is already in the right language, keep it as is
								if ($row[$tableCtrl['languageField']] == $currentLanguage) {
									$overlaidRecordset[] = $row;

									// Else try to apply an overlay
								} elseif (isset($overlays[$row['uid']][$row['pid']])) {
									$overlaidRecordset[] = self::overlaySingleRecord($table, $row, $overlays[$row['uid']][$row['pid']]);

									// No overlay exists, apply relevant translation rules
								} else {
									// Take original record, only if non-translated are not hidden, or if language is [All]
									if ($overlayMode != 'hideNonTranslated' || $row[$tableCtrl['languageField']] == -1) {
										$overlaidRecordset[] = $row;
									}
								}
							}
							// Return the overlaid recordset
							return $overlaidRecordset;

						} else {
							// When default language is displayed, we never want to return a record carrying another language!
							// Return the filtered recordset
							return $filteredRecordset;
						}

						// Provided recordset does not contain languageField field, return recordset unchanged
					} else {
						return $recordset;
					}

					// Test if the TCA definition includes translation information for a foreign table
				} elseif (isset($tableCtrl['transForeignTable'])) {
					// The foreign table has a TCA structure. We can proceed.
					if (isset($GLOBALS['TCA'][$tableCtrl['transForeignTable']])) {
						$foreignCtrl = $GLOBALS['TCA'][$tableCtrl['transForeignTable']]['ctrl'];
						// Check that the foreign table is indeed the appropriate translation table
						// and also check that the foreign table has all the necessary TCA definitions
						if (!empty($foreignCtrl['transOrigPointerTable']) && $foreignCtrl['transOrigPointerTable'] == $table && !empty($foreignCtrl['transOrigPointerField']) && !empty($foreignCtrl['languageField'])) {
							// Assemble a list of all uid's of records to translate
							$uidList = array();
							foreach ($recordset as $row) {
								$uidList[] = $row['uid'];
							}

							// Get all overlay records
							$overlays = self::getForeignOverlayRecords($tableCtrl['transForeignTable'], $uidList, $currentLanguage, $doVersioning);

							// Now loop on the filtered recordset and try to overlay each record
							$overlaidRecordset = array();
							foreach ($recordset as $row) {
								// An overlay exists, apply it
								if (isset($overlays[$row['uid']])) {
									$overlaidRecordset[] = self::overlaySingleRecord($table, $row, $overlays[$row['uid']]);

									// No overlay exists
								} else {
									// Take original record, only if non-translated are not hidden
									if ($overlayMode != 'hideNonTranslated') {
										$overlaidRecordset[] = $row;
									}
								}
							}
							// Return the overlaid recordset
							return $overlaidRecordset;
						}

						// The foreign table has no TCA definition, it's impossible to perform overlays
						// Return recordset as is
					} else {
						return $recordset;
					}

					// No appropriate language fields defined in TCA, return recordset unchanged
				} else {
					return $recordset;
				}

				// No TCA for table, return recordset unchanged
			} else {
				return $recordset;
			}
		} // Recordset did not contain uid or pid field, return recordset unchanged
		else {
			return $recordset;
		}
	}

	/**
	 * This method is a wrapper around getLocalOverlayRecords() and getForeignOverlayRecords().
	 * It makes it possible to use the same call whether translations are in the same table or
	 * in a foreign table. This method dispatches accordingly.
	 *
	 * @param    string        $table: name of the table for which to fetch the records
	 * @param    array        $uids: array of all uid's of the original records for which to fetch the translation
	 * @param    integer        $currentLanguage: uid of the system language to translate to
	 * @param    boolean        $doVersioning: true if versioning overlay must be performed
	 * @return    array        All overlay records arranged per original uid and per pid, so that they can be checked (this is related to workspaces)
	 */
	public static function getOverlayRecords($table, $uids, $currentLanguage, $doVersioning = FALSE) {
		if (is_array($uids) && count($uids) > 0) {
			if (isset($GLOBALS['TCA'][$table]['ctrl']['transForeignTable'])) {
				return self::getForeignOverlayRecords($GLOBALS['TCA'][$table]['ctrl']['transForeignTable'], $uids, $currentLanguage, $doVersioning);
			} else {
				return self::getLocalOverlayRecords($table, $uids, $currentLanguage, $doVersioning);
			}
		} else {
			return array();
		}
	}

	/**
	 * This method is used to retrieve all the records for overlaying other records
	 * when those records are stored in the same table as the originals
	 *
	 * @param    string        $table: name of the table for which to fetch the records
	 * @param    array        $uids: array of all uid's of the original records for which to fetch the translation
	 * @param    integer        $currentLanguage: uid of the system language to translate to
	 * @param    boolean        $doVersioning: true if versioning overlay must be performed
	 * @return    array        All overlay records arranged per original uid and per pid, so that they can be checked (this is related to workspaces)
	 */
	public static function getLocalOverlayRecords($table, $uids, $currentLanguage, $doVersioning = FALSE) {
		$overlays = array();
		if (is_array($uids) && count($uids) > 0) {
			$tableCtrl = $GLOBALS['TCA'][$table]['ctrl'];
			// Select overlays for all records
			$where = $tableCtrl['languageField'] . ' = ' . intval($currentLanguage) .
				' AND ' . $tableCtrl['transOrigPointerField'] . ' IN (' . implode(', ', $uids) . ')';
			$enableCondition = self::getEnableFieldsCondition($table);
			if (!empty($enableCondition)) {
				$where .= ' AND ' . $enableCondition;
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, $where);
			// Arrange overlay records according to transOrigPointerField, so that it's easy to relate them to the originals
			// This structure is actually a 2-dimensional array, with the pid as the second key
			// Because of versioning, there may be several overlays for a given original and matching the pid too
			// ensures that we are referring to the correct overlay
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				// Perform version overlays, if needed
				if ($doVersioning) {
					$GLOBALS['TSFE']->sys_page->versionOL($table, $row);
				}
				// The versioned record may actually be FALSE if it is meant to be deleted
				// in the workspace. To be really clean, unset it.
				if ($row !== FALSE) {
					if (!isset($overlays[$row[$tableCtrl['transOrigPointerField']]])) {
						$overlays[$row[$tableCtrl['transOrigPointerField']]] = array();
					}
					$overlays[$row[$tableCtrl['transOrigPointerField']]][$row['pid']] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $overlays;
	}

	/**
	 * This method is used to retrieve all the records for overlaying other records
	 * when those records are stored in a different table than the originals
	 *
	 * @param    string        $table: name of the table for which to fetch the records
	 * @param    array        $uids: array of all uid's of the original records for which to fetch the translation
	 * @param    integer        $currentLanguage: uid of the system language to translate to
	 * @param    boolean        $doVersioning: true if versioning overlay must be performed
	 * @return    array        All overlay records arranged per original uid and per pid, so that they can be checked (this is related to workspaces)
	 */
	public static function getForeignOverlayRecords($table, $uids, $currentLanguage, $doVersioning = FALSE) {
		$overlays = array();
		if (is_array($uids) && count($uids) > 0) {
			$tableCtrl = $GLOBALS['TCA'][$table]['ctrl'];
			// Select overlays for all records
			$where = $tableCtrl['languageField'] . ' = ' . intval($currentLanguage) .
				' AND ' . $tableCtrl['transOrigPointerField'] . ' IN (' . implode(', ', $uids) . ')';
			$enableCondition = self::getEnableFieldsCondition($table);
			if (!empty($enableCondition)) {
				$where .= ' AND ' . $enableCondition;
			}
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, $where);
			// Arrange overlay records according to transOrigPointerField, so that it's easy to relate them to the originals
			while (($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
				// Perform version overlays, if needed
				if ($doVersioning) {
					$GLOBALS['TSFE']->sys_page->versionOL($table, $row);
				}
				// The versioned record may actually be FALSE if it is meant to be deleted
				// in the workspace. To be really clean, unset it.
				if ($row !== FALSE) {
					$overlays[$row[$tableCtrl['transOrigPointerField']]] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $overlays;
	}

	/**
	 * This method takes a record and its overlay and performs the overlay according to active translation rules
	 * This piece of code is extracted from t3lib_page::getRecordOverlay()
	 *
	 * @param    string    $table: name of the table for which the operation is taking place
	 * @param    array    $record: record to overlay
	 * @param    array    $overlay: overlay of the record
	 * @return    array    Overlaid record
	 */
	public static function overlaySingleRecord($table, $record, $overlay) {
		$overlaidRecord = $record;
		$overlaidRecord['_LOCALIZED_UID'] = $overlay['uid'];
		foreach ($record as $key => $value) {
			if ($key != 'uid' && $key != 'pid' && isset($overlay[$key])) {
				if (isset($GLOBALS['TSFE']->TCAcachedExtras[$table]['l10n_mode'][$key])) {
					if ($GLOBALS['TSFE']->TCAcachedExtras[$table]['l10n_mode'][$key] != 'exclude'
						&& ($GLOBALS['TSFE']->TCAcachedExtras[$table]['l10n_mode'][$key] != 'mergeIfNotBlank' || strcmp(trim($overlay[$key]), ''))
					) {
						$overlaidRecord[$key] = $overlay[$key];
					}
				} else {
					$overlaidRecord[$key] = $overlay[$key];
				}
			}
		}
		return $overlaidRecord;
	}
}
