<?php

namespace Fab\Media\Security;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
use TYPO3\CMS\Extbase\Persistence\Generic\Exception\InvalidNumberOfConstraintsException;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use Fab\Media\Module\MediaModule;
use Fab\Media\Module\VidiModule;
use Fab\Vidi\Module\ModuleLoader;
use Fab\Vidi\Persistence\ConstraintContainer;
use Fab\Vidi\Service\DataService;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Persistence\Query;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;

/**
 * Class which handle signal slot for Vidi Content controller
 */
class FilePermissionsAspect
{
    /**
     * Post-process the matcher object to respect the file storages.
     *
     * @param Matcher $matcher
     * @param string $dataType
     * @return void
     * @throws \InvalidArgumentException
     */
    public function addFilePermissionsForFileStorages(Matcher $matcher, $dataType)
    {
        if ($dataType === 'sys_file' && $this->isPermissionNecessary()) {
            if ($this->isFolderConsidered()) {
                $folder = $this->getMediaModule()->getCurrentFolder();

                if ($this->getMediaModule()->hasRecursiveSelection()) {
                    // Only add like condition if needed.
                    if ($folder->getStorage()->getRootLevelFolder() !== $folder) {
                        $matcher->like('identifier', $folder->getIdentifier() . '%', $automaticallyAddWildCard = false);
                    }
                } else {
                    // Browse only currently
                    $files = $this->getFileUids($folder);
                    $matcher->in('uid', $files);
                }

                $matcher->equals('storage', $folder->getStorage()->getUid());
            } else {
                $storage = $this->getMediaModule()->getCurrentStorage();

                // Set the storage identifier only if the storage is on-line.
                $identifier = -1;
                if ($storage->isOnline()) {
                    $identifier = $storage->getUid();
                }

                if ($this->getModuleLoader()->hasPlugin() && !$this->getCurrentBackendUser()->isAdmin()) {
                    $fileMounts = $this->getCurrentBackendUser()->getFileMountRecords();
                    $collectedFiles = [];
                    foreach ($fileMounts as $fileMount) {
                        $combinedIdentifier = $fileMount['base'] . ':' . $fileMount['path'];
                        $folder = $this->getResourceFactory()->getFolderObjectFromCombinedIdentifier($combinedIdentifier);

                        $files = $this->getFileUids($folder);
                        $collectedFiles = array_merge($collectedFiles, $files);
                    }

                    $matcher->in('uid', $collectedFiles);
                }

                $matcher->equals('storage', $identifier);
            }
        }
    }

    /**
     * @return bool
     */
    protected function isPermissionNecessary()
    {
        $isNecessary = true;

        $parameters = GeneralUtility::_GET(VidiModule::getParameterPrefix());

        if ($parameters['controller'] === 'Clipboard' && ($parameters['action'] === 'show' || $parameters['action'] === 'flush')) {
            $isNecessary = false;
        }

        if ($parameters['controller'] === 'Content' && ($parameters['action'] === 'copyClipboard' || $parameters['action'] === 'moveClipboard')) {
            $isNecessary = false;
        }

        return $isNecessary;
    }

    /**
     * @return bool
     */
    protected function isFolderConsidered()
    {
        return $this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin();
    }

    /**
     * @param Folder $folder
     * @return array
     */
    protected function getFileUids(Folder $folder)
    {
        $files = [];
        foreach ($folder->getFiles() as $file) {
            $files[] = $file->getUid();
        }
        return $files;
    }

    /**
     * Post-process the constraints object to respect the file mounts.
     *
     * @param Query $query
     * @param ConstraintInterface|null $constraints
     * @param ConstraintContainer $constraintContainer
     * @throws \InvalidArgumentException
     * @throws InvalidNumberOfConstraintsException
     */
    public function addFilePermissionsForFileMounts(Query $query, $constraints, ConstraintContainer $constraintContainer)
    {
        if ($query->getType() === 'sys_file') {
            if (!$this->getCurrentBackendUser()->isAdmin()) {
                $this->respectFileMounts($query, $constraints, $constraintContainer);
            }
        }
    }

    /**
     * @param Query $query
     * @param ConstraintInterface|null $constraints
     * @param ConstraintContainer $constraintContainer
     * @return array
     * @throws \InvalidArgumentException
     * @throws InvalidNumberOfConstraintsException
     */
    protected function respectFileMounts(Query $query, $constraints, ConstraintContainer $constraintContainer)
    {
        // Get the file mount identifiers for the current Backend User.
        $fileMountRecords = $this->getCurrentBackendUser()->getFileMountRecords();
        $constraintsRespectingFileMounts = [];
        foreach ((array)$fileMountRecords as $fileMountRecord) {
            if ($fileMountRecord['path']) {
                $constraintsRespectingFileMounts[] = $query->like(
                    'identifier',
                    $fileMountRecord['path'] . '%'
                );
            }
        }

        $logicalOrForRespectingFileMounts = $query->logicalOr($constraintsRespectingFileMounts);

        if ($constraints) {
            $constraints = $query->logicalAnd([$constraints, $logicalOrForRespectingFileMounts]);
        } else {
            $constraints = $logicalOrForRespectingFileMounts;
        }

        $constraintContainer->setConstraint($constraints);

        return [$query, $constraints, $constraintContainer];
    }

    /**
     * @return BackendUserAuthentication
     */
    protected function getCurrentBackendUser()
    {
        return $GLOBALS['BE_USER'];
    }

    /**
     * @return object|DataService
     */
    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    /**
     * @return MediaModule|object
     * @throws \InvalidArgumentException
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }

    /**
     * Get the Vidi Module Loader.
     *
     * @return object|ModuleLoader
     */
    protected function getModuleLoader()
    {
        return GeneralUtility::makeInstance(ModuleLoader::class);
    }

    protected function getResourceFactory(): ResourceFactory
    {
        return GeneralUtility::makeInstance(ResourceFactory::class);
    }
}
