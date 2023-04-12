<?php

namespace Fab\Media\Security;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */
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
     */
    public function addFilePermissionsForFileStorages(Matcher $matcher, string $dataType): void
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

    protected function isPermissionNecessary(): bool
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

    protected function isFolderConsidered(): bool
    {
        return $this->getMediaModule()->hasFolderTree() && !$this->getModuleLoader()->hasPlugin();
    }

    protected function getFileUids(Folder $folder): array
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
     * @param ConstraintInterface|null $constraints
     */
    public function addFilePermissionsForFileMounts(Query $query, $constraints, ConstraintContainer $constraintContainer): void
    {
        if ($query->getType() === 'sys_file') {
            if (!$this->getCurrentBackendUser()->isAdmin()) {
                $this->respectFileMounts($query, $constraints, $constraintContainer);
            }
        }
    }

    /**
     * @param ConstraintInterface|null $constraints
     */
    protected function respectFileMounts(Query $query, $constraints, ConstraintContainer $constraintContainer): array
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

    protected function getCurrentBackendUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function getDataService(): DataService
    {
        return GeneralUtility::makeInstance(DataService::class);
    }

    protected function getMediaModule(): MediaModule
    {
        return GeneralUtility::makeInstance(MediaModule::class);
    }

    protected function getModuleLoader(): ModuleLoader
    {
        return GeneralUtility::makeInstance(ModuleLoader::class);
    }

    protected function getResourceFactory(): ResourceFactory
    {
        return GeneralUtility::makeInstance(ResourceFactory::class);
    }
}
