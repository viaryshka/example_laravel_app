<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Property;
use App\Repositories\AttachmentRepository;
use App\Repositories\PropertyRepository;
use Uploader;

class AttachmentService
{
    private $attachmentRepo;
    private $propertyRepo;

    public function __construct(AttachmentRepository $attachmentRepo, PropertyRepository $propertyRepo)
    {
        $this->attachmentRepo = $attachmentRepo;
        $this->propertyRepo = $propertyRepo;
    }

    /**
     * delete image.
     *
     * @param  Attachment  $image
     * @return bool
     */
    public function delete(Attachment $image)
    {
        $deleted = $this->attachmentRepo->delete($image);
        if (! $deleted) {
            return false;
        }
        Uploader::delete($image->src);
        $owner = $image->attachable;
        if ($owner instanceof Property) {
            $this->propertyRepo->updateAvatar($owner, false);
        }

        return true;
    }
}
