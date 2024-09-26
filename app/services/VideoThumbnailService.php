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
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries'  =>  env('FFMPEG_BINARIES'),    // مسار ffmpeg
            'ffprobe.binaries' => env('FFPROBE_BINARIES'),   // مسار ffprobe
            'timeout'          => 3600,                       // المدة القصوى للتنفيذ
            'ffmpeg.threads'   => 12,                         // عدد الخيوط المستخدمة
        ]);
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
