<?php
/**
 * @todo Finish me! This script is a starting point and needs to be adapted for FAL.
 * @param $className
 * @param $pObj
 */
function user_secure_download($className, $pObj) {

	/** @var $database \TYPO3\CMS\Core\Database\DatabaseConnection */
	$database = $GLOBALS['TYPO3_DB'];

	// check is done only if the resource comes from Media
	// @todo check the mount point handled by Media
	if (preg_match('/fileadmin\/user_upload\/resources\//is', $pObj->file)) {

		$fileName = str_replace(dirname($pObj->file) . '/', '', $pObj->file);

		// Fetch the records from the database
		// @todo use the File Factory
		$records = $database->exec_SELECTgetRows('fe_group', 'sys_file', 'deleted = 0 AND hidden = 0 AND name = "' . $fileName . '"');

		// We have got a problem!
		if (empty($records)) {
			die("Resource does not exist.");
		} else {
			if ($records[0]['fe_group'] != '' && $records[0]['fe_group'] != 0) {
				$hasAccess = FALSE;
				$feUser = $pObj->feUserObj->user;
				// Makes sure has been logged in
				if ($feUser && $feUser['usergroup'] != '') {
					$permissions = explode(',', $records[0]['fe_group']);
					$groups = explode(',', $feUser['usergroup']);

					// Check whether the user has access to the resource
					foreach ($groups as $group) {
						if (in_array($group, $permissions)) {
							$hasAccess = TRUE;
							break;
						}
					}
				}

				// Not access
				if (!$hasAccess) {
					die("Accessing the resource is forbidden!");
				}
			}
		} // end else
	} // end if
}

?>