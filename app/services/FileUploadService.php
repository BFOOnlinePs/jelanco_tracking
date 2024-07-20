<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    /**
     * Handle the file upload.
     *
     * @param UploadedFile $file
     * @param string $folderPath
     * @param string $disk
     * @return string The file path
     */
    public function uploadFile(UploadedFile $file, $folderPath, $disk = 'public')
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $file->storeAs($folderPath, $fileName, $disk);

        return $fileName;
    }


}
