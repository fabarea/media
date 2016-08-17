<?php
namespace Fab\Media\FileUpload;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * A interface for optimizing a file upload.
 */
interface ImageOptimizerInterface
{

    /**
     * Optimize the given uploaded image
     *
     * @param \Fab\Media\FileUpload\UploadedFileInterface $uploadedFile
     * @return \Fab\Media\FileUpload\UploadedFileInterface
     */
    public function optimize($uploadedFile);
}
