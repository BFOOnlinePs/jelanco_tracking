<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\AttachmentsModel;

class MediaService
{
    /**
     * Get and categorize media (images, videos, files) for a given table and foreign key.
     *
     * @param string $table
     * @param int $foreignKey
     * @return array
     */
    public function getMedia($table, $foreignKey)
    {
        $imageTypes = config('filetypes.image_types');
        $videoTypes = config('filetypes.video_types');

        $images = [];
        $videos = [];
        $files = [];

        // Fetch attachments for the specified table and foreign key
        $attachments = AttachmentsModel::where('a_table', $table)
            ->where('a_fk_id', $foreignKey)
            ->get();

        foreach ($attachments as $attachment) {
            $extension = strtolower(pathinfo($attachment->a_attachment, PATHINFO_EXTENSION));

            if (in_array($extension, $imageTypes)) {
                $images[] = $attachment;
            } elseif (in_array($extension, $videoTypes)) {
                // Generate thumbnail for videos
                $fileNameWithoutExtension = pathinfo($attachment->a_attachment, PATHINFO_FILENAME);
                $thumbnailPath = config('constants.thumbnail_path') . $fileNameWithoutExtension . '.' . config('constants.thumbnail_extension');

                if (Storage::disk('public')->exists($thumbnailPath)) {
                    $attachment->thumbnail = $fileNameWithoutExtension . '.' . config('constants.thumbnail_extension');
                }

                $videos[] = $attachment;
            } else {
                $files[] = $attachment;
            }
        }

        return [
            'images' => $images,
            'videos' => $videos,
            'files' => $files,
        ];
    }
}
