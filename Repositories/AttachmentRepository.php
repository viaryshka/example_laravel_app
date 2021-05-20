<?php

namespace App\Repositories;

use App\Models\Attachment;

class AttachmentRepository
{
    /**
     * create attachment in db.
     *
     * @param $path
     * @param  null  $name
     * @param  null  $type
     * @param  null  $sort
     * @return Attachment
     */
    public function create($path, $name = null, $type = null, $sort = null)
    {
        $attachment = new Attachment([
            'src'  => $path,
            'name' => $name,
            'type' => $type,
            'sort' => $sort,
        ]);
        $attachment->save();

        return $attachment;
    }

    /**
     * delete attachment from db.
     *
     * @param  Attachment  $attachment
     * @return bool
     */
    public function delete(Attachment $attachment)
    {
        try {
            $attachment->delete();
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
        }

        return true;
    }
}
