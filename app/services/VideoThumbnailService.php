<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\Coordinate\TimeCode;
use Illuminate\Support\Facades\Storage;

class VideoThumbnailService
{
    protected $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create();
    }

    public function generateThumbnail($videoPath, $thumbnailPath, $timeInSeconds = 1)
    {
        $video = $this->ffmpeg->open($videoPath);
        $video->frame(TimeCode::fromSeconds($timeInSeconds))->save($thumbnailPath);
    }

    public function getThumbnailUrl($thumbnailPath)
    {
        return Storage::url($thumbnailPath);
    }
}
