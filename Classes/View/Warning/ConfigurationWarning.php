<?php
namespace Fab\Media\View\Warning;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use Fab\Media\Cache\CacheService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Tca\Tca;
use Fab\Vidi\View\AbstractComponentView;

/**
 * View which renders a button for uploading assets.
 */
class ConfigurationWarning extends AbstractComponentView
{

    /**
     * @var array
     */
    protected $notAllowedMountPoints = [];

    /**
     * Renders a button for uploading assets.
     *
     * @return string
     */
    public function render()
    {

        $result = '';

        // Check whether storage is configured or not.
        if ($this->checkStorageNotConfigured()) {
            $this->configureStorage();
            $result .= $this->formatMessageForStorageConfigured();
        }

        // Check whether storage is online or not.
        if ($this->checkStorageOffline()) {
            $result .= $this->formatMessageForStorageOffline();
        }

        // Check all mount points of the storage are available
        if (!$this->checkMountPoints()) {
            $result .= $this->formatMessageForMountPoints();
        }

        // Check all mount points of the storage are available
        if (!$this->hasBeenWarmedUp() && !$this->checkColumnNumberOfReferences()) {
            if ($this->canBeInitializedSilently() < 2000) {
                $numberOfFiles = $this->getCacheService()->warmUp();
                $result .= $this->formatMessageForSilentlyUpdatedColumnNumberOfReferences($numberOfFiles);
                touch($this->getWarmUpSemaphoreFile());
            } else {
                $result .= $this->formatMessageForUpdateRequiredColumnNumberOfReferences();
            }
        }

        return $result;
    }

    /**
     * @return CacheService|object
     */
    protected function getCacheService()
    {
        return GeneralUtility::makeInstance(CacheService::class);
    }

    protected function configureStorage()
    {
        $tableName = 'sys_file_storage';
        $fields = array(
            'maximum_dimension_original_image',
            'extension_allowed_file_type_1',
            'extension_allowed_file_type_2',
            'extension_allowed_file_type_3',
            'extension_allowed_file_type_4',
            'extension_allowed_file_type_5',
        );

        $values = [];
        foreach ($fields as $field) {
            $values[$field] = Tca::table($tableName)->field($field)->getDefaultValue();
        }

        /** @var ConnectionPool $connectionPool */
        $storage = $this->getMediaModule()->getCurrentStorage();
        $connection = GeneralUtility::makeInstance(ConnectionPool::class);
        $connection->getConnectionForTable($tableName)->update(
            $tableName,
            $values,
            [ 'uid' => $storage->getUid() ]
        );
    }

    /**
     * @return bool
     */
    protected function hasBeenWarmedUp()
    {
        return is_file(($this->getWarmUpSemaphoreFile()));
    }

    /**
     * @return string
     */
    protected function getWarmUpSemaphoreFile()
    {
        return Environment::getPublicPath() . '/typo3temp/.media_cache_warmed_up';
    }

    /**
     * Check whether the storage is correctly configured.
     *
     * @return boolean
     */
    protected function checkStorageNotConfigured()
    {
        $currentStorage = $this->getMediaModule()->getCurrentStorage();
        $storageRecord = $currentStorage->getStorageRecord();

        // Take the storage fields and check whether some data was initialized.
        $fields = array(
            'extension_allowed_file_type_1',
            'extension_allowed_file_type_2',
            'extension_allowed_file_type_3',
            'extension_allowed_file_type_4',
            'extension_allowed_file_type_5',
        );

        $result = true;
        foreach ($fields as $fieldName) {
            // true means the storage has data and thus was configured / saved once.
            if (!empty($storageRecord[$fieldName])) {
                $result = false;
                break;
            }
        }
        return $result;
    }

    /**
     * Format a message whenever the storage is offline.
     *
     * @return string
     */
    protected function formatMessageForStorageConfigured()
    {
        $storage = $this->getMediaModule()->getCurrentStorage();

        $result = <<< EOF
			<div class="alert alert-info">
				<div class="alert-title">
						Storage has been configured.
				</div>
				<div class="alert-message">
					The storage "{$storage->getName()}" was not configured for Media. Some default values have automatically been added.
					To see those values, open the storage record "{$storage->getName()}" ({$storage->getUid()})
					and check under tab "Upload Settings" or "Default mount points".
				</div>
			</div>
EOF;

        return $result;
    }

    /**
     * Check whether the storage is online or not.
     *
     * @return boolean
     */
    protected function checkStorageOffline()
    {
        return !$this->getMediaModule()->getCurrentStorage()->isOnline();
    }

    /**
     * Format a message whenever the storage is offline.
     *
     * @return string
     */
    protected function formatMessageForStorageOffline()
    {
        $storage = $this->getMediaModule()->getCurrentStorage();

        $result = <<< EOF
			<div class="alert alert-warning">
					<div class="alert-title">
						Storage is currently offline
				</div>
					<div class="alert-message">
						The storage "{$storage->getName()}" looks currently to be off-line. Contact an administrator if you think this is an error.
					</div>
				</div>
			</div>
EOF;

        return $result;
    }

    /**
     * Check whether mount points privilege are ok.
     *
     * @return boolean
     */
    protected function checkMountPoints()
    {
        if (!$this->getBackendUser()->isAdmin()) {

            $fileMounts = $this->getBackendUser()->getFileMountRecords();

            $fileMountIdentifiers = [];
            foreach ($fileMounts as $fileMount) {
                $fileMountIdentifiers[] = $fileMount['uid'];
            }

            $storage = $this->getMediaModule()->getCurrentStorage();
            $storageRecord = $storage->getStorageRecord();
            $fieldNames = array(
                'mount_point_file_type_1',
                'mount_point_file_type_2',
                'mount_point_file_type_3',
                'mount_point_file_type_4',
                'mount_point_file_type_5',
            );
            foreach ($fieldNames as $fileName) {
                $fileMountIdentifier = (int)$storageRecord[$fileName];
                if ($fileMountIdentifier > 0 && !in_array($fileMountIdentifier, $fileMountIdentifiers)) {
                    $this->notAllowedMountPoints[] = $this->fetchMountPoint($fileMountIdentifier);
                } else {
                    # $fileMountIdentifier
                    $folder = $storage->getRootLevelFolder();
                }
            }
        }
        return empty($this->notAllowedMountPoints);
    }

    /**
     * Return a mount point according to an file mount identifier.
     *
     * @param string $identifier
     * @return array
     */
    protected function fetchMountPoint($identifier)
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder('sys_filemounts');
        return $queryBuilder
            ->select('*')
            ->from('sys_filemounts')
            ->where('uid = ' . $identifier)
            ->execute()
            ->fetchAssociative();
    }

    /**
     * Format a message whenever mount points privilege are not OK.
     *
     * @return string
     */
    protected function formatMessageForMountPoints()
    {

        $storage = $this->getMediaModule()->getCurrentStorage();
        $backendUser = $this->getBackendUser();

        foreach ($this->notAllowedMountPoints as $notAllowedMountPoints) {
            $list = sprintf('<li>"%s" with path %s</li>',
                $notAllowedMountPoints['title'],
                $notAllowedMountPoints['path']
            );

        }

        $result = <<< EOF
			<div class="alert alert-warning">
					<div class="alert-title">
						File mount are wrongly configured for user "{$backendUser->user['username']}".
				</div>
					<div class="alert-message">
						User "{$backendUser->user['username']}" has no access to the following mount point configured in storage "{$storage->getName()}":
						<ul>
						{$list}
						</ul>
					</div>
				</div>
			</div>
EOF;

        return $result;
    }

    /**
     * @return boolean
     */
    protected function canBeInitializedSilently()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder('sys_file');
        $count = $queryBuilder
            ->count('*')
            ->from('sys_file')
            ->execute()
            ->fetchColumn(0);
        return (int)$count;

    }

    /**
     * Check whether the column "total_of_references" has been already processed once.
     *
     * @return boolean
     */
    protected function checkColumnNumberOfReferences()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getQueryBuilder('sys_file');
        $file = $queryBuilder
            ->select('*')
            ->from('sys_file')
            ->where('number_of_references > 0')
            ->execute()
            ->fetchAssociative();

        return !empty($file);
    }

    /**
     * Format a message if columns "total_of_references" looks wrong.
     *
     * @param int $numberOfFile
     * @return string
     */
    protected function formatMessageForSilentlyUpdatedColumnNumberOfReferences($numberOfFile)
    {

        $result = <<< EOF
			<div class="alert alert-success">
				<div class="alert-title">
						Initialized column "number_of_references" for ${numberOfFile} files
				</div>
				<div class="alert-message">
					The column "sys_file.number_of_references" is used as a caching column for storing the total number of usage of a file.
					It is required when searching files by "usage" in the visual search bar. For example searching for files with 0 usage,
					corresponds to file that are not used on the Frontend,
					The column can be initialized manually  <strong>by opening the tool "Cache warm up" in in the upper right button of this module</strong>
					or by a scheduler task.
					The number of usage is then updated by a Hook each time a record is edited which contains file references coming from "sys_file_reference" or from "sys_refindex" if soft
					reference.
				</div>
			</div>
EOF;

        return $result;
    }


    /**
     * Format a message if columns "total_of_references" looks wrong.
     *
     * @return string
     */
    protected function formatMessageForUpdateRequiredColumnNumberOfReferences()
    {

        $result = <<< EOF
			<div class="alert alert-warning">
				<div class="alert-title">
						Column "number_of_references" requires to be initialized.
				</div>
				<div class="alert-message">
				    This action can not be done automatically as there are more than 2000 files. <br/>

					The column "number_of_references" in "sys_file" is used as a caching column for storing the total number of usage of a file.
					It is required when searching files by "usage" in the visual search bar. For example searching for files with 0 usage,
					corresponds to file that are not used on the Frontend,
					The column can be initialized <strong>by opening the tool "Cache warm up" in in the upper right button of this module</strong>
					or by a scheduler task.
					The number of usage is then updated by a Hook each time a record is edited which contains file references coming from "sys_file_reference" or from "sys_refindex" if soft
					reference.
				</div>
			</div>
EOF;

        return $result;
    }

    /**
     * Returns an instance of the current Backend User.
     *
     * @return BackendUserAuthentication
     */
    protected function getBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @param string $tableName
     * @return object|QueryBuilder
     */
    protected function getQueryBuilder($tableName): QueryBuilder
    {
        /** @var ConnectionPool $connectionPool */
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        return $connectionPool->getQueryBuilderForTable($tableName);
    }

    /**
     * @return MediaModule|object
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }

}
