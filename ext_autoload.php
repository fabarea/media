<?php
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('extbase');
$extensionClassesPath = $extensionPath . 'Classes/';
return array(
	'Tx_Media_Controller_MediaController' => $extensionClassesPath . 'Controller/MediaController.php',
);
?>