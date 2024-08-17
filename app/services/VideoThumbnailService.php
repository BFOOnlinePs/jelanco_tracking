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

    /**
     * Generate thumbnail for a given video.
     *
     * @param String $videoPath
     * @param String $thumbnailPath
     * @param integer $timeInSeconds
     * @return void
     */
    public function generateThumbnail($videoPath, $thumbnailPath, $timeInSeconds = 1)
    {
        $video = $this->ffmpeg->open($videoPath);
        $video->frame(TimeCode::fromSeconds($timeInSeconds))->save($thumbnailPath);
    }

}
