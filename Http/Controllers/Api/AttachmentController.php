<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Services\AttachmentService;

class AttachmentController extends Controller
{
    private $attachmentService;

    public function __construct(AttachmentService $attachmentService)
    {
        $this->authorizeResource(Attachment::class);
        $this->attachmentService = $attachmentService;
    }

    /**
     * delete image route handler.
     * @param  Attachment  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Attachment $attachment)
    {
        $deleted = $this->attachmentService->delete($attachment);
        if (! $deleted) {
            abort(400);
        }

        return response()->noContent();
    }
}
