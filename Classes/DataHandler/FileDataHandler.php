<?php
namespace Fab\Media\DataHandler;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Media\Module\MediaModule;
use TYPO3\CMS\Core\Resource\DuplicationBehavior;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Fab\Vidi\DataHandler\AbstractDataHandler;
use Fab\Vidi\Domain\Model\Content;

/**
 * Special Data Handler for File.
 */
class FileDataHandler extends AbstractDataHandler
{

    /**
     * Process File with action "update".
     *
     * @param Content $content
     * @throws \Exception
     * @return bool
     */
    public function processUpdate(Content $content)
    {
        throw new \Exception('Not yet implemented', 1409988673);
    }

    /**
     * Process File with action "remove".
     *
     * @param Content $content
     * @return bool|void
     */
    public function processRemove(Content $content)
    {
        $file = ResourceFactory::getInstance()->getFileObject($content->getUid());

        $numberOfReferences = $this->getFileReferenceService()->countTotalReferences($file);
        if ($numberOfReferences === 0) {
            $file->delete();
        } else {
            $message = sprintf('I could not delete file "%s" as it is has %s reference(s).', $file->getUid(), $numberOfReferences);
            $this->errorMessages = $message;
        }
    }

    /**
     * Process File with action "copy".
     *
     * @param Content $content
     * @param string $target
     * @throws \Exception
     * @return bool
     */
    public function processCopy(Content $content, $target)
    {
        $file = ResourceFactory::getInstance()->getFileObject($content->getUid());

        if ($this->getMediaModule()->hasFolderTree()) {

            $targetFolder = $this->getMediaModule()->getCurrentFolder();

            // Move file
            $file->copyTo($targetFolder, $file->getName(), DuplicationBehavior::RENAME);
        }
        return true;
    }

    /**
     * Process File with action "move".
     *
     * @param Content $content
     * @param string $target
     * @throws \Exception
     * @return bool
     */
    public function processMove(Content $content, $target)
    {
        $file = ResourceFactory::getInstance()->getFileObject($content->getUid());

        if ($this->getMediaModule()->hasFolderTree()) {

            $targetFolder = $this->getMediaModule()->getCurrentFolder();
            if ($targetFolder->getIdentifier() !== $file->getParentFolder()->getIdentifier()) {

                // Move file
                $file->moveTo($targetFolder, $file->getName(), DuplicationBehavior::RENAME);
            }
        } else {

            // Only process if the storage is different.
            if ((int)$file->getStorage()->getUid() !== (int)$target) {

                $targetStorage = ResourceFactory::getInstance()->getStorageObject((int)$target);

                // Retrieve target directory in the new storage. The folder will only be returned if the User has the correct permission.
                $targetFolder = $this->getMediaModule()->getDefaultFolderInStorage($targetStorage, $file);

                try {
                    // Move file
                    $file->moveTo($targetFolder, $file->getName(), DuplicationBehavior::RENAME);
                } catch (\Exception $e) {
                    $this->errorMessages = $e->getMessage();
                }
            }
        }
        return true;
    }

    /**
     * @return \Fab\Media\Resource\FileReferenceService
     */
    protected function getFileReferenceService()
    {
        return GeneralUtility::makeInstance('Fab\Media\Resource\FileReferenceService');
    }

    /**
     * Process Content with action "localize".
     *
     * @param Content $content
     * @param int $language
     * @throws \Exception
     * @return bool
     */
    public function processLocalize(Content $content, $language)
    {
        throw new \Exception('Nothing to implement here. Localization is done by the Core DataHandler', 1412760788);
    }

    /**
     * @return MediaModule
     */
    protected function getMediaModule()
    {
        return GeneralUtility::makeInstance('Fab\Media\Module\MediaModule');
    }

}
