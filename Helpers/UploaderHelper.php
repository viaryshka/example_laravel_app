<?php

namespace App\Helpers;

use App\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class UploaderHelper
{
    /**
     * put file.
     * @param  string  $name
     * @param  \Illuminate\Http\UploadedFile  $content
     * @return string
     */
    public function put(string $name, \Illuminate\Http\UploadedFile $content)
    {
        $directory = $this->getDirectory();
        $relativePath = $directory.'/'.$name;
        Storage::disk('public')->putFileAs($directory, $content, $name);

        return $relativePath;
    }

    /**
     * convert image to jpg and put file.
     *
     * @param  string  $name
     * @param  \Illuminate\Http\UploadedFile  $content
     * @return string
     */
    public function putImage(string $name, \Illuminate\Http\UploadedFile $content)
    {
        $directory = $this->getDirectory();
        $relativePath = $directory.'/'.$name;
        $path = storage_path('app/public/').$relativePath;
        Image::make($content)->encode('jpg')->save($path);

        return $relativePath;
    }

    /**
     * if file is image then convert to jpg and put file else put file.
     * @param  string  $name
     * @param  \Illuminate\Http\UploadedFile  $content
     * @return string
     */
    public function checkAndPut(string $name, \Illuminate\Http\UploadedFile $content)
    {
        $ext = $content->getClientOriginalExtension();
        if (in_array($ext, Attachment::allowedImages())) {
            return $this->putImage($name, $content);
        }

        return $this->put($name, $content);
    }

    /**
     * delete file.
     *
     * @param $path
     * @return bool
     */
    public function delete($path)
    {
        $fullPath = storage_path('app/public/').$path;
        Storage::delete($fullPath);

        return true;
    }

    /**
     * get current directory.
     *
     * @return string
     */
    private function getDirectory()
    {
        $now = now();
        $year = $now->format('Y');
        $month = $now->format('m');
        $dir = 'uploads/'.$year.'/'.$month;
        Storage::makeDirectory('public/'.$dir);

        return $dir;
    }

    public function generateName($ext)
    {
        if ($ext == 'jpeg') {
            $ext = 'jpg';
        }

        return Str::uuid().'.'.$ext;
    }
}
