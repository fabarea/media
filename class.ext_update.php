<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2006 Rene Fritz (r.fritz@colorcube.de)
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
 * Class for updating the db
 *
 * @author	 Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage tx_media
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		if (!t3lib_div::_GP('do_update'))	{
			$onClick = "document.location.href='".t3lib_div::linkThisScript(array('do_update'=>1))."'; return false;";

			return 'Do you want to perform the database update now?

				<form action=""><input type="submit" value="DO IT" onclick="'.htmlspecialchars($onClick).'"></form>
			';
		} else {
				
			return $this->perform_update();
		}
	}

	/**
	 * Checks how many rows are found and returns true if there are any
	 *
	 * @return	boolean
	 */
	function access()	{
		
		
			// Just do the upgrade without asking
		$this->perform_update();
		return false;
		
		
		//-------------------------
		
		$doit = false;
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*)', 'pages', 'module='.$GLOBALS['TYPO3_DB']->fullQuoteStr('media', 'pages').' AND doktype<254');
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);
		$doit = $row[0] ? true : $doit;
		
		$res = $GLOBALS['TYPO3_DB']->admin_get_fields('tt_content');

		if (isset($res['tx_media_flexform']) && !isset($res['ce_flexform'])) {
			$doit = true;
		}

		if (t3lib_div::compat_version('4.1')) {
			$res = $GLOBALS['TYPO3_DB']->admin_get_fields('tx_media_mm_ref');
			if (!isset($res['sorting_foreign'])) {
				$doit = true;
			} else {
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(sorting_foreign)', 'tx_media_mm_ref', 'sorting_foreign>0');
				if ($rows['0']['count(sorting_foreign)']==0) {
					$doit = true;
				}
			}
		}
		
		return $doit;
	}



	/**
	 * Do the DB update
	 * 
	 * @return	string		HTML
	 */
	function perform_update()	{

		$content = '';
		
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery('pages', 'module='.$GLOBALS['TYPO3_DB']->fullQuoteStr('media', 'pages').'', array('doktype'=>'254'));
		$content .= 'Updated Media folder to be a SysFolder<br />';

		$res = $GLOBALS['TYPO3_DB']->admin_get_fields('tt_content');
		if (isset($res['tx_media_flexform']) && !isset($res['ce_flexform'])) {
			$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tt_content CHANGE tx_media_flexform ce_flexform mediumtext NOT NULL');
			$content .= 'Renamed field tt_content.tx_media_flexform to ce_flexform<br />';
		}	

		if (t3lib_div::compat_version('4.1')) {
			$existingTables=$GLOBALS['TYPO3_DB']->admin_get_tables();
			if(isset($existingTables['tx_media_mm_ref']))	{
				$res = $GLOBALS['TYPO3_DB']->admin_get_fields('tx_media_mm_ref');
				if (isset($res['sorting_foreign'])) {
					# for testing only: $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_media_mm_ref', '', array('sorting_foreign'=>'0'));
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('count(sorting_foreign)', 'tx_media_mm_ref', 'sorting_foreign>0');
					if ($rows['0']['count(sorting_foreign)']==0) {
						$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_media_mm_ref DROP sorting_foreign');
						unset($res['sorting_foreign']);
					}
				}
				
				if (!isset($res['sorting_foreign'])) {
					$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_media_mm_ref CHANGE sorting sorting_foreign int(11) unsigned DEFAULT 0 NOT NULL');
					$GLOBALS['TYPO3_DB']->admin_query('ALTER TABLE tx_media_mm_ref ADD sorting int(11) unsigned DEFAULT 0 NOT NULL');
					$content .= 'Renamed field tx_media_mm_ref.sorting to sorting_foreign<br />';
				}
			}
		}
		
		return $content;
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media/class.ext_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/media/class.ext_update.php']);
}


?>
